<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para forçar mudança de password
 * 
 * Verifica se o utilizador autenticado tem o campo mudanca_password ativo.
 * Se sim, redireciona para a página de mudança obrigatória de password,
 * exceto se já estiver nessa página ou na rota de logout.
 */
class RequirePasswordChange
{
    // ============================================
    // MÉTODOS PÚBLICOS
    // ============================================

    /**
     * Processa uma requisição HTTP
     * 
     * Verifica se o utilizador autenticado precisa mudar a password.
     * Se sim, redireciona para a página de mudança obrigatória, exceto
     * se já estiver nessa página ou na rota de logout.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se não estiver autenticado, continuar normalmente
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Se o utilizador precisa mudar a password
        if ($user->mudanca_password == 1) {
            // Verificar se está numa rota permitida
            if ($this->isAllowedRoute($request)) {
                return $next($request);
            }

            // Se não estiver numa rota permitida, redirecionar
            return $this->redirectToPasswordChange();
        }

        return $next($request);
    }

    // ============================================
    // MÉTODOS PRIVADOS (Auxiliares)
    // ============================================

    /**
     * Verifica se a requisição está numa rota permitida
     * 
     * Permite acesso à rota de mudança obrigatória de password e logout,
     * mesmo quando o utilizador precisa mudar a password.
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    private function isAllowedRoute(Request $request): bool
    {
        $path = $request->path();
        $routeName = $request->route() ? $request->route()->getName() : null;

        // Permitir acesso à rota de mudança obrigatória e logout
        $allowedRoutes = $this->getAllowedRoutes();
        
        // Também permitir se a URL contém logout (rota pode estar fora do grupo admin)
        if (str_contains($path, 'logout')) {
            return true;
        }

        // Se a rota tiver nome, verificar se está na lista de permitidas
        if ($routeName) {
            return in_array($routeName, $allowedRoutes);
        }
        
        // Se a rota não tiver nome mas estiver no grupo admin,
        // verificar se não está na página de mudança de password
        if (str_starts_with($path, 'admin') && !str_contains($path, 'password/force-change')) {
            return false;
        }

        return true;
    }

    /**
     * Retorna a lista de rotas permitidas quando o utilizador precisa mudar a password
     * 
     * @return array<string>
     */
    private function getAllowedRoutes(): array
    {
        return [
            'admin.password.force-change',
            'admin.password.force-change.update',
            'logout',
        ];
    }

    /**
     * Redireciona para a página de mudança obrigatória de password
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function redirectToPasswordChange(): Response
    {
        return redirect()->route('admin.password.force-change')
            ->with('warning', 'Por favor, altere a sua password antes de continuar.');
    }
}

