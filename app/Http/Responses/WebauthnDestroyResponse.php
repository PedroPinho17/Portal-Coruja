<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use LaravelWebauthn\Contracts\DestroyResponse as DestroyResponseContract;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * Response personalizada para remoção bem-sucedida de chave WebAuthn
 * 
 * Esta classe implementa a resposta após a remoção bem-sucedida de uma
 * chave WebAuthn. Suporta respostas JSON (para APIs) e HTML (redirecionamento).
 */
class WebauthnDestroyResponse implements DestroyResponseContract
{
    // ============================================
    // MÉTODOS PÚBLICOS
    // ============================================

    /**
     * Cria uma resposta HTTP que representa o objeto
     * 
     * Retorna uma resposta 204 No Content se a requisição esperar JSON,
     * caso contrário redireciona de volta à página anterior com mensagem de sucesso.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[\Override]
    public function toResponse($request): HttpResponse
    {
        if ($request->wantsJson()) {
            return $this->jsonResponse();
        }
        
        return $this->redirectResponse();
    }

    // ============================================
    // MÉTODOS PRIVADOS (Auxiliares)
    // ============================================

    /**
     * Cria uma resposta JSON para requisições AJAX/API
     * 
     * Retorna uma resposta 204 No Content, indicando que a operação
     * foi bem-sucedida mas não há conteúdo para retornar.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function jsonResponse(): HttpResponse
    {
        return Response::noContent();
    }

    /**
     * Cria uma resposta de redirecionamento para requisições HTML
     * 
     * Redireciona de volta à página anterior com uma mensagem de sucesso
     * informando que a chave foi removida.
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    private function redirectResponse()
    {
        return back()->with('status', 'Chave de segurança removida com sucesso.');
    }
}

