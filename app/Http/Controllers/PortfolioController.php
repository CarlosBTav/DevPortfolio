<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Project;
use App\Models\DocumentationClientNote;
use App\Models\QuoteVersion;

class PortfolioController extends Controller
{
    public function index()
    {
        // Portada: Pedimos al Modelo 3 proyectos para el "destacados"
            /* Usamos 'with' (Eager Loading) para traer las tecnologías de golpe
               y ahorrar consultas a la base de datos (mejor rendimiento).    */
        $projects = Project::with(['technologies', 'links'])
                    ->where('visibility', 'public')
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();

        // Devolvemos la vista (el HTML) pasándole los datos
        return view('public.home', compact('projects'));
    }

    // Método para la página de "Ver todos los proyectos"
    public function showAll()
    {
        // Aquí traemos todos, pero paginados de 9 en 9 para que cargue rápido
        $projects = Project::with(['technologies', 'links'])
                    ->where('visibility', 'public')
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->paginate(9); 

        return view('public.projects', compact('projects'));
    }

    //Método para la página de historia
    public function about()
    {
        $jobs = collect(config('experience.jobs'))
            ->map(fn (array $job) => $job + [
                'current' => collect($job['periods'])->contains(fn ($period) => $period[1] === null),
                'starts_at' => collect($job['periods'])->min(fn ($period) => $period[0]),
            ])
            ->sortBy('starts_at')
            ->groupBy('track');

        $experienceTracks = collect(config('experience.tracks'))
            ->map(fn (array $track, string $key) => $track + ['jobs' => $jobs->get($key, collect())->values()])
            ->filter(fn (array $track) => $track['jobs']->isNotEmpty());

        return view('public.about', compact('experienceTracks'));
    }

    // Método para la página de stack tecnológico
    public function stack()
    {
        return view('public.stack');
    }

    public function contact()
    {
        return view('public.contact-funnel');
    }

    public function services()
    {
        return view('public.services.index');
    }

    public function webDevelopment()
    {
        $featuredProjects = Project::with(['technologies', 'links'])
            ->where('visibility', 'public')
            ->whereJsonContains('categories', 'Web')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->take(3)
            ->get();

        $whatsappPhone = preg_replace('/\D+/', '', (string) config('services.callmebot_whatsapp.phone'));

        return view('public.services.web-development', compact('featuredProjects', 'whatsappPhone'));
    }

    public function appDevelopment()
    {
        $featuredProjects = Project::with(['technologies', 'links'])
            ->where('visibility', 'public')
            ->whereJsonContains('categories', 'Mobile')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->take(3)
            ->get();

        $whatsappPhone = preg_replace('/\D+/', '', (string) config('services.callmebot_whatsapp.phone'));

        return view('public.services.app-development', compact('featuredProjects', 'whatsappPhone'));
    }

    public function faq()
    {
        return view('public.faq');
    }

    public function cv()
    {
        return view('public.cv');
    }

    public function cvDownload()
    {
        return redirect()->route('public.cv', ['descargar' => 'pdf']);
    }

    public function quote(string $slug = 'general')
    {
        $quoteVersion = QuoteVersion::with('client')->where('slug', $slug)->first();

        if ($quoteVersion) {
            return view('public.quote', [
                'quote' => [
                    'title' => $quoteVersion->title,
                    'client_name' => $quoteVersion->client->commercial_name,
                    'updated_at' => optional($quoteVersion->quote_date)->toDateString() ?? $quoteVersion->updated_at->toDateString(),
                    'currency' => $quoteVersion->currency,
                    'notes' => $quoteVersion->notes ?? [],
                    'items' => $quoteVersion->items ?? [],
                ],
                'slug' => $slug,
            ]);
        }

        $quoteVersions = config('quotes.versions', []);
        $quote = $quoteVersions[$slug] ?? null;

        if (! $quote) {
            abort(404);
        }

        return view('public.quote', compact('quote', 'slug'));
    }

    public function documentation(string $slug = 'general')
    {
        $documentationVersions = config('documentation.versions', []);
        $documentation = $documentationVersions[$slug] ?? null;

        if (! $documentation) {
            abort(404);
        }

        $clientNotes = ($documentation['allow_client_notes'] ?? false)
            ? DocumentationClientNote::query()
                ->where('documentation_slug', $slug)
                ->orderByDesc('created_at')
                ->get()
            : collect();

        $pendingNotesByIndex = $clientNotes
            ->filter(fn (DocumentationClientNote $note) => $note->pending_item_index !== null)
            ->groupBy(fn (DocumentationClientNote $note) => (int) $note->pending_item_index);

        $generalClientNotes = $clientNotes
            ->filter(fn (DocumentationClientNote $note) => $note->pending_item_index === null)
            ->values();

        return view('public.documentation', compact('documentation', 'slug', 'generalClientNotes', 'pendingNotesByIndex'));
    }

    public function storeDocumentationNote(Request $request, string $slug)
    {
        $documentationVersions = config('documentation.versions', []);
        $documentation = $documentationVersions[$slug] ?? null;

        if (! $documentation || ! ($documentation['allow_client_notes'] ?? false)) {
            abort(404);
        }

        $pendingInformation = $documentation['pending_information'] ?? [];
        $allowedPendingIndexes = is_array($pendingInformation) ? array_keys($pendingInformation) : [];

        $validated = $request->validate([
            'body' => 'required|string|min:3|max:8000',
            'pending_item_index' => ['nullable', 'integer', 'min:0', Rule::in($allowedPendingIndexes)],
        ], [
            'body.required' => 'Escribe algo antes de enviar.',
            'body.min' => 'Añade al menos unas pocas palabras para que quede claro el apunte.',
            'pending_item_index.in' => 'Ese punto de la lista ya no existe o ha cambiado. Recarga la página e inténtalo de nuevo.',
        ]);

        $pendingIndex = $validated['pending_item_index'] ?? null;

        $note = DocumentationClientNote::query()->create([
            'documentation_slug' => $slug,
            'body' => $validated['body'],
            'pending_item_index' => $pendingIndex,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'note' => [
                    'id' => $note->id,
                    'body' => $note->body,
                    'pending_item_index' => $note->pending_item_index,
                    'created_at' => $note->created_at->timezone(config('app.timezone'))->translatedFormat('d M Y, H:i'),
                    'destroy_url' => auth()->check()
                        ? route('documentation.notes.destroy', [$slug, $note])
                        : null,
                ],
            ]);
        }

        $response = back()->with('documentation_note_success', true);

        if ($pendingIndex !== null) {
            return $response->with('documentation_pending_response_index', $pendingIndex);
        }

        return $response
            ->withFragment('apuntes-cliente');
    }

    public function destroyDocumentationNote(Request $request, string $slug, DocumentationClientNote $note)
    {
        $documentationVersions = config('documentation.versions', []);
        $documentation = $documentationVersions[$slug] ?? null;

        if (! $documentation || ! ($documentation['allow_client_notes'] ?? false)) {
            abort(404);
        }

        if ($note->documentation_slug !== $slug) {
            abort(404);
        }

        $note->delete();

        if ($request->expectsJson()) {
            return response()->json(['deleted' => true]);
        }

        return back()
            ->withFragment('apuntes-cliente')
            ->with('documentation_note_deleted', true);
    }
}