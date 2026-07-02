@extends('layout.app')

@section('title', 'Equipa - Corujinha')

@section('content')
@php
    // Dividir equipa em linhas: primeira linha até 3, resto em pares
    function chunkTeamForLayout($teams) {
        $teamArray = $teams->toArray();
        if (count($teamArray) <= 3) return [$teamArray];
        
        $rows = [];
        $rows[] = array_slice($teamArray, 0, 3);
        
        for ($i = 3; $i < count($teamArray); $i += 2) {
            $rows[] = array_slice($teamArray, $i, 2);
        }
        
        return $rows;
    }

    $rows = chunkTeamForLayout($teams);
@endphp

<div class="pt-20">
    <section class="py-20 bg-gradient-to-br from-pink-50 to-orange-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 bg-pink-100 px-4 py-2 rounded-full mb-4">
                    <svg class="text-pink-600 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span class="text-pink-600 font-semibold">A Nossa Equipa</span>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
                    Conheça os Profissionais da <span class="text-pink-600">Corujinha</span>
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Uma equipa dedicada, qualificada e apaixonada pelo desenvolvimento integral das crianças
                </p>
            </div>

            @if(count($teams) === 0)
                <div class="text-center py-12">
                    <p class="text-gray-600 text-lg">Nenhum membro da equipa disponível no momento.</p>
                </div>
            @else
                <div class="max-w-7xl mx-auto space-y-8">
                    @foreach($rows as $rowIndex => $row)
                        @if($rowIndex === 0)
                            {{-- Primeira linha: grid com 3 colunas --}}
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                @foreach($row as $member)
                                    <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all transform hover:scale-105 w-full max-w-sm mx-auto" style="min-width: 220px;">
                                        <div class="aspect-[3/4] overflow-hidden bg-gray-200">
                                            <img
                                                src="{{ asset('img/teams/' . $member['image']) }}"
                                                alt="{{ $member['name'] }}"
                                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                                                onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gradient-to-br from-pink-200 to-orange-200\'><div class=\'text-center\'><div class=\'text-7xl mb-4\'>👤</div></div></div>'"
                                            />
                                        </div>
                                        <div class="p-6">
                                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $member['name'] }}</h3>
                                            @if($member['description'])
                                                <p class="text-gray-600 leading-relaxed">{{ $member['description'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- Linhas seguintes: centralizadas (flex) para que 1 ou 2 itens fiquem no centro --}}
                            <div class="flex justify-center gap-8 flex-wrap">
                                @foreach($row as $member)
                                    <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all transform hover:scale-105 w-full max-w-sm" style="min-width: 220px;">
                                        <div class="aspect-[3/4] overflow-hidden bg-gray-200">
                                            <img
                                                src="{{ asset('img/teams/' . $member['image']) }}"
                                                alt="{{ $member['name'] }}"
                                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                                                onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gradient-to-br from-pink-200 to-orange-200\'><div class=\'text-center\'><div class=\'text-7xl mb-4\'>👤</div></div></div>'"
                                            />
                                        </div>
                                        <div class="p-6">
                                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $member['name'] }}</h3>
                                            @if($member['description'] ?? false)
                                                <p class="text-gray-600 leading-relaxed">{{ $member['description'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
