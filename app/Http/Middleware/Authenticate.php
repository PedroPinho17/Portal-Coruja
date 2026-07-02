<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

/**
 * Middleware para autenticação de utilizadores
 * 
 * Este middleware estende o middleware de autenticação padrão do Laravel.
 * Define para onde redirecionar utilizadores não autenticados baseado
 * no tipo de requisição (JSON ou web).
 */
class Authenticate extends Middleware
{
    // ============================================
    // MÉTODOS PROTEGIDOS (Override do Middleware)
    // ============================================

    /**
     * Obtém o caminho para onde o utilizador deve ser redirecionado quando não autenticado
     * 
     * Se a requisição espera JSON, retorna null (para permitir resposta JSON).
     * Caso contrário, redireciona para a rota de login.
     * 
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // Se a requisição espera JSON, retornar null para permitir resposta JSON
        if ($request->expectsJson()) {
            return null;
        }

        // Caso contrário, redirecionar para a rota de login
        return route('login');
    }
}
