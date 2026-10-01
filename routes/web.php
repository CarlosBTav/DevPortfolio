<?php

/*
|--------------------------------------------------------------------------
| importación de todas las rutas de la web
|--------------------------------------------------------------------------
*/
use Illuminate\Support\Facades\Route;
//Rutas públicas
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ContactController;

//Rutas Privadas
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SeoController;

/*
|--------------------------------------------------------------------------
| Rutas Web PÚBLICAS
|--------------------------------------------------------------------------
*/

// Portada
Route::get('/', [PortfolioController::class, 'index'])->name('home');

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');

// Ubicación tienda Musical Luthier (misma fuente que config/documentation.php → musical-luthier)
Route::view('/welcome', 'welcome')->name('welcome');

// Ruta para ver el catálogo completo
Route::get('/proyectos', [PortfolioController::class, 'showAll'])->name('public.projects');

// Ruta de servicios (overview)
Route::get('/servicios', [PortfolioController::class, 'services'])->name('public.services');

// Rutas de servicios (detalle)
Route::get('/servicios/desarrollo-web', [PortfolioController::class, 'webDevelopment'])->name('public.services.web');
Route::get('/servicios/desarrollo-apps', [PortfolioController::class, 'appDevelopment'])->name('public.services.app');
Route::get('/servicios/preguntas-frecuentes', [PortfolioController::class, 'faq'])->name('public.services.faq');

Route::redirect('/preguntas-frecuentes', '/servicios/preguntas-frecuentes', 301);

// Para mensajes de contacto
Route::post('/contact', [ContactController::class, 'storePublicMessage'])->name('contact.store');

// Ruta para "Sobre mí / Historia"
Route::get('/sobre-mi',[PortfolioController::class, 'about'])->name('public.about');

// Página de stack tecnológico
Route::get('/stack-tecnologico', [PortfolioController::class, 'stack'])->name('public.stack');

// Asistente de contacto (embudo, sin recargas entre pasos)
Route::get('/contacto', [PortfolioController::class, 'contact'])->name('public.contact');

// CV (solo por enlace directo, sin entrada en el menú público)
Route::get('/cv', [PortfolioController::class, 'cv'])->name('public.cv');
Route::get('/cv/descargar', [PortfolioController::class, 'cvDownload'])->name('public.cv.download');

// Presupuestos (solo por enlace directo, sin entrada en el menú público)
Route::get('/presupuesto/{slug?}', [PortfolioController::class, 'quote'])
    ->where('slug', '[A-Za-z0-9\-_]+')
    ->name('public.quote');

// Documentación compartida con clientes (solo por enlace directo)
Route::get('/documentacion/{slug?}', [PortfolioController::class, 'documentation'])
    ->where('slug', '[A-Za-z0-9\-_]+')
    ->name('public.documentation');
Route::post('/documentacion/{slug}/apuntes', [PortfolioController::class, 'storeDocumentationNote'])
    ->middleware('throttle:12,1')
    ->where('slug', '[A-Za-z0-9\-_]+')
    ->name('public.documentation.notes.store');

/*
|--------------------------------------------------------------------------
| Rutas Web PRIVADAS  (Backend / Admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard'); 
    })->name('dashboard');


    Route::patch('projects/reorder', [ProjectController::class, 'reorder'])->name('projects.reorder');

    Route::patch('projects/{project}/clients-assignment', [ProjectController::class, 'updateClientsAssignment'])
        ->name('projects.clients-assignment');

    // Esto crea automáticamente las rutas: projects.index, projects.create, projects.store...
    Route::resource('projects', ProjectController::class);


    // CRUD de Clientes
    Route::resource('clients', ClientController::class);
    Route::post('/clients/{client}/quotes', [ClientController::class, 'storeQuote'])->name('clients.quotes.store');
    Route::delete('/clients/{client}/quotes/{quoteVersion}', [ClientController::class, 'destroyQuote'])->name('clients.quotes.destroy');


    // GESTIÓN DE CONTACTOS (Agenda dentro de Clientes)
    // 1. Guardar nuevo contacto vinculado a un cliente
    Route::post('/clients/{client}/contacts', [ContactController::class, 'store'])->name('client.contacts.store');
    // 2. Actualizar un contacto existente
    Route::put('/contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    // 3. Borrar un contacto
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');


    // CRUD de Mensajes
    Route::resource('messages', MessageController::class)->only(['index', 'show', 'destroy']);
    Route::put('/messages/{message}/assign', [MessageController::class, 'assign'])->name('messages.assign');

    Route::delete('/documentacion/{slug}/apuntes/{note}', [PortfolioController::class, 'destroyDocumentationNote'])
        ->where('slug', '[A-Za-z0-9\-_]+')
        ->name('documentation.notes.destroy');

    // Perfil de Usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas de Autenticación (Login, Register, etc.)
require __DIR__.'/auth.php';
