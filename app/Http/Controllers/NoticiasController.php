<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class NoticiasController extends Controller
{
    public function index()
    {
        $posts = $this->getPostsForFeature();

        return view('noticias', compact('posts'));
    }

    private function getPostsForFeature()
    {
        return Post::whereNotNull('published_at')
            ->where('feature', 1)
            ->orderBy('id')
            ->get();
    }
}
