<section id="training" class="py-20 bg-gradient-to-br from-blue-50 to-green-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                <span class="text-blue-600">Formações</span> Profissionais
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Cursos certificados em parceria com entidades estatais
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto mb-12">
            @foreach($formacoes as $f)
                <div class="bg-white rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <svg class="text-white w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $f->name ?? 'Sem título' }}</h3>
                    <p class="text-gray-700 mb-6 leading-relaxed">{{ $f->description ?? '' }}</p>
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center gap-3">
                            <svg class="text-blue-600 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-gray-700">Duração: {{ $f->duration ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="text-blue-600 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-gray-700">Entidade: 
                                @if($f->entity && $f->entity->website)
                                    <a href="{{ $f->entity->website }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800 hover:underline font-semibold">
                                        {{ $f->entity->name }}
                                    </a>
                                @else
                                    {{ $f->entity->name ?? 'N/D' }}
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="text-blue-600 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="text-gray-700">Local: {{ $f->location ?? 'N/D' }}</span>
                        </div>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-4">
                        <p class="text-sm text-blue-900 font-semibold">
                            Financiamento disponível através de entidades estatais parceiras
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="max-w-5xl mx-auto">
            <div class="bg-white rounded-3xl p-8 shadow-xl">
                <div class="grid md:grid-cols-2 gap-8 items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">
                            Cursos Modulares de 25 Horas
                        </h3>
                        <p class="text-gray-700 mb-6 leading-relaxed">
                            Além dos cursos anuais, oferecemos formações modulares de curta duração
                            em diversas áreas profissionais. Estes cursos são ideais para quem
                            pretende adquirir competências específicas de forma rápida e eficaz.
                        </p>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start gap-2">
                                <svg class="text-blue-600 shrink-0 mt-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-gray-700">Certificação ao completar o módulo</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="text-blue-600 shrink-0 mt-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-gray-700">Horários flexíveis</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="text-blue-600 shrink-0 mt-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-gray-700">Formadores especializados</span>
                            </li>
                        </ul>
                    </div>
                    <div class="bg-gradient-to-br from-blue-100 to-green-100 rounded-2xl p-8">
                        <div class="space-y-4">
                            <div class="bg-white rounded-xl p-4 shadow-md">
                                <p class="font-bold text-gray-900">Parceria com Entidades Estatais</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Todas as formações podem ser financiadas através dos nossos parceiros institucionais
                                </p>
                            </div>
                            <div class="bg-white rounded-xl p-4 shadow-md">
                                <p class="font-bold text-gray-900">Apoio na Candidatura</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Ajudamos no processo de candidatura aos financiamentos disponíveis
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 text-center">
            <button
                onclick="document.getElementById('contact')?.scrollIntoView({ behavior: 'smooth' })"
                class="bg-gradient-to-r from-blue-600 to-green-600 text-white px-10 py-4 rounded-full hover:shadow-xl transition-all transform hover:scale-105 font-semibold text-lg"
            >
                Saiba Mais Sobre as Formações
            </button>
        </div>
    </div>
</section>
