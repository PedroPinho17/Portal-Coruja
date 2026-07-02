<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-2 bg-pink-100 px-4 py-2 rounded-full mb-4">
                <svg class="text-pink-600 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="text-pink-600 font-semibold">Conhece a Nossa Equipa</span>
            </div>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Os Rostos por Trás da <span class="text-pink-600">Corujinha</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Profissionais dedicados ao desenvolvimento e bem-estar das crianças
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto mb-12">
            @foreach($teams as $team)
                <div class="group bg-gradient-to-br from-pink-50 to-orange-50 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all transform hover:scale-105">
                    <div class="aspect-[3/4] overflow-hidden bg-gray-200">
                        <img
                            src="{{ asset('img/teams/' . $team->image) }}"
                            alt="{{ $team->name }}"
                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                        />
                        <div class="w-full h-full hidden items-center justify-center bg-gradient-to-br from-pink-200 to-orange-200">
                            <div class="text-center">
                                <div class="text-7xl mb-4">👤</div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-1">{{  $team->name }}</h3>
                        <p class="text-pink-600 font-semibold text-sm mb-3">{{ $team->description }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center">
            <a
                href="{{ route('equipa') }}"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-pink-600 to-orange-600 text-white px-8 py-4 rounded-xl hover:shadow-xl transition-all transform hover:scale-105 font-semibold text-lg"
            >
                Ver Equipa Completa
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </a>
        </div>
    </div>
</section>

