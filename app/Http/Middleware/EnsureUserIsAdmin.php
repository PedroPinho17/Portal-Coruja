<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para garantir que apenas administradores acessem rotas admin
 * 
 * Este middleware verifica se o utilizador autenticado é um administrador
 * (id_permissao = 1). Se não for, redireciona para o dashboard ou retorna
 * erro 403 dependendo do tipo de requisição.
 */
class EnsureUserIsAdmin
{
    /**
     * Processa uma requisição HTTP
     * 
     * Verifica se o utilizador autenticado é administrador.
     * Se não for, redireciona ou retorna erro 403.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se não estiver autenticado, o middleware 'auth' já deve ter tratado isso
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Não autenticado.'], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Verificar se é administrador
        if (!$user->isAdministrador()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Acesso negado. Apenas administradores podem acessar esta área.'], 403);
            }
            
            // Redirecionar para home ou dashboard com mensagem de erro
            return redirect()->route('home')
                ->with('error', 'Acesso negado. Apenas administradores podem acessar esta área.');
        }

        return $next($request);
    }
}
