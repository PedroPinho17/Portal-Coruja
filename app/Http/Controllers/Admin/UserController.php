<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use App\Models\TipoPermissao;
use App\Traits\ChecksRelations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Helpers\AuditLogger;
use App\Helpers\PasswordRules;
use function view;
use function redirect;
use function back;
use function now;

/**
 * Controller para gerir utilizadores do sistema
 * 
 * Este controller permite criar e eliminar utilizadores.
 * Também verifica relacionamentos antes de eliminar para evitar erros de integridade.
 */
class UserController extends Controller
{
    use AuthorizesRequests;
    use ChecksRelations;

    // ============================================
    // MÉTODOS PÚBLICOS (Rotas)
    // ============================================

    /**
     * Mostra o formulário para criar um novo utilizador
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', User::class);
        
        $permissoes = Permission::orderBy('id', 'asc')->get();
        
        return view('admin.users.create', compact('permissoes'));
    }

    /**
     * Guarda um novo utilizador na base de dados
     * 
     * Se mudanca_password=1, permite password simples (será mudada no primeiro login).
     * Se mudanca_password=0, exige password complexa (não será forçada a mudar).
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        
        // Obter valor de mudanca_password do request
        $mudancaPassword = $request->input('mudanca_password', 1);
        
        // Validar dados do formulário
        $validated = $request->validate(
            $this->getStoreValidationRules($mudancaPassword),
            $this->getStoreValidationMessages($mudancaPassword)
        );

        // Criar utilizador
        $user = $this->createUser($validated, $mudancaPassword);

        // Registrar criação de utilizador no audit log
        $this->logUserCreation($user);

        return redirect()->route('admin.dashboard')
            ->with('status', 'Novo utilizador criado com sucesso.');
    }

    /**
     * Mostra o formulário para editar um utilizador existente
     * 
     * @param int $id ID do utilizador
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        
        $permissoes = Permission::orderBy('id', 'asc')->get();
        
        return view('admin.users.edit', compact('user', 'permissoes'));
    }

    /**
     * Atualiza um utilizador existente na base de dados
     * 
     * A password é opcional na edição. Se não for fornecida, mantém a password atual.
     * Se mudanca_password=1, permite password simples (será mudada no primeiro login).
     * Se mudanca_password=0, exige password complexa (não será forçada a mudar).
     * 
     * @param int $id ID do utilizador
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);

        // Obter valor de mudanca_password do request
        $mudancaPassword = $request->input('mudanca_password', $user->mudanca_password);
        
        // Validar dados do formulário
        $validated = $request->validate(
            $this->getUpdateValidationRules($user, $request, $mudancaPassword),
            $this->getUpdateValidationMessages($mudancaPassword)
        );

        // Capturar dados antigos antes da atualização
        $oldData = [
            'nome' => $user->nome,
            'email' => $user->email,
            'id_permissao' => $user->id_permissao,
        ];

        // Atualizar utilizador
        $this->updateUser($user, $validated, $mudancaPassword);

        // Registrar atualização de utilizador no audit log
        $this->logUserUpdate($user, $validated, $oldData);

        return redirect()->route('admin.dashboard')
            ->with('status', 'Utilizador atualizado com sucesso.');
    }

    /**
     * Elimina um utilizador (após verificar relacionamentos)
     * 
     * Previne auto-eliminação, verifica autorização e relacionamentos antes de eliminar.
     * 
     * Regras de autorização:
     * - Apenas administradores podem eliminar utilizadores
     * - Se o utilizador a eliminar é imperador, apenas imperadores podem eliminá-lo
     * - Administradores não podem eliminar imperadores
     * 
     * @param int $id ID do utilizador
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id, Request $request)
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);

        // Prevenir auto-eliminação por segurança
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Não pode eliminar o seu próprio utilizador.');
        }

        // Verificação adicional: apenas administradores podem eliminar outros administradores
        // (a policy já faz isso, mas esta é uma camada extra de segurança)
        if ($user->isAdministrador() && !Auth::user()->isAdministrador()) {
            return back()->with('error', 'Apenas administradores podem eliminar outros administradores.');
        }

        // Verificar se existem registos relacionados
        // Se existirem, não permite eliminar e mostra mensagem de erro
        $errorRedirect = $this->checkRelationsBeforeDelete($id);
        if ($errorRedirect) {
            return $errorRedirect;
        }

        // Capturar dados antes de eliminar para auditoria
        $userData = $this->captureUserData($user);

        $user->delete();

        // Registrar eliminação de utilizador no audit log
        AuditLogger::logDelete('User', $id, $userData, "Utilizador eliminado por " . Auth::user()->email);

        return back()->with('status', 'Utilizador eliminado com sucesso.');
    }

    // ============================================
    // MÉTODOS PROTEGIDOS (Trait ChecksRelations)
    // ============================================

    /**
     * Define quais relacionamentos verificar antes de eliminar um utilizador
     * 
     * Este método é usado pelo trait ChecksRelations para saber quais
     * tabelas verificar antes de permitir a eliminação.
     * 
     * @param int $id ID do utilizador
     * @return array
     */
    protected function getRelationsToCheck($id)
    {
        return []; // User não tem relacionamentos filhos conhecidos
    }

