<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AdminPostController extends Controller
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
        $posts = Post::orderBy('ordem', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Mostra o formulário para criar um novo idioma
     */
    public function create()
    {
        return view('admin.posts.create', [
            'post' => new Post()
        ]);
    }

    /**
     * Guarda um novo post na base de dados
     */
    public function store(Request $request)
    {
        // Validar dados do formulário
        $data = $request->validate($this->getValidationRules(), $this->getValidationMessages());

        // Criar novo idioma
        $post = new Post();
        $this->fillPostData($post, $data, $request);

        // Definir ordem (último da lista)
        $post->ordem = $this->getNextOrder();

        // Guardar Imagem se foi enviado
        $newImage = null;
        if ($request->hasFile('image_file')) {
            $newImage = $this->saveImageToPublic($request->file('image_file'));
        }

        $post->image = $newImage ?? '';


        // atribuir data/hora atual ao published_at
        //$post->published_at = now();

        $post->save();

        return redirect()->route('admin.posts.index')
            ->with('status', 'Post criado com sucesso.');
    }


    /**
     * Mostra o formulário para editar um elemento da equipa
     */
    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }


    /**
     * Atualiza um elemento da equipa na base de dados
     */
    public function update(Request $request, Post $post){
        // Validar dados (incluindo o ID do team para unique)
        $rules = $this->getValidationRules();
        $data = $request->validate($rules, $this->getValidationMessages());

        // Atualizar dados do elemento da equipa
        $this->fillPostData($post, $data, $request);

        // Guardar Imagem se foi enviado
        if ($request->hasFile('image_file')) {
            $this->deleteOldImage($post->image);
            $newImage = $this->saveImageToPublic($request->file('image_file'));
            if ($newImage !== null) {
                $post->image = $newImage;
            }
        }

        $post->published_at = now();

        $post->save();
        return redirect()->route('admin.posts.index')
            ->with('status', 'Elemento da equipa atualizado com sucesso.');
    }


    /**
     * Elimina um elemento da equipa (após verificar relacionamentos)
     */
    public function destroy(Post $post)
    {
        // Eliminar ícone do servidor se existir
        $this->deleteOldImage($post->image);

        // Eliminar idioma
        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('status', 'Post removido.');
    }

    /**
     * AJAX: Ativa/desativa um idioma
     */
    public function toggleAtivo(Request $request, Post $post)
    {
        $post->feature = $request->input('ativo') ? 1 : 0;
        $post->save();

        return response()->json([
            'success' => true,
            'ativo' => $post->feature
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
            'title' => ['required', 'string', 'max:50'],
            'content' => ['required', 'string', 'max:255'],
            'link'=> ['required', 'string', 'max:255'],
            'phone' => ['required', 'digits:9'],
            'email'=> ['required', 'string','max:100','email'],
            'feature' => ['nullable', 'boolean'],
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
            'title.required' => 'O título é obrigatório.',
            'title.max' => 'O título não pode exceder 50 caracteres.',
            'content.required' => 'O conteúdo é obrigatório.',
            'content.max' => 'O conteúdo não pode exceder 255 caracteres.',
            'link.required' => 'O link é obrigatório.',
            'phone.required' => 'O telefone é obrigatório.',
            'phone.numeric' => 'O telefone deve ser numérico.',
            'phone.max' => 'O telefone não pode exceder 9 dígitos.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser um endereço válido.',
            'email.max' => 'O email não pode exceder 100 caracteres.',
            'feature.boolean' => 'O campo destaque deve ser verdadeiro ou falso.',
            'image_file.image' => 'O ficheiro deve ser uma imagem.',
            'image_file.max' => 'A imagem não pode exceder 4MB.',
        ];
    }

    /**
     * Preenche os dados do idioma com os valores do formulário
     */
    private function fillPostData(Post $post, array $data, Request $request): void
    {
        $post->title = $data['title'];
        $post->content = $data['content'];
        $post->link = $data['link'];
        $post->phone = $data['phone'];   
        $post->email = $data['email'];
        $post->feature = $data['feature'] ?? false;
    }


    private function saveImageToPublic($file): string
    {
        $dir = public_path('img/posts');
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

        $path = public_path('img/posts/' . $imageName);
        
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
        $maxOrdem = Post::max('ordem') ?? 0;
        return $maxOrdem + 1;
    }

}
