<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\Team;
use App\Models\Post;
use App\Models\SchoolProtocol;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function index()
    {
        // Buscar formações do banco de dados
        $formacoes = Formation::with('entity')
        ->where('active', 1)
        ->orderBy('id', 'asc')
        ->limit(2)
        ->get();


        $previewImages = [
            [
                'src' => '/gallery/caldas/classroom-2.webp',
                'alt' => 'Sala de aula - Centro Caldas',
                'label' => 'Sala de Aula (Lobão)'
            ],
            [
                'src' => '/gallery/activities/classroom-lesson.png',
                'alt' => 'Atividades - Centro Caldas',
                'label' => 'Atividades (Lobão)'
            ],
            [
                'src' => '/gallery/sao-joao/placeholder-1.webp',
                'alt' => 'Aula no Centro São João de Ver',
                'label' => 'Aula em São João de Ver'
            ],
        ];

        // Buscar top 3 membros da equipa do banco de dados
        $teams = Team::orderBy('id', 'desc')
        ->limit(3)
        ->get();

         // Buscar últimos 3 posts ativos do banco de dados
        $posts = Post::where('feature', true)
        ->orderBy('published_at', 'desc') // Depois por data
        ->limit(3)
        ->get();

        // Buscar protocolos escolares ativos
        $protocols = SchoolProtocol::where('ativo', true)
            ->orderBy('ordem', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('home', compact('formacoes', 'previewImages', 'teams', 'posts', 'protocols'));
    }

}
