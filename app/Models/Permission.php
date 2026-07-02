<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    //
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;


    protected $fillable = [
        'name', 
        'description',
    ];


    //Relacionamento
    public function users()
    {
        return $this->hasMany(User::class, 'id_permissao', 'id');
    }
}
