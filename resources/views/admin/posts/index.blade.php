@extends('admin.layouts.app')

@section('title', 'Posts')

@push('styles')
    <link rel="stylesheet" href="{{ asset('admin-css/idiomas.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin-css/toggle-custom.css') }}" />
@endpush

@section('content')
    <div id="posts-list">
        <div class="row">
            <x-admin.table-card title="Posts" :count="$posts->count()" data-row-reorder="true"
                data-row-reorder-url="{{ route('admin.posts.reorder') }}">
                <x-slot:actions>
                    <x-admin.live-search-bar :new-route="route('admin.posts.create')" />
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
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Título
                        </th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Conteúdo
                        </th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Link
                        </th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Numero
                            telefone</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Email
                        </th>
                        <th
                            class="text-uppercase text-secondary text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                            Ativo</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Data
                            publicação</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Imagem
                        </th>
                        <!-- coluna de ações (corrige contagem) -->
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center"
                            style="width:8rem;">Ações</th>
                    </tr>
                </x-slot:head>
                @forelse($posts as $post)
                    <tr data-edit-url="{{ route('admin.posts.edit', $post) }}" data-id="{{ $post->id }}"
                        style="cursor: pointer;">
                        <!-- mostrar apenas ícone (não mostrar o número da ordem) mas manter data-ordem para RowReorder -->
                        <td class="text-xs text-center reorder-handle" data-ordem="{{ $post->ordem }}" style="cursor:move;">
                            <i class="fas fa-bars"></i>
                            {{-- número de ordem escondido intencionalmente --}}
                        </td>
                        <td class="text-xs fw-bold text-center" data-order="{{ $post->id }}" data-id="{{ $post->id }}">
                            #{{ $post->id }}</td>
                        <td class="text-xs fw-bold">{{ $post->title }}</td>
                        <td class="text-xs">{{ $post->content }}</td>
                        <td class="text-xs">{{ $post->link }}</td>
                        <td class="text-xs">{{ $post->phone }}</td>
                        <td class="text-xs">{{ $post->email }}</td>
                        <td class="text-xs col-img">
                            <input type="checkbox" class="js-post-ativo-toggle toggle-custom"
                                data-id="{{ $post->id }}" data-toggle="toggle" data-onlabel="✔" data-offlabel="✖"
                                data-onstyle="success" data-offstyle="danger" {{ $post->feature ? 'checked' : '' }}>
                        </td>
                        <td class="text-xs">{{ $post->published_at }}</td>
                        <td class="text-xs col-img">
                            @if(!empty($post->image))
                                <img src="{{ asset('img/posts/' . ltrim($post->image, '/')) }}" alt="{{ $post->description }}"
                                    style="height:150px; width:auto;">
                            @endif
                        </td>
                        <td class="text-xs">
                            <div class="d-flex justify-content-center align-items-center" style="gap:.75rem;">
                                <a href="{{ route('admin.posts.edit', $post) }}" class="text-xs font-weight-bold"
                                    style="color:#5e72e4;" title="Editar">Editar</a>
                                <form action="{{ route('admin.posts.destroy', $post) }}" method="POST"
                                    class="m-0 p-0 js-delete-confirm" data-confirm="Eliminar este post?"
                                    data-check-relations="true" data-entity="posts" data-id="{{ $post->id }}">
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
                        <td colspan="11" class="text-center text-muted py-4">Nenhum post encontrado.</td>
                    </tr>
                @endforelse
            </x-admin.table-card>
        </div>
        <div class="row"></div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin-js/posts-toggle.js') }}"></script>
@endpush