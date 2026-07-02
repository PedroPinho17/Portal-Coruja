<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para autenticação HTTP Basic na área administrativa
 * 
 * Este middleware protege rotas administrativas com autenticação HTTP Basic.
 * As credenciais são obtidas do ficheiro de configuração (admin.php) em vez
 * de env() para permitir cache de configuração e melhorar a performance.
 */
class AdminBasicAuth
{
    // ============================================
    // MÉTODOS PÚBLICOS
    // ============================================

    /**
     * Processa uma requisição HTTP
     * 
     * Verifica se as credenciais HTTP Basic fornecidas correspondem
     * às credenciais configuradas. Se não estiver configurado ou as
     * credenciais forem inválidas, retorna erro apropriado.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se a autenticação está configurada
        if (!$this->isAuthConfigured()) {
            return $this->buildNotConfiguredResponse();
        }

        // Verificar credenciais fornecidas
        if (!$this->validateCredentials($request)) {
            return $this->buildUnauthorizedResponse();
        }

        return $next($request);
    }

    // ============================================
    // MÉTODOS PRIVADOS (Auxiliares)
    // ============================================

    /**
     * Verifica se a autenticação HTTP Basic está configurada
     * 
     * @return bool
     */
    private function isAuthConfigured(): bool
    {
        $user = config('admin.basic_auth.user');
        $pass = config('admin.basic_auth.pass');

        return !empty($user) && !empty($pass);
    }

    /**
     * Valida as credenciais HTTP Basic fornecidas na requisição
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    private function validateCredentials(Request $request): bool
    {
        $user = config('admin.basic_auth.user');
        $pass = config('admin.basic_auth.pass');

        // Ler credenciais HTTP Basic da requisição
        $providedUser = $request->getUser();
        $providedPass = $request->getPassword();

        return $providedUser === $user && $providedPass === $pass;
    }

    /**
     * Constrói resposta de erro quando a autenticação não está configurada
     * 
     * Retorna 403 para evitar expor acidentalmente a área administrativa.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function buildNotConfiguredResponse(): Response
    {
        return response('Admin auth not configured. Set ADMIN_USER and ADMIN_PASS in .env', 403);
    }

    /**
     * Constrói resposta de erro quando as credenciais são inválidas
     * 
     * Retorna 401 com header WWW-Authenticate para solicitar credenciais.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function buildUnauthorizedResponse(): Response
    {
        return response('Unauthorized', 401, [
            'WWW-Authenticate' => 'Basic realm="Admin Area"',
        ]);
    }
}
