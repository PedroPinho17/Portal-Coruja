@extends('layout.app')

@section('title', 'Galeria - Corujinha')

@section('content')
<div class="pt-20">
    <section id="gallery" class="py-20 bg-gradient-to-br from-pink-50 to-orange-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    A Nossa <span class="text-pink-600">Galeria</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                    Momentos especiais e atividades na Corujinha
                </p>

                <div class="flex flex-wrap justify-center gap-4">
                    <button type="button" data-filter="all" id="filter-all" class="px-6 py-3 rounded-xl font-semibold transition-all bg-pink-600 text-white shadow-lg">
                        Todas
                    </button>
                    <button type="button" data-filter="lobao" id="filter-lobao" class="px-6 py-3 rounded-xl font-semibold transition-all bg-white text-gray-700 hover:shadow-md">
                        Centro Lobão
                    </button>
                    <button type="button" data-filter="sao-joao" id="filter-sao-joao" class="px-6 py-3 rounded-xl font-semibold transition-all bg-white text-gray-700 hover:shadow-md">
                        Centro São João de Ver
                    </button>
                    <button type="button" data-filter="activities" id="filter-activities" class="px-6 py-3 rounded-xl font-semibold transition-all bg-white text-gray-700 hover:shadow-md">
                        Atividades
                    </button>
                </div>
            </div>

            <div id="gallery-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
                <!-- Imagens serão carregadas via JavaScript -->
            </div>
        </div>
    </section>

    <!-- Lightbox -->
    <div id="lightbox" class="fixed inset-0 z-50 bg-black/95 hidden items-center justify-center p-4">
        <button type="button" data-lightbox-action="close" class="absolute top-4 right-4 text-white hover:text-pink-400 transition-colors z-10">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <button type="button" data-lightbox-action="prev" id="prev-btn" class="absolute left-4 text-white hover:text-pink-400 transition-colors z-10 hidden">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <button type="button" data-lightbox-action="next" id="next-btn" class="absolute right-4 text-white hover:text-pink-400 transition-colors z-10 hidden">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
        <div class="max-w-6xl w-full">
            <img id="lightbox-img" src="" alt="" class="w-full h-auto max-h-[85vh] object-contain rounded-lg">
            <p id="lightbox-title" class="text-white text-center mt-4 text-lg"></p>
        </div>
    </div>
