<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    protected $table = 'posts';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;


    protected $fillable = [
        'ordem',
        'title',
        'content',
        'link',
        'phone',
        'email',
        'feature',
        'image',
        'published_at'
    ];
}
