
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 bg-blue-100 px-4 py-2 rounded-full mb-4">
                <svg class="text-blue-600 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span class="text-blue-600 font-semibold">Protocolos Educativos</span>
            </div>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Associações de Pais <span class="text-pink-600">Parceiras</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Estamos em protocolo com as seguintes associações de pais para promover oportunidades educativas
            </p>
        </div>

        @php
            $count = $protocols->count();
            // Determinar número de colunas baseado na quantidade
            if ($count <= 1) {
                $cols = 'grid-cols-1';
            } elseif ($count == 2) {
                $cols = 'md:grid-cols-2';
            } elseif ($count == 3) {
                $cols = 'md:grid-cols-2 lg:grid-cols-3';
            } elseif ($count == 4) {
                $cols = 'md:grid-cols-2 lg:grid-cols-4';
            } else {
                $cols = 'md:grid-cols-2 lg:grid-cols-5';
            }
        @endphp
        <div class="grid grid-cols-1 {{ $cols }} gap-6 max-w-7xl mx-auto">
            @foreach($protocols as $index => $school)
                @php
                    $url = $school->link ?? null;
                @endphp
                @if($url)
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                        class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-50 to-pink-50 p-8 hover:shadow-2xl transition-all transform hover:scale-105 hover:from-blue-100 hover:to-pink-100 block cursor-pointer"
                        style="animation-delay: {{ $index * 0.1 }}s;"
                    >
                @else
                    <div
                        class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-50 to-pink-50 p-8 hover:shadow-2xl transition-all transform hover:scale-105 hover:from-blue-100 hover:to-pink-100"
                        style="animation-delay: {{ $index * 0.1 }}s;"
                    >
                @endif
                    <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-pink-200 to-transparent opacity-50 rounded-bl-full"></div>
                    <div class="absolute bottom-0 left-0 w-16 h-16 bg-gradient-to-tr from-blue-200 to-transparent opacity-50 rounded-tr-full"></div>

                    <div class="relative z-10 flex flex-col items-center text-center h-full justify-between">
                        <div class="mb-4 p-3 bg-white rounded-full shadow-md group-hover:shadow-lg transition-shadow">
                            <svg class="text-pink-600 w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>

                        <h3 class="text-lg font-bold text-gray-900 leading-tight">
                            {{ $school->school_name }}
                        </h3>

                        <div class="mt-4 pt-4 border-t border-gray-200 w-full">
                            <span class="inline-block px-3 py-1 bg-pink-100 text-pink-700 rounded-full text-xs font-semibold">
                                Protocolo Ativo
                            </span>
                        </div>
                    </div>
                @if($url)
                    </a>
                @else
                    </div>
                @endif
            @endforeach
        </div>

        <div class="mt-16 text-center">
            <p class="text-gray-600 text-lg max-w-3xl mx-auto">
                Através destes protocolos, oferecemos oportunidades de formação, apoio educativo e inserção profissional aos alunos e comunidades educativas destas instituições.
            </p>
        </div>
    </div>
</section>
