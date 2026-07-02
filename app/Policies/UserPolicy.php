<?php

namespace App\Policies;

use App\Models\User;

/**
 * Policy para autorização de ações relacionadas a utilizadores
 * 
 * Esta policy define quais utilizadores podem realizar ações CRUD
 * sobre outros utilizadores. Apenas administradores têm permissão
 * para criar, editar e eliminar utilizadores.
 * 
 * Funcionalidades de visualização, restauro e eliminação permanente
 * não estão atualmente implementadas no sistema.
 */
class UserPolicy
{
    // ============================================
    // MÉTODOS PÚBLICOS (Autorização)
    // ============================================

    /**
     * Determina se o utilizador pode visualizar qualquer modelo
     * 
     * Esta funcionalidade não está atualmente implementada no sistema.
     * 
     * @param \App\Models\User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determina se o utilizador pode visualizar um modelo específico
     * 
     * Esta funcionalidade não está atualmente implementada no sistema.
     * 
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return bool
     */
    public function view(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determina se o utilizador pode criar novos utilizadores
     * 
     * Apenas administradores têm permissão para criar novos utilizadores.
     * A verificação usa o método isAdministrador() do modelo User,
     * que verifica o relacionamento com tipos_permissoes.
     * 
     * @param \App\Models\User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $this->isAdministrator($user);
    }

    /**
     * Determina se o utilizador pode atualizar um modelo específico
     * 
     * Apenas administradores têm permissão para editar utilizadores.
     * A verificação usa o método isAdministrador() do modelo User,
     * que verifica o relacionamento com tipos_permissoes.
     * 
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return bool
     */
    public function update(User $user, User $model): bool
    {
        return $this->isAdministrator($user);
    }

    /**
     * Determina se o utilizador pode eliminar um modelo específico
     * 
     * Regras de autorização:
     * - Apenas administradores (id_permissao = 1) podem eliminar utilizadores
     * - Se o utilizador a eliminar é administrador (id_permissao = 1), apenas administradores podem eliminá-lo
     * - Se o utilizador a eliminar não é administrador, administradores podem eliminá-lo
     * 
     * @param \App\Models\User $user Utilizador que está tentando eliminar
     * @param \App\Models\User $model Utilizador a ser eliminado
     * @return bool
     */
    public function delete(User $user, User $model): bool
    {
        // Apenas administradores podem eliminar utilizadores
        if (!$this->isAdministrator($user)) {
            return false;
        }

        // Se o utilizador a eliminar é administrador, apenas administradores podem eliminá-lo
        if ($model->isAdministrador()) {
            return $user->isAdministrador();
        }

        // Se não é administrador, administradores podem eliminar
        return true;
    }

    /**
     * Determina se o utilizador pode restaurar um modelo eliminado
     * 
     * Esta funcionalidade não está atualmente implementada no sistema.
     * 
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return bool
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determina se o utilizador pode eliminar permanentemente um modelo
     * 
     * Esta funcionalidade não está atualmente implementada no sistema.
     * 
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return bool
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }

    // ============================================
    // MÉTODOS PRIVADOS (Auxiliares)
    // ============================================

    /**
     * Verifica se o utilizador é um administrador
     * 
     * Centraliza a lógica de verificação de permissões de administrador.
     * Usa o método isAdministrador() do modelo User, que verifica
     * o relacionamento com tipos_permissoes.
     * 
     * @param \App\Models\User $user
     * @return bool
     */
    private function isAdministrator(User $user): bool
    {
        return $user->isAdministrador();
    }
}
