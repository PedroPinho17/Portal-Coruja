<footer class="bg-gradient-to-br from-gray-900 to-gray-800 text-white">
    <div class="container mx-auto px-4 py-12">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('logo.webp') }}" alt="Corujinha Logo" class="h-12 w-12 object-contain">
                    <span class="text-2xl font-bold">Corujinha</span>
                </div>
                <p class="text-gray-400 leading-relaxed">
                    Centro de explicações e apoio educativo dedicado ao desenvolvimento das crianças.
                </p>
            </div>

            <div>
                <h4 class="text-lg font-bold mb-4">Links Rápidos</h4>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('home') }}#home" class="text-gray-400 hover:text-pink-400 transition-colors">
                            Início
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}#about" class="text-gray-400 hover:text-pink-400 transition-colors">
                            Sobre Nós
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}#history" class="text-gray-400 hover:text-pink-400 transition-colors">
                            História
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}#services" class="text-gray-400 hover:text-pink-400 transition-colors">
                            Serviços
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}#training" class="text-gray-400 hover:text-pink-400 transition-colors">
                            Formações
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="text-lg font-bold mb-4">Serviços</h4>
                <ul class="space-y-2 text-gray-400">
                    <li>Explicações até ao 12º ano e preparação para exames</li>
                    <li>Atividades de Férias</li>
                    <li>Transporte Escolar</li>
                    <li>Formações Profissionais</li>
                    <li>Cursos Certificados</li>
                </ul>
            </div>

            <div>
                <h4 class="text-lg font-bold mb-4">Contactos</h4>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <svg class="text-pink-400 shrink-0 mt-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <a href="tel:+351916280509" class="text-gray-400 hover:text-pink-400 transition-colors">
                            +351 916 280 509
                        </a>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="text-pink-400 shrink-0 mt-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div class="text-gray-400">
                            <p>R. Namorados 566</p>
                            <p>4505-444 Lobão</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="text-pink-400 shrink-0 mt-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div class="text-gray-400">
                            <p>Av. Dr. Francisco Sá Carneiro 1192 Loja H</p>
                            <p>4520-617 São João de Ver</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-700 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-400 text-center md:text-left">
                    {{ date('Y') }} Corujinha. Todos os direitos reservados.
                </p>
                <p class="text-gray-400 flex items-center gap-2">
                    Feito com <svg class="text-pink-400 w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg> para as nossas crianças
                </p>
            </div>
        </div>
    </div>
</footer>

