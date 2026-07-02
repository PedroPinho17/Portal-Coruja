@extends('admin.layouts.app')

@section('title', 'Alterar Password')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-header bg-warning text-dark text-center py-3">
        <h5 class="mb-0 font-weight-bold">
          <i class="bi bi-shield-exclamation me-2"></i>
          Alteração Obrigatória de Password
        </h5>
      </div>
      <div class="card-body p-4">
        @if(session('warning'))
          <div class="alert alert-light-warning alert-dismissible fade show mb-4" role="alert" style="background-color: #fff3cd; border: 1px solid #ffc107; color: #856404;">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <div class="alert alert-light-info mb-4" role="alert" style="background-color: #e7f3ff; border: 1px solid #0d6efd; color: #084298;">
          <i class="bi bi-info-circle me-2"></i>
          <strong>Bem-vindo!</strong> Esta é a primeira vez que faz login. Por questões de segurança, 
          é obrigatório alterar a sua password antes de continuar.
        </div>

        <form action="{{ route('admin.password.force-change.update') }}" method="POST" id="force-password-form">
          @csrf
          @method('PUT')

          <div class="mb-3">
            <label for="password" class="form-label">
              Nova Password <span class="text-danger">*</span>
            </label>
            <input 
              type="password" 
              name="password" 
              id="password" 
              class="form-control @error('password') is-invalid @enderror" 
              autocomplete="new-password"
              required
              minlength="8"
              placeholder="Digite uma password segura"
            >
            @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">
              <i class="bi bi-shield-check me-1"></i>
              <strong>Requisitos da password:</strong>
              <ul class="mb-0 mt-2 small password-requirements" id="password-requirements">
                <li data-requirement="length">
                  <i class="bi bi-circle password-check-icon"></i>
                  <span>Mínimo 8 caracteres</span>
                </li>
                <li data-requirement="lowercase">
                  <i class="bi bi-circle password-check-icon"></i>
                  <span>Pelo menos uma letra minúscula (a-z)</span>
                </li>
                <li data-requirement="uppercase">
                  <i class="bi bi-circle password-check-icon"></i>
                  <span>Pelo menos uma letra maiúscula (A-Z)</span>
                </li>
                <li data-requirement="number">
                  <i class="bi bi-circle password-check-icon"></i>
                  <span>Pelo menos um número (0-9)</span>
                </li>
                <li data-requirement="special">
                  <i class="bi bi-circle password-check-icon"></i>
                  <span>Pelo menos um caractere especial (@$!%*#?&)</span>
                </li>
              </ul>
            </div>
          </div>

          <div class="mb-4">
            <label for="password_confirmation" class="form-label">
              Confirmar Nova Password <span class="text-danger">*</span>
            </label>
            <input 
              type="password" 
              name="password_confirmation" 
              id="password_confirmation" 
              class="form-control @error('password_confirmation') is-invalid @enderror" 
              autocomplete="new-password"
              required
              minlength="8"
              placeholder="Repita a nova password"
            >
            @error('password_confirmation')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-dashboard-main btn-lg">
              <i class="bi bi-check-circle me-2"></i>
              Alterar Password e Continuar
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="text-center mt-3">
      <small class="text-muted">
        <i class="bi bi-lock me-1"></i>
        Esta ação é obrigatória por questões de segurança
      </small>
    </div>
  </div>
</div>

@push('styles')
<link href="{{ asset('admin-css/password-validation.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
<script src="{{ asset('admin-js/password-validation.js') }}"></script>
@endpush
@endsection

