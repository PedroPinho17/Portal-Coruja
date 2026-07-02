<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\Entity;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    //
    public function index(){

        $formations = Formation::orderBy('id', 'asc')->get();
        return view('admin.formations.index', compact('formations'));
    }

    /**
     * Mostra o formulário para criar uma nova formação
     */
    public function create()
    {
        $entities = Entity::orderBy('name', 'asc')->get();
        
        return view('admin.formations.create', [
            'formation' => new Formation(),
            'entities' => $entities
        ]);
    }

    public function store(Request $request){

        // Validar dados do formulário
        $data = $request->validate($this->getValidationRules(), $this->getValidationMessages());

        $formation = new Formation();
        $this->fillFormationData($formation, $data, $request);

        $formation->ordem = $this->getNextOrder();
        $formation->save();

        return redirect()->route('admin.formations.index')->with('success','Formação criada com sucesso.');
    }

    /**
     * Mostra o formulário para editar um elemento da equipa
     */
    public function edit(Formation $formation)
    {
         $entities = Entity::orderBy('name', 'asc')->get();
        return view('admin.formations.edit', compact('formation', 'entities'));
    }

    /**
     * Atualiza um elemento da equipa na base de dados
     */
    public function update(Request $request, Formation $formation){

        // Validar dados (incluindo o ID do team para unique)
        $rules = $this->getValidationRules();
        $data = $request->validate($rules, $this->getValidationMessages());

        // Atualizar dados do elemento da equipa
        $this->fillFormationData($formation, $data, $request);

        $formation->save();
        return redirect()->route('admin.formations.index')
            ->with('status', 'Formação atualizada com sucesso.');
    }

    public function destroy(Formation $formation)
    {
        $formation->delete();

        return redirect()->route('admin.formations.index')
            ->with('status', 'Formação eliminada com sucesso.');
    }


    /**
     * AJAX: Ativa/desativa um idioma
     */
    public function toggleAtivo(Request $request, Formation $formation)
    {
        $formation->active = $request->input('ativo') ? 1 : 0;
        $formation->save();

        return response()->json([
            'success' => true,
            'ativo' => $formation->active
        ]);
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
                    Post::where('id', $item['id'])
                        ->update(['ordem' => $idx + 1]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Ordem atualizada com sucesso.'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar ordem dos posts: ' . $e->getMessage());
            
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
     * Retorna as regras de validação para criar/editar idioma
     * 
     * Centraliza as regras de validação em um único lugar,
     * facilitando manutenção e consistência.
     */
    private function getValidationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:255'],
            'duration'=> ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:9'],
            'id_entity'=> ['required', 'integer', 'exists:entities,id'],
            'active' => ['nullable', 'boolean'],
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
            'description.required' => 'A descrição é obrigatória.',
            'description.max' => 'A descrição não pode exceder 255 caracteres.',
            'duration.required' => 'A duração é obrigatória.',
            'location.required' => 'A localização é obrigatória.',
            'id_entity.required' => 'A entidade é obrigatória.',
            'id_entity.exists' => 'A entidade selecionada é inválida.',
            'active.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
        ];
    }

    /**
     * Preenche os dados do idioma com os valores do formulário
     */
    private function fillFormationData(Formation $formation, array $data, Request $request): void
    {
        $formation->name = $data['name'];
        $formation->description = $data['description'];
        $formation->duration = $data['duration'];
        $formation->location = $data['location'];
        $formation->id_entity = $data['id_entity'];
        $formation->active = $data['active'] ?? false;
    }

    /**
     * Obtém a próxima ordem disponível
     * 
     * @return int
     */
    private function getNextOrder(): int
    {
        $maxOrdem = Formation::max('ordem') ?? 0;
        return $maxOrdem + 1;
    }
}
