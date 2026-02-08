@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Progress Bar -->
        <div class="mb-12">
            <div class="flex items-center justify-between max-w-2xl mx-auto relative">
                <div class="absolute top-1/2 left-0 w-full h-0.5 bg-gray-200 -translate-y-1/2 z-0"></div>
                
                @foreach(['Récapitulatif', 'Passagers', 'Options', 'Paiement'] as $index => $step)
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-500 {{ $index === 0 ? 'bg-blue-600 text-white' : 'bg-white text-gray-400 border-2 border-gray-200' }}" id="step-circle-{{ $index + 1 }}">
                            {{ $index + 1 }}
                        </div>
                        <span class="text-[10px] md:text-xs font-bold mt-2 uppercase tracking-wider {{ $index === 0 ? 'text-blue-600' : 'text-gray-400' }}" id="step-label-{{ $index + 1 }}">{{ $step }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-8 rounded-r-xl">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
            @csrf
            <input type="hidden" name="segment_ids" value="{{ implode(',', $segmentIds) }}">
            <input type="hidden" name="base_price" value="{{ $totalPrice }}">

            <!-- Step 1: Recap -->
            <div class="step-content" id="step-content-1">
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-8">
                    <div class="bg-blue-600 p-6 text-white">
                        <h2 class="text-xl font-bold flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                            Récapitulatif de votre trajet
                        </h2>
                    </div>
                    <div class="p-8">
                        <div class="space-y-6">
                            @foreach($segments as $segment)
                                <div class="flex items-start justify-between border-b border-gray-100 pb-6 last:border-0 last:pb-0">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-4 mb-2">
                                            <span class="text-2xl font-black text-blue-900">{{ \Carbon\Carbon::parse($segment->heure_depart)->format('H:i') }}</span>
                                            <div class="h-0.5 flex-1 bg-gray-200 relative">
                                                <div class="absolute -top-1 left-0 w-2 h-2 rounded-full bg-blue-500"></div>
                                                <div class="absolute -top-1 right-0 w-2 h-2 rounded-full bg-blue-500"></div>
                                            </div>
                                            <span class="text-2xl font-black text-blue-900">{{ \Carbon\Carbon::parse($segment->heure_arrivee)->format('H:i') }}</span>
                                        </div>
                                        <div class="flex justify-between text-xs font-bold text-gray-500 uppercase tracking-widest">
                                            <span>{{ $segment->departureGare->ville->nom }} ({{ $segment->departureGare->nom }})</span>
                                            <span>{{ $segment->arrivalGare->ville->nom }} ({{ $segment->arrivalGare->nom }})</span>
                                        </div>
                                    </div>
                                    <div class="ml-8 text-right">
                                        <div class="text-xs font-bold text-blue-600 uppercase tracking-widest">{{ $segment->bus->classe }}</div>
                                        <div class="text-sm font-medium text-gray-500">{{ $segment->bus->immatriculation }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="nextStep(2)" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-10 rounded-2xl shadow-lg shadow-blue-200 transition-all flex items-center group">
                        Continuer vers les passagers
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </button>
                </div>
            </div>

            <!-- Step 2: Passengers -->
            <div class="step-content hidden" id="step-content-2">
                <div class="space-y-6 mb-8">
                    @for($i = 0; $i < $nbPassagers; $i++)
                        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
                            <div class="bg-gray-50 px-8 py-4 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="font-bold text-gray-900 flex items-center">
                                    <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 text-sm">#{{ $i + 1 }}</span>
                                    Passager {{ $i + 1 }}
                                </h3>
                                <select name="passengers[{{ $i }}][type]" class="text-xs font-bold uppercase border-none bg-blue-50 text-blue-600 rounded-lg focus:ring-0">
                                    <option value="Adulte">Adulte</option>
                                    <option value="Enfant">Enfant (-12 ans)</option>
                                </select>
                            </div>
                            <div class="p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Nom Complet</label>
                                    <input type="text" name="passengers[{{ $i }}][nom_complet]" required class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500" placeholder="ex: Jean Dupont">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">CIN</label>
                                    <input type="text" name="passengers[{{ $i }}][cin]" required class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500" placeholder="ex: AB123456">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Date de naissance</label>
                                    <input type="date" name="passengers[{{ $i }}][date_naissance]" required class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
                <div class="flex justify-between">
                    <button type="button" onclick="nextStep(1)" class="text-gray-500 hover:text-gray-900 font-bold py-4 px-8 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Retour
                    </button>
                    <button type="button" onclick="nextStep(3)" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-10 rounded-2xl shadow-lg shadow-blue-200 transition-all flex items-center group">
                        Continuer vers les options
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </button>
                </div>
            </div>

            <!-- Step 3: Options -->
            <div class="step-content hidden" id="step-content-3">
                <div class="space-y-6 mb-8">
                    @for($i = 0; $i < $nbPassagers; $i++)
                        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
                            <div class="bg-gray-50 px-8 py-4 border-b border-gray-100">
                                <h3 class="font-bold text-gray-900">Options pour Passager #{{ $i + 1 }}</h3>
                            </div>
                            <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                                <label class="relative flex flex-col p-6 bg-white border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-blue-300 transition-all group">
                                    <input type="checkbox" name="passengers[{{ $i }}][insurance]" value="1" onchange="calculateTotal()" class="hidden-checkbox sr-only">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                        </div>
                                        <div class="text-emerald-700 font-black">+25 MAD</div>
                                    </div>
                                    <span class="font-bold text-gray-900">Assurance</span>
                                    <span class="text-[10px] text-gray-400 leading-tight mt-1">Annulation remboursable à 80%</span>
                                    <div class="absolute top-2 right-2 w-5 h-5 rounded-full border-2 border-gray-200 check-indicator hidden flex items-center justify-center bg-blue-600 text-white border-blue-600">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    </div>
                                </label>

                                <label class="relative flex flex-col p-6 bg-white border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-blue-300 transition-all group">
                                    <input type="checkbox" name="passengers[{{ $i }}][snack_box]" value="1" onchange="calculateTotal()" class="hidden-checkbox sr-only">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c.053.164.082.341.082.527 0 1.105-.895 2-2 2H5c-1.105 0-2-.895-2-2 0-.186.029-.363.082-.527L5.584 5.313C5.78 4.545 6.46 4 7.25 4h9.5c.79 0 1.47.545 1.666 1.313L21 15.546z" /></svg>
                                        </div>
                                        <div class="text-amber-700 font-black">+15 MAD</div>
                                    </div>
                                    <span class="font-bold text-gray-900">Snack-box</span>
                                    <span class="text-[10px] text-gray-400 leading-tight mt-1">Eau + Sandwich + Fruit</span>
                                    <div class="absolute top-2 right-2 w-5 h-5 rounded-full border-2 border-gray-200 check-indicator hidden flex items-center justify-center bg-blue-600 text-white border-blue-600">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    </div>
                                </label>

                                <label class="relative flex flex-col p-6 bg-white border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-blue-300 transition-all group">
                                    <input type="checkbox" name="passengers[{{ $i }}][premium_seat]" value="1" onchange="calculateTotal()" class="hidden-checkbox sr-only">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                        </div>
                                        <div class="text-blue-700 font-black">+30 MAD</div>
                                    </div>
                                    <span class="font-bold text-gray-900">Siège Premium</span>
                                    <span class="text-[10px] text-gray-400 leading-tight mt-1">Siège confort à l'avant</span>
                                    <div class="absolute top-2 right-2 w-5 h-5 rounded-full border-2 border-gray-200 check-indicator hidden flex items-center justify-center bg-blue-600 text-white border-blue-600">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    </div>
                                </label>
                            </div>
                        </div>
                    @endfor
                </div>
                <div class="flex justify-between">
                    <button type="button" onclick="nextStep(2)" class="text-gray-500 hover:text-gray-900 font-bold py-4 px-8 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Retour
                    </button>
                    <button type="button" onclick="nextStep(4)" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-10 rounded-2xl shadow-lg shadow-blue-200 transition-all flex items-center group">
                        Récapitulatif final
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </button>
                </div>
            </div>

            <!-- Step 4: Summary -->
            <div class="step-content hidden" id="step-content-4">
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-8">
                    <div class="p-8">
                        <h3 class="text-2xl font-black text-gray-900 mb-8 border-b pb-4">Résumé de votre commande</h3>
                        
                        <div class="flex justify-between mb-4">
                            <span class="text-gray-500 font-medium">Billet (x{{ $nbPassagers }})</span>
                            <span class="font-bold text-gray-900" id="recap-base-price">0.00 MAD</span>
                        </div>
                        
                        <div id="recap-options" class="space-y-4 pt-4 border-t border-gray-50">
                        </div>

                        <div class="mt-8 pt-8 border-t-2 border-dashed border-gray-100 flex items-center justify-between">
                            <span class="text-xl font-bold text-gray-900">Total à payer</span>
                            <span class="text-4xl font-black text-blue-600" id="final-total">0.00 MAD</span>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-xl bg-white border border-gray-200 flex items-center justify-center p-2">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal" class="h-6 mx-auto">
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-white border border-gray-200 flex items-center justify-center p-2">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa" class="h-4 mx-auto">
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-white border border-gray-200 flex items-center justify-center p-2">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" class="h-6 mx-auto">
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-black py-5 rounded-2xl shadow-xl shadow-emerald-100 transition-all text-xl flex items-center justify-center group">
                            Confirmer & Payer
                            <svg class="w-6 h-6 ml-3 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </button>
                    </div>
                </div>
                <button type="button" onclick="nextStep(3)" class="text-gray-500 hover:text-gray-900 font-bold py-4 px-8 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Retour aux options
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .hidden-checkbox:checked ~ .check-indicator { display: flex !important; }
    label:has(.hidden-checkbox:checked) {
        border-color: #2563eb;
        background-color: #f8fbff;
    }
</style>

<script>
    const basePricePerPerson = {{ $totalPrice }};
    const nbPassagers = {{ $nbPassagers }};

    function nextStep(step) {
        document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
        document.getElementById('step-content-' + step).classList.remove('hidden');

        // Update progress bar
        for (let i = 1; i <= 4; i++) {
            const circle = document.getElementById('step-circle-' + i);
            const label = document.getElementById('step-label-' + i);
            
            if (i < step) {
                circle.classList.replace('bg-white', 'bg-blue-600');
                circle.classList.replace('text-gray-400', 'text-white');
                circle.innerHTML = '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>';
                label.classList.replace('text-gray-400', 'text-blue-600');
            } else if (i === step) {
                // Current step
                circle.classList.add('bg-blue-600', 'text-white');
                circle.classList.remove('bg-white', 'text-gray-400');
                circle.innerText = i;
                label.classList.replace('text-gray-400', 'text-blue-600');
            } else {
                // Future steps
                circle.classList.add('bg-white', 'text-gray-400');
                circle.classList.remove('bg-blue-600', 'text-white');
                circle.innerText = i;
                label.classList.replace('text-blue-600', 'text-gray-400');
            }
        }

        if (step === 4) calculateTotal();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function calculateTotal() {
        let optionsTotal = 0;
        let optionsHtml = '';

        const insuranceCount = document.querySelectorAll('input[name$="[insurance]"]:checked').length;
        const snackCount = document.querySelectorAll('input[name$="[snack_box]"]:checked').length;
        const premiumCount = document.querySelectorAll('input[name$="[premium_seat]"]:checked').length;

        if (insuranceCount > 0) {
            optionsTotal += insuranceCount * 25;
            optionsHtml += `<div class="flex justify-between text-sm text-gray-600"><span>Assurance (x${insuranceCount})</span><span class="font-bold text-gray-900">+${insuranceCount * 25} MAD</span></div>`;
        }
        if (snackCount > 0) {
            optionsTotal += snackCount * 15;
            optionsHtml += `<div class="flex justify-between text-sm text-gray-600"><span>Snack-box (x${snackCount})</span><span class="font-bold text-gray-900">+${snackCount * 15} MAD</span></div>`;
        }
        if (premiumCount > 0) {
            optionsTotal += premiumCount * 30;
            optionsHtml += `<div class="flex justify-between text-sm text-gray-600"><span>Siège Premium (x${premiumCount})</span><span class="font-bold text-gray-900">+${premiumCount * 30} MAD</span></div>`;
        }

        document.getElementById('recap-base-price').innerText = (basePricePerPerson * nbPassagers).toFixed(2) + ' MAD';
        document.getElementById('recap-options').innerHTML = optionsHtml || '<p class="text-xs text-gray-400 italic">Aucune option sélectionnée</p>';
        document.getElementById('final-total').innerText = ((basePricePerPerson * nbPassagers) + optionsTotal).toFixed(2) + ' MAD';
    }
</script>
@endsection
