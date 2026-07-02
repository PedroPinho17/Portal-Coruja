<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Helper para registrar ações de auditoria no sistema
 * 
 * Este helper permite registrar ações sensíveis como:
 * - Criação de registos
 * - Edição de registos
 * - Eliminação de registos
 * - Alteração de permissões
 * - Alteração de palavras-passe
 * - Login/Logout
 * 
 * Os logs são salvos em:
 * - storage/logs/laravel.log (padrão do Laravel)
 * - Tabela de auditoria (se configurada)
 */
class AuditLogger
{
    /**
     * Registra uma ação de auditoria
     * 
     * @param string $action Ação realizada (ex: 'create', 'update', 'delete', 'login')
     * @param string $model Modelo afetado (ex: 'User', 'Categoria', 'Apresentacao')
     * @param int|null $modelId ID do modelo afetado
     * @param array|null $oldData Dados antes da alteração (para updates)
     * @param array|null $newData Dados depois da alteração (para updates/creates)
     * @param string|null $description Descrição adicional da ação
     * @return void
     */
    public static function log(
        string $action,
        string $model,
        ?int $modelId = null,
        ?array $oldData = null,
        ?array $newData = null,
        ?string $description = null
    ): void {
        $user = Auth::user();
        $userId = $user ? $user->id : null;
        $userEmail = $user ? $user->email : 'Sistema';
        
        // Preparar dados para log
        $logData = [
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'user_id' => $userId,
            'user_email' => $userEmail,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'timestamp' => now()->toDateTimeString(),
        ];
        
        // Adicionar dados de alteração se fornecidos
        if ($oldData !== null) {
            $logData['old_data'] = $oldData;
        }
        
        if ($newData !== null) {
            $logData['new_data'] = $newData;
        }
        
        // Adicionar descrição se fornecida
        if ($description !== null) {
            $logData['description'] = $description;
        }
        
        // Criar mensagem de log legível
        $message = self::buildLogMessage($action, $model, $modelId, $userEmail, $description);
        
        // Registrar no log do Laravel (usa o canal padrão configurado)
        // O canal 'daily' cria um ficheiro por dia, mas se não existir, usa o padrão
        try {
            Log::channel('daily')->info('AUDIT: ' . $message, $logData);
        } catch (\Exception $e) {
            // Se o canal 'daily' não existir, usa o canal padrão
            Log::info('AUDIT: ' . $message, $logData);
        }
        
        // Opcional: Salvar em tabela de auditoria (descomentar se criar a tabela)
        // self::saveToDatabase($logData);
    }
    
    /**
     * Constrói mensagem de log legível
     */
    private static function buildLogMessage(
        string $action,
        string $model,
        ?int $modelId,
        string $userEmail,
        ?string $description
    ): string {
        $actionText = self::getActionText($action);
        $message = "{$actionText} {$model}";
        
        if ($modelId !== null) {
            $message .= " (ID: {$modelId})";
        }
        
        $message .= " por {$userEmail}";
        
        if ($description !== null) {
            $message .= " - {$description}";
        }
        
        return $message;
    }
    
    /**
     * Retorna texto legível para a ação
     */
    private static function getActionText(string $action): string
    {
        $actions = [
            'create' => 'Criou',
            'update' => 'Atualizou',
            'delete' => 'Eliminou',
            'login' => 'Login realizado por',
            'logout' => 'Logout realizado por',
            'password_change' => 'Alterou palavra-passe de',
            'permission_change' => 'Alterou permissões de',
        ];
        
        return $actions[$action] ?? ucfirst($action);
    }
    
    /**
     * Salva log em tabela de banco de dados (opcional)
     * 
     * Para usar este método, crie uma migration para a tabela 'audit_logs':
     * 
     * Schema::create('audit_logs', function (Blueprint $table) {
     *     $table->id();
     *     $table->string('action');
     *     $table->string('model');
     *     $table->unsignedBigInteger('model_id')->nullable();
     *     $table->unsignedBigInteger('user_id')->nullable();
     *     $table->string('user_email');
     *     $table->string('ip_address')->nullable();
     *     $table->text('user_agent')->nullable();
     *     $table->text('url')->nullable();
     *     $table->string('method')->nullable();
     *     $table->json('old_data')->nullable();
     *     $table->json('new_data')->nullable();
     *     $table->text('description')->nullable();
     *     $table->timestamp('created_at');
     * });
     */
    private static function saveToDatabase(array $logData): void
    {
        try {
            DB::table('audit_logs')->insert([
                'action' => $logData['action'],
                'model' => $logData['model'],
                'model_id' => $logData['model_id'],
                'user_id' => $logData['user_id'],
                'user_email' => $logData['user_email'],
                'ip_address' => $logData['ip_address'],
                'user_agent' => $logData['user_agent'],
                'url' => $logData['url'],
                'method' => $logData['method'],
                'old_data' => $logData['old_data'] ? json_encode($logData['old_data']) : null,
                'new_data' => $logData['new_data'] ? json_encode($logData['new_data']) : null,
                'description' => $logData['description'] ?? null,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Se a tabela não existir, apenas loga o erro mas não quebra a aplicação
            Log::warning('Falha ao salvar log de auditoria no banco de dados: ' . $e->getMessage());
        }
    }
    
    /**
     * Métodos de conveniência para ações comuns
     */
    
    public static function logCreate(string $model, int $modelId, ?array $data = null, ?string $description = null): void
    {
        self::log('create', $model, $modelId, null, $data, $description);
    }
    
    public static function logUpdate(string $model, int $modelId, ?array $oldData = null, ?array $newData = null, ?string $description = null): void
    {
        self::log('update', $model, $modelId, $oldData, $newData, $description);
    }
    
    public static function logDelete(string $model, int $modelId, ?array $data = null, ?string $description = null): void
    {
        self::log('delete', $model, $modelId, $data, null, $description);
    }
    
    public static function logLogin(?string $email = null): void
    {
        $user = Auth::user();
        $email = $email ?? ($user ? $user->email : 'Desconhecido');
        self::log('login', 'User', $user?->id, null, null, "Login realizado: {$email}");
    }
    
    public static function logLogout(): void
    {
        $user = Auth::user();
        $email = $user ? $user->email : 'Desconhecido';
        self::log('logout', 'User', $user?->id, null, null, "Logout realizado: {$email}");
    }
    
    public static function logPasswordChange(int $userId, ?string $description = null): void
    {
        self::log('password_change', 'User', $userId, null, null, $description ?? 'Palavra-passe alterada');
    }
    
    public static function logPermissionChange(int $userId, ?array $oldPermissions = null, ?array $newPermissions = null): void
    {
        self::log('permission_change', 'User', $userId, $oldPermissions, $newPermissions, 'Permissões alteradas');
    }
}

