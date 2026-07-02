<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Helpers\AuditLogger;
use App\Helpers\PasswordRules;

/**
 * Controller para mudança obrigatória de password
 * 
 * Este controller gerencia a mudança obrigatória de password quando
 * o campo mudanca_password está ativo (primeiro login após criação).
 */
class ForcePasswordChangeController extends Controller
{
    // ============================================
    // MÉTODOS PÚBLICOS (Rotas)
    // ============================================

    /**
     * Mostra o formulário de mudança obrigatória de password
     * 
     * Verifica se o utilizador está autenticado e se precisa mudar a password.
     * Se não estiver autenticado, redireciona para login.
     * Se não precisar mudar password, redireciona para dashboard.
     * 
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show()
    {
        $user = Auth::user();
        
        // Verificar autenticação e necessidade de mudança de password
        $redirect = $this->checkAuthAndPasswordChangeRequired($user);
        if ($redirect) {
            return $redirect;
        }

        return view('admin.password.force-change');
    }

    /**
     * Processa a mudança obrigatória de password
     * 
     * Valida a nova password (deve ser complexa) e atualiza o utilizador.
     * Desativa a flag de mudança obrigatória e registra a alteração no audit log.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Verificar autenticação e necessidade de mudança de password
        $redirect = $this->checkAuthAndPasswordChangeRequired($user);
        if ($redirect) {
            return $redirect;
        }

        // Validar a nova password - deve ser complexa pois é mudança após primeiro login
        $validated = $request->validate(
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        // Atualizar password e desativar flag de mudança obrigatória
        $this->updateUserPassword($user, $validated['password']);

        // Registrar mudança de password no audit log
        AuditLogger::logPasswordChange($user->id, 'Password alterada obrigatoriamente no primeiro login');

        return redirect()->route('admin.dashboard')
            ->with('status', 'Password alterada com sucesso! Bem-vindo ao sistema.');
    }

    // ============================================
    // MÉTODOS PRIVADOS (Auxiliares)
    // ============================================

    /**
     * Retorna as regras de validação para mudança de password
     * 
     * A password deve ser complexa pois é mudança após primeiro login.
     * 
     * @return array<string, array>
     */
    private function getValidationRules(): array
    {
        return [
            'password' => PasswordRules::complex(),
        ];
    }

    /**
     * Retorna as mensagens de erro de validação personalizadas
     * 
     * @return array<string, string>
     */
    private function getValidationMessages(): array
    {
        return PasswordRules::messages();
    }

    /**
     * Verifica se o utilizador está autenticado e se precisa mudar a password
     * 
     * Se não estiver autenticado, retorna redirect para login.
     * Se não precisar mudar password, retorna redirect para dashboard.
     * 
     * @param \App\Models\User|null $user Utilizador autenticado
     * @return \Illuminate\Http\RedirectResponse|null Retorna redirect se necessário, null caso contrário
     */
    private function checkAuthAndPasswordChangeRequired($user)
    {
        // Se não estiver autenticado, redirecionar para login
        if (!$user) {
            return redirect()->route('login');
        }

        // Se não precisar mudar password, redirecionar para dashboard
        if ($user->mudanca_password != 1) {
            return redirect()->route('admin.dashboard');
        }

        return null;
    }

    /**
     * Atualiza a password do utilizador e desativa a flag de mudança obrigatória
     * 
     * @param \App\Models\User $user Utilizador a atualizar
     * @param string $newPassword Nova password (já validada)
     * @return void
     */
    private function updateUserPassword(User $user, string $newPassword): void
    {
        $user->password = Hash::make($newPassword);
        $user->mudanca_password = 0; // Desativar flag de mudança obrigatória
        $user->timestamp_ultima_alteracao = now();
        $user->save();
    }
}

