<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Vehicle;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            'user_id' => 'sometimes|exists:users,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        $vehicle = Vehicle::findOrFail($data['vehicle_id']);

        $data['user_id'] = $data['user_id'] ?? Auth::id();

        if ($vehicle->status !== 'available') {
            return response()->json(['error' => 'The vehicle is not available for reservation.'], 400);
        }

        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        $hours = ceil($startDate->floatDiffInHours($endDate));

        if ($hours < 1) {
            $hours = 1;
        }

        $data['amount'] = $hours * $vehicle->hourly_price;

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
            'status' => 'sometimes|in:pending,confirmed,cancelled,completed',
        ]);

        if ($request->has('status')) {
            $oldStatus = $reservation->status;
            $newStatus = $request->status;

            if ($oldStatus !== $newStatus) {
                if ($oldStatus === 'completed') {
                    return response()->json(['error' => 'Completed reservations cannot be modified.'], 400);
                }

                if ($newStatus === 'confirmed') {
                    $vehicle = $reservation->vehicle;
                    if ($vehicle->status !== 'available') {
                        return response()->json(['error' => 'The vehicle is no longer available for reservation.'], 400);
                    }
                    $vehicle->status = 'reserved';
                    $vehicle->save();
                } elseif ($oldStatus === 'confirmed' && $newStatus === 'cancelled') {
                    $vehicle = $reservation->vehicle;
                    if ($vehicle) {
                        $vehicle->status = 'available';
                        $vehicle->save();
                    }
                } elseif ($newStatus === 'pending' && $oldStatus === 'confirmed') {
                    $vehicle = $reservation->vehicle;
                    if ($vehicle) {
                        $vehicle->status = 'available';
                        $vehicle->save();
                    }
                } elseif ($newStatus === 'completed' && $oldStatus === 'confirmed') {
                    $vehicle = $reservation->vehicle;
                    if ($vehicle) {
                        $vehicle->status = 'available';
                        $vehicle->save();
                    }
                }
            }
        }

        if ($request->has('start_date') || $request->has('end_date')) {
            $startDate = $request->has('start_date') ? Carbon::parse($data['start_date']) : Carbon::parse($reservation->start_date);
            $endDate = $request->has('end_date') ? Carbon::parse($data['end_date']) : Carbon::parse($reservation->end_date);

            if ($endDate <= $startDate) {
                return response()->json(['error' => 'End date must be after start date.'], 400);
            }

            $hours = ceil($startDate->floatDiffInHours($endDate));

            if ($hours < 1) {
                $hours = 1;
            }

            $vehicle = Vehicle::findOrFail($reservation->vehicle_id);
            $data['amount'] = $hours * $vehicle->hourly_price;
        }

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
            $query = Reservation::where('id', $id)
                ->whereIn('status', ['confirmed', 'pending']);

            if (Auth::user()->role !== 'admin') {
                $query->where('user_id', Auth::id());
            }

            $reservation = $query->firstOrFail();

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
            $query = Reservation::with('vehicle')
                ->where('id', $id);

            if (Auth::user()->role !== 'admin') {
                $query->where('user_id', Auth::id());
            }

            $reservation = $query->firstOrFail();

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
