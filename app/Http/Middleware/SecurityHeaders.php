<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\CspNonce;

/**
 * Middleware para adicionar headers de segurança HTTP
 * 
 * Este middleware adiciona vários headers de segurança que ajudam a proteger
 * a aplicação contra ataques comuns como clickjacking, XSS, MIME sniffing, etc.
 * 
 * Implementa CSP com nonces para permitir scripts inline específicos sem usar
 * 'unsafe-inline' que é um risco de segurança.
 */
class SecurityHeaders
{
    // ============================================
    // MÉTODOS PÚBLICOS
    // ============================================

    /**
     * Processa uma requisição HTTP e adiciona headers de segurança
     * 
     * Gera um nonce CSP único para a requisição, compartilha com as views,
     * e adiciona todos os headers de segurança necessários à resposta.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Gerar nonce único para esta requisição ANTES de processar a resposta
        // Isso permite que as views usem o mesmo nonce
        $nonce = $this->generateAndShareNonce($request);
        
        $response = $next($request);

        // Adicionar todos os headers de segurança
        $this->addBasicSecurityHeaders($response);
        $this->addHstsHeader($request, $response);
        $this->addCspHeader($response, $nonce);
        $this->addPermissionsPolicyHeader($response);

        return $response;
    }

    // ============================================
    // MÉTODOS PRIVADOS (Auxiliares)
    // ============================================

    /**
     * Gera um nonce CSP único e compartilha com as views
     * 
     * O nonce é gerado antes de processar a resposta para garantir
     * que as views possam usar o mesmo nonce nos scripts inline.
     * 
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    private function generateAndShareNonce(Request $request): string
    {
        $nonce = CspNonce::get($request);
        
        // Tornar o nonce disponível para as views via view()->share()
        view()->share('cspNonce', $nonce);
        
        return $nonce;
    }

    /**
     * Adiciona headers básicos de segurança à resposta
     * 
     * Inclui:
     * - X-Content-Type-Options: Previne MIME type sniffing
     * - X-Frame-Options: Previne clickjacking
     * - X-XSS-Protection: Ativa proteção XSS do navegador
     * - Referrer-Policy: Controla informação de referrer enviada
     * 
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    private function addBasicSecurityHeaders(Response $response): void
    {
        // X-Content-Type-Options: Previne MIME type sniffing
        // Impede que o navegador tente "adivinhar" o tipo de conteúdo
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // X-Frame-Options: Previne clickjacking
        // Impede que a página seja carregada em um iframe de outro site
        $response->headers->set('X-Frame-Options', 'DENY');

        // X-XSS-Protection: Ativa proteção XSS do navegador (legacy, mas ainda útil)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer-Policy: Controla quanto de informação de referrer é enviado
        // 'strict-origin-when-cross-origin' é um bom equilíbrio entre privacidade e funcionalidade
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /**
     * Adiciona header Strict-Transport-Security (HSTS) se a requisição for HTTPS
     * 
     * HSTS força o uso de HTTPS para todas as requisições futuras.
     * Só é adicionado em requisições HTTPS para evitar problemas.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    private function addHstsHeader(Request $request, Response $response): void
    {
        // Strict-Transport-Security (HSTS): Força uso de HTTPS
        // Só adiciona em requisições HTTPS para evitar problemas
        if ($request->secure()) {
            // max-age=31536000 = 1 ano
            // includeSubDomains = aplica a todos os subdomínios
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }
    }

    /**
     * Constrói e adiciona o header Content-Security-Policy (CSP)
     * 
     * CSP controla quais recursos podem ser carregados pela página.
     * Usa nonces para permitir scripts inline específicos sem usar
     * 'unsafe-inline' que é um risco de segurança.
     * 
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param string $nonce
     * @return void
     */
    private function addCspHeader(Response $response, string $nonce): void
    {
        $csp = $this->buildCsp($nonce);
        $response->headers->set('Content-Security-Policy', $csp);
    }

