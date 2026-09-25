<?php

/*
|--------------------------------------------------------------------------
| Datos de contacto públicos
|--------------------------------------------------------------------------
|
| Las vistas llamaban a env() directamente. Con la configuración cacheada
| (`php artisan config:cache`, que es como corre esto en producción) Laravel
| NO carga el fichero .env, así que env() devuelve siempre el valor por
| defecto y cambiar el .env no tiene ningún efecto. Silencioso: no da error,
| simplemente ignora lo que pongas.
|
| Por eso viven aquí: config() sí lee del caché, y env() solo se usa dentro
| de config/, que es donde Laravel espera encontrarlo.
|
*/

return [

    'email' => env('APP_CONTACT_EMAIL', ''),

    'phone' => env('APP_CONTACT_PHONE', ''),

    'location' => env('APP_CONTACT_LOCATION', ''),

    'github' => env('APP_GITHUB_URL', 'https://github.com/CarlosBTav'),

    'linkedin' => env('APP_LINKEDIN_URL', 'https://www.linkedin.com/in/carlos-b-6a8a9a2b5/'),

];
