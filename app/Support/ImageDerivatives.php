<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Versiones ligeras (WebP) de las imágenes subidas, para no servir el original.
 *
 * Las capturas de los proyectos se suben tal cual (PNG de 2500 px y varios MB).
 * El carrusel las muestra en una caja `aspect-video` de unos 400-600 px, así
 * que servir el original significaba mandar ~19 MB en la portada. Aquí se
 * generan copias WebP de 640 y 1280 px de ancho y la vista las sirve con
 * `srcset`, dejando que el navegador elija.
 *
 * Reglas:
 * - **El original no se toca nunca.** Las copias viven en un subdirectorio
 *   `opt/` al lado, y si algo falla se sirve el original: una imagen pesada es
 *   un problema, una imagen rota es otro peor.
 * - Es idempotente: si la copia ya existe y es más nueva que el original, no
 *   se rehace. Se puede relanzar sin coste.
 * - Solo JPEG, PNG y WebP. Un GIF animado se rompería al recomprimirlo y un
 *   SVG ya es ligero, así que se dejan como están.
 */
class ImageDerivatives
{
    /** Anchos que se generan, de menor a mayor. */
    public const WIDTHS = [640, 1280];

    /** Calidad WebP: por encima de ~85 el tamaño sube mucho y no se nota. */
    private const QUALITY = 82;

    /**
     * Techo de píxeles que aceptamos descomprimir en memoria (~48 MP).
     * GD necesita ~4 bytes por píxel, así que esto son unos 190 MB en el peor
     * caso. Por encima se deja el original: mejor una imagen pesada que un
     * proceso muerto por falta de memoria.
     */
    private const MAX_PIXELS = 48_000_000;

    private const EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Datos listos para un <img>: `src` (el ancho mayor), `srcset` y, cuando se
     * conocen, `width` y `height` reales para que el navegador reserve el hueco.
     *
     * Devuelve siempre algo usable: si no hay copias, apunta al original.
     *
     * @param  string  $stored  Ruta guardada en base de datos, p. ej. "storage/projects/x.png"
     * @return array{src: string, srcset: string, width: int|null, height: int|null}
     */
    public static function img(string $stored): array
    {
        $fallback = ['src' => asset($stored), 'srcset' => '', 'width' => null, 'height' => null];

        $relative = static::relative($stored);
        if ($relative === null) {
            return $fallback;
        }

        $sources = [];
        foreach (static::WIDTHS as $width) {
            $candidate = static::derivativePath($relative, $width);
            if (Storage::disk('public')->exists($candidate)) {
                $sources[$width] = asset('storage/' . $candidate);
            }
        }

        if ($sources === []) {
            return $fallback + static::dimensions($relative);
        }

        $srcset = [];
        foreach ($sources as $width => $url) {
            $srcset[] = $url . ' ' . $width . 'w';
        }

        return [
            'src' => end($sources),
            'srcset' => implode(', ', $srcset),
        ] + static::dimensions($relative);
    }

    /**
     * Genera las copias que falten de una imagen del disco `public`.
     *
     * @param  string  $relative  Ruta dentro del disco, p. ej. "projects/x.png"
     * @return int Número de copias escritas (0 si ya estaban o no aplica)
     */
    public static function generate(string $relative): int
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($relative)) {
            return 0;
        }

        $extension = strtolower(pathinfo($relative, PATHINFO_EXTENSION));
        if (! in_array($extension, static::EXTENSIONS, true)) {
            return 0;
        }

        $source = $disk->path($relative);
        $info = @getimagesize($source);
        if ($info === false) {
            return 0;
        }

        [$srcWidth, $srcHeight] = $info;
        if ($srcWidth * $srcHeight > static::MAX_PIXELS) {
            Log::warning('ImageDerivatives: imagen demasiado grande, se deja el original', [
                'path' => $relative,
                'pixels' => $srcWidth * $srcHeight,
            ]);

            return 0;
        }

        $written = 0;

        foreach (static::WIDTHS as $width) {
            // Ampliar una imagen pequeña solo añade peso sin ganar nitidez.
            if ($srcWidth <= $width && $extension === 'webp') {
                continue;
            }

            $target = static::derivativePath($relative, $width);

            if ($disk->exists($target)
                && $disk->lastModified($target) >= $disk->lastModified($relative)) {
                continue;
            }

            if (static::write($source, $disk->path($target), min($width, $srcWidth), $srcWidth, $srcHeight)) {
                $written++;
            }
        }

        return $written;
    }

    /** Borra las copias de una imagen (al borrar el original). */
    public static function forget(string $stored): void
    {
        $relative = static::relative($stored);
        if ($relative === null) {
            return;
        }

        foreach (static::WIDTHS as $width) {
            Storage::disk('public')->delete(static::derivativePath($relative, $width));
        }
    }

    /** "storage/projects/x.png" → "projects/x.png"; null si no es del disco público. */
    private static function relative(string $stored): ?string
    {
        $stored = ltrim($stored, '/');

        if (! str_starts_with($stored, 'storage/')) {
            return null;
        }

        return substr($stored, strlen('storage/'));
    }

    /** "projects/x.png" + 640 → "projects/opt/x-640.webp" */
    private static function derivativePath(string $relative, int $width): string
    {
        $directory = trim(dirname($relative), '.' . DIRECTORY_SEPARATOR);
        $name = pathinfo($relative, PATHINFO_FILENAME);

        return ($directory !== '' ? $directory . '/' : '') . 'opt/' . $name . '-' . $width . '.webp';
    }

    /** Dimensiones reales del original, para el hueco que reserva el navegador. */
    private static function dimensions(string $relative): array
    {
        $info = @getimagesize(Storage::disk('public')->path($relative));

        return $info === false
            ? ['width' => null, 'height' => null]
            : ['width' => $info[0], 'height' => $info[1]];
    }

    /** Redimensiona y guarda como WebP. Devuelve true si escribió el fichero. */
    private static function write(string $source, string $target, int $width, int $srcWidth, int $srcHeight): bool
    {
        $image = @imagecreatefromstring(@file_get_contents($source) ?: '');
        if ($image === false) {
            return false;
        }

        try {
            $height = (int) round($srcHeight * ($width / $srcWidth));
            $resized = imagescale($image, $width, $height, IMG_BICUBIC);
            if ($resized === false) {
                return false;
            }

            // Sin esto, un PNG con transparencia sale con el fondo negro.
            imagepalettetotruecolor($resized);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);

            if (! is_dir($directory = dirname($target))) {
                mkdir($directory, 0775, true);
            }

            $ok = imagewebp($resized, $target, static::QUALITY);
            imagedestroy($resized);

            return $ok;
        } catch (\Throwable $e) {
            Log::warning('ImageDerivatives: no se pudo generar la copia', [
                'target' => $target,
                'error' => $e->getMessage(),
            ]);

            return false;
        } finally {
            imagedestroy($image);
        }
    }
}
