<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    //
    protected $table = 'entities';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'ordem',
        'name',
        'description',
        'location',
        'website'
    ];


    //Relacionamento
    public function formations()
    {
        return $this->hasMany(Formation::class, 'id_entity', 'id');
    }
}
