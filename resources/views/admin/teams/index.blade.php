@extends('admin.layouts.app')

@section('title', 'Equipas')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin-css/idiomas.css') }}" />
<link rel="stylesheet" href="{{ asset('admin-css/toggle-idioma-custom.css') }}" />
@endpush

@section('content')
<div id="teams-list">
    <div class="row">
        <x-admin.table-card title="Teams" :count="$teams->count()" 
            data-row-reorder="true" 
            data-row-reorder-url="{{ route('admin.teams.reorder') }}">
            <x-slot:actions>
                <x-admin.live-search-bar :new-route="route('admin.teams.create')" />
            </x-slot:actions>
            <x-slot:head>
                <tr>
                    <!-- manter a coluna técnica para RowReorder (texto escondido) -->
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center reorder-handle-col" style="width:3rem;">
                        <span class="visually-hidden">Ordem</span>
                    </th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width:6rem;">ID</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Nome</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Descrição</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Imagem</th>
                    <!-- coluna de ações (corrige contagem) -->
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width:8rem;">Ações</th>
                </tr>
            </x-slot:head>
            @forelse($teams as $team)
            <tr data-edit-url="{{ route('admin.teams.edit', $team) }}" data-id="{{ $team->id }}" style="cursor: pointer;">
                <!-- mostrar apenas ícone (não mostrar o número da ordem) mas manter data-ordem para RowReorder -->
                <td class="text-xs text-center reorder-handle" data-ordem="{{ $team->ordem }}" style="cursor:move;">
                    <i class="fas fa-bars"></i>
                    {{-- número de ordem escondido intencionalmente --}}
                </td>
                <td class="text-xs fw-bold text-center" data-order="{{ $team->id }}" data-id="{{ $team->id }}">#{{ $team->id }}</td>
                <td class="text-xs fw-bold">{{ $team->name }}</td>
                <td class="text-xs">{{ $team->description }}</td>
                <td class="text-xs col-img">
                        @if(!empty($team->image))
                            <img src="{{ asset('img/teams/'.ltrim($team->image,'/')) }}" alt="{{ $team->description }}" style="height:150px; width:auto;">
                        @endif
                    </td>
                <td class="text-xs">
                    <div class="d-flex justify-content-center align-items-center" style="gap:.75rem;">
                        <a href="{{ route('admin.teams.edit', $team) }}" class="text-xs font-weight-bold"
                            style="color:#5e72e4;" title="Editar">Editar</a>
                        <form action="{{ route('admin.teams.destroy', $team) }}" method="POST"
                            class="m-0 p-0 js-delete-confirm" data-confirm="Eliminar este team?" data-check-relations="true" data-entity="teams" data-id="{{ $team->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 m-0"
                                style="text-decoration:none;" title="Eliminar">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">Nenhum team encontrado.</td>
            </tr>
            @endforelse
        </x-admin.table-card>
    </div>
    <div class="row"></div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin-js/idiomas-toggle.js') }}"></script>
@endpush