<?php

namespace App\Http\Controllers\Webauthn;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use LaravelWebauthn\Actions\PrepareAssertionData;
use LaravelWebauthn\Services\Webauthn;
use App\Actions\CustomLoginUserRetrieval;

/**
 * Controller para gerir autenticação WebAuthn
 * 
 * Este controller prepara os dados de autenticação WebAuthn (publicKey).
 * Suporta autenticação usernameless (sem email) quando configurado.
 */
class AuthenticateController extends Controller
{
    // ============================================
    // MÉTODOS PÚBLICOS (Rotas)
    // ============================================

    /**
     * Prepara e retorna os dados de autenticação WebAuthn (publicKey)
     * 
     * Valida o email (opcional para suportar usernameless), busca o utilizador
     * se fornecido, e prepara os dados de asserção. Retorna sempre JSON.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        try {
            // Validar o email (opcional para suportar usernameless)
            $request->validate($this->getValidationRules());

            // Buscar o utilizador (pode ser null se usernameless estiver ativo)
            $user = $this->retrieveUser($request);

            // Preparar os dados de asserção (publicKey)
            $publicKey = $this->prepareAssertionData($user);

            // Retornar JSON com o publicKey
            return $this->buildSuccessResponse($publicKey);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Retornar erros de validação como JSON
            return $this->buildValidationErrorResponse($e);
        } catch (\Exception $e) {
            // Retornar outros erros como JSON
            return $this->buildErrorResponse($e);
        }
    }

    // ============================================
    // MÉTODOS PRIVADOS (Auxiliares)
    // ============================================

    /**
     * Retorna as regras de validação para autenticação WebAuthn
     * 
     * O email é opcional (sometimes) para suportar autenticação usernameless.
     * 
     * @return array<string, string>
     */
    private function getValidationRules(): array
    {
        return [
            Webauthn::username() => 'sometimes|string|email',
        ];
    }

    /**
     * Busca o utilizador usando CustomLoginUserRetrieval
     * 
     * Se userless estiver ativo, permite autenticação sem email (usernameless).
     * Se não encontrar utilizador com email mas userless está ativo, retorna null.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\User|null
     */
    private function retrieveUser(Request $request)
    {
        $email = $request->input(Webauthn::username());
        
        // Se não houver email e userless estiver ativo, permite autenticação sem utilizador
        // O WebAuthn vai usar discoverable credentials (resident keys) para identificar o utilizador
        if (!$email) {
            return Webauthn::userless() ? null : null;
        }
        
        // Se email foi fornecido, tentar buscar utilizador
        try {
            return app(CustomLoginUserRetrieval::class)($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Se não encontrar utilizador com email, mas userless está ativo,
            // permite continuar sem utilizador para tentar usernameless
            if (!Webauthn::userless()) {
                throw $e;
            }
            // Se userless está ativo, continua com user = null
            return null;
        }
    }

    /**
     * Prepara os dados de asserção (publicKey) para autenticação WebAuthn
     * 
     * Se user = null e userless está ativo, o PrepareAssertionData vai preparar
     * uma requisição que permite discoverable credentials.
     * 
     * @param \App\Models\User|null $user Utilizador (pode ser null para usernameless)
     * @return \LaravelWebauthn\Contracts\PublicKeyCredentialRequestOptions
     */
    private function prepareAssertionData($user)
    {
        return app(PrepareAssertionData::class)($user);
    }

    /**
     * Constrói resposta JSON de sucesso com publicKey
     * 
     * @param \LaravelWebauthn\Contracts\PublicKeyCredentialRequestOptions $publicKey
     * @return \Illuminate\Http\JsonResponse
     */
    private function buildSuccessResponse($publicKey): JsonResponse
    {
        // O publicKey é um PublicKeyCredentialRequestOptions que implementa JsonSerializable
        $publicKeyData = $publicKey->jsonSerialize();
        
        return response()->json([
            'publicKey' => $publicKeyData
        ], 200, [
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache, private'
        ]);
    }

    /**
     * Constrói resposta JSON de erro de validação
     * 
     * @param \Illuminate\Validation\ValidationException $e
     * @return \Illuminate\Http\JsonResponse
     */
    private function buildValidationErrorResponse(\Illuminate\Validation\ValidationException $e): JsonResponse
    {
        return response()->json([
            'message' => 'Erro de validação',
            'errors' => $e->errors()
        ], 422);
    }

    /**
     * Constrói resposta JSON de erro genérico
     * 
     * @param \Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    private function buildErrorResponse(\Exception $e): JsonResponse
    {
        return response()->json([
            'message' => 'Erro ao obter dados de autenticação',
            'error' => $e->getMessage()
        ], 500);
    }
}

