@extends('admin.layouts.app')

@section('title', 'Formações')

@push('styles')
    <link rel="stylesheet" href="{{ asset('admin-css/idiomas.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin-css/toggle-custom.css') }}" />
@endpush

@section('content')
    <div id="posts-list">
        <div class="row">
            <x-admin.table-card title="Formações" :count="$formations->count()" data-row-reorder="true"
                data-row-reorder-url="{{ route('admin.formations.reorder') }}">
                <x-slot:actions>
                    <x-admin.live-search-bar :new-route="route('admin.formations.create')" />
                </x-slot:actions>
                <x-slot:head>
                    <tr>
                        <!-- manter a coluna técnica para RowReorder (texto escondido) -->
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center reorder-handle-col"
                            style="width:3rem;">
                            <span class="visually-hidden">Ordem</span>
                        </th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center"
                            style="width:6rem;">ID</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Name
                        </th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                            Descrição
                        </th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Duração
                        </th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                            Localização
                        </th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Entidade
                        </th>
                        <th
                            class="text-uppercase text-secondary text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                            Ativo</th>
                        <!-- coluna de ações (corrige contagem) -->
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center"
                            style="width:8rem;">Ações</th>
                    </tr>
                </x-slot:head>
                @forelse($formations as $formation)
                    <tr data-edit-url="{{ route('admin.formations.edit', $formation) }}" data-id="{{ $formation->id }}"
                        style="cursor: pointer;">
                        <!-- mostrar apenas ícone (não mostrar o número da ordem) mas manter data-ordem para RowReorder -->
                        <td class="text-xs text-center reorder-handle" data-ordem="{{ $formation->ordem }}"
                            style="cursor:move;">
                            <i class="fas fa-bars"></i>
                            {{-- número de ordem escondido intencionalmente --}}
                        </td>
                        <td class="text-xs fw-bold text-center" data-order="{{ $formation->id }}"
                            data-id="{{ $formation->id }}">
                            #{{ $formation->id }}</td>
                        <td class="text-xs fw-bold">{{ $formation->name }}</td>
                        <td class="text-xs">{{ $formation->description }}</td>
                        <td class="text-xs">{{ $formation->duration }}</td>
                        <td class="text-xs">{{ $formation->location }}</td>
                        <td class="text-xs">{{ $formation->entity->name }}</td>
                        <td class="text-xs col-img">
                            <input type="checkbox" class="js-formation-ativo-toggle toggle-custom"
                                data-id="{{ $formation->id }}" data-toggle="toggle" data-onlabel="✔" data-offlabel="✖"
                                data-onstyle="success" data-offstyle="danger" {{ $formation->active ? 'checked' : '' }}>
                        </td>
                        <td class="text-xs">
                            <div class="d-flex justify-content-center align-items-center" style="gap:.75rem;">
                                <a href="{{ route('admin.formations.edit', $formation) }}" class="text-xs font-weight-bold"
                                    style="color:#5e72e4;" title="Editar">Editar</a>
                                <form action="{{ route('admin.formations.destroy', $formation) }}" method="POST"
                                    class="m-0 p-0 js-delete-confirm" data-confirm="Eliminar este post?"
                                    data-check-relations="true" data-entity="posts" data-id="{{ $formation->id }}">
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
                        <td colspan="9" class="text-center text-muted py-4">Nenhuma formação encontrada.</td>
                    </tr>
                @endforelse
            </x-admin.table-card>
        </div>
        <div class="row"></div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin-js/formation-toggle.js') }}"></script>
@endpush