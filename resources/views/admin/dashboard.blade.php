@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

<!-- Cards de Estatísticas -->
<div class="row mb-4">
<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card h-100 dashboard-card-hover" style="transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold text-secondary">Total Utilizadores</p>
                <h5 class="font-weight-bolder mb-0 text-dark">
                  {{ number_format($users?->count() ?? 0, 0, ',', '.') }}
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
  <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card h-100 dashboard-card-hover" style="transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold text-secondary">Total Equipes</p>
                <h5 class="font-weight-bolder mb-0 text-dark">
                  {{ number_format($equipes?->count() ?? 0, 0, ',', '.') }}
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                <i class="ni ni-collection text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
  <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
    <div class="card h-100">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-8">
            <div class="numbers">
              <p class="text-sm mb-0 text-capitalize font-weight-bold text-secondary">Total Entidades</p>
              <h5 class="font-weight-bolder mb-0 text-dark">
                {{ number_format($entidades?->count() ?? 0, 0, ',', '.') }}
              </h5>
            </div>
          </div>
          <div class="col-4 text-end">
            <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
              <i class="ni ni-tag text-lg opacity-10" aria-hidden="true"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-sm-6">
    <div class="card h-100">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-8">
            <div class="numbers">
              <p class="text-sm mb-0 text-capitalize font-weight-bold text-secondary">Total Formações</p>
              <h5 class="font-weight-bolder mb-0 text-dark">
                {{ number_format($formacoes?->count() ?? 0, 0, ',', '.') }}
              </h5>
            </div>
          </div>
          <div class="col-4 text-end">
            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
              <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  @php
    $userPermissao = auth()->user()->id_permissao ?? null;
    $isAdmin = $userPermissao == 1; // ID 1 = Administrador (acesso total)
    $showActions = auth()->check() && $isAdmin;
  @endphp
  
  @if($showActions)
    {{-- Tabela de utilizadores apenas para administradores (permissão == 1) --}}
    <x-admin.table-card title="Utilizadores" :count="$users?->count()" data-show-actions="{{ $showActions ? 'true' : 'false' }}">
      <x-slot:actions>
        @if($showActions)
          <x-admin.live-search-bar :new-route="route('admin.users.create')" />
        @endif
      </x-slot:actions>
      <x-slot:head>
        <tr>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width:6rem;">ID</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Author</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Permissão</th>
          <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Creator</th>
          <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Criado em</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Ações</th>
        </tr>
      </x-slot:head>
      @forelse ($users ?? [] as $user)
      @include('admin.rows.user-row', ['row' => $user, 'showActions' => $showActions])
      @empty
      <tr>
        <td colspan="{{ $showActions ? '6' : '5' }}" class="text-center text-secondary text-xs py-3">Sem utilizadores.</td>
      </tr>
      @endforelse
    </x-admin.table-card>
  @else
    {{-- Imagem de fundo apenas para utilizadores (permissão == 2) --}}
    <div class="col-12">
      <div class="card" style="min-height: 600px; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: url('{{ asset('logo1.webp') }}'); background-size: 40%; background-position: center; background-repeat: no-repeat; z-index: 0; padding-top: 60px; padding-bottom: 60px;"></div>
        <div style="position: relative; z-index: 1; min-height: 600px;"></div>
      </div>
    </div>
  @endif
</div>

@endsection

@push('styles')
<link href="{{ asset('admin-css/hover-effects.css') }}" rel="stylesheet">
@endpush