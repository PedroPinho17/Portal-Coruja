<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolProtocol extends Model
{
    protected $table = 'school_protocols';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'school_name',
        'link',
        'ordem',
        'ativo'
    ];

}
