@extends('layout.app')

@section('title', 'Erro do Servidor')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 text-center">
            <div class="error-page">
                <div class="error-code">500</div>
                <h1 class="error-title">Erro do Servidor</h1>
                <p class="error-message">
                    Ocorreu um erro interno no servidor. Por favor, tente novamente mais tarde.
                </p>
                <div class="error-actions mt-4">
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="bi bi-house me-2"></i>Voltar à Página Inicial
                    </a>
                    <button onclick="window.location.reload()" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-arrow-clockwise me-2"></i>Recarregar Página
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .error-page {
        padding: 2rem;
    }
    .error-code {
        font-size: 8rem;
        font-weight: 700;
        color: #dc3545;
        line-height: 1;
        margin-bottom: 1rem;
    }
    .error-title {
        font-size: 2rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1rem;
    }
    .error-message {
        font-size: 1.1rem;
        color: #6c757d;
        margin-bottom: 2rem;
    }
    .min-vh-100 {
        min-height: 100vh;
    }
</style>
@endpush
@endsection

