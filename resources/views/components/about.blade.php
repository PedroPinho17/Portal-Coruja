<section id="about" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Sobre a <span class="text-pink-600">Corujinha</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Um projeto nascido do coração, dedicado ao desenvolvimento e bem-estar das crianças
            </p>
        </div>

        <div class="mb-16">
            <div class="max-w-4xl mx-auto bg-gradient-to-br from-pink-50 to-orange-50 rounded-3xl p-8 md:p-12 shadow-xl">
                <div class="grid md:grid-cols-2 gap-8 items-center mb-8">
                    <div class="order-2 md:order-1">
                        <h3 class="text-3xl font-bold text-gray-900 mb-4">Conheça a Fundadora</h3>
                        <h4 class="text-2xl text-pink-600 font-semibold mb-2">Vera Branquinho</h4>
                        <p class="text-gray-600 mb-6">Fundadora da Corujinha</p>
                        <p class="text-sm text-gray-500 mb-6">
                            Nascida a 10 de outubro de 1980, Licenciada e detentora de um Mestrado em Sociologia das Organizações e do Trabalho,
                            Vera sempre valorizou o estudo das dinâmicas humanas, das relações sociais e do impacto que a educação pode ter no desenvolvimento individual e coletivo.
                            Complementou a sua formação com uma Pós-Graduação em Gestão de Recursos Humanos, adquirindo competências de liderança, gestão de equipas e desenvolvimento de
                            pessoas. Paralelamente, investiu em formação especializada na área do TDAH e Hiperatividade, reforçando a sua capacidade para compreender e
                            apoiar crianças com necessidades educativas específicas, garantindo práticas inclusivas e estratégias de intervenção eficazes
                        </p>
                    </div>
                    <div class="order-1 md:order-2">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-pink-400 to-orange-400 rounded-2xl transform rotate-3"></div>
                            <img
                                src="{{ asset('Vera.webp') }}"
                                alt="Vera Branquinho - Fundadora da Corujinha"
                                class="relative rounded-2xl shadow-2xl w-full h-auto object-cover"
                            />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 space-y-4">
                    <p class="text-gray-700 leading-relaxed">
                        <strong class="text-pink-600">Vera Branquinho</strong> sempre teve um sonho: fazer a diferença na vida das pessoas — especialmente das crianças e jovens. Desde cedo ligada à área da educação e formação, acreditou sempre que aprender vai muito além dos livros: é uma forma de transformar vidas, abrir oportunidades e construir futuros com propósito.
                    </p>

                    <div class="border-l-4 border-pink-400 pl-4 my-6">
                        <p class="text-gray-700 leading-relaxed italic">
                            "O que começou como um pequeno sonho tornou-se, com muito trabalho e determinação, um projeto sólido e reconhecido na região."
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-center">
        <a
            href="{{ route('about') }}"
            class="inline-flex items-center gap-2 bg-gradient-to-r from-pink-600 to-orange-600 text-white px-8 py-4 rounded-xl hover:shadow-xl transition-all transform hover:scale-105 font-semibold text-lg"
        >
            Saber Mais Sobre a Corujinha
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
        </a>
    </div>
</section>

