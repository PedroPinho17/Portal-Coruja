<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para forçar HTTPS em todas as requisições
 * 
 * NORMA: "Forçar segurança sempre com HTTPS"
 * 
 * Este middleware garante que:
 * - Em PRODUÇÃO: SEMPRE força HTTPS (redireciona HTTP para HTTPS)
 * - Em DESENVOLVIMENTO: Permite HTTP para evitar erros de SSL
 * 
 * Funciona em conjunto com:
 * - AppServiceProvider::boot() que força scheme HTTPS nas URLs geradas
 * - SecurityHeaders middleware que adiciona HSTS header
 */
class ForceHttps
{
    // ============================================
    // MÉTODOS PÚBLICOS
    // ============================================

    /**
     * Processa uma requisição HTTP
     * 
     * Força todas as requisições para HTTPS em produção.
     * Em ambiente local/desenvolvimento, permite HTTP para evitar erros de SSL.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se deve forçar HTTPS
        if (!$this->shouldForceHttps()) {
            return $next($request);
        }
        
        // Se estiver em ambiente local, permite HTTP
        if ($this->isLocalEnvironment($request)) {
            return $next($request);
        }

        // EM PRODUÇÃO: SEMPRE força HTTPS conforme norma
        if (!$request->secure()) {
            return $this->redirectToHttps($request);
        }

        return $next($request);
    }

    // ============================================
    // MÉTODOS PRIVADOS (Auxiliares)
    // ============================================

    /**
     * Verifica se deve forçar HTTPS
     * 
     * Verifica a configuração FORCE_HTTPS do .env.
     * Padrão: true (conforme norma).
     * Pode ser desabilitado via .env apenas para desenvolvimento.
     * 
     * ATENÇÃO: Em produção, NUNCA desabilitar esta opção!
     * 
     * @return bool
     */
    private function shouldForceHttps(): bool
    {
        $forceHttps = env('FORCE_HTTPS', 'true');
        
        // Se FORCE_HTTPS estiver explicitamente desabilitado, não força HTTPS
        return !($forceHttps === 'false' || $forceHttps === false);
    }

    /**
     * Redireciona a requisição para HTTPS
     * 
     * Constrói a URL HTTPS e redireciona com código 301 (permanente).
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function redirectToHttps(Request $request): Response
    {
        $url = 'https://' . $request->getHttpHost() . $request->getRequestUri();
        return redirect($url, 301);
    }

    /**
     * Detecta se está em ambiente local/desenvolvimento
     * 
     * Verifica múltiplos indicadores de ambiente local:
     * - APP_ENV (local ou testing)
     * - Host (localhost, 127.0.0.1, .local, .test)
     * - Porta (diferente de 80 e 443)
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    private function isLocalEnvironment(Request $request): bool
    {
        // Verificar APP_ENV
        if ($this->isLocalByAppEnv()) {
            return true;
        }
        
        // Verificar host e servidor
        if ($this->isLocalByHost($request)) {
            return true;
        }
        
        // Verificar porta
        if ($this->isLocalByPort($request)) {
            return true;
        }
        
        return false;
    }

    /**
     * Verifica se é ambiente local baseado em APP_ENV
     * 
     * @return bool
     */
    private function isLocalByAppEnv(): bool
    {
        $appEnv = config('app.env');
        return $appEnv === 'local' || $appEnv === 'testing';
    }

    /**
     * Verifica se é ambiente local baseado no host
     * 
     * Verifica se o host é localhost, 127.0.0.1, ou contém indicadores
     * de ambiente local (.local, .test).
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    private function isLocalByHost(Request $request): bool
    {
        $host = $request->getHost();
        $serverName = $request->server('SERVER_NAME', '');
        $serverAddr = $request->server('SERVER_ADDR', '');
        
        // Verificar se é localhost ou 127.0.0.1 exato
        if ($host === '127.0.0.1' || 
            $host === 'localhost' ||
            $serverName === '127.0.0.1' || 
            $serverName === 'localhost' ||
            $serverAddr === '127.0.0.1') {
            return true;
        }
        
        // Verificar se contém indicadores de ambiente local
        if (str_contains($host, '127.0.0.1') ||
            str_contains($host, 'localhost') ||
            str_contains($host, '.local') ||
            str_contains($host, '.test')) {
            return true;
        }
        
        return false;
    }

    /**
     * Verifica se é ambiente local baseado na porta
     * 
     * Portas não padrão (diferentes de 80 e 443) geralmente indicam desenvolvimento.
     * Exemplos: 8000, 8080, 3000, etc.
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    private function isLocalByPort(Request $request): bool
    {
        $serverPort = $request->server('SERVER_PORT');
        
        // Portas não padrão (diferentes de 80 e 443) geralmente indicam desenvolvimento
        return $serverPort && $serverPort != '80' && $serverPort != '443';
    }
}
