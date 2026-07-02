<section id="home" class="pt-20 bg-gradient-to-br from-pink-50 via-white to-orange-50">
    <div class="container mx-auto px-4 py-20">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 leading-tight">
                    Educação com <span class="text-pink-600">Carinho</span> e <span class="text-orange-600">Dedicação</span>
                </h1>
                <p class="text-xl text-gray-600 leading-relaxed">
                    Centro de explicações e apoio educativo para crianças até ao 12º ano e preparação para o exames.
                    Na Corujinha, cada criança recebe atenção personalizada para alcançar o seu melhor potencial.
                </p>
                <div class="flex gap-4 flex-wrap">
                    <button
                        onclick="scrollToContact()"
                        class="bg-pink-600 text-white px-8 py-3 rounded-full hover:bg-pink-700 transition-all transform hover:scale-105 font-medium shadow-lg"
                    >
                        Entre em Contacto
                    </button>
                    <button
                        onclick="document.getElementById('about')?.scrollIntoView({ behavior: 'smooth' })"
                        class="border-2 border-pink-600 text-pink-600 px-8 py-3 rounded-full hover:bg-pink-50 transition-all font-medium"
                    >
                        Saiba Mais
                    </button>
                </div>
            </div>

            <div class="relative">
                <div class="bg-gradient-to-br from-pink-400 to-orange-400 rounded-3xl p-8 shadow-2xl transform hover:rotate-1 transition-transform">
                    <img src="{{ asset('logo1.webp') }}" alt="Corujinha" class="w-full h-auto" />
                </div>
                <div class="absolute -bottom-6 -left-6 bg-white rounded-2xl shadow-xl p-4 transform hover:scale-105 transition-transform">
                    <div class="flex items-center gap-3">
                        <div class="bg-pink-100 p-3 rounded-xl">
                            <svg class="text-pink-600 w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">+500</p>
                            <p class="text-sm text-gray-600">Crianças Apoiadas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-8 mt-20">
            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-2">
                <div class="bg-pink-100 w-16 h-16 rounded-xl flex items-center justify-center mb-4">
                    <svg class="text-pink-600 w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7M5 12h14" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Explicações</h3>
                <p class="text-gray-600">
                    Apoio escolar personalizado para todas as disciplinas até ao 12º ano e preparação para o exames
                </p>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-2">
                <div class="bg-orange-100 w-16 h-16 rounded-xl flex items-center justify-center mb-4">
                    <svg class="text-orange-600 w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Atividades de Férias</h3>
                <p class="text-gray-600">
                    Programas divertidos e educativos durante as férias escolares
                </p>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-2">
                <div class="bg-blue-100 w-16 h-16 rounded-xl flex items-center justify-center mb-4">
                    <svg class="text-blue-600 w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Formações Profissionais</h3>
                <p class="text-gray-600">
                    Diferentes Cursos certificados
                </p>
            </div>
        </div>
    </div>
</section>

<script>
    function scrollToContact() {
        const element = document.getElementById('contact');
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    }
</script>

