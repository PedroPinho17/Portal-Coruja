<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;

class EquipaController extends Controller
{
    //
    public function index()
    {
        // Obter os dados da equipa
        $teams = $this->getTeam();

        return view('equipa', compact('teams'));
    }

    private function getTeam()
    {
        return Team::orderBy('id')
            ->get();
    }
}
