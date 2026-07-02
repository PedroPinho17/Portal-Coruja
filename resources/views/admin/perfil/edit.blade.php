@extends('admin.layouts.app')

@section('title','Perfil')

@push('styles')
    <link rel="stylesheet" href="{{ asset('admin-css/idiomas.css') }}" />
@endpush

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card shadow-sm p-3">
      <h6 class="mb-3">Editar Perfil</h6>
      <form action="{{ route('admin.perfil.update') }}" method="post">
        @csrf
        @method('PUT')
        <div class="row">
          <div class="col-12">
            <div class="alert alert-light text-secondary py-2" role="alert" style="border:1px solid #e9ecef;">
              Para alterar o email ou a password, introduza a sua password atual.
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label for="nome" class="form-label">Nome</label>
              <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome', $user->nome) }}" required maxlength="150">
              @error('nome')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required maxlength="255" readonly disabled>
              @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="col-12">
            <div class="mb-3">
              <label for="current_password" class="form-label">Password Atual</label>
              <input type="password" name="current_password" id="current_password" class="form-control" autocomplete="current-password" placeholder="Obrigatória para alterar email ou password">
              @error('current_password')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="col-12">
            <div class="mb-3">
              <label for="password" class="form-label">Nova Password</label>
              <input type="password" name="password" id="password" class="form-control" autocomplete="new-password" placeholder="Deixe em branco para manter a password atual">
              <div class="form-text" style="font-size:.7rem;">Preencha apenas se pretende alterar a sua password.</div>
              @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="col-12">
            <div class="mb-3">
              <label for="password_confirmation" class="form-label">Confirmar Password</label>
              <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password" placeholder="Obrigatória se definir nova password">
              <div class="form-text" style="font-size:.7rem;">Preencha apenas se pretende alterar a sua password.</div>
            </div>
          </div>
        </div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-save">Guardar</button>
          <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Seção WebAuthn / Passkeys --}}
<div class="row mt-4">
  <div class="col-12">
    <div class="card shadow-sm p-3">
      <h6 class="mb-3">
        <i class="bi bi-shield-lock me-2"></i>Chaves de Segurança (WebAuthn/Passkeys)
      </h6>
      <p class="text-muted small mb-3">
        Gerir as tuas chaves de segurança para autenticação sem palavra-passe. Podes usar a impressão digital, o reconhecimento facial ou chaves de segurança físicas.
      </p>

      @if($user->webauthnKeys && $user->webauthnKeys->count() > 0)
        <div class="table-responsive mb-3">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Nome</th>
                <th>Registada em</th>
                <th class="text-end">Ações</th>
              </tr>
            </thead>
            <tbody>
              @foreach($user->webauthnKeys as $key)
                <tr>
                  <td>
                    <i class="bi bi-key me-2"></i>{{ $key->name }}
                  </td>
                  <td>
                    <small class="text-muted">{{ $key->created_at->format('d/m/Y H:i') }}</small>
                  </td>
                  <td class="text-end">
                    <form method="POST" action="{{ route('webauthn.destroy', $key) }}" class="d-inline js-delete-confirm" data-confirm="Tem certeza que deseja remover esta chave de segurança?">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash me-1"></i>Remover
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="alert alert-light-info mb-3" role="alert" style="background-color: #e7f3ff; border: 1px solid #0d6efd; color: #084298;">
          <i class="bi bi-info-circle me-2"></i>
          Nenhuma chave de segurança registada. Clique em "Registar Nova Chave" para adicionar uma.
        </div>
      @endif

      <a href="{{ route('webauthn.create') }}" class="btn webauthn-btn-hover" style="background:rgb(8, 42, 234); border: none; color: white; box-shadow: 0 4px 12px rgba(94, 114, 228, 0.3); border-radius: 0.75rem; font-weight: 600; font-size: 0.875rem; padding: 0.875rem 2rem; transition: all 0.2s ease;">
        <i class="bi bi-plus-circle me-2"></i>Registar Nova Chave
      </a>
    </div>
  </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('admin-css/hover-effects.css') }}" rel="stylesheet">
@endpush
