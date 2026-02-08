@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-emerald-50 py-12">
    <div class="container mx-auto px-4 max-w-2xl">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
            <div class="bg-emerald-500 p-8 text-white text-center">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-3xl font-black mb-2">Réservation Confirmée !</h1>
                <p class="text-emerald-50 opacity-90">Merci de votre confiance. Votre voyage est réservé.</p>
            </div>

            <div class="p-8">
                <div class="flex items-center justify-between mb-8 pb-8 border-b border-gray-100">
                    <div>
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Numéro de réservation</div>
                        <div class="text-2xl font-black text-blue-900">#SATAS-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Date de réservation</div>
                        <div class="font-bold text-gray-900">{{ $booking->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>

                <div class="space-y-6 mb-12">
                    <h3 class="font-black text-gray-900 uppercase tracking-tight flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Détails du Trajet
                    </h3>
                    @foreach($booking->segments as $segment)
                        <div class="bg-gray-50 rounded-2xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <span class="font-bold text-gray-900">{{ $segment->departureGare->ville->nom }}</span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M13 7l5 5-5 5M6 12h12"/></svg>
                                    <span class="font-bold text-gray-900">{{ $segment->arrivalGare->ville->nom }}</span>
                                </div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($segment->heure_depart)->format('H:i') }} &rarr; {{ \Carbon\Carbon::parse($segment->heure_arrivee)->format('H:i') }}</div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-[10px] font-black uppercase">{{ $segment->bus->classe }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="space-y-6 mb-12">
                    <h3 class="font-black text-gray-900 uppercase tracking-tight flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        Passagers ({{ $booking->nb_passagers }})
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($booking->passengers as $passenger)
                            <div class="border border-gray-100 rounded-2xl p-4">
                                <div class="font-bold text-gray-900">{{ $passenger->nom_complet }}</div>
                                <div class="text-xs text-gray-500">{{ $passenger->cin }} • {{ $passenger->type }}</div>
                                @if($passenger->insurance || $passenger->snack_box || $passenger->premium_seat)
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        @if($passenger->insurance) <span class="bg-emerald-50 text-emerald-600 text-[8px] font-bold px-1.5 py-0.5 rounded">Assurance</span> @endif
                                        @if($passenger->snack_box) <span class="bg-amber-50 text-amber-600 text-[8px] font-bold px-1.5 py-0.5 rounded">Snack-box</span> @endif
                                        @if($passenger->premium_seat) <span class="bg-blue-50 text-blue-600 text-[8px] font-bold px-1.5 py-0.5 rounded">Siège Premium</span> @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-blue-50 rounded-3xl p-8 flex flex-col md:flex-row items-center gap-8 border-2 border-blue-100 border-dashed">
                    <div class="w-32 h-32 bg-white p-2 rounded-2xl shadow-sm">
                        <!-- Simulated QR Code -->
                        <div class="w-full h-full bg-gray-900 rounded-lg flex flex-wrap p-1">
                            @for($i = 0; $i < 64; $i++)
                                <div class="w-1/8 h-1/8 {{ rand(0, 1) ? 'bg-white' : 'bg-gray-900' }}"></div>
                            @endfor
                        </div>
                    </div>
                    <div class="text-center md:text-left">
                        <h4 class="font-black text-blue-900 mb-1">Votre E-Billet</h4>
                        <p class="text-xs text-blue-600 font-medium mb-4">Présentez ce QR code lors de l'embarquement.</p>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-xl text-sm transition-all shadow-lg shadow-blue-100">
                            Télécharger PDF
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-8 border-t border-gray-100 flex items-center justify-between">
                <div>
                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Total Payé</div>
                    <div class="text-3xl font-black text-blue-900">{{ number_format($booking->total_price, 2) }} <span class="text-sm font-medium text-gray-500">MAD</span></div>
                </div>
                <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 font-bold flex items-center">
                    Retour à l'accueil
                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
    .w-1\/8 { width: 12.5%; }
    .h-1\/8 { height: 12.5%; }
</style>
