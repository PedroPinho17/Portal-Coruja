@extends('admin.layouts.app')

@section('title', 'Novo Utilizador')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Novo Utilizador</h6>
      </div>
      <div class="card-body pt-3">
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" novalidate>
          @csrf
          <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome') }}" required autofocus>
            @error('nome')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label for="id" class="form-label">Permissão</label>
            <select class="form-select @error('id') is-invalid @enderror" id="id" name="id" required>
              @foreach($permissoes ?? [] as $permissao)
                <option value="{{ $permissao->id }}" {{ old('id', 3) == $permissao->id ? 'selected' : '' }}>
                  {{ $permissao->description }}
                </option>
              @endforeach
            </select>
            @error('id')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
            @error('password')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirmar Password</label>
            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required>
            @error('password_confirmation')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-4">
            <label for="mudanca_password" class="form-label">Forçar Mudança de Password no Primeiro Login</label>
            <select class="form-select @error('mudanca_password') is-invalid @enderror" id="mudanca_password" name="mudanca_password" required>
              <option value="1" {{ old('mudanca_password', 1) == 1 ? 'selected' : '' }}>Sim</option>
              <option value="0" {{ old('mudanca_password', 1) == 0 ? 'selected' : '' }}>Não</option>
            </select>
            <div class="form-text">
              <i class="bi bi-info-circle me-1"></i>
              Se <strong>Sim</strong>, o utilizador será obrigado a alterar a password no primeiro login.
            </div>
            @error('mudanca_password')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-main">Registar</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-cancel">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection