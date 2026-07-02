@extends('admin.layouts.app')

@section('title', 'Protocolos Escolares')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin-css/idiomas.css') }}" />
<link rel="stylesheet" href="{{ asset('admin-css/toggle-custom.css') }}" />
@endpush

@section('content')
<div id="protocols-list">
    <div class="row">
        <x-admin.table-card title="Protocolos Escolares" :count="$protocols->count()" 
            data-row-reorder="true" 
            data-row-reorder-url="{{ route('admin.protocols.reorder') }}">
            <x-slot:actions>
                <x-admin.live-search-bar :new-route="route('admin.protocols.create')" />
            </x-slot:actions>
            <x-slot:head>
                <tr>
                    <!-- manter a coluna técnica para RowReorder (texto escondido) -->
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center reorder-handle-col" style="width:3rem;">
                        <span class="visually-hidden">Ordem</span>
                    </th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width:6rem;">ID</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Nome da Escola</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Link</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width:6rem;">Estado</th>
                    <!-- coluna de ações (corrige contagem) -->
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width:8rem;">Ações</th>
                </tr>
            </x-slot:head>
            @forelse($protocols as $protocol)
            <tr data-edit-url="{{ route('admin.protocols.edit', $protocol) }}" data-id="{{ $protocol->id }}" style="cursor: pointer;">
                <!-- mostrar apenas ícone (não mostrar o número da ordem) mas manter data-ordem para RowReorder -->
                <td class="text-xs text-center reorder-handle" data-ordem="{{ $protocol->ordem }}" style="cursor:move;">
                    <i class="fas fa-bars"></i>
                </td>
                <td class="text-xs fw-bold text-center" data-order="{{ $protocol->id }}" data-id="{{ $protocol->id }}">#{{ $protocol->id }}</td>
                <td class="text-xs fw-bold">{{ $protocol->school_name }}</td>
                <td class="text-xs">
                    @if($protocol->link)
                        <a href="{{ $protocol->link }}" target="_blank" class="text-primary">
                            <i class="fas fa-external-link-alt"></i> Ver link
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td class="text-xs col-img">
                            <input type="checkbox" class="js-protocol-ativo-toggle toggle-custom"
                                data-id="{{ $protocol->id }}" data-toggle="toggle" data-onlabel="✔" data-offlabel="✖"
                                data-onstyle="success" data-offstyle="danger" {{ $protocol->ativo ? 'checked' : '' }}>
                        </td>
                <td class="text-xs">
                    <div class="d-flex justify-content-center align-items-center" style="gap:.75rem;">
                        <a href="{{ route('admin.protocols.edit', $protocol) }}" class="text-xs font-weight-bold"
                            style="color:#5e72e4;" title="Editar">Editar</a>
                        <form action="{{ route('admin.protocols.destroy', $protocol) }}" method="POST"
                            class="m-0 p-0 js-delete-confirm" data-confirm="Eliminar este protocolo?" data-check-relations="false" data-entity="protocols" data-id="{{ $protocol->id }}">
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
                <td colspan="6" class="text-center text-muted py-4">Nenhum protocolo encontrado.</td>
            </tr>
            @endforelse
        </x-admin.table-card>
    </div>
    <div class="row"></div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin-js/protocols-toggle.js') }}"></script>
@endpush
