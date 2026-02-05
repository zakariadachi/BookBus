@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        <!-- Search Recap Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-8 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="bg-blue-100 p-3 rounded-full text-blue-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">
                        {{ App\Models\Ville::find(request('departure_city'))->nom ?? '?' }} 
                        <span class="text-gray-400 mx-1">&rarr;</span> 
                        {{ App\Models\Ville::find(request('arrival_city'))->nom ?? '?' }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse(request('date'))->format('D d M Y') }}
                    </p>
                </div>
            </div>
            <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Modifier la recherche
            </a>
        </div>

        <form action="{{ route('search.results') }}" method="GET" id="filterForm">
            <input type="hidden" name="departure_city" value="{{ request('departure_city') }}">
            <input type="hidden" name="arrival_city" value="{{ request('arrival_city') }}">
            <input type="hidden" name="date" value="{{ request('date') }}">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Sidebar Filters -->
                <div class="lg:col-span-3 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-24">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-bold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                                Filtres
                            </h3>
                            <a href="{{ route('search.results', request()->only(['departure_city', 'arrival_city', 'date', 'passengers'])) }}" class="text-xs text-blue-600 hover:underline">Réinitialiser</a>
                        </div>
                        
                        <div class="space-y-6">
                            <!-- Classes de Bus -->
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Classe de Bus</h4>
                                <div class="space-y-2">
                                    @php
                                        $classesToShow = !empty($availableClasses) ? $availableClasses : ['Standard', 'Confort', 'Premium'];
                                    @endphp
                                    @foreach($classesToShow as $class)
                                        <label class="flex items-center group cursor-pointer">
                                            <input type="checkbox" name="classes[]" value="{{ $class }}" 
                                                {{ is_array(request('classes')) && in_array($class, request('classes')) ? 'checked' : '' }}
                                                onchange="this.form.submit()"
                                                class="rounded text-blue-600 focus:ring-blue-500 border-gray-300 transition">
                                            <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-900 transition">{{ $class }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Heure de Départ -->
                            <div class="pt-4 border-t border-gray-100">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Heure de départ</h4>
                                <div class="space-y-2">
                                    @foreach(['matin' => 'Matin (05:00 - 12:00)', 'apres-midi' => 'Après-midi (12:00 - 18:00)', 'soir' => 'Soir (18:00 - 00:00)'] as $value => $label)
                                        <label class="flex items-center group cursor-pointer">
                                            <input type="radio" name="departure_time" value="{{ $value }}" 
                                                {{ request('departure_time') == $value ? 'checked' : '' }}
                                                onchange="this.form.submit()"
                                                class="text-blue-600 focus:ring-blue-500 border-gray-300 transition">
                                            <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-900 transition">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Prix Maximum -->
                            <div class="pt-4 border-t border-gray-100">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="text-sm font-semibold text-gray-700">Prix maximum</h4>
                                    <span class="text-blue-600 font-bold text-sm" id="priceValue">{{ request('max_price', 500) }} MAD</span>
                                </div>
                                <input type="range" name="max_price" min="50" max="500" step="10" 
                                    value="{{ request('max_price', 500) }}"
                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600"
                                    id="priceRange"
                                    oninput="document.getElementById('priceValue').innerText = this.value + ' MAD'"
                                    onchange="this.form.submit()">
                                <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                                    <span>50 MAD</span>
                                    <span>500 MAD</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results Feed -->
                <div class="lg:col-span-9">
                    <!-- Results Header / Sorting -->
                    <div class="flex items-center justify-between mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                        <div class="text-sm text-gray-600">
                            <span class="font-bold text-gray-900">{{ $results->count() }}</span> trajets trouvés
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500 font-medium">Trier par:</span>
                            <select name="sort_by" onchange="this.form.submit()" class="text-sm border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 py-1.5 pl-3 pr-8 bg-gray-50 font-medium">
                                <option value="price_asc" {{ request('sort_by') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                                <option value="price_desc" {{ request('sort_by') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                                <option value="time_asc" {{ request('sort_by') == 'time_asc' ? 'selected' : '' }}>Heure de départ</option>
                                <option value="duration_asc" {{ request('sort_by') == 'duration_asc' ? 'selected' : '' }}>Durée la plus courte</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-4 relative" id="resultsContainer">
                        <!-- Loading Overlay -->
                        <div id="loadingOverlay" class="hidden absolute inset-0 bg-white bg-opacity-60 z-20 flex items-center justify-center rounded-xl transition-opacity duration-300">
                            <div class="flex flex-col items-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-2"></div>
                                <span class="text-blue-600 font-medium animate-pulse">Filtrage en cours...</span>
                            </div>
                        </div>

                                @forelse($results as $result)
                            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-lg hover:border-blue-300 border border-gray-200 transition-all duration-300 overflow-hidden relative fade-in">
                                
                                <div class="p-6 cursor-pointer group-hover:bg-blue-50/30 transition-colors">
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                                        <!-- Times & Route -->
                                        <div class="flex-1 flex items-center justify-between md:justify-start gap-8">
                                            <div class="text-center md:text-left min-w-[80px]">
                                                <div class="text-2xl font-bold text-gray-900">{{ \Carbon\Carbon::parse($result->heure_depart)->format('H:i') }}</div>
                                                <div class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ $result->departureGare->ville->nom }}</div>
                                                <div class="text-xs text-gray-400 mt-1 truncate max-w-[100px]" title="{{ $result->departureGare->nom }}">{{ $result->departureGare->nom }}</div>
                                            </div>

                                            <!-- Duration Line -->
                                            <div class="flex-1 flex flex-col items-center max-w-[160px]">
                                                <div class="text-xs text-gray-500 mb-1 font-medium">
                                                    {{ floor($result->duration_minutes / 60) }}h {{ $result->duration_minutes % 60 }}min
                                                </div>
                                                <div class="w-full h-0.5 bg-gray-200 relative flex items-center justify-center">
                                                    <div class="w-2 h-2 rounded-full bg-blue-500 absolute left-0 shadow-sm border border-white"></div>
                                                    <div class="w-2 h-2 rounded-full bg-blue-500 absolute right-0 shadow-sm border border-white"></div>
                                                    <!-- Icon in middle -->
                                                    <div class="bg-white px-2 duration-icon">
                                                        <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    @if(isset($result->is_direct) && !$result->is_direct)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 uppercase tracking-tighter shadow-sm">
                                                            1 Escale à {{ $result->segments[0]->arrivalGare->ville->nom }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-tighter shadow-sm">
                                                            Direct
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="text-center md:text-right min-w-[80px]">
                                                <div class="text-2xl font-bold text-gray-900">{{ \Carbon\Carbon::parse($result->heure_arrivee)->format('H:i') }}</div>
                                                <div class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ $result->arrivalGare->ville->nom }}</div>
                                                <div class="text-xs text-gray-400 mt-1 truncate max-w-[100px]" title="{{ $result->arrivalGare->nom }}">{{ $result->arrivalGare->nom }}</div>
                                            </div>
                                        </div>

                                        <!-- Action Side -->
                                        <div class="flex items-center justify-between md:flex-col md:items-end md:border-l md:pl-8 border-gray-100 gap-2 min-w-[140px]">
                                            <div class="text-left md:text-right">
                                                <div class="text-[10px] text-blue-600 font-bold uppercase tracking-widest mb-1">{{ $result->bus->classe }}</div>
                                                <div class="text-3xl font-black text-blue-900 tracking-tight">
                                                    {{ number_format($result->tarif, 0) }} <span class="text-xs font-medium text-gray-500">MAD</span>
                                                </div>
                                                <div class="text-[10px] text-gray-400 flex items-center justify-end gap-1">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M5 13l4 4L19 7"/></svg>
                                                    {{ $result->bus->capacite }} places disponibles
                                                </div>
                                                <div class="text-[10px] text-gray-400 flex items-center justify-end gap-1">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M5 13l4 4L19 7"/></svg>
                                                    Annulation gratuite
                                                </div>
                                            </div>
                                            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-blue-200 transition-all flex items-center group-hover:px-10 transform duration-300">
                                                Réserver
                                                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Amenities -->
                                    <div class="mt-4 pt-4 border-t border-gray-50 flex gap-4 text-gray-400 text-[10px]">
                                        <span>Wi-Fi @if($result->bus->classe != 'Premium') limité @endif</span>
                                        <span>Prises @if($result->bus->classe == 'Standard') non dispos @endif</span>
                                        <span>Billet Mobile</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300 fade-in">
                                <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun itinéraire trouvé</h3>
                                <p class="text-gray-500 max-w-sm mx-auto mb-8">
                                    Nous n'avons trouvé aucun bus pour le 
                                    <span class="font-bold text-blue-600">{{ \Carbon\Carbon::parse(request('date'))->format('d/m/Y') }}</span>. 
                                    Essayez d'élargir votre recherche ou de réinitialiser les filtres.
                                </p>
                                @if(isset($suggested_date))
                                    <p class="mt-4 text-sm text-blue-600">
                                        Prochaine disponibilité le : <a href="{{ route('search.results', array_merge(request()->all(), ['date' => $suggested_date])) }}" class="font-bold underline">{{ \Carbon\Carbon::parse($suggested_date)->format('d/m/Y') }}</a>
                                    </p>
                                @endif
                                <a href="{{ route('search.results', request()->only(['departure_city', 'arrival_city', 'date', 'passengers'])) }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition mt-6">
                                    Réinitialiser les filtres
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </form>

        <style>
            .fade-in { animation: fadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; }
            @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
            
            .group:hover .duration-icon { transform: translateX(5px); transition: transform 0.3s ease; }
            
            #filterForm input[type="radio"]:checked + span,
            #filterForm input[type="checkbox"]:checked + span {
                color: #2563eb;
                font-weight: 600;
            }

            input[type=range]::-webkit-slider-thumb {
                height: 20px;
                width: 20px;
                border-radius: 50%;
                background: #2563eb;
                cursor: pointer;
                border: 2px solid white;
                box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            }
        </style>

        <script>
            document.addEventListener('submit', function(e) {
                if (e.target.id === 'filterForm') {
                    document.getElementById('loadingOverlay').classList.remove('hidden');
                }
            });

            // Prevent scroll jump on submit
            if (window.location.search.includes('classes') || window.location.search.includes('max_price') || window.location.search.includes('sort_by')) {
                const resultsTop = document.getElementById('resultsContainer').getBoundingClientRect().top + window.scrollY - 100;
                window.scrollTo({ top: resultsTop, behavior: 'smooth' });
            }
        </script>
    </div>
</div>
@endsection
