<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para prevenir histórico do navegador (back button)
 * 
 * Este middleware adiciona headers HTTP que impedem o navegador de
 * cachear páginas, evitando que o botão "voltar" mostre conteúdo antigo
 * ou sensível após logout ou mudanças de estado.
 * 
 * Headers adicionados:
 * - Cache-Control: no-cache, no-store, max-age=0, must-revalidate
 * - Pragma: no-cache
 * - Expires: Sat, 01 Jan 1990 00:00:00 GMT (data no passado)
 */
class PreventBackHistory
{
    // ============================================
    // MÉTODOS PÚBLICOS
    // ============================================

    /**
     * Processa uma requisição HTTP
     * 
     * Adiciona headers HTTP para prevenir cache do navegador,
     * impedindo que o botão "voltar" mostre conteúdo antigo.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Adicionar headers para prevenir cache
        return $this->addNoCacheHeaders($response);
    }

    // ============================================
    // MÉTODOS PRIVADOS (Auxiliares)
    // ============================================

    /**
     * Adiciona headers HTTP para prevenir cache do navegador
     * 
     * Adiciona os headers necessários para garantir que o navegador
     * não cacheie a resposta, impedindo que o botão "voltar" mostre
     * conteúdo antigo ou sensível.
     * 
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function addNoCacheHeaders(Response $response): Response
    {
        return $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
                        ->header('Pragma', 'no-cache')
                        ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
    }
}
