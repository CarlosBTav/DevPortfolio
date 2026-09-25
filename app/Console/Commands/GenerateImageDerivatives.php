<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Support\ImageDerivatives;
use Illuminate\Console\Command;

/**
 * Genera las copias ligeras de las imágenes ya subidas.
 *
 * Las subidas nuevas las generan solas (ProjectController), así que esto es
 * para el histórico y para rehacerlas si se cambian los anchos. Es idempotente:
 * lo que ya está al día no se vuelve a comprimir.
 */
class GenerateImageDerivatives extends Command
{
    protected $signature = 'images:derive {--force : Rehace las copias aunque estén al día}';

    protected $description = 'Genera las versiones WebP ligeras de las imágenes de los proyectos';

    public function handle(): int
    {
        $images = Project::query()
            ->whereNotNull('images')
            ->pluck('images')
            ->flatten()
            ->filter()
            ->unique()
            ->values();

        if ($images->isEmpty()) {
            $this->info('No hay imágenes que procesar.');

            return self::SUCCESS;
        }

        if ($this->option('force')) {
            $images->each(fn (string $stored) => ImageDerivatives::forget($stored));
        }

        $bar = $this->output->createProgressBar($images->count());
        $bar->start();

        $written = 0;
        foreach ($images as $stored) {
            $relative = preg_replace('#^/?storage/#', '', $stored);
            $written += ImageDerivatives::generate($relative);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Copias generadas: {$written} (imágenes revisadas: {$images->count()}).");

        return self::SUCCESS;
    }
}
