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
                        {{ \Carbon\Carbon::parse(request('date'))->format('D d M Y') }} &bull; {{ request('passengers') }} Voyageur(s)
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

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Sidebar Filters -->
            <div class="lg:col-span-3 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Filtres
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Escales</h4>
                            <label class="flex items-center space-x-2 text-sm text-gray-600 hover:text-gray-900 cursor-pointer">
                                <input type="checkbox" class="rounded text-blue-600 focus:ring-blue-500 border-gray-300">
                                <span>Trajets directs uniquement</span>
                            </label>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-100">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Compagnies</h4>
                            <label class="flex items-center space-x-2 text-sm text-gray-600">
                                <input type="checkbox" checked class="rounded text-blue-600 border-gray-300">
                                <span>SATAS Premier</span>
                            </label>
                             <label class="flex items-center space-x-2 text-sm text-gray-600 mt-2">
                                <input type="checkbox" checked class="rounded text-blue-600 border-gray-300">
                                <span>SATAS Confort</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Feed -->
            <div class="lg:col-span-9 space-y-4">
                @forelse($results as $result)
                    <div class="group bg-white rounded-2xl shadow-sm hover:shadow-lg hover:border-blue-300 border border-gray-200 transition-all duration-300 overflow-hidden relative">
                         @if($loop->first)
                            <div class="absolute top-0 right-0 bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg z-10">
                                Meilleur Prix
                            </div>
                        @endif
                        
                        <div class="p-6">
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
                                        <div class="text-xs text-gray-500 mb-1">
                                            {{ \Carbon\Carbon::parse($result->heure_depart)->diff(\Carbon\Carbon::parse($result->heure_arrivee))->format('%Hh %I') }}
                                        </div>
                                        <div class="w-full h-px bg-gray-300 relative flex items-center justify-center">
                                            <div class="w-2 h-2 rounded-full bg-gray-400 absolute left-0"></div>
                                            <div class="w-2 h-2 rounded-full bg-gray-400 absolute right-0"></div>
                                            <!-- Icon in middle -->
                                            <div class="bg-white px-1">
                                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                                                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="mt-1">
                                            @if(isset($result->is_direct) && !$result->is_direct)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    1 Escale
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
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
                                <div class="flex items-center justify-between md:flex-col md:items-end md:border-l md:pl-8 border-gray-100 gap-2 min-w-[120px]">
                                    <div class="text-left md:text-right">
                                        <div class="text-sm text-gray-400 line-through">
                                            {{ number_format($result->tarif * 1.2, 0) }} MAD
                                        </div>
                                        <div class="text-3xl font-extrabold text-orange-600">
                                            {{ number_format($result->tarif, 0) }} <span class="text-sm font-normal text-gray-600">MAD</span>
                                        </div>
                                    </div>
                                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg shadow transition-colors flex items-center group-hover:scale-105 transform duration-200">
                                        Choisir
                                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 bg-white rounded-xl border border-dashed border-gray-300">
                        <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Aucun trajet trouvé</h3>
                        <p class="mt-2 text-gray-500 max-w-sm mx-auto">Nous n'avons trouvé aucun résultat pour votre recherche. Essayez de changer de date ou de ville.</p>
                        <a href="{{ route('home') }}" class="mt-6 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Nouvelle recherche
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
