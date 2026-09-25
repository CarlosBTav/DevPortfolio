<?php

namespace App\Models;

use App\Services\Documentation\DocumentationConfigStore;
use App\Support\ClientDocumentation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Support\ImageDerivatives;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    use HasFactory, Prunable, SoftDeletes;

    protected $fillable =[
        'title', 'description', 'images', 'sort_order',
        'visibility', 'is_internal', 'categories', 'url_demo', 'url_repo', 'demo_cta_label'
    ];

    protected $casts =[
        'images' => 'array',
        'categories' => 'array',
        'is_internal' => 'boolean',
    ];

    public function technologies()
    {
        return $this->belongsToMany(Technology::class);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'project_client');
    }

    public function links()
    {
        return $this->hasMany(ProjectLink::class)->orderBy('sort_order')->orderBy('id');
    }

    public function publicLinks()
    {
        return $this->links()->where('visibility', 'public');
    }

    public function prunable(): Builder
    {
        return static::onlyTrashed()
            ->where('deleted_at', '<=', now()->subDays(30));
    }

    protected function pruning(): void
    {
        $this->deleteStoredImages();
        $this->deleteAssociatedDocumentation();
    }

    private function deleteStoredImages(): void
    {
        foreach ($this->images ?? [] as $image) {
            Storage::disk('public')->delete(str_replace('storage/', '', $image));
            ImageDerivatives::forget($image);
        }
    }

    private function deleteAssociatedDocumentation(): void
    {
        $clients = $this->clients()->get();
        $documentationSlugs = $clients
            ->flatMap(fn (Client $client) => ClientDocumentation::linkedSlugs($client))
            ->unique()
            ->values();

        if ($documentationSlugs->isEmpty()) {
            return;
        }

        foreach ($clients as $client) {
            if ($client->documentation_slug && $documentationSlugs->contains($client->documentation_slug)) {
                $client->update(['documentation_slug' => null]);
            }
        }

        app(DocumentationConfigStore::class)->deleteVersions($documentationSlugs->all());
    }
}
