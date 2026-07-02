<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AuditLogger;



/**
 * Controller para gerir autenticação de utilizadores
 * 
 * Este controller gerencia o login, logout e exibição do formulário de login.
 * Também verifica se o utilizador precisa mudar a password e registra eventos no audit log.
 */
class LoginController extends Controller
{

    // ============================================
    // MÉTODOS PÚBLICOS (Rotas)
    // ============================================

    /**
     * Mostra o formulário de login
     * 
     * Se o utilizador já estiver autenticado, redireciona para o dashboard.
     * 
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            // Utilizador já autenticado - redirecionar para dashboard com mensagem
            return redirect()->route('admin.dashboard')
                ->with('info', 'Já se encontra autenticado. Foi redirecionado para o dashboard.');
        }
        
        return view('auth.login');
    }

    /**
     * Processa o login do utilizador
     * 
     * Valida as credenciais, tenta autenticar o utilizador e verifica se precisa
     * mudar a password. Registra eventos de login (bem-sucedido ou falhado) no audit log.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validar credenciais
        $credentials = $request->validate($this->getLoginValidationRules(), $this->getLoginValidationMessages());

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Registrar login bem-sucedido
            AuditLogger::logLogin($credentials['email']);
            
            // Verificar se o utilizador precisa mudar a password
            if ($user->mudanca_password == 1) {
                return redirect()->route('admin.password.force-change')
                    ->with('warning', 'Por favor, altere a sua password antes de continuar.');
            }
            
            return redirect()->intended(route('admin.dashboard'));
        }
        
        // Registrar tentativa de login falhada (usando hash do email para privacidade/GDPR)
        $this->logFailedLoginAttempt($credentials['email']);

        return back()->withErrors([
            'email' => 'Credenciais inválidas.',
        ])->onlyInput('email');
    }

    /**
     * Processa o logout do utilizador
     * 
     * Registra o logout no audit log, invalida a sessão e regenera o token CSRF.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Registrar logout ANTES de invalidar a sessão (para ter acesso ao user)
        try {
            AuditLogger::logLogout();
        } catch (\Exception $e) {
            // Se houver erro no log, não quebra o logout
            \Log::warning('Erro ao registrar logout: ' . $e->getMessage());
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Sinalizar logout em outras abas usando localStorage
        // Isso é feito no frontend, mas documentamos aqui para referência
        // O JavaScript no layout já trata isso automaticamente
        
        return redirect()->route('login')->with('success', 'Sessão terminada com sucesso.');
    }

    // ============================================
    // MÉTODOS PRIVADOS (Auxiliares)
    // ============================================

    /**
     * Retorna as regras de validação para login
     * 
     * @return array<string, array>
     */
    private function getLoginValidationRules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Retorna as mensagens de erro de validação personalizadas
     * 
     * @return array<string, string>
     */
    private function getLoginValidationMessages(): array
    {
        return [
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser válido.',
            'password.required' => 'A password é obrigatória.',
            'password.string' => 'A password deve ser uma string.',
        ];
    }

    /**
     * Registra tentativa de login falhada no audit log
     * 
     * Usa hash do email para privacidade/GDPR. Apenas os primeiros 8 caracteres
     * do hash SHA-256 são usados para identificar padrões sem expor o email completo nos logs.
     * 
     * @param string $email Email usado na tentativa de login
     * @return void
     */
    private function logFailedLoginAttempt(string $email): void
    {
        $emailHash = hash('sha256', $email);
        $emailPrefix = substr($emailHash, 0, 8);
        
        AuditLogger::log(
            'login_failed',
            'User',
            null,
            null,
            null,
            'Tentativa de login falhada (hash: ' . $emailPrefix . ')'
        );
    }
    
}
