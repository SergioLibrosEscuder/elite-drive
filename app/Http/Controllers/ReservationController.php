<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['user', 'vehicle'])->orderBy('created_at', 'desc')->get();

        return response()->json($reservations);
    }

    public function show($id)
    {
        $reservation = Reservation::with(['user', 'vehicle'])->findOrFail($id);

        return response()->json($reservation);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'amount' => 'required|numeric|min:0',
        ]);

        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        $reservation = Reservation::create($data);

        return response()->json($reservation, 201);
    }

    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $data = $request->validate([
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'amount' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:pending,confirmed,cancelled,completed',
        ]);

        $reservation->update($data);

        return response()->json($reservation);
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return response()->json(null, 204);
    }

    public function userReservations()
    {
        $reservations = Reservation::with(['vehicle', 'user'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reservations);
    }

    public function cancel($id)
    {
        return DB::transaction(function () use ($id) {
            $reservation = Reservation::where('id', $id)
                ->where('user_id', Auth::id())
                ->whereIn('status', ['confirmed', 'pending'])
                ->firstOrFail();

            if ($reservation->status === 'completed') {
                return response()->json(['error' => 'Completed reservations cannot be cancelled.'], 400);
            }

            if ($reservation->status === 'confirmed') {
                $vehicle = $reservation->vehicle;
                if ($vehicle) {
                    $vehicle->status = 'available';
                    $vehicle->save();
                }
            }
            $reservation->status = 'cancelled';
            $reservation->save();

            return response()->json($reservation);
        });
    }

    public function confirm($id)
    {
        return DB::transaction(function () use ($id) {
            $reservation = Reservation::with('vehicle')
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $vehicle = $reservation->vehicle;

            if ($reservation->status !== 'pending') {
                return response()->json(['error' => 'Only pending reservations can be confirmed.'], 400);
            }

            if ($vehicle->status !== 'available') {
                return response()->json(['error' => 'The vehicle is no longer available for reservation.'], 400);
            }

            $vehicle->status = 'reserved';
            $vehicle->save();

            $reservation->status = 'confirmed';
            $reservation->save();

            return response()->json($reservation);
        });
    }
}
