@extends('layout.app')

@section('title', 'Notícias - Corujinha')

@section('content')
<div class="pt-20">
    <section class="py-20 bg-gradient-to-br from-blue-50 via-pink-50 to-orange-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 bg-blue-100 px-4 py-2 rounded-full mb-4">
                    <svg class="text-blue-600 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                    </svg>
                    <span class="text-blue-600 font-semibold">Novidades e Anúncios</span>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
                    Conheça as Nossas <span class="text-pink-600">Últimas Notícias</span>
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Fique atualizado sobre formações, parcerias, oportunidades e muito mais
                </p>
            </div>

            @if(count($posts) === 0)
                <div class="text-center py-12">
                    <p class="text-gray-600 text-lg">Nenhuma novidade disponível no momento.</p>
                </div>
            @else
                <div class="max-w-5xl mx-auto space-y-8">
                    @foreach($posts as $post)
                        <div class="rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all transform hover:scale-105 max-h-[386px] {{ $post->featured ? 'ring-2 ring-pink-600 bg-pink-50' : 'bg-white' }}">
                            <div class="grid md:grid-cols-[55%_45%] gap-0 h-full">
                                <div class="bg-gray-200 overflow-hidden h-full">
                                    @if($post->image)
                                        <img
                                            src="{{ asset('img/posts/' . $post->image) }}"
                                            alt="{{ $post->title }}"
                                            class="w-full h-full object-cover transition-transform duration-300 hover:scale-110"
                                            onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gradient-to-br from-pink-200 to-orange-200\'><div class=\'text-7xl\'>📢</div></div>'"
                                        />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-pink-200 to-orange-200">
                                            <div class="text-7xl">📢</div>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4 flex flex-col justify-between h-full max-h-[386px]">
                                    <div class="flex-1 overflow-hidden">
                                        <h2 class="text-lg font-bold text-gray-900 mb-2">{{ $post->title }}</h2>
                                        <p class="text-gray-600 text-sm mb-3 leading-relaxed line-clamp-5">
                                            {{ $post->content }}
                                        </p>
                                    </div>
                                    <div class="pt-3 border-t border-gray-200 space-y-2 mt-auto">
                                        <div class="flex flex-col gap-2">
                                            @if($post->link)
                                                <a
                                                    href="{{ $post->link }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="inline-flex items-center gap-2 text-pink-600 hover:text-pink-700 font-semibold hover:underline transition-colors text-sm"
                                                >
                                                    Saber Mais
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if($post->phone)
                                                <a
                                                    href="tel:{{ $post->phone }}"
                                                    class="flex items-center gap-2 text-gray-700 hover:text-pink-600 transition-colors text-sm"
                                                >
                                                    <span>📱</span>
                                                    <span>{{ $post->phone }}</span>
                                                </a>
                                            @endif
                                            @if($post->email)
                                                <a
                                                    href="mailto:{{ $post->email }}"
                                                    class="flex items-center gap-2 text-gray-700 hover:text-pink-600 transition-colors text-sm break-all"
                                                >
                                                    <span>✉️</span>
                                                    <span>{{ $post->email }}</span>
                                                </a>
                                            @endif
                                            <a
                                                href="/#contact"
                                                class="inline-flex items-center gap-2 text-gray-600 hover:text-pink-600 font-semibold hover:underline transition-colors text-sm"
                                            >
                                                Contact
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
