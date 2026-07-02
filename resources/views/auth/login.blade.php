@extends('layout.app')

@section('title', 'Login - Corujinha')

@push('styles')
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
<style>
    body {
        background: linear-gradient(to bottom right, #fdf2f8, #fff7ed, #fdf2f8);
        min-height: 100vh;
    }
    
    /* Esconder header e footer na página de login */
    /* header,
    footer {
        display: none !important;
    }
     */
    main {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    
    /* Melhorar qualidade da imagem do logo */
    #login-form-container img {
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
        image-rendering: auto;
        -ms-interpolation-mode: bicubic;
        transform: translateZ(0);
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        will-change: transform;
    }
</style>
@endpush



@section('content')
    <!-- Login Form -->
    <div id="login-form-container" class="flex items-center justify-center min-h-screen w-full">
        <div class="w-full max-w-md px-4">
            <div class="bg-white rounded-2xl shadow-xl border border-pink-200 overflow-hidden">
                <div class="space-y-6 text-center p-8">
                    <div class="flex justify-center">
                        <div class="relative w-24 h-24">
                            <img 
                                src="{{ asset('logo.webp') }}"
                                alt="Corujinha Logo" 
                                class="object-contain w-full h-full"
                                loading="eager"
                                decoding="async"
                            />
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-pink-600 mb-2">Back Office Corujinha</h1>
                        <p class="text-base text-gray-600">
                            Entre com suas credenciais para acessar o painel administrativo
                        </p>
                    </div>
                </div>

                <div class="px-8 pb-8">
                    <form id="login-form" method="POST" action="{{ route('login.post') }}" class="space-y-5">
                        @csrf
                        
                        @if($errors->any())
                            <div class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-md p-3">
                                @foreach($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <div class="space-y-2">
                            <label for="email" class="block text-gray-700 font-semibold text-left">Email</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                placeholder="seu@email.com"
                                required
                                class="w-full px-4 py-3 border border-pink-200 rounded-lg focus:border-pink-400 focus:ring-2 focus:ring-pink-400 outline-none transition-colors"
                            />
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="block text-gray-700 font-semibold text-left">Password</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="••••••••"
                                required
                                class="w-full px-4 py-3 border border-pink-200 rounded-lg focus:border-pink-400 focus:ring-2 focus:ring-pink-400 outline-none transition-colors"
                            />
                        </div>

                        <div class="flex items-center">
                            <input
                                id="remember"
                                name="remember"
                                type="checkbox"
                                class="w-4 h-4 text-pink-600 border-pink-300 rounded focus:ring-pink-500"
                            />
                            <label for="remember" class="ml-2 text-sm text-gray-700">Lembrar-me</label>
                        </div>

                        <button
                            type="submit"
                            class="w-full bg-gradient-to-r from-pink-500 to-orange-500 hover:from-pink-600 hover:to-orange-600 text-white font-semibold py-3 rounded-lg transition-all transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed shadow-lg"
                            id="submit-btn"
                        >
                            Entrar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('js/password-toggle.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('login-form');
    const submitBtn = document.getElementById('submit-btn');
    
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Entrando...';
    });
});
</script>
@endpush
