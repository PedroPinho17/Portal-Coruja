<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Helpers\AuditLogger;
use App\Helpers\PasswordRules;

/**
 * Controller para gerir o perfil do utilizador autenticado
 * 
 * Este controller permite visualizar e atualizar o perfil do utilizador,
 * incluindo nome, email e password. Também carrega as chaves WebAuthn do utilizador.
 */
class ProfileController extends Controller
{
    // ============================================
    // MÉTODOS PÚBLICOS (Rotas)
    // ============================================

    /**
     * Mostra o formulário de edição do perfil
     * 
     * Carrega o utilizador autenticado e suas chaves WebAuthn.
     * 
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = Auth::user();
        
        // Carregar chaves WebAuthn do utilizador
        $user->load('webauthnKeys');
        
        return view('admin.perfil.edit', compact('user'));
    }

    /**
     * Atualiza o perfil do utilizador autenticado
     * 
     * Permite atualizar nome, email e password. Se estiver a alterar
     * campos sensíveis (email ou password), requer a password atual.
     * Registra todas as alterações no audit log.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Verificar autenticação
        if (!$user) {
            return redirect()->route('login');
        }

        // Obter regras de validação dinâmicas (podem incluir current_password)
        $rules = $this->getValidationRules($request, $user);
        
        // Validar dados do formulário
        $data = $request->validate($rules, $this->getValidationMessages($user));

        // Capturar dados antigos para auditoria
        $oldData = $this->captureOldData($user);
        
        // Atualizar dados do utilizador
        $this->updateUserData($user, $data);
        
        // Registrar atualização de perfil no audit log
        $this->logProfileUpdate($user, $oldData);

        return redirect()->route('admin.perfil.edit')
            ->with('status', 'Perfil atualizado com sucesso.');
    }

    // ============================================
    // MÉTODOS PRIVADOS (Auxiliares)
    // ============================================

    /**
     * Retorna as regras de validação para atualizar o perfil
     * 
     * As regras são dinâmicas: se estiver a alterar password,
     * requer a password atual.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user Utilizador autenticado
     * @return array<string, array>
     */
    private function getValidationRules(Request $request, User $user): array
    {
        // Regras base - apenas nome é obrigatório
        $rules = [
            'nome' => ['required', 'string', 'max:150'],
            'password' => PasswordRules::complexNullable(),
        ];

        // Se estiver a alterar password, requer password atual
        $changingPassword = filled($request->input('password'));
        
        if ($changingPassword) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        return $rules;
    }

    /**
     * Retorna as mensagens de erro de validação personalizadas
     * 
     * @param \App\Models\User $user Utilizador autenticado
     * @return array<string, string>
     */
    private function getValidationMessages(User $user): array
    {
        return array_merge([
            'nome.required' => 'O nome é obrigatório.',
            'current_password.required' => 'A password atual é obrigatória para alterar a password.',
            'current_password.current_password' => 'A password atual está incorreta.',
        ], PasswordRules::messages());
    }

    /**
     * Captura os dados antigos do utilizador para auditoria
     * 
     * @param \App\Models\User $user Utilizador
     * @return array<string, mixed>
     */
    private function captureOldData(User $user): array
    {
        return [
            'nome' => $user->nome,
        ];
    }

    /**
     * Atualiza os dados do utilizador
     * 
     * Atualiza nome e password (se fornecida).
     * Registra alteração de password no audit log se houver.
     * 
     * @param \App\Models\User $user Utilizador a atualizar
     * @param array $data Dados validados
     * @return void
     */
    private function updateUserData(User $user, array $data): void
    {
        $user->nome = $data['nome'];
        
        // Registrar alteração de password se houver
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
            AuditLogger::logPasswordChange($user->id, 'Palavra-passe alterada pelo próprio utilizador');
        }
        
        $user->timestamp_ultima_alteracao = now();
        $user->save();
    }

    /**
     * Registra a atualização de perfil no audit log
     * 
     * @param \App\Models\User $user Utilizador atualizado
     * @param array $oldData Dados antigos do utilizador
     * @return void
     */
    private function logProfileUpdate(User $user, array $oldData): void
    {
        $newData = [
            'nome' => $user->nome,
        ];
        
        // Apenas registar mudanças reais
        if ($oldData['nome'] !== $newData['nome']) {
            AuditLogger::logUpdate('User', $user->id, $oldData, $newData, 'Nome atualizado');
        }
    }
}