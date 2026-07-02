<?php

namespace App\Actions;

use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use LaravelWebauthn\Services\LoginRateLimiter;
use LaravelWebauthn\Services\Webauthn;
use App\Models\User as UserModel;

/**
 * Action para recuperação personalizada de utilizadores durante autenticação WebAuthn
 * 
 * Esta classe implementa a lógica customizada para recuperar utilizadores durante
 * o processo de autenticação WebAuthn. Suporta dois modos de autenticação:
 * 
 * 1. **Autenticação tradicional**: Requer email e chaves WebAuthn registadas
 * 2. **Autenticação usernameless (userless)**: Permite autenticação sem email,
 *    utilizando discoverable credentials armazenadas no dispositivo
 * 
 * A classe verifica se o modo userless está ativo e ajusta o comportamento
 * conforme necessário, permitindo ou bloqueando tentativas de autenticação
 * baseado na configuração do sistema.
 * 
 * @package App\Actions
 * @author Sistema
 * @since 1.0.0
 */
class CustomLoginUserRetrieval
{
    /**
     * Serviço de rate limiting para tentativas de autenticação
     * 
     * Controla o número de tentativas de login permitidas por período,
     * prevenindo ataques de força bruta.
     * 
     * @var \LaravelWebauthn\Services\LoginRateLimiter
     */
    protected LoginRateLimiter $limiter;

