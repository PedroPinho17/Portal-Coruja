<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EquipaController;
use App\Http\Controllers\NoticiasController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TeamController as AdminEquipeController;
use App\Http\Controllers\Admin\AdminPostController as AdminPostController;
use App\Http\Controllers\Admin\EntityController as AdminEntityController;
use App\Http\Controllers\Admin\FormationController as AdminFormationPostController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SchoolProtocolController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/galeria', function () {
    return view('galeria');
})->name('galeria');

Route::get('/equipa', [EquipaController::class, 'index'])->name('equipa');

Route::get('/noticias', [NoticiasController::class, 'index'])->name('noticias');

Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// API Auth route (for JSON responses)
Route::post('/api/auth/login', [LoginController::class, 'login'])->name('api.auth.login');

// Web Auth routes (login/logout) - com rate limiting para prevenir força bruta
// GET /login - sem middleware guest para permitir que o controller trate utilizadores autenticados
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// POST /login - com middleware guest para prevenir acesso de utilizadores já autenticados
Route::middleware('guest')->group(function () {
    // Rate limiting: 15 tentativas de login por 1 minuto por IP
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:15,1')
        ->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// WebAuthn Routes - Comentadas porque o pacote LaravelWebauthn não está instalado
// Descomente estas rotas se instalar o pacote: composer require asbiin/laravel-webauthn
Route::prefix('webauthn')->group(function () {
    // Authentication routes
    if (config('webauthn.views.authenticate') !== null) {
        Route::get('auth', [\LaravelWebauthn\Http\Controllers\AuthenticateController::class, 'create'])
            ->middleware(['web', 'throttle:10,1']) // 10 tentativas por minuto
            ->name('webauthn.login');
    }
    
    // Rota personalizada para auth/options - sempre retorna JSON
    // Rate limiting: 10 tentativas por minuto para prevenir abuso
    Route::post('auth/options', [\App\Http\Controllers\Webauthn\AuthenticateController::class, 'create'])
        ->middleware(['web', 'throttle:10,1'])
        ->name('webauthn.auth.options');
    
    // Rate limiting: 5 tentativas por 15 minutos (mais restritivo para autenticação final)
    Route::post('auth', [\LaravelWebauthn\Http\Controllers\AuthenticateController::class, 'store'])
        ->middleware(['web', 'throttle:5,15'])
        ->name('webauthn.auth');
    
    // Webauthn key registration routes (requerem autenticação)
    Route::middleware(['auth:web'])->group(function () {
        if (config('webauthn.views.register') !== null) {
            Route::get('keys/create', [\LaravelWebauthn\Http\Controllers\WebauthnKeyController::class, 'create'])
                ->name('webauthn.create');
        }
        // Rate limiting: 10 tentativas por minuto para registro de chaves
        Route::post('keys/options', [\LaravelWebauthn\Http\Controllers\WebauthnKeyController::class, 'create'])
            ->middleware('throttle:10,1')
            ->name('webauthn.store.options');
        Route::post('keys', [\LaravelWebauthn\Http\Controllers\WebauthnKeyController::class, 'store'])
            ->middleware('throttle:5,15') // 5 tentativas por 15 minutos para registro final
            ->name('webauthn.store');
        Route::delete('keys/{id}', [\LaravelWebauthn\Http\Controllers\WebauthnKeyController::class, 'destroy'])
            ->name('webauthn.destroy');
        Route::put('keys/{id}', [\LaravelWebauthn\Http\Controllers\WebauthnKeyController::class, 'update'])
            ->name('webauthn.update');
        Route::post('confirm-key', [\LaravelWebauthn\Http\Controllers\ConfirmableKeyController::class, 'store'])
            ->middleware('throttle:5,15') // 5 tentativas por 15 minutos para confirmação
            ->name('webauthn.key.confirm');
    });
});

// Admin (Backoffice) - protected by session auth and admin check
Route::middleware(['auth', 'admin', 'no_back', 'require.password.change'])->prefix('/admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Team index (paginated)
    Route::get('/teams', [AdminEquipeController::class, 'index'])->name('admin.teams.index');
    Route::get('/teams/create', [AdminEquipeController::class, 'create'])->name('admin.teams.create');
    Route::post('/teams', [AdminEquipeController::class, 'store'])->name('admin.teams.store');
    Route::get('/teams/{team}/edit', [AdminEquipeController::class, 'edit'])->name('admin.teams.edit');
    Route::put('/teams/{team}', [AdminEquipeController::class, 'update'])->name('admin.teams.update');
    Route::get('/teams/{team}/check-relations', [\App\Http\Controllers\Admin\TeamController::class, 'checkRelations'])->name('admin.teams.check_relations');
    Route::delete('/teams/{team}', [AdminEquipeController::class, 'destroy'])->name('admin.teams.destroy');

    // AJAX: Reordenação de idiomas via DataTables RowReorder
    Route::post('/teams/reorder', [\App\Http\Controllers\Admin\TeamController::class, 'reorder'])->name('admin.teams.reorder');


    // Post index (paginated)
    Route::get('/posts', [AdminPostController::class, 'index'])->name('admin.posts.index');
    Route::get('/posts/create', [AdminPostController::class, 'create'])->name('admin.posts.create');
    Route::post('/posts', [AdminPostController::class, 'store'])->name('admin.posts.store');
    Route::get('/posts/{post}/edit', [AdminPostController::class, 'edit'])->name('admin.posts.edit');
    Route::put('/posts/{post}', [AdminPostController::class, 'update'])->name('admin.posts.update');
    Route::get('/posts/{post}/check-relations', [\App\Http\Controllers\Admin\PostController::class, 'checkRelations'])->name('admin.posts.check_relations');
    Route::delete('/posts/{post}', [AdminPostController::class, 'destroy'])->name('admin.posts.destroy');

    // AJAX: Reordenação de idiomas via DataTables RowReorder
    Route::post('/posts/reorder', [\App\Http\Controllers\Admin\AdminPostController::class, 'reorder'])->name('admin.posts.reorder');

    // AJAX toggle ativo
    Route::post('/posts/{post}/toggle-ativo', [AdminPostController::class, 'toggleAtivo'])->name('admin.posts.toggleAtivo');


    // Entity index (paginated)
    Route::get('/entities', [AdminEntityController::class, 'index'])->name('admin.entities.index');
    Route::get('/entities/create', [AdminEntityController::class, 'create'])->name('admin.entities.create');
    Route::post('/entities', [AdminEntityController::class, 'store'])->name('admin.entities.store');
    Route::get('/entities/{entity}/edit', [AdminEntityController::class, 'edit'])->name('admin.entities.edit');
    Route::put('/entities/{entity}', [AdminEntityController::class, 'update'])->name('admin.entities.update');
    Route::get('/entities/{entity}/check-relations', [\App\Http\Controllers\Admin\EntityController::class, 'checkRelations'])->name('admin.entities.check_relations');
    Route::delete('/entities/{entity}', [AdminEntityController::class, 'destroy'])->name('admin.entities.destroy');
    // AJAX: Reordenação de idiomas via DataTables RowReorder
    Route::post('/entities/reorder', [\App\Http\Controllers\Admin\EntityController::class, 'reorder'])->name('admin.entities.reorder');
    
    // Formações index (paginated)
    Route::get('/formations', [AdminFormationPostController::class, 'index'])->name('admin.formations.index');
    Route::get('/formations/create', [AdminFormationPostController::class, 'create'])->name('admin.formations.create');
    Route::post('/formations', [AdminFormationPostController::class, 'store'])->name('admin.formations.store');
    Route::get('/formations/{formation}/edit', [AdminFormationPostController::class, 'edit'])->name('admin.formations.edit');
    Route::put('/formations/{formation}', [AdminFormationPostController::class, 'update'])->name('admin.formations.update');
    Route::get('/formations/{formation}/check-relations', [\App\Http\Controllers\Admin\FormationController::class, 'checkRelations'])->name('admin.formations.check_relations');
    Route::delete('/formations/{formation}', [AdminFormationPostController::class, 'destroy'])->name('admin.formations.destroy');

    // AJAX: Reordenação de idiomas via DataTables RowReorder
    Route::post('/formations/reorder', [\App\Http\Controllers\Admin\AdminFormationPostController::class, 'reorder'])->name('admin.formations.reorder');
    // AJAX toggle ativo
    Route::post('/formations/{formation}/toggle-ativo', [AdminFormationPostController::class, 'toggleAtivo'])->name('admin.formations.toggleAtivo');

    // Protocols index (paginated)
    Route::get('/protocols', [SchoolProtocolController::class, 'index'])->name('admin.protocols.index');
    Route::get('/protocols/create', [SchoolProtocolController::class, 'create'])->name('admin.protocols.create');
    Route::post('/protocols', [SchoolProtocolController::class, 'store'])->name('admin.protocols.store');
    Route::get('/protocols/{protocol}/edit', [SchoolProtocolController::class, 'edit'])->name('admin.protocols.edit');
    Route::put('/protocols/{protocol}', [SchoolProtocolController::class, 'update'])->name('admin.protocols.update');
    Route::delete('/protocols/{protocol}', [SchoolProtocolController::class, 'destroy'])->name('admin.protocols.destroy');

    // AJAX: Reordenação de protocolos via DataTables RowReorder
    Route::post('/protocols/reorder', [SchoolProtocolController::class, 'reorder'])->name('admin.protocols.reorder');
    // AJAX toggle ativo
    Route::post('/protocols/{protocol}/toggle-ativo', [SchoolProtocolController::class, 'toggleAtivo'])->name('admin.protocols.toggleAtivo');

    // Perfil do utilizador autenticado
    Route::get('/perfil', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('admin.perfil.edit');
    Route::put('/perfil', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('admin.perfil.update');

    // Mudança obrigatória de password (primeiro login)
    Route::get('/password/force-change', [\App\Http\Controllers\Admin\ForcePasswordChangeController::class, 'show'])->name('admin.password.force-change');
    Route::put('/password/force-change', [\App\Http\Controllers\Admin\ForcePasswordChangeController::class, 'update'])->name('admin.password.force-change.update');

    // Users actions
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::get('/users/{id}/check-relations', [UserController::class, 'checkRelations'])->name('admin.users.check_relations');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
});