<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
// WebAuthn package not installed - commenting out related code
// use LaravelWebauthn\Contracts\DestroyResponse as DestroyResponseContract;
// use LaravelWebauthn\Contracts\RegisterSuccessResponse as RegisterSuccessResponseContract;
// use LaravelWebauthn\Actions\LoginUserRetrieval;
// use LaravelWebauthn\Services\Webauthn;
// use App\Http\Responses\WebauthnDestroyResponse;
// use App\Http\Responses\WebauthnRegisterSuccessResponse;
// use App\Actions\CustomLoginUserRetrieval;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // WebAuthn package not installed - commenting out related code
        // Bind custom WebAuthn responses
        // $this->app->singleton(DestroyResponseContract::class, WebauthnDestroyResponse::class);
        // $this->app->singleton(RegisterSuccessResponseContract::class, WebauthnRegisterSuccessResponse::class);
        
        // Bind custom LoginUserRetrieval para buscar utilizadores apenas pelo email (sem password)
        // $this->app->singleton(LoginUserRetrieval::class, CustomLoginUserRetrieval::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // WebAuthn package not installed - commenting out related code
        // Desabilitar registo automático de rotas do WebAuthn
        // Vamos registrar as rotas manualmente para ter controle total
        // Webauthn::ignoreRoutes();

        // NORMA: "Forçar segurança sempre com HTTPS"
        // Força HTTPS nas URLs geradas pelo Laravel APENAS em produção
        // Em desenvolvimento local, usa HTTP para evitar problemas de conexão
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Use Bootstrap styles for pagination links
        Paginator::useBootstrap();
    }
}