    /**
     * Constrói a string Content-Security-Policy
     * 
     * Define as políticas de segurança para diferentes tipos de recursos:
     * - default-src: Política padrão para todos os recursos
     * - script-src: Permite scripts do mesmo domínio e scripts inline com nonce
     * - style-src: Permite estilos do mesmo domínio e inline (sem risco de XSS)
     * - img-src: Permite imagens do mesmo domínio, data URIs e HTTPS
     * - font-src: Permite fontes do mesmo domínio e data URIs
     * - connect-src: Permite AJAX/fetch apenas para o mesmo domínio
     * - object-src: Permite objetos/iframes do mesmo domínio e blob URLs (PDFs)
     * - frame-src: Permite frames do mesmo domínio, blob URLs e serviços de vídeo
     * - frame-ancestors: Equivalente a X-Frame-Options: DENY
     * 
     * @param string $nonce
     * @return string
     */
    private function buildCsp(string $nonce): string
    {
        return "default-src 'self'; " .
               "script-src 'self' 'nonce-{$nonce}' https://cdn.tailwindcss.com 'sha256-mu5wJDzPbeIUsysu97/fJ1OKaPcup9D+bn1lAbMiWFM=' 'sha256-wNlK8DGnmckSQ1VF8F3O7kDluCIscWA91WkjQMMRbhQ=' 'sha256-zI6sEIgb2/a09i3fkiImZO/98xL2ifeZzoUmlguwxfs='; " . // Tailwind CDN + hashes dos scripts inline injetados pelo Tailwind
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " . // Google Fonts stylesheet
               "img-src 'self' data: https:; " . // Permite imagens do mesmo domínio, data URIs e HTTPS
               "font-src 'self' data: https://fonts.gstatic.com; " . // Google Fonts (ficheiros de fonte)
               "connect-src 'self' https://maps.googleapis.com https://*.googleapis.com; " . // Permite AJAX/fetch apenas para o mesmo domínio
               "object-src 'self' blob:; " . // Permite objetos/iframes do mesmo domínio e blob URLs (necessário para preview de PDFs)
               "frame-src 'self' blob: https://www.google.com https://*.google.com https://www.youtube.com https://*.youtube.com https://youtube.com https://www.youtube-nocookie.com https://*.youtube-nocookie.com https://youtube-nocookie.com https://player.vimeo.com https://*.vimeo.com; " . // Permite frames do mesmo domínio, blob URLs e serviços de vídeo (YouTube, Vimeo)
               "frame-ancestors 'none';"; // Equivalente a X-Frame-Options: DENY
    }

    /**
     * Constrói e adiciona o header Permissions-Policy
     * 
     * Permissions-Policy controla quais APIs e features do navegador podem ser usadas.
     * Desabilita features que não são necessárias para reduzir superfície de ataque.
     * 
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    private function addPermissionsPolicyHeader(Response $response): void
    {
        $permissionsPolicy = $this->buildPermissionsPolicy();
        $response->headers->set('Permissions-Policy', $permissionsPolicy);
    }

    /**
     * Constrói a string Permissions-Policy
     * 
     * Define quais APIs e features do navegador podem ser usadas:
     * - geolocation: Desabilitado
     * - microphone: Desabilitado
     * - camera: Desabilitado
     * - payment: Desabilitado
     * - usb: Desabilitado
     * - fullscreen: Permitido apenas do mesmo domínio (necessário para preview de PDFs)
     * 
     * @return string
     */
    private function buildPermissionsPolicy(): string
    {
        return "geolocation=(), " .
               "microphone=(), " .
               "camera=(), " .
               "payment=(), " .
               "usb=(), " .
               "fullscreen=(self \"https://www.youtube.com\" \"https://youtube.com\" \"https://www.youtube-nocookie.com\" \"https://youtube-nocookie.com\" \"https://player.vimeo.com\")"; // Permite fullscreen do mesmo domínio e serviços de vídeo (necessário para preview de PDFs e vídeos)
    }
}

