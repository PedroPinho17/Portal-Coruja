@php
    // Helper function para resolver caminho da imagem do post
    function getPostImagePath($imageUrl) {
        if (!$imageUrl) return null;
        if (str_starts_with($imageUrl, 'http')) return $imageUrl;
        if (str_starts_with($imageUrl, '/')) return asset($imageUrl);
        return asset("img/posts/{$imageUrl}");
    }
    
    // Usar dados do controller se existirem (já vem como Collection ordenada)
    $posts = $posts ?? collect([]);
    
    // Não mostrar seção se não houver posts
    if ($posts->isEmpty()) {
        return;
    }
@endphp

<section class="py-20 bg-gradient-to-br from-blue-50 via-pink-50 to-orange-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-2 bg-blue-100 px-4 py-2 rounded-full mb-4">
                <svg class="text-blue-600 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
                <span class="text-blue-600 font-semibold">Novidades e Anúncios</span>
            </div>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Fique Atualizado com as <span class="text-pink-600">Últimas Notícias</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Conheça as nossas formações, parcerias e oportunidades
            </p>
        </div>

        @php
            $postCount = $posts->count();
            // Determinar número de colunas baseado na quantidade
            if ($postCount <= 1) {
                $cols = 'grid-cols-1';
            } elseif ($postCount == 2) {
                $cols = 'md:grid-cols-2';
            } elseif ($postCount == 3) {
                $cols = 'md:grid-cols-2 lg:grid-cols-3';
            } elseif ($postCount == 4) {
                $cols = 'md:grid-cols-2 lg:grid-cols-4';
            } else {
                $cols = 'md:grid-cols-2 lg:grid-cols-3';
            }
        @endphp
        <div class="grid grid-cols-1 {{ $cols }} gap-8 max-w-7xl mx-auto mb-12">
            @foreach($posts as $post)
                @php
                    $postTitle = $post->title ?? 'Sem título';
                    $postContent = $post->content ?? '';
                    $postImage = $post->image ?? null;
                    $postFeatured = $post->feature ?? false;
                    $postLinkUrl = $post->link ?? null;
                    $postContactPhone = $post->phone ?? null;
                    $postContactEmail = $post->email ?? null;
                    $imagePath = getPostImagePath($postImage);
                @endphp
                
                <div class="group rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all transform hover:scale-105 flex flex-col h-full {{ $postFeatured ? 'ring-2 ring-pink-600 lg:col-span-1' : '' }}">
                    <div class="relative bg-gray-200 overflow-hidden h-48">
                        @if($imagePath)
                            <img
                                src="{{ $imagePath }}"
                                alt="{{ $postTitle }}"
                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                            />
                            <div class="w-full h-full hidden items-center justify-center bg-gradient-to-br from-pink-200 to-orange-200">
                                <div class="text-6xl">📢</div>
                            </div>
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-pink-200 to-orange-200">
                                <div class="text-6xl">📢</div>
                            </div>
                        @endif
                        
                        @if($postFeatured)
                            <div class="absolute top-3 right-3 bg-pink-600 text-white px-3 py-1 rounded-full text-xs font-bold">
                                DESTAQUE
                            </div>
                        @endif
                    </div>

                    <div class="p-6 flex flex-col flex-grow bg-white">
                        <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">{{ $postTitle }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3 flex-grow">{{ $postContent }}</p>

                        <div class="space-y-3 pt-4 border-t border-gray-200">
                            @if($postLinkUrl || $postContactPhone || $postContactEmail)
                                <div class="space-y-2">
                                    @if($postLinkUrl)
                                        <a
                                            href="{{ $postLinkUrl }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center gap-2 text-pink-600 hover:text-pink-700 font-semibold text-sm hover:underline"
                                        >
                                            Saber Mais
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                            </svg>
                                        </a>
                                    @endif
                                    
                                    @if($postContactPhone || $postContactEmail)
                                        <div class="flex flex-col gap-1 text-sm">
                                            @if($postContactPhone)
                                                <a
                                                    href="tel:{{ $postContactPhone }}"
                                                    class="text-gray-600 hover:text-pink-600 transition-colors"
                                                >
                                                    📱 {{ $postContactPhone }}
                                                </a>
                                            @endif
                                            @if($postContactEmail)
                                                <a
                                                    href="mailto:{{ $postContactEmail }}"
                                                    class="text-gray-600 hover:text-pink-600 transition-colors break-all"
                                                >
                                                    ✉️ {{ $postContactEmail }}
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center">
            <a
                href="{{ route('noticias') }}"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-pink-600 text-white px-8 py-4 rounded-xl hover:shadow-xl transition-all transform hover:scale-105 font-semibold text-lg"
            >
                Ver Todas as Novidades
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </a>
        </div>
    </div>
</section>