    /**
     * Construtor da classe
     * 
     * Inicializa a action com o serviço de rate limiting necessário
     * para controlar tentativas de autenticação.
     * 
     * @param \LaravelWebauthn\Services\LoginRateLimiter $limiter
     *        Serviço de rate limiting para autenticação
     */
    public function __construct(LoginRateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Processa a requisição de autenticação e retorna o utilizador correspondente
     * 
     * Este método implementa a lógica principal de recuperação de utilizadores:
     * 
     * 1. Verifica se já existe um utilizador autenticado na requisição
     * 2. Se não existir, tenta buscar pelo email fornecido
     * 3. Valida se o utilizador possui chaves WebAuthn registadas (quando necessário)
     * 4. Permite ou bloqueia autenticação baseado no modo userless
     * 
     * **Fluxo de autenticação tradicional (userless desativado):**
     * - Requer email válido
     * - Requer que o utilizador tenha chaves WebAuthn registadas
     * - Lança exceção se as condições não forem atendidas
     * 
     * **Fluxo de autenticação usernameless (userless ativado):**
     * - Permite autenticação sem email (retorna null)
     * - Utiliza discoverable credentials do dispositivo
     * - Não requer chaves pré-registadas no servidor
     * 
     * @param \Illuminate\Http\Request $request Requisição HTTP contendo dados de autenticação
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     *         Utilizador encontrado ou null se autenticação usernameless for permitida
     * 
     * @throws \Illuminate\Validation\ValidationException
     *         Quando a autenticação falha e userless está desativado
     */
    public function __invoke(Request $request): ?User
    {
        // Verificar se já existe um utilizador autenticado na requisição
        $user = $request->user();
        
        // Se não houver utilizador autenticado, tentar buscar pelo email
        if ($user === null) {
            $user = $this->retrieveUserByEmail($request);
        }

        // Validar se a autenticação sem utilizador é permitida
        if ($user === null && !$this->isUserlessEnabled()) {
            $this->handleFailedAuthentication($request);
            return null;
        }

        return $user;
    }

    /**
     * Recupera um utilizador pelo email fornecido na requisição
     * 
     * Busca o utilizador na base de dados usando o email fornecido.
     * Valida se o utilizador possui chaves WebAuthn registadas quando
     * o modo userless está desativado.
     * 
     * @param \Illuminate\Http\Request $request Requisição HTTP
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     *         Utilizador encontrado ou null se userless estiver ativo
     */
    protected function retrieveUserByEmail(Request $request): ?User
    {
        $email = $request->input(Webauthn::username());
        
        // Se não houver email fornecido, permitir autenticação usernameless (se ativo)
        if (empty($email)) {
            return null;
        }

        // Buscar utilizador na base de dados pelo email
        $user = UserModel::where('email', $email)->first();
        
        // Se utilizador não encontrado
        if ($user === null) {
            return $this->handleUserNotFound($request);
        }

        // Verificar se o utilizador possui chaves WebAuthn registadas
        // O trait WebauthnAuthenticatable fornece o relacionamento webauthnKeys
        $hasKeys = $user->webauthnKeys()->exists();
        
        // Se não tiver chaves e userless estiver desativado, bloquear autenticação
        if (!$hasKeys && !$this->isUserlessEnabled()) {
            $this->handleFailedAuthentication($request);
            return null;
        }

        // Se userless estiver ativo, permite tentar usernameless mesmo sem chaves
        // (o utilizador pode ter chaves em outro dispositivo)
        return $user;
    }

    /**
     * Trata o caso quando o utilizador não é encontrado na base de dados
     * 
     * Se o modo userless estiver ativo, permite continuar com autenticação
     * usernameless. Caso contrário, bloqueia a tentativa de autenticação.
     * 
     * @param \Illuminate\Http\Request $request Requisição HTTP
     * @return null Sempre retorna null (permite ou bloqueia baseado em userless)
     */
    protected function handleUserNotFound(Request $request): ?User
    {
        // Se userless estiver ativo, permite tentar autenticação usernameless
        if ($this->isUserlessEnabled()) {
            return null;
        }

        // Se userless estiver desativado, bloquear autenticação
        $this->handleFailedAuthentication($request);
        return null;
    }

    /**
     * Trata falhas de autenticação
     * 
     * Dispara eventos de falha e lança exceção de validação quando
     * a autenticação não pode prosseguir.
     * 
     * @param \Illuminate\Http\Request $request Requisição HTTP
     * @return void
     * 
     * @throws \Illuminate\Validation\ValidationException
     *         Sempre lança exceção de validação
     */
    protected function handleFailedAuthentication(Request $request): void
    {
        $this->fireFailedEvent($request);
        $this->throwFailedAuthenticationException($request);
    }

    /**
     * Verifica se o modo userless (autenticação sem email) está ativo
     * 
     * O modo userless permite autenticação utilizando apenas discoverable
     * credentials armazenadas no dispositivo, sem necessidade de fornecer
     * email ou ter chaves pré-registadas no servidor.
     * 
     * @return bool True se userless estiver ativo, false caso contrário
     */
    protected function isUserlessEnabled(): bool
    {
        return Webauthn::userless();
    }

    /**
     * Lança exceção de validação para autenticação falhada
     * 
     * Incrementa o contador de rate limiting e lança uma exceção
     * de validação com mensagem de erro apropriada.
     * 
     * @param \Illuminate\Http\Request $request Requisição HTTP
     * @return void
     * 
     * @throws \Illuminate\Validation\ValidationException
     *         Sempre lança exceção de validação
     */
    protected function throwFailedAuthenticationException(Request $request): void
    {
        // Incrementar contador de tentativas falhadas (rate limiting)
        $this->limiter->increment($request);

        // Lançar exceção com mensagem de erro traduzida
        throw ValidationException::withMessages([
            Webauthn::username() => [trans('webauthn::errors.login_failed')],
        ]);
    }

    /**
     * Dispara evento de falha de autenticação
     * 
     * Registra um evento de falha de autenticação no sistema Laravel,
     * permitindo que outros componentes (como listeners) reajam à falha.
     * 
     * @param \Illuminate\Http\Request $request Requisição HTTP
     * @return void
     */
    protected function fireFailedEvent(Request $request): void
    {
        event(new Failed(
            config('webauthn.guard'),
            null,
            [
                Webauthn::username() => $request->{Webauthn::username()},
            ]
        ));
    }
}

