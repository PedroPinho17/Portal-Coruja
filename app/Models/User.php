<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use LaravelWebauthn\WebauthnAuthenticatable;
use LaravelWebauthn\Models\WebauthnKey;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, WebauthnAuthenticatable;

    protected $table = 'utilizadores';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'nome',
        'id_permissao',
        'creator',
        'mudanca_password',
        'timestamp_criacao',
        'timestamp_ultima_alteracao'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'id_permissao' => 'int',
            'mudanca_password' => 'boolean',
            'timestamp_criacao' => 'datetime',
            'timestamp_ultima_alteracao' => 'datetime',
        ];
    }

    // Relacionamentos
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'id_permissao', 'id');
    }

    /**
     * Verifica se o utilizador é administrador
     * ID 1 = Administrador (acesso total)
     * 
     * @return bool
     */
    public function isAdministrador(): bool
    {
        return $this->id_permissao == 1;
    }

    /**
     * Verifica se o utilizador é Administrador (ID 1)
     * Mantido para compatibilidade
     * 
     * @return bool
     */
    public function isImperador(): bool
    {
        return $this->id_permissao == 1;
    }

}
