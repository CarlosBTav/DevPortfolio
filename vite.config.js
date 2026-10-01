/**
 * CONFIGURACIÓN DE VITE - "La Fábrica de Recursos"
 * 
 * ¿Qué hace este archivo?
 * 1. Define los "Puntos de Entrada": Archivos CSS/JS que Vite debe procesar (ver sección 'input').
 * 2. Compresión y Optimización: Al ejecutar 'npm run build', transforma el código de 'resources/' 
 *    en archivos ligeros, seguros y con nombres únicos dentro de 'public/build/'.
 * 3. Generación del Manifiesto: Crea un mapa (manifest.json) para que Laravel sepa qué 
 *    archivo final corresponde a cada archivo fuente, evitando problemas de caché.
 * 
 * Nota: Si añades un nuevo CSS o JS y lo llamas desde un Blade usando @vite(), 
 * HAY incluirlo aquí en la lista de 'input'.
 */

import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/public-layout.css',
                'resources/js/app.js',
                'resources/js/public-layout.js',
                'resources/css/spotlight.css',
                'resources/js/spotlight.js',
                'resources/js/ai-dots-touch.js',
                'resources/js/documentation-notes.js',
                'resources/js/public-faq-accordion.js',
                'resources/js/experience-autoscroll.js',
                'resources/js/overscroll-bounce.js',
            ],
            refresh: true,
        }),
    ],
});
