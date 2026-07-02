<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Permission;
use App\Models\SchoolProtocol;
use Illuminate\Support\Facades\DB;

/**
 * Controller para o dashboard administrativo
 * 
 * Exibe estatísticas gerais e listas de utilizadores, categorias e permissões.
 */
class DashboardController extends Controller
{
    /**
     * Exibe a página principal do dashboard
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.dashboard', [
            'users' => $this->getUsers(),
            'permissoes' => $this->getPermissoes(),
            ...$this->getStatistics(),
        ]);
    }

    // ============================================
    // MÉTODOS PRIVADOS (Auxiliares)
    // ============================================

    /**
     * Obtém a lista de utilizadores para exibir na tabela
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getUsers()
    {
        return User::with('permission')
            ->select('id', 'email', 'nome', 'id_permissao', 'creator', 'timestamp_criacao')
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Obtém a lista de permissões ordenadas
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getPermissoes()
    {
        return Permission::orderBy('id', 'asc')->get();
    }

    /**
     * Obtém todas as estatísticas para os cards do dashboard
     * 
     * Retorna um array associativo com todas as estatísticas calculadas.
     * Usa spread operator (...) para passar como parâmetros separados para a view.
     * 
     * @return array<string, int>
     */
    private function getStatistics(): array
    {
        return [
            'totalUtilizadores' => $this->getTotalUtilizadores(),
            'protocolosEscolares' => $this->getProtocolosEscolares(),
        ];
    }

    /**
     * Conta o total de utilizadores
     * 
     * @return int
     */
    private function getTotalUtilizadores(): int
    {
        return User::count();
    }

    /**
     * Obtém a lista de protocolos escolares para o dashboard
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getProtocolosEscolares()
    {
        return SchoolProtocol::orderBy('ordem', 'asc')->get();
    }   
}