    // ============================================
    // MÉTODOS PRIVADOS (Auxiliares)
    // ============================================

    /**
     * Retorna as regras de validação para criar utilizadores
     * 
     * As regras de password são dinâmicas baseadas em mudanca_password:
     * - Se mudanca_password=1: password simples (será mudada no primeiro login)
     * - Se mudanca_password=0: password complexa (não será forçada a mudar)
     * 
     * @param int $mudancaPassword Valor de mudanca_password (0 ou 1)
     * @return array<string, array>
     */
    private function getStoreValidationRules(int $mudancaPassword): array
    {
        // Determinar regras de password baseadas em mudanca_password
        $passwordRules = ($mudancaPassword == 1) 
            ? PasswordRules::simple() 
            : PasswordRules::complex();
        
        return [
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:utilizadores,email'],
            'password' => $passwordRules,
            'id' => ['required', 'integer'],
            'mudanca_password' => ['required', 'integer', 'in:0,1'], // Aceita apenas 0 ou 1
        ];
    }

    /**
     * Retorna as mensagens de erro de validação personalizadas
     * 
     * @param int $mudancaPassword Valor de mudanca_password (0 ou 1)
     * @return array<string, string>
     */
    private function getStoreValidationMessages(int $mudancaPassword): array
    {
        $messages = [
            'nome.required' => 'O nome é obrigatório.',
            'nome.string' => 'O nome deve ser uma string.',
            'nome.max' => 'O nome não pode exceder 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser válido.',
            'email.max' => 'O email não pode exceder 255 caracteres.',
            'email.unique' => 'Este email já está em uso.',
            'password.required' => 'A password é obrigatória.',
            'password.confirmed' => 'A confirmação da password não corresponde.',
            'id.required' => 'A permissão é obrigatória.',
            'id.integer' => 'A permissão deve ser um número inteiro.',
            'mudanca_password.required' => 'O campo mudança de password é obrigatório.',
            'mudanca_password.integer' => 'O campo mudança de password deve ser um número inteiro.',
            'mudanca_password.in' => 'O campo mudança de password deve ser 0 ou 1.',
        ];

        // Adicionar mensagens de password complexa apenas se mudanca_password=0
        if ($mudancaPassword == 0) {
            $messages = array_merge($messages, PasswordRules::messages());
        }

        return $messages;
    }

