@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col">
    <!-- Hero Section -->
    <div class="relative bg-blue-900 h-64 md:h-80 lg:h-96 overflow-hidden">
        <div class="absolute inset-0 opacity-20 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=1920&auto=format&fit=crop');"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-blue-900/80"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex flex-col justify-center items-center text-center">
            <h1 class="text-3xl md:text-5xl font-extrabold text-white tracking-tight mb-4">
                Voyagez confortablement avec <span class="text-orange-500">SATAS</span>
            </h1>
            <p class="text-lg md:text-xl text-blue-100 max-w-2xl mb-8">
                Découvrez le Maroc à travers notre réseau de plus de 100 destinations. Confort, sécurité et ponctualité garantis.
            </p>
        </div>
    </div>

    <!-- Search Card  -->
    <div class="flex-grow -mt-20 md:-mt-24 px-4 sm:px-6 lg:px-8 relative z-10 pb-12">
        <div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-6 md:p-8">
                <!-- Error Messages -->
                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg animate-pulse">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-bold text-red-800 uppercase tracking-wider">Erreur de saisie</h3>
                                <div class="mt-1 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('search.process') }}" method="GET">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                        
                        <!-- Departure -->
                        <div class="md:col-span-3">
                            <label for="departure_city" class="block text-sm font-semibold text-gray-700 mb-2">Départ</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <select name="departure_city" id="departure_city" required
                                    class="block w-full pl-10 pr-3 py-3 text-base border @error('departure_city') border-red-500 @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent rounded-lg bg-gray-50 hover:bg-white transition-all duration-200">
                                    <option value="">Ville de départ</option>
                                    @foreach($villes as $ville)
                                        <option value="{{ $ville->id }}" {{ request('departure_city') == $ville->id ? 'selected' : '' }}>
                                            {{ $ville->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="mt-1 text-[10px] text-gray-400 italic">Choisissez votre ville d'origine</p>
                        </div>

                        <!-- Swap Button (Visible on desktop) -->
                        <div class="hidden md:flex md:col-span-1 justify-center pb-2">
                             <button type="button" id="swap-cities" class="p-2 rounded-full hover:bg-gray-100 text-gray-400 hover:text-blue-600 transition-colors">
                                <svg class="w-6 h-6 transform rotate-90 md:rotate-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                             </button>
                        </div>

                        <!-- Arrival -->
                        <div class="md:col-span-3">
                            <label for="arrival_city" class="block text-sm font-semibold text-gray-700 mb-2">Arrivée</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <select name="arrival_city" id="arrival_city" required
                                    class="block w-full pl-10 pr-3 py-3 text-base border @error('arrival_city') border-red-500 @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent rounded-lg bg-gray-50 hover:bg-white transition-all duration-200">
                                    <option value="">Ville d'arrivée</option>
                                    @foreach($villes as $ville)
                                        <option value="{{ $ville->id }}" {{ request('arrival_city') == $ville->id ? 'selected' : '' }}>
                                            {{ $ville->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="mt-1 text-[10px] text-gray-400 italic">Où voulez-vous aller ?</p>
                        </div>

                        <!-- Date -->
                        <div class="md:col-span-4">
                            <label for="date" class="block text-sm font-semibold text-gray-700 mb-2">Date du voyage</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="date" name="date" id="date" required min="{{ date('Y-m-d') }}" value="{{ request('date', date('Y-m-d')) }}"
                                    class="block w-full pl-10 pr-3 py-3 text-base border @error('date') border-red-500 @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent rounded-lg bg-gray-50 hover:bg-white transition-all duration-200">
                            </div>
                        </div>

                        <!-- Search Button -->
                        <div class="md:col-span-1 flex flex-col justify-end">
                            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3.5 px-4 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex justify-center items-center group">
                                <span class="sr-only">Rechercher</span>
                                <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Features Banner -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 grid grid-cols-1 md:grid-cols-3 gap-4 text-center text-sm text-gray-600">
                <div class="flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Annulation gratuite jusqu'à 24h
                </div>
                <div class="flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    E-Billet sur mobile
                </div>
                <div class="flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Paiement 100% sécurisé
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const departureSelect = document.getElementById('departure_city');
        const arrivalSelect = document.getElementById('arrival_city');
        const swapBtn = document.getElementById('swap-cities');

        function validateCities() {
            const departure = departureSelect.value;
            const arrival = arrivalSelect.value;
            
            // Disable logic
            Array.from(arrivalSelect.options).forEach(opt => opt.disabled = (opt.value === departure && departure !== ''));
            Array.from(departureSelect.options).forEach(opt => opt.disabled = (opt.value === arrival && arrival !== ''));
        }

        departureSelect.addEventListener('change', validateCities);
        arrivalSelect.addEventListener('change', validateCities);
        
        if(swapBtn) {
            swapBtn.addEventListener('click', () => {
                const temp = departureSelect.value;
                departureSelect.value = arrivalSelect.value;
                arrivalSelect.value = temp;
                validateCities();
            });
        }

        validateCities();
    });
</script>
@endsection
