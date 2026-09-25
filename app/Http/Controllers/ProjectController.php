<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Support\ImageDerivatives;
use App\Models\Technology;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['technologies', 'clients', 'links'])
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($request->filled('visibility')) {
            $query->where('visibility', $request->visibility);
        }

        if ($request->filled('client_id')) {
            $query->whereHas('clients', function ($q) use ($request) {
                $q->where('clients.id', $request->client_id);
            });
        }

        $projects = $query->get();
        $clients = Client::orderBy('commercial_name')->get();

        $canReorder = ! $request->filled('visibility') && ! $request->filled('client_id');

        return view('admin.projects.index', compact('projects', 'clients', 'canReorder'));
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'project_ids' => 'required|array',
            'project_ids.*' => 'integer|exists:projects,id',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['project_ids'] as $index => $id) {
                Project::whereKey($id)->update(['sort_order' => $index]);
            }
        });

        return response()->json(['ok' => true]);
    }

    public function updateClientsAssignment(Request $request, Project $project)
    {
        $validated = $request->validate([
            'clients' => 'nullable|array',
            'clients.*' => 'exists:clients,id',
            'is_internal' => 'required|boolean',
        ]);

        $wantsInternal = (bool) ($validated['is_internal'] ?? false);
        $clientIds = array_values(array_unique($validated['clients'] ?? []));

        if ($wantsInternal && $clientIds !== []) {
            throw ValidationException::withMessages([
                'clients' => 'No puedes marcar el proyecto como interno y asociar clientes a la vez.',
            ]);
        }

        $storeAsInternal = $wantsInternal && $clientIds === [];

        $project->update(['is_internal' => $storeAsInternal]);

        if ($storeAsInternal) {
            $project->clients()->sync([]);
        } else {
            $project->clients()->sync($clientIds);
        }

        $project->load('clients');

        return response()->json([
            'is_internal' => (bool) $project->is_internal,
            'clients' => $project->clients->map(fn (Client $c) => [
                'id' => $c->id,
                'commercial_name' => $c->commercial_name,
                'show_url' => route('clients.show', $c),
            ])->values(),
        ]);
    }

    public function create()
    {
        $project = new Project(); 
        $technologies = Technology::orderBy('name')->get();
        $clients = Client::orderBy('commercial_name')->get();
        
        $projectCategories = config('projects.categories', []);
        $linkTypeOptions = $this->linkTypeOptions();
        $initialLinks = $this->initialLinksForForm($project);

        return view('admin.projects.form', compact('project', 'technologies', 'clients', 'projectCategories', 'linkTypeOptions', 'initialLinks'));
    }

    public function edit(Project $project)
    {
        $project->load(['links', 'clients']);

        $technologies = Technology::orderBy('name')->get();
        $clients = Client::orderBy('commercial_name')->get();
        $projectCategories = config('projects.categories', []);
        $linkTypeOptions = $this->linkTypeOptions();
        $initialLinks = $this->initialLinksForForm($project);

        return view('admin.projects.form', compact('project', 'technologies', 'clients', 'projectCategories', 'linkTypeOptions', 'initialLinks'));
    }

    public function update(Request $request, Project $project)
    {
        $this->normalizeSubmittedLinks($request);

        $data = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'order' => 'nullable|array', // Array que nos manda el Front con el orden
            'links' => 'nullable|array',
            'links.*.url' => 'required|url',
            'links.*.type' => ['required', Rule::in(array_keys(config('projects.link_types', [])))],
            'links.*.visibility' => 'required|in:public,private',
            'visibility' => 'required|in:public,private,draft',
            'categories' => 'required|array|min:1',
            'categories.*' => [Rule::in(config('projects.categories', []))],
            'technologies' => 'nullable|array',
            'technologies.*' => 'exists:technologies,id',
            'clients' => 'nullable|array',
            'clients.*' => 'exists:clients,id',
            'is_internal' => 'sometimes|boolean',
        ]);

        $this->validateClientAssignment($request);
        $data['is_internal'] = $this->isInternalProject($request);

        $links = $data['links'] ?? [];

        // GESTIÓN DE IMÁGENES Y SU ORDEN
        $finalImages = [];
        $newFiles = $request->file('images') ??[];
        $order = $request->input('order', []);
        $oldImages = $project->images ??[];

        foreach ($order as $item) {
            if (str_starts_with($item, 'old:')) {
                // Es una imagen que ya existía, la mantenemos en esta posición
                $finalImages[] = substr($item, 4); 
            } elseif (str_starts_with($item, 'new:')) {
                // Es una imagen nueva, sacamos su índice y la subimos
                $index = (int) substr($item, 4);
                if (isset($newFiles[$index])) {
                    $path = $newFiles[$index]->store('projects', 'public');
                    // Copia WebP ligera: el carrusel sirve esa, no el original.
                    ImageDerivatives::generate($path);
                    $finalImages[] = 'storage/' . $path;
                }
            }
        }

        // Borrar del disco las imágenes antiguas que el usuario ha quitado
        $imagesToDelete = array_diff($oldImages, $finalImages);
        foreach ($imagesToDelete as $img) {
            Storage::disk('public')->delete(str_replace('storage/', '', $img));
            ImageDerivatives::forget($img);
        }

        $data['images'] = $finalImages;

        // GUARDAR TODO
        $project->update($data);

        // Sincronizar relaciones
        $project->technologies()->sync($request->technologies ??[]);
        $this->syncProjectClients($project, $request);
        $this->syncProjectLinks($project, $links);

        return redirect()->route('projects.index')->with('success', 'Proyecto actualizado correctamente.');
    }

    public function store(Request $request)
    {
        $this->normalizeSubmittedLinks($request);

        $data = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'order' => 'nullable|array',
            'links' => 'nullable|array',
            'links.*.url' => 'required|url',
            'links.*.type' => ['required', Rule::in(array_keys(config('projects.link_types', [])))],
            'links.*.visibility' => 'required|in:public,private',
            'visibility' => 'required|in:public,private,draft',
            'categories' => 'required|array|min:1',
            'categories.*' => [Rule::in(config('projects.categories', []))],
            'technologies' => 'nullable|array', 
            'technologies.*' => 'exists:technologies,id',
            'clients' => 'nullable|array',
            'clients.*' => 'exists:clients,id',
            'is_internal' => 'sometimes|boolean',
        ]);

        $this->validateClientAssignment($request);
        $data['is_internal'] = $this->isInternalProject($request);

        $links = $data['links'] ?? [];

        // GESTIÓN DE IMÁGENES NUEVAS Y ORDEN
        $finalImages = [];
        $newFiles = $request->file('images') ??[];
        $order = $request->input('order',[]);

        foreach ($order as $item) {
            if (str_starts_with($item, 'new:')) {
                $index = (int) substr($item, 4);
                if (isset($newFiles[$index])) {
                    $path = $newFiles[$index]->store('projects', 'public');
                    // Copia WebP ligera: el carrusel sirve esa, no el original.
                    ImageDerivatives::generate($path);
                    $finalImages[] = 'storage/' . $path;
                }
            }
        }
        
        $data['images'] = $finalImages;

        $nextOrder = ((int) Project::max('sort_order')) + 1;
        $data['sort_order'] = $nextOrder;

        // Crear Proyecto
        $project = Project::create($data);

        // Sincronizar Relaciones
        $project->technologies()->sync($request->technologies ??[]);
        $this->syncProjectClients($project, $request);
        $this->syncProjectLinks($project, $links);

        return redirect()->route('projects.index')->with('success', 'Proyecto creado correctamente.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Proyecto enviado a la papelera. Se eliminará definitivamente en 30 días.');
    }

    private function validateClientAssignment(Request $request): void
    {
        if ($request->boolean('is_internal') && $request->filled('clients')) {
            throw ValidationException::withMessages([
                'clients' => 'No puedes marcar el proyecto como interno y asociar clientes a la vez.',
            ]);
        }
    }

    private function isInternalProject(Request $request): bool
    {
        return $request->boolean('is_internal') && ! $request->filled('clients');
    }

    private function syncProjectClients(Project $project, Request $request): void
    {
        if ($this->isInternalProject($request)) {
            $project->clients()->sync([]);

            return;
        }

        $project->clients()->sync($request->input('clients', []));
    }

    private function linkTypeOptions(): array
    {
        return collect(config('projects.link_types', []))
            ->map(fn (string $label, string $value) => ['value' => $value, 'label' => $label])
            ->values()
            ->all();
    }

    private function initialLinksForForm(Project $project): array
    {
        if (is_array(old('links'))) {
            return array_values(old('links'));
        }

        if (! $project->exists) {
            return [];
        }

        return $project->links
            ->map(fn ($link) => [
                'url' => $link->url,
                'type' => $link->type,
                'visibility' => $link->visibility,
            ])
            ->values()
            ->all();
    }

    private function normalizeSubmittedLinks(Request $request): void
    {
        $request->merge([
            'links' => collect($request->input('links', []))
                ->filter(fn ($link) => filled($link['url'] ?? null))
                ->map(function (array $link) {
                    $url = trim($link['url']);

                    if (! preg_match('/^https?:\/\//i', $url)) {
                        $url = 'https://'.$url;
                    }

                    $link['url'] = $url;

                    return $link;
                })
                ->values()
                ->all(),
        ]);
    }

    private function syncProjectLinks(Project $project, array $links): void
    {
        $project->links()->delete();

        foreach ($links as $index => $link) {
            $project->links()->create([
                'url' => $link['url'],
                'type' => $link['type'],
                'visibility' => $link['visibility'] ?? 'public',
                'sort_order' => $index,
            ]);
        }
    }
}