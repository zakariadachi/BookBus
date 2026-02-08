<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Segment;
use App\Models\Booking;
use App\Models\Passenger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $segmentIds = explode(',', $request->segment_ids);
        $segments = Segment::with(['bus', 'departureGare.ville', 'arrivalGare.ville'])
            ->whereIn('id', $segmentIds)
            ->get();

        if ($segments->isEmpty()) {
            return redirect()->route('home')->with('error', 'Aucun trajet sélectionné.');
        }

        $nbPassagers = (int)$request->get('passengers', 1);
        $totalPrice = (float)$request->get('total_price', 0);

        return view('booking.create', compact('segments', 'nbPassagers', 'totalPrice', 'segmentIds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'segment_ids' => 'required',
            'passengers' => 'required|array|min:1',
            'passengers.*.nom_complet' => 'required|string|max:255',
            'passengers.*.cin' => 'required|string|max:20',
            'passengers.*.date_naissance' => 'required|date',
            'passengers.*.type' => 'required|in:Adulte,Enfant',
        ]);

        $segmentIds = explode(',', $request->segment_ids);
        $segments = Segment::whereIn('id', $segmentIds)->get();
        $nbPassagers = count($request->passengers);

        // Simple availability check
        foreach ($segments as $segment) {
            $bookedCount = DB::table('booking_segment')
                ->join('bookings', 'booking_segment.booking_id', '=', 'bookings.id')
                ->where('booking_segment.segment_id', $segment->id)
                ->sum('bookings.nb_passagers');
            
            if (($bookedCount + $nbPassagers) > $segment->bus->capacite) {
                return back()->with('error', "Plus de places disponibles pour le trajet {$segment->departureGare->ville->nom} -> {$segment->arrivalGare->ville->nom}.");
            }
        }

        DB::beginTransaction();
        try {
            $basePricePerPerson = (float)$request->base_price;
            $totalBookingPrice = 0;

            $booking = Booking::create([
                'user_id' => auth()->id(),
                'total_price' => 0,
                'status' => 'confirmed',
                'nb_passagers' => $nbPassagers,
            ]);

            $booking->segments()->attach($segmentIds);

            foreach ($request->passengers as $pData) {
                $passengerPrice = $basePricePerPerson;
                
                // Options calculation
                if (isset($pData['insurance'])) $passengerPrice += 25;
                if (isset($pData['snack_box'])) $passengerPrice += 15;
                if (isset($pData['premium_seat'])) $passengerPrice += 30;

                Passenger::create([
                    'booking_id' => $booking->id,
                    'nom_complet' => $pData['nom_complet'],
                    'cin' => $pData['cin'],
                    'date_naissance' => $pData['date_naissance'],
                    'type' => $pData['type'],
                    'insurance' => isset($pData['insurance']),
                    'snack_box' => isset($pData['snack_box']),
                    'premium_seat' => isset($pData['premium_seat']),
                ]);

                $totalBookingPrice += $passengerPrice;
            }

            $booking->update(['total_price' => $totalBookingPrice]);

            DB::commit();
            return redirect()->route('booking.confirmation', $booking->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Booking storage failed: " . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la réservation.');
        }
    }

    public function confirmation($id)
    {
        $booking = Booking::with(['passengers', 'segments.departureGare.ville', 'segments.arrivalGare.ville'])->findOrFail($id);
        return view('booking.confirmation', compact('booking'));
    }
}
