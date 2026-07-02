<section id="contact" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Entre em <span class="text-pink-600">Contacto</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Estamos aqui para ajudar. Fale connosco e conheça melhor os nossos serviços
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 gap-12 max-w-6xl mx-auto">
            <div class="space-y-8">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Informações de Contacto</h3>
                    <div class="space-y-6">
                        <div class="flex items-start gap-4 bg-pink-50 rounded-2xl p-6 hover:shadow-lg transition-all">
                            <div class="bg-pink-600 p-3 rounded-xl shrink-0">
                                <svg class="text-white w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-1">Telefone</h4>
                                <a href="tel:+351916280509" class="text-gray-700 hover:text-pink-600 transition-colors">
                                    +351 916 280 509
                                </a>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4 bg-orange-50 rounded-2xl p-6 hover:shadow-lg transition-all">
                            <div class="bg-orange-600 p-3 rounded-xl shrink-0">
                                <svg class="text-white w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-1">Morada Principal</h4>
                                <p class="text-gray-700">R. Namorados 566</p>
                                <p class="text-gray-700">4505-444 Lobão</p>
                                <br />
                                <h4 class="font-bold text-gray-900 mb-1">Morada São João de Ver</h4>
                                <p class="text-gray-700">Av. Dr. Francisco Sá Carneiro 1192 Loja H</p>
                                <p class="text-gray-700">4520-617 São João de Ver</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4 bg-blue-50 rounded-2xl p-6 hover:shadow-lg transition-all">
                            <div class="bg-blue-600 p-3 rounded-xl shrink-0">
                                <svg class="text-white w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-1">Email</h4>
                                <a href="mailto:Formacao@ninhodacoruja.pt" class="text-gray-700 hover:text-blue-600 transition-colors block">Formacao@ninhodacoruja.pt</a>
                                <a href="mailto:Geral@ninhodacoruja.pt" class="text-gray-700 hover:text-blue-600 transition-colors block">Geral@ninhodacoruja.pt</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-pink-100 to-orange-100 rounded-2xl p-6">
                    <h4 class="font-bold text-gray-900 mb-3">Localizações</h4>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2">
                            <svg class="text-pink-600 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-gray-700">Centro de Lobão</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="text-pink-600 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-gray-700">Centro de São João de Ver</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div>
                <div class="bg-gradient-to-br from-pink-50 to-orange-50 rounded-3xl p-8 shadow-xl">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Envie-nos uma Mensagem</h3>
                    
                    @if(session('success'))
                        <div class="text-center py-12">
                            <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="text-green-600 w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h4 class="text-2xl font-bold text-gray-900 mb-2">Mensagem Enviada!</h4>
                            <p class="text-gray-600">Entraremos em contacto em breve.</p>
                        </div>
                    @else
                        @if($errors->any())
                            <div class="mb-6 bg-red-50 border-2 border-red-200 rounded-xl p-4 flex items-start gap-3">
                                <svg class="text-red-600 shrink-0 mt-0.5 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    @foreach($errors->all() as $error)
                                        <p class="text-red-700 text-sm">{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                            @csrf
                            
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nome Completo
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    required
                                    value="{{ old('name') }}"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-pink-600 focus:outline-none transition-colors"
                                    placeholder="O seu nome"
                                />
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    required
                                    value="{{ old('email') }}"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-pink-600 focus:outline-none transition-colors"
                                    placeholder="seu@email.com"
                                />
                            </div>
                            
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Telefone
                                </label>
                                <input
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-pink-600 focus:outline-none transition-colors"
                                    placeholder="+351 XXX XXX XXX"
                                />
                            </div>
                            
                            <div>
                                <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Mensagem
                                </label>
                                <textarea
                                    id="message"
                                    name="message"
                                    required
                                    rows="5"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-pink-600 focus:outline-none transition-colors resize-none"
                                    placeholder="Como podemos ajudar?"
                                >{{ old('message') }}</textarea>
                            </div>
                            
                            <button
                                type="submit"
                                class="w-full bg-gradient-to-r from-pink-600 to-orange-600 text-white py-4 rounded-xl hover:shadow-xl transition-all transform hover:scale-105 font-semibold flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Enviar Mensagem
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="mt-16 max-w-6xl mx-auto">
            <div class="text-center mb-8">
                <h3 class="text-3xl font-bold text-gray-900 mb-2">Onde Estamos</h3>
                <p class="text-gray-600">Encontre-nos em Lobão</p>
            </div>
            
            <div class="bg-gradient-to-br from-pink-50 to-orange-50 rounded-3xl p-6 shadow-xl">
                <div class="relative w-full h-[500px] rounded-2xl overflow-hidden shadow-lg">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m23!1m12!1m3!1d3012.614285186859!2d-8.49057696377977!3d40.96802790587404!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m8!3e6!4m0!4m5!1s0xd247f83484b6231%3A0x9c3013157ecde0bd!2sR.%20Namorados%20566%2C%204505-444%20Lob%C3%A3o!3m2!1d40.9678744!2d-8.490356199999999!5e0!3m2!1spt-PT!2spt!4v1760630492927!5m2!1spt-PT!2spt"
                        width="100%"
                        height="100%"
                        style="border: 0;"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Localização Corujinha - Lobão"
                    ></iframe>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6 mt-6">
                    <div class="bg-white rounded-xl p-6 shadow-md">
                        <div class="flex items-start gap-4">
                            <div class="bg-pink-600 p-3 rounded-xl shrink-0">
                                <svg class="text-white w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-2">Centro Lobão</h4>
                                <p class="text-gray-700 text-sm">R. Namorados 566</p>
                                <p class="text-gray-700 text-sm">4505-444 Lobão</p>
                                <a
                                    href="https://maps.google.com/?q=R.+Namorados+566+4505-444+Lobão"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-pink-600 hover:text-pink-700 text-sm font-semibold mt-2 inline-block"
                                >
                                    Ver no Google Maps →
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl p-6 shadow-md">
                        <div class="flex items-start gap-4">
                            <div class="bg-orange-600 p-3 rounded-xl shrink-0">
                                <svg class="text-white w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-2">Centro São João de Ver</h4>
                                <p class="text-gray-700 text-sm">Av. Dr. Francisco Sá Carneiro 1192 Loja H</p>
                                <p class="text-gray-700 text-sm">4520-617 São João de Ver</p>
                                <a
                                    href="https://maps.google.com/?q=Av.+Dr.+Francisco+S%C3%A1+Carneiro+1192+Loja+H+4520-617+S%C3%A3o+Jo%C3%A3o+de+Ver"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-pink-600 hover:text-pink-700 text-sm font-semibold mt-2 inline-block"
                                >
                                    Ver no Google Maps →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

