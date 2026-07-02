<?php

namespace App\Helpers;

use Illuminate\Http\Request;

/**
 * Helper para gerenciar CSP Nonces
 * 
 * Nonces são valores aleatórios únicos gerados para cada requisição
 * que permitem identificar scripts inline específicos como seguros,
 * sem precisar usar 'unsafe-inline' que permite qualquer script.
 */
class CspNonce
{
    /**
     * Gera um nonce único para esta requisição
     * 
     * @return string Nonce base64-encoded de 16 bytes
     */
    public static function generate(): string
    {
        // Gerar 16 bytes aleatórios e codificar em base64
        // Base64 é seguro para uso em HTML/CSP
        return base64_encode(random_bytes(16));
    }

    /**
     * Obtém o nonce da requisição atual ou gera um novo
     * 
     * @param \Illuminate\Http\Request|null $request
     * @return string
     */
    public static function get(?Request $request = null): string
    {
        $request = $request ?? request();
        
        // Armazenar o nonce na requisição para reutilizar na mesma requisição
        if (!$request->has('_csp_nonce')) {
            $request->merge(['_csp_nonce' => static::generate()]);
        }
        
        return $request->input('_csp_nonce');
    }
}