</div>

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
    const galleryImages = [
        { src: '{{ asset("gallery/caldas/classroom-1.webp") }}', alt: 'Sala de aula - Centro Caldas', category: 'lobao', title: 'Sala de Aula (Lobão)' },
        { src: '{{ asset("gallery/caldas/classroom-2.webp") }}', alt: 'Sala de aula 2 - Centro Lobão', category: 'lobao', title: 'Sala de Aula 2 (Lobão)' },
        { src: '{{ asset("gallery/caldas/tutoring-session.webp") }}', alt: 'Aula no Centro Lobão', category: 'lobao', title: 'Aula em Lobão' },
        { src: '{{ asset("gallery/sao-joao/placeholder-1.webp") }}', alt: 'Centro São João de Ver - 1', category: 'sao-joao', title: 'São João de Ver 1' },
        { src: '{{ asset("gallery/sao-joao/placeholder-2.jpg") }}', alt: 'Centro São João de Ver - 2', category: 'sao-joao', title: 'São João de Ver 2' },
        { src: '{{ asset("gallery/sao-joao/placeholder-3.jpg") }}', alt: 'Centro São João de Ver - 3', category: 'sao-joao', title: 'São João de Ver 3' },
        { src: '{{ asset("gallery/activities/beach-trip.png") }}', alt: 'Passeio à praia', category: 'activities', title: 'Passeio à Praia' },
        { src: '{{ asset("gallery/activities/carnival.jpeg") }}', alt: 'Carnaval Corujinha', category: 'activities', title: 'Carnaval Corujinha' },
        { src: '{{ asset("gallery/activities/classroom-lesson.png") }}', alt: 'Escola de Condução', category: 'activities', title: 'Escola de condução' },
        { src: '{{ asset("gallery/activities/dance-kids.png") }}', alt: 'Aula de Dance Kids', category: 'activities', title: 'Dance Kids' }
    ];

    let currentFilter = 'all';
    let currentImageIndex = 0;
    let filteredImages = galleryImages;

    function renderGallery() {
        const container = document.getElementById('gallery-container');
        filteredImages = currentFilter === 'all' ? galleryImages : galleryImages.filter(img => img.category === currentFilter);
        
        container.innerHTML = filteredImages.map((image, index) => `
            <div data-gallery-index="${index}" class="group relative overflow-hidden rounded-2xl shadow-lg cursor-pointer transform transition-all hover:scale-105 hover:shadow-2xl bg-white">
                <div class="aspect-[4/3] overflow-hidden">
                    <img src="${escapeHtml(image.src)}" alt="${escapeHtml(image.alt)}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" data-title="${escapeHtml(image.title)}">
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                    <div class="absolute bottom-0 left-0 right-0 p-4">
                        <h3 class="text-white font-bold text-lg">${escapeHtml(image.title)}</h3>
                    </div>
                </div>
            </div>
        `).join('');

        container.querySelectorAll('img').forEach(img => {
            img.addEventListener('error', function () {
                const title = this.dataset.title || 'Imagem';
                this.parentElement.innerHTML = '<div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-pink-200 to-orange-200"><div class="text-center p-8"><div class="text-6xl mb-4">🦉</div><p class="text-gray-700 font-semibold">' + title + '</p></div></div>';
            });
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function filterGallery(category) {
        currentFilter = category;
        renderGallery();
        
        // Atualizar botões
        ['all', 'lobao', 'sao-joao', 'activities'].forEach(cat => {
            const btn = document.getElementById(`filter-${cat}`);
            if (cat === category) {
                btn.className = 'px-6 py-3 rounded-xl font-semibold transition-all bg-pink-600 text-white shadow-lg';
            } else {
                btn.className = 'px-6 py-3 rounded-xl font-semibold transition-all bg-white text-gray-700 hover:shadow-md';
            }
        });
    }

    function openLightbox(index) {
        currentImageIndex = index;
        const image = filteredImages[index];
        document.getElementById('lightbox-img').src = image.src;
        document.getElementById('lightbox-title').textContent = image.title;
        document.getElementById('lightbox').classList.remove('hidden');
        document.getElementById('lightbox').classList.add('flex');
        
        document.getElementById('prev-btn').classList.toggle('hidden', index === 0);
        document.getElementById('next-btn').classList.toggle('hidden', index === filteredImages.length - 1);
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.add('hidden');
        document.getElementById('lightbox').classList.remove('flex');
    }

    function previousImage() {
        if (currentImageIndex > 0) {
            openLightbox(currentImageIndex - 1);
        }
    }

    function nextImage() {
        if (currentImageIndex < filteredImages.length - 1) {
            openLightbox(currentImageIndex + 1);
        }
    }

    renderGallery();

    document.querySelectorAll('[data-filter]').forEach(btn => {
        btn.addEventListener('click', function () { filterGallery(this.getAttribute('data-filter')); });
    });

    document.getElementById('gallery-container').addEventListener('click', function (e) {
        const card = e.target.closest('[data-gallery-index]');
        if (card) openLightbox(parseInt(card.getAttribute('data-gallery-index'), 10));
    });

    document.getElementById('lightbox').addEventListener('click', function (e) {
        const action = e.target.closest('[data-lightbox-action]')?.getAttribute('data-lightbox-action');
        if (action === 'close') closeLightbox();
        if (action === 'prev') previousImage();
        if (action === 'next') nextImage();
    });
</script>
@endpush
@endsection

