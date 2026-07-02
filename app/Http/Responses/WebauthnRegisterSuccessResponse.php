<?php

namespace App\Http\Responses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use LaravelWebauthn\Contracts\RegisterSuccessResponse as RegisterSuccessResponseContract;
use LaravelWebauthn\Facades\Webauthn;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * Response personalizada para registo bem-sucedido de chave WebAuthn
 * 
 * Esta classe implementa a resposta após o registo bem-sucedido de uma
 * nova chave WebAuthn. Suporta respostas JSON (para APIs) e HTML (redirecionamento).
 */
class WebauthnRegisterSuccessResponse implements RegisterSuccessResponseContract
{
    // ============================================
    // PROPRIEDADES
    // ============================================

    /**
     * A nova chave WebAuthn registada
     * 
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected Model $webauthnKey;

    // ============================================
    // MÉTODOS PÚBLICOS
    // ============================================

    /**
     * Cria uma resposta HTTP que representa o objeto
     * 
     * Retorna uma resposta JSON se a requisição esperar JSON,
     * caso contrário redireciona para a página de perfil com mensagem de sucesso.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[\Override]
    public function toResponse($request): HttpResponse
    {
        if ($request->wantsJson()) {
            return $this->jsonResponse($request);
        }
        
        return $this->redirectResponse();
    }

    /**
     * Define a nova chave WebAuthn
     * 
     * Método chamado pelo pacote LaravelWebauthn após o registo bem-sucedido
     * da chave WebAuthn.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Model $webauthnKey
     * @return self
     */
    #[\Override]
    public function setWebauthnKey(Request $request, Model $webauthnKey): self
    {
        $this->webauthnKey = $webauthnKey;

        return $this;
    }

    // ============================================
    // MÉTODOS PROTEGIDOS (Auxiliares)
    // ============================================

    /**
     * Cria uma resposta JSON para requisições AJAX/API
     * 
     * Retorna a chave WebAuthn registada e a URL de callback
     * (se houver uma URL intencionada na sessão, caso contrário usa a padrão).
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function jsonResponse(Request $request): HttpResponse
    {
        $callback = $this->getCallbackUrl($request);

        return Response::json([
            'result' => $this->webauthnKey,
            'callback' => $callback,
        ], 201);
    }

    /**
     * Cria uma resposta de redirecionamento para requisições HTML
     * 
     * Redireciona para a página de edição de perfil com uma mensagem
     * de sucesso informando que a chave foi registada.
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectResponse()
    {
        return redirect()->route('admin.perfil.edit')
            ->with('status', 'Chave de segurança registada com sucesso.');
    }

    // ============================================
    // MÉTODOS PRIVADOS (Auxiliares)
    // ============================================

    /**
     * Obtém a URL de callback para redirecionamento após registo
     * 
     * Verifica se há uma URL intencionada na sessão (por exemplo, se o utilizador
     * foi redirecionado para login antes de registar a chave). Se não houver,
     * usa a URL padrão configurada no pacote WebAuthn.
     * 
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    private function getCallbackUrl(Request $request): string
    {
        return $request->session()->pull('url.intended', Webauthn::redirects('register'));
    }
}

