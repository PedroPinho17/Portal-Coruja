<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Trait genérico para verificar relacionamentos antes de eliminar registos
 * 
 * Este trait fornece uma solução reutilizável para verificar se um registo
 * possui relacionamentos que impedem a sua eliminação, evitando erros de
 * integridade referencial na base de dados.
 * 
 * Como usar:
 * 1. Adicione "use ChecksRelations;" no seu controller
 * 2. Implemente o método getRelationsToCheck($id) definindo os relacionamentos
 * 3. Adicione a rota: Route::get('/{entidade}/{id}/check-relations', [Controller::class, 'checkRelations'])
 * 4. No método destroy(), use a lógica de verificação (ou chame checkRelations via AJAX)
 */
trait ChecksRelations
{
    /**
     * Verifica relacionamentos antes de eliminar um registo
     * 
     * Este método é chamado via AJAX pelo JavaScript quando o utilizador tenta
     * eliminar um registo. Ele verifica todos os relacionamentos definidos no
     * método getRelationsToCheck() e retorna uma resposta JSON indicando se
     * existem relacionamentos que impedem a eliminação.
     * 
     * Suporta route model binding: aceita tanto um ID (int) quanto um objeto
     * modelo Eloquent, extraindo automaticamente o ID quando necessário.
     * 
     * @param Request $request - Requisição HTTP (não utilizado diretamente, mas mantido para compatibilidade)
     * @param mixed $idOrModel - Pode ser um ID (int) ou um modelo Eloquent (objeto)
     * @return \Illuminate\Http\JsonResponse - Resposta JSON com informações sobre os relacionamentos
     */
    public function checkRelations(Request $request, $idOrModel)
    {
        // Extrair ID se for um objeto (route model binding)
        // Exemplo: Se a rota for /traducoes/{traducao}/check-relations e {traducao}
        // for um modelo Traducao, extraímos o ID usando getKey()
        $id = is_object($idOrModel) && method_exists($idOrModel, 'getKey') 
            ? $idOrModel->getKey() 
            : $idOrModel;
        
        // Obter a lista de relacionamentos a verificar
        // Este método é implementado em cada controller que usa este trait
        $relations = $this->getRelationsToCheck($id);
        
        // Array para armazenar os relacionamentos encontrados
        $foundRelations = [];
        
        // Iterar sobre cada relacionamento definido
        foreach ($relations as $relation) {
            // Obter a classe do modelo a verificar (ex: TraducaoIdioma::class)
            $model = $relation['model'];
            
            // Obter a chave estrangeira (ex: 'id' ou 'id_categoria')
            $foreignKey = $relation['foreign_key'];
            
            // Obter o valor da chave estrangeira (por padrão usa o ID do registo atual)
            // Pode ser sobrescrito se o relacionamento usar um campo diferente
            $foreignValue = $relation['foreign_value'] ?? $id;
            
            // Obter o label para exibição (ex: 'tradução(ões) por idioma')
            $label = $relation['label'];
            
            // Contar quantos registos relacionados existem
            // Exemplo: SELECT COUNT(*) FROM traducoes_idiomas WHERE id = $id
            $count = $model::where($foreignKey, $foreignValue)->count();
            
            // Se encontrou relacionamentos, adicionar à lista
            if ($count > 0) {
                $foundRelations[] = [
                    'type' => $relation['type'] ?? $label,  // Tipo do relacionamento (para identificação)
                    'count' => $count,                      // Quantidade de registos relacionados
                    'label' => $label,                      // Label para exibição ao utilizador
                    'message' => "Este registo possui {$count} {$label} relacionada(s)."  // Mensagem formatada
                ];
            }
        }
        
        // Retornar resposta JSON para o JavaScript processar
        return response()->json([
            'has_relations' => count($foundRelations) > 0,  // true se encontrou relacionamentos
            'relations' => $foundRelations,                  // Array com detalhes dos relacionamentos encontrados
            'message' => count($foundRelations) > 0 
                ? 'Este registo possui relacionamentos que impedem a eliminação.' 
                : 'Nenhum relacionamento encontrado. Pode eliminar com segurança.'
        ]);
    }
    
    /**
     * Define os relacionamentos a verificar antes de eliminar um registo
     * 
     * Este método deve ser implementado em cada controller que usa este trait.
     * Ele retorna um array com a configuração dos relacionamentos a verificar.
     * 
     * Estrutura do array de retorno:
     * [
     *     [
     *         'model' => ModelClass::class,        // Classe do modelo relacionado
     *         'foreign_key' => 'campo_fk',          // Nome do campo chave estrangeira
     *         'foreign_value' => $id,               // Valor da FK (opcional, usa $id por padrão)
     *         'label' => 'descrição do relacionamento',  // Label para exibição
     *         'type' => 'tipo_relacionamento'       // Tipo (opcional, usa label por padrão)
     *     ],
     *     // ... mais relacionamentos
     * ]
     * 
     * Exemplo de implementação (em CategoriaController):
     * protected function getRelationsToCheck($id_categoria)
     * {
     *     return [
     *         [
     *             'model' => Subcategoria::class,
     *             'foreign_key' => 'id_categoria',
     *             'foreign_value' => $id_categoria,
     *             'label' => 'subcategoria(s)',
     *             'type' => 'subcategorias'
     *         ],
     *         [
     *             'model' => Apresentacao::class,
     *             'foreign_key' => 'id_categoria',
     *             'foreign_value' => $id_categoria,
     *             'label' => 'apresentação(ões)',
     *             'type' => 'apresentacoes'
     *         ]
     *     ];
     * }
     * 
     * @param mixed $id - ID do registo a verificar
     * @return array - Array com a configuração dos relacionamentos a verificar
     */
    abstract protected function getRelationsToCheck($id);
}

