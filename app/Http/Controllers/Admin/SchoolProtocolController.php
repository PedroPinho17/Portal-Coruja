<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolProtocol;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolProtocolController extends Controller
{
    // ============================================
    // MÉTODOS PÚBLICOS (Rotas)
    // ============================================

    /**
     * Lista todos os protocolos escolares ordenados
     */
    public function index()
    {
        $protocols = SchoolProtocol::orderBy('ordem', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.protocols.index', compact('protocols'));
    }

    /**
     * Mostra o formulário para criar um novo protocolo
     */
    public function create()
    {
        return view('admin.protocols.create', [
            'protocol' => new SchoolProtocol()
        ]);
    }

    /**
     * Guarda um novo protocolo escolar na base de dados
     */
    public function store(Request $request)
    {
        // Validar dados do formulário
        $data = $request->validate($this->getValidationRules(), $this->getValidationMessages());

        // Criar novo protocolo
        $protocol = new SchoolProtocol();
        $this->fillProtocolData($protocol, $data, $request);

        // Definir ordem (último da lista)
        $protocol->ordem = $this->getNextOrder();

        $protocol->save();
        return redirect()->route('admin.protocols.index')
            ->with('status', 'Protocolo criado com sucesso.');
    }

    /**
     * Mostra o formulário para editar um protocolo escolar
     */
    public function edit(SchoolProtocol $protocol)
    {
        return view('admin.protocols.edit', compact('protocol'));
    }

    /**
     * Atualiza um protocolo escolar na base de dados
     */
    public function update(Request $request, SchoolProtocol $protocol)
    {
        // Validar dados
        $rules = $this->getValidationRules();
        $data = $request->validate($rules, $this->getValidationMessages());

        // Atualizar dados do protocolo
        $this->fillProtocolData($protocol, $data, $request);

        $protocol->save();
        return redirect()->route('admin.protocols.index')
            ->with('status', 'Protocolo atualizado com sucesso.');
    }

    /**
     * Elimina um protocolo escolar
     */
    public function destroy(SchoolProtocol $protocol)
    {
        // Eliminar protocolo
        $protocol->delete();

        return redirect()->route('admin.protocols.index')
            ->with('status', 'Protocolo removido.');
    }

    /**
     * AJAX: Reordena protocolos após arrastar na tabela (RowReorder)
     */
    public function reorder(Request $request)
    {
        $ordem = $request->input('ordem') ?? $request->input('items');

        // Validar formato
        if (!is_array($ordem)) {
            return response()->json([
                'success' => false,
                'message' => 'Formato inválido: esperado array de ordem.'
            ], 422);
        }

        // Validar IDs
        foreach ($ordem as $item) {
            if (!isset($item['id']) || !is_numeric($item['id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cada item deve conter um id numérico.'
                ], 422);
            }
        }

        // Atualizar ordem na base de dados
        try {
            DB::transaction(function() use ($ordem) {
                foreach ($ordem as $idx => $item) {
                    SchoolProtocol::where('id', $item['id'])
                        ->update(['ordem' => $idx + 1]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Ordem atualizada com sucesso.'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar ordem dos protocolos: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar ordem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX: Toggle ativo/inativo
     */
    public function toggleAtivo(Request $request, SchoolProtocol $protocol)
    {
        $protocol->ativo = $request->input('ativo') ? 1 : 0;
        $protocol->save();

        return response()->json([
            'success' => true,
            'ativo' => $protocol->ativo
        ]);
    }

    // ============================================
    // MÉTODOS PRIVADOS (Auxiliares)
    // ============================================

    /**
     * Retorna as regras de validação para criar/editar protocolo
     */
    private function getValidationRules(): array
    {
        return [
            'school_name' => ['required', 'string', 'max:255'],
            'link' => ['nullable', 'url', 'max:500'],
            'ativo' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Retorna as mensagens de erro de validação personalizadas
     */
    private function getValidationMessages(): array
    {
        return [
            'school_name.required' => 'O nome da escola é obrigatório.',
            'school_name.max' => 'O nome da escola não pode exceder 255 caracteres.',
            'link.url' => 'O link deve ser uma URL válida.',
            'link.max' => 'O link não pode exceder 500 caracteres.',
        ];
    }

    /**
     * Preenche os dados do protocolo com os valores do formulário
     */
    private function fillProtocolData(SchoolProtocol $protocol, array $data, Request $request): void
    {
        $protocol->school_name = $data['school_name'];
        $protocol->link = $data['link'] ?? null;
        $protocol->ativo = $request->has('ativo') ? (bool)$request->input('ativo') : true;
    }

    /**
     * Obtém a próxima ordem disponível
     */
    private function getNextOrder(): int
    {
        $maxOrdem = SchoolProtocol::max('ordem') ?? 0;
        return $maxOrdem + 1;
    }
}
