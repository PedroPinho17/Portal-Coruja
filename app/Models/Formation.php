<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    protected $table = 'formations';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'ordem',
        'name',
        'description',
        'duration',
        'location',
        'id_entity',
        'active'
    ];

    //Relacionamento
    public function entity()
    {
        return $this->belongsTo(Entity::class, 'id_entity', 'id');
    }
}
