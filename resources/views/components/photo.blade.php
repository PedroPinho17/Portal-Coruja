@if(isset($previewImages) && count($previewImages) > 0)
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Nossa <span class="text-pink-600">Galeria</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Veja momentos especiais das nossas aulas e atividades em Lobão e São João de Ver
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-7xl mx-auto mb-12">
            @foreach($previewImages as $index => $image)
                <div class="group relative overflow-hidden rounded-2xl shadow-lg transform transition-all hover:scale-105 hover:shadow-2xl bg-gradient-to-br from-pink-100 to-orange-100">
                    <div class="aspect-[4/3] overflow-hidden">
                        @php
                            $imageSrc = $image['src'] ?? $image->src ?? '';
                            // Se já for uma URL completa (http/https), usar diretamente
                            if (str_starts_with($imageSrc, 'http://') || str_starts_with($imageSrc, 'https://')) {
                                $finalSrc = $imageSrc;
                            } else {
                                // Garantir que começa com / e usar asset() para o caminho completo
                                $cleanPath = ltrim($imageSrc, '/');
                                $finalSrc = asset($cleanPath);
                            }
                        @endphp
                        <img
                            src="{{ $finalSrc }}"
                            alt="{{ $image['alt'] ?? $image->alt ?? 'Imagem da galeria' }}"
                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                        />
                        <div class="w-full h-full hidden items-center justify-center bg-gradient-to-br from-pink-200 to-orange-200">
                            <div class="text-center p-8">
                                <div class="text-6xl mb-4">🦉</div>
                                <p class="text-gray-700 font-semibold">{{ $image['label'] ?? $image->label ?? '' }}</p>
                                <p class="text-gray-600 text-sm mt-2">{{ $image['alt'] ?? $image->alt ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="absolute top-4 left-4">
                        <span class="bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-semibold text-pink-600">
                            {{ $image['label'] ?? $image->label ?? '' }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center">
            <a
                href="{{ route('galeria') }}"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-pink-600 to-orange-600 text-white px-8 py-4 rounded-xl hover:shadow-xl transition-all transform hover:scale-105 font-semibold text-lg"
            >
                Ver Galeria Completa
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </a>
        </div>
    </div>
</section>
@endif

