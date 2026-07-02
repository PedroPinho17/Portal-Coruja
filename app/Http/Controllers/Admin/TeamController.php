<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ChecksRelations;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TeamController extends Controller
{
    //use ChecksRelations;

    // ============================================
    // MÉTODOS PÚBLICOS (Rotas)
    // ============================================


    /**
     * Lista todos os idiomas ordenados
     */
    public function index()
    {
        $teams = Team::orderBy('ordem', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.teams.index', compact('teams'));
    }

    /**
     * Mostra o formulário para criar um novo idioma
     */
    public function create()
    {
        return view('admin.teams.create', [
            'team' => new Team()
        ]);
    }

    /**
     * Guarda um novo elemento da equipa na base de dados
     */
    public function store(Request $request)
    {
        // Validar dados do formulário
        $data = $request->validate($this->getValidationRules(), $this->getValidationMessages());

        // Criar novo idioma
        $team = new Team();
        $this->fillTeamData($team, $data, $request);

        // Definir ordem (último da lista)
        $team->ordem = $this->getNextOrder();

        // Guardar Imagem se foi enviado
        $newImage = null;
        if ($request->hasFile('image_file')) {
            $newImage = $this->saveImageToPublic($request->file('image_file'));
        }

        $team->image = $newImage;

        $team->save();
        return redirect()->route('admin.teams.index')
            ->with('status', 'Elemento da equipa criado com sucesso.');
    }


    /**
     * Mostra o formulário para editar um elemento da equipa
     */
    public function edit(Team $team)
    {
        return view('admin.teams.edit', compact('team'));
    }

    /**
     * Atualiza um elemento da equipa na base de dados
     */
    public function update(Request $request, Team $team){
        // Validar dados (incluindo o ID do team para unique)
        $rules = $this->getValidationRules();
        $data = $request->validate($rules, $this->getValidationMessages());

        // Atualizar dados do elemento da equipa
        $this->fillTeamData($team, $data, $request);

        // Guardar Imagem se foi enviado
        if ($request->hasFile('image_file')) {
            $this->deleteOldImage($team->image);
            $newImage = $this->saveImageToPublic($request->file('image_file'));
            if ($newImage !== null) {
                $team->image = $newImage;
            }
        }

        $team->save();
        return redirect()->route('admin.teams.index')
            ->with('status', 'Elemento da equipa atualizado com sucesso.');
    }

    /**
     * Elimina um elemento da equipa (após verificar relacionamentos)
     */
    public function destroy(Team $team)
    {
        // Verificar se existem registos relacionados
        // Se existirem, não permite eliminar e mostra mensagem de erro
        /* if ($this->hasRelatedRecords($team->id)) {
            return redirect()->route('admin.teams.index')
                ->with('error', 'Não é possível eliminar este elemento da equipa porque existem registos relacionados.');
        } */

        // Eliminar ícone do servidor se existir
        $this->deleteOldImage($team->image);

        // Eliminar idioma
        $team->delete();

        return redirect()->route('admin.teams.index')
            ->with('status', 'Elemento da equipa removido.');
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
    // MÉTODOS PROTEGIDOS (Trait ChecksRelations)
    // ============================================

    /**
     * Define quais relacionamentos verificar antes de eliminar um idioma
     * 
     * Este método é usado pelo trait ChecksRelations para saber quais
     * tabelas verificar antes de permitir a eliminação.
     */
    /* protected function getRelationsToCheck($id)
    {
        return [
            
        ];
    } */


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
            'descricao' => ['required', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'max:4096'],
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
            'image_file.image' => 'O ficheiro deve ser uma imagem.',
            'image_file.max' => 'A imagem não pode exceder 4MB.',
        ];
    }

    /**
     * Preenche os dados do idioma com os valores do formulário
     */
    private function fillTeamData(Team $team, array $data, Request $request): void
    {
        $team->name = $data['name'];
        $team->description = $data['descricao'];   
    }


    private function saveImageToPublic($file): string
    {
        $dir = public_path('img/teams');
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $orig = $file->getClientOriginalName();
        $ext = strtolower($file->getClientOriginalExtension());
        $base = pathinfo($orig, PATHINFO_FILENAME);
        $base = Str::slug($base, '-');
        if ($base === '') {
            $base = 'imagem';
        }
        $maxLen = 50; // including extension
        $available = $maxLen - (strlen($ext) + 1);
        $base = substr($base, 0, $available);
        $candidate = $base . '.' . $ext;
        $i = 1;
        while (is_file($dir . DIRECTORY_SEPARATOR . $candidate)) {
            $suffix = '-' . $i;
            $available = $maxLen - (strlen($ext) + 1 + strlen($suffix));
            $baseTrim = substr($base, 0, $available);
            $candidate = $baseTrim . $suffix . '.' . $ext;
            $i++;
            if ($i > 500) break;
        }
        $file->move($dir, $candidate);
        return $candidate;
    }

    /**
     * Processa o upload de imagem e elimina imagens antigas se necessário
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Support\Collection $existing Registos existentes
     * @return string|null Caminho da nova imagem ou null
     */
    /* private function handleImageUpload(Request $request, $existing): ?string
    {
        if (!$request->hasFile('image_file')) {
            return null;
        }
        
        // Eliminar todas as imagens antigas (usar apenas o campo 'image')
        foreach ($existing as $cat) {
            if (!empty($cat->image)) {
                $this->deleteImage($cat->image);
            }
        }
        
        return $this->saveImageToPublic($request->file('image_file'));
    } */

    /**
     * Elimina o ícone antigo do servidor
     */
    private function deleteOldImage(?string $imageName): void
    {
        if (empty($imageName)) {
            return;
        }

        $path = public_path('img/teams/' . $imageName);
        
        if (is_file($path)) {
            @unlink($path);
        }
    }

    /**
     * Obtém a próxima ordem disponível
     * 
     * @return int
     */
    private function getNextOrder(): int
    {
        $maxOrdem = Team::max('ordem') ?? 0;
        return $maxOrdem + 1;
    }
}
