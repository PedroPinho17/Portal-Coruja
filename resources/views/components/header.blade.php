<header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm shadow-md transition-all">
    <div class="container mx-auto px-3 sm:px-4 lg:px-6 py-3 lg:py-4">
        <div class="flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 sm:gap-3 shrink-0">
                <img src="{{ asset('logo.webp') }}" alt="Corujinha Logo" class="h-10 w-10 sm:h-12 sm:w-12 object-contain">
                <span class="text-xl sm:text-2xl font-bold text-pink-600 whitespace-nowrap">Corujinha</span>
            </a>

            <!-- Menu Desktop -->
            <nav class="hidden lg:flex items-center gap-4 xl:gap-6 flex-wrap justify-end">
                <a href="{{ route('home') }}#home" class="text-sm xl:text-base text-gray-700 hover:text-pink-600 transition-colors font-medium whitespace-nowrap">
                    Início
                </a>
                <a href="{{ route('about') }}" class="text-sm xl:text-base text-gray-700 hover:text-pink-600 transition-colors font-medium whitespace-nowrap">
                    Sobre a Corujinha
                </a>
                <a href="{{ route('home') }}#services" class="text-sm xl:text-base text-gray-700 hover:text-pink-600 transition-colors font-medium whitespace-nowrap">
                    Serviços
                </a>
                <a href="{{ route('home') }}#training" class="text-sm xl:text-base text-gray-700 hover:text-pink-600 transition-colors font-medium whitespace-nowrap">
                    Formações
                </a>
                <a href="{{ route('galeria') }}" class="text-sm xl:text-base text-gray-700 hover:text-pink-600 transition-colors font-medium whitespace-nowrap">
                    Galeria
                </a>
                <a href="{{ route('equipa') }}" class="text-sm xl:text-base text-gray-700 hover:text-pink-600 transition-colors font-medium whitespace-nowrap">
                    Equipa
                </a>
                <a href="{{ route('home') }}#partner" class="text-sm xl:text-base text-gray-700 hover:text-pink-600 transition-colors font-medium whitespace-nowrap">
                    Parceria
                </a>
                <a href="{{ route('noticias') }}" class="text-sm xl:text-base text-gray-700 hover:text-pink-600 transition-colors font-medium whitespace-nowrap">
                    Notícias
                </a>
                <a href="{{ route('home') }}#contact" class="bg-pink-600 text-white px-4 xl:px-6 py-1.5 xl:py-2 rounded-full hover:bg-pink-700 transition-colors font-medium text-sm xl:text-base whitespace-nowrap ml-2">
                    Contacto
                </a>
            </nav>

            <!-- Botão Menu Mobile -->
            <button
                id="mobile-menu-toggle"
                class="lg:hidden text-gray-700 p-2 hover:text-pink-600 transition-colors"
                aria-label="Abrir menu"
            >
                <svg id="menu-icon" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <svg id="close-icon" class="w-7 h-7 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Menu Mobile -->
        <nav id="mobile-menu" class="lg:hidden hidden mt-4 pb-4 flex flex-col gap-3 border-t border-gray-200 pt-4">
            <a href="{{ route('home') }}#home" class="text-gray-700 hover:text-pink-600 transition-colors font-medium text-left py-2">
                Início
            </a>
            <a href="{{ route('about') }}" class="text-gray-700 hover:text-pink-600 transition-colors font-medium text-left py-2">
                Sobre a Corujinha
            </a>
            <a href="{{ route('home') }}#services" class="text-gray-700 hover:text-pink-600 transition-colors font-medium text-left py-2">
                Serviços
            </a>
            <a href="{{ route('home') }}#training" class="text-gray-700 hover:text-pink-600 transition-colors font-medium text-left py-2">
                Formações
            </a>
            <a href="{{ route('galeria') }}" class="text-gray-700 hover:text-pink-600 transition-colors font-medium text-left py-2">
                Galeria
            </a>
            <a href="{{ route('equipa') }}" class="text-gray-700 hover:text-pink-600 transition-colors font-medium text-left py-2">
                Equipa
            </a>
            <a href="{{ route('home') }}#partner" class="text-gray-700 hover:text-pink-600 transition-colors font-medium text-left py-2">
                Parceria
            </a>
            <a href="{{ route('noticias') }}" class="text-gray-700 hover:text-pink-600 transition-colors font-medium text-left py-2">
                Notícias
            </a>
            <a href="{{ route('home') }}#contact" class="bg-pink-600 text-white px-6 py-3 rounded-full hover:bg-pink-700 transition-colors font-medium text-center mt-2">
                Contacto
            </a>
        </nav>
    </div>
</header>

<script>
    document.getElementById('mobile-menu-toggle')?.addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');
        const closeIcon = document.getElementById('close-icon');
        
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            menuIcon.classList.add('hidden');
            closeIcon.classList.remove('hidden');
        } else {
            menu.classList.add('hidden');
            menuIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
        }
    });
</script>