    /**
     * Cria um novo utilizador na base de dados
     * 
     * @param array $validated Dados validados
     * @param int $mudancaPassword Valor de mudanca_password
     * @return \App\Models\User
     */
    private function createUser(array $validated, int $mudancaPassword): User
    {
        // Obter o email do utilizador autenticado (quem está criando)
        $creator = Auth::user()->email ?? 'Sistema';

        return User::create([
            'nome' => $validated['nome'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'creator' => $creator, // Preenchido automaticamente com o email do utilizador logado
            'id_permissao' => (int) $validated['id'],
            'ativa_filtro_visualizar_meus_codigos' => 1,
            'filtro_ativo_default_visualizar_meus_codigos' => 0,
            'mudanca_password' => (int) $validated['mudanca_password'], // Usa o valor do formulário (padrão: 1)
            'timestamp_criacao' => now(),
            'timestamp_ultima_alteracao' => now(),
        ]);
    }

    /**
     * Registra a criação de utilizador no audit log
     * 
     * @param \App\Models\User $user Utilizador criado
     * @return void
     */
    private function logUserCreation(User $user): void
    {
        AuditLogger::logCreate('User', $user->id, [
            'nome' => $user->nome,
            'email' => $user->email,
            'id_permissao' => $user->id_permissao,
        ], "Novo utilizador criado por " . Auth::user()->email);
    }

    /**
     * Captura dados do utilizador para auditoria
     * 
     * @param \App\Models\User $user Utilizador
     * @return array<string, mixed>
     */
    private function captureUserData(User $user): array
    {
        return [
            'nome' => $user->nome,
            'email' => $user->email,
            'id_permissao' => $user->id_permissao,
        ];
    }

    /**
     * Retorna as regras de validação para atualizar utilizadores
     * 
     * A password é opcional na edição. Se não for fornecida, mantém a password atual.
     * As regras de password são dinâmicas baseadas em mudanca_password:
     * - Se mudanca_password=1: password simples (será mudada no primeiro login)
     * - Se mudanca_password=0: password complexa (não será forçada a mudar)
     * 
     * @param \App\Models\User $user Utilizador a ser atualizado
     * @param \Illuminate\Http\Request $request Request com os dados
     * @param int $mudancaPassword Valor de mudanca_password (0 ou 1)
     * @return array<string, array>
     */
    private function getUpdateValidationRules(User $user, Request $request, int $mudancaPassword): array
    {
        // Determinar regras de password baseadas em mudanca_password (apenas se password for fornecida)
        $passwordRules = ($mudancaPassword == 1) 
            ? PasswordRules::simple() 
            : PasswordRules::complex();
        
        $rules = [
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:utilizadores,email,' . $user->id],
            'id_permissao' => ['required', 'integer'],
            'mudanca_password' => ['required', 'integer', 'in:0,1'],
        ];

        // Password é opcional na edição
        if ($request->filled('password')) {
            $rules['password'] = $passwordRules;
            $rules['password_confirmation'] = ['required_with:password', 'same:password'];
        }

        return $rules;
    }

    /**
     * Retorna as mensagens de erro de validação personalizadas para atualização
     * 
     * @param int $mudancaPassword Valor de mudanca_password (0 ou 1)
     * @return array<string, string>
     */
    private function getUpdateValidationMessages(int $mudancaPassword): array
    {
        $messages = [
            'nome.required' => 'O nome é obrigatório.',
            'nome.string' => 'O nome deve ser uma string.',
            'nome.max' => 'O nome não pode exceder 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser válido.',
            'email.max' => 'O email não pode exceder 255 caracteres.',
            'email.unique' => 'Este email já está em uso.',
            'password.confirmed' => 'A confirmação da password não corresponde.',
            'id_permissao.required' => 'A permissão é obrigatória.',
            'id_permissao.integer' => 'A permissão deve ser um número inteiro.',
            'mudanca_password.required' => 'O campo mudança de password é obrigatório.',
            'mudanca_password.integer' => 'O campo mudança de password deve ser um número inteiro.',
            'mudanca_password.in' => 'O campo mudança de password deve ser 0 ou 1.',
        ];

        // Adicionar mensagens de password complexa apenas se mudanca_password=0
        if ($mudancaPassword == 0) {
            $messages = array_merge($messages, PasswordRules::messages());
        }

        return $messages;
    }

    /**
     * Atualiza um utilizador existente na base de dados
     * 
     * @param \App\Models\User $user Utilizador a ser atualizado
     * @param array $validated Dados validados
     * @param int $mudancaPassword Valor de mudanca_password
     * @return void
     */
    private function updateUser(User $user, array $validated, int $mudancaPassword): void
    {
        $user->nome = $validated['nome'];
        $user->email = $validated['email'];
        $user->id_permissao = (int) $validated['id_permissao'];
        $user->mudanca_password = (int) $validated['mudanca_password'];
        $user->timestamp_ultima_alteracao = now();

        // Atualizar password apenas se fornecida
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
    }

    /**
     * Registra a atualização de utilizador no audit log
     * 
     * @param \App\Models\User $user Utilizador atualizado
     * @param array $validated Dados validados
     * @param array $oldData Dados antigos antes da atualização
     * @return void
     */
    private function logUserUpdate(User $user, array $validated, array $oldData): void
    {
        // Preparar dados novos
        $newData = [
            'nome' => $validated['nome'],
            'email' => $validated['email'],
            'id_permissao' => $validated['id_permissao'],
        ];

        // Incluir mudança de password apenas se foi alterada
        if (!empty($validated['password'])) {
            $newData['password'] = '***';
        }

        AuditLogger::logUpdate('User', $user->id, $oldData, $newData, "Utilizador atualizado por " . Auth::user()->email);
    }

    /**
     * Verifica relacionamentos antes de eliminar
     * Se existirem relacionamentos, retorna redirect com erro
     * 
     * @param int $id ID do utilizador
     * @return \Illuminate\Http\RedirectResponse|null Retorna redirect se houver relacionamentos, null caso contrário
     */
    private function checkRelationsBeforeDelete(int $id)
    {
        $relations = $this->getRelationsToCheck($id);
        $messages = [];

        foreach ($relations as $relation) {
            $count = $relation['model']::where($relation['foreign_key'], $relation['foreign_value'])->count();
            
            if ($count > 0) {
                $messages[] = "{$count} {$relation['label']}";
            }
        }

        if (!empty($messages)) {
            return redirect()->back()->withErrors([
                'delete_error' => 'Não é possível eliminar este utilizador porque possui ' . 
                    implode(', ', $messages) . ' relacionada(s). Por favor, elimine primeiro os registos relacionados.'
            ]);
        }

        return null;
    }
}
