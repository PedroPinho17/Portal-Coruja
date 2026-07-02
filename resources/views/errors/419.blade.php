@extends('layout.app')

@section('title', 'Token Expirado')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 text-center">
            <div class="error-page">
                <div class="error-code">419</div>
                <h1 class="error-title">Token de Segurança Expirado</h1>
                <p class="error-message">
                    O token de segurança expirou. Por favor, recarregue a página e tente novamente.
                </p>
                <div class="error-actions mt-4">
                    <button onclick="window.location.reload()" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Recarregar Página
                    </button>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-house me-2"></i>Voltar à Página Inicial
                    </a>
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
        color: #0dcaf0;
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

