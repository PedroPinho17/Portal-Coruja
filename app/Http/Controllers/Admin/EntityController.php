<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use Illuminate\Http\Request;

class EntityController extends Controller
{
    public function index(){

        $entities = Entity::orderBy('id', 'asc')->get();
        return view('admin.entities.index', compact('entities'));
    }

    /**
     * Mostra o formulário para criar uma nova entidade
     */
    public function create()
    {
        return view('admin.entities.create', [
            'entity' => new Entity()
        ]);
    }

    public function store(Request $request){

        $data = $request->validate($this->getValidationRules(), $this->getValidationMessages());
        $entity = new Entity();

        $this->fillEntityData($entity, $data, $request);

        $entity->ordem = $this->getNextOrder();
        $entity->save();

        return redirect()->route('admin.entities.index')->with('success','Entidade criada com sucesso.');

    }

    /**
     * Mostra o formulário para editar um elemento da equipa
     */
    public function edit(Entity $entity)
    {
        return view('admin.entities.edit', compact('entity'));
    }

    public function update(Request $request, Entity $entity){
        $rules = $this->getValidationRules();
        $data = $request->validate($rules, $this->getValidationMessages());

        $this->fillEntityData($entity, $data, $request);
        $entity->save();
        return redirect()->route('admin.entities.index')->with('success','Entidade atualizada com sucesso.');
    }

    public function destroy(Entity $entity){
        $entity->delete();
        return redirect()->route('admin.entities.index')->with('success','Entidade eliminada com sucesso.');
    }

    /**
     * AJAX: Reordena idiomas após arrastar na tabela (RowReorder)
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
                    Team::where('id', $item['id'])
                        ->update(['ordem' => $idx + 1]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Ordem atualizada com sucesso.'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar ordem das equipas: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar ordem: ' . $e->getMessage()
            ], 500);
        }
    }
    // ============================================
    // MÉTODOS PRIVADOS (Auxiliares)
    // ============================================

    /**
     * Retorna as regras de validação para criar/editar entidade
     * 
     * Centraliza as regras de validação em um único lugar,
     * facilitando manutenção e consistência.
     */
    private function getValidationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'descricao' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
        ];
    }

    /**
     * Retorna as mensagens de erro de validação personalizadas
     * 
     * Mensagens mais claras e específicas para melhor UX.
     */
    private function getValidationMessages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode exceder 50 caracteres.',
            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.max' => 'A descrição não pode exceder 255 caracteres.',
            'location.required' => 'A localização é obrigatória.',
            'location.max' => 'A localização não pode exceder 255 caracteres.',
            'website.url'=> 'O website deve ser uma URL válida.',
            'website.max'=> 'O website não pode exceder 255 caracteres.',
        ];
    }

    /**
     * Preenche os dados do idioma com os valores do formulário
     */
    private function fillEntityData(Entity $entity, array $data, Request $request): void
    {
        $entity->name = $data['name'];
        $entity->description = $data['descricao'];   
        $entity->location = $data['location'];
        $entity->website = $data['website'] ?? null;
    }

    /**
     * Obtém a próxima ordem disponível
     * 
     * @return int
     */
    private function getNextOrder(): int
    {
        $maxOrdem = Entity::max('ordem') ?? 0;
        return $maxOrdem + 1;
    }

}
