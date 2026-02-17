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
    // Get all reservations
    public function index()
    {
        // Make sure reservations have user and vehicle associated
        $reservations = Reservation::with(['user', 'vehicle'])->orderBy('created_at', 'desc')->get();

        return response()->json($reservations);
    }

    // Show a single concrete reservation
    public function show($id)
    {
        // Make sure that reservation has user and vehicle associated
        $reservation = Reservation::with(['user', 'vehicle'])->findOrFail($id);

        return response()->json($reservation);
    }

    // Create a reservation
    public function store(Request $request)
    {
        // Validate recieved data
        $data = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'user_id' => 'sometimes|exists:users,id',
            // Make sure start date has not passed
            'start_date' => 'required|date|after_or_equal:today',
            // Make sure end date is after start date
            'end_date' => 'required|date|after:start_date',
        ]);

        $vehicle = Vehicle::findOrFail($data['vehicle_id']);

        // If no user id is passed, the user is set to be the actual user
        $data['user_id'] = $data['user_id'] ?? Auth::id();

        // Make sure a user cannot reserve an unavailable vehicle
        if ($vehicle->status !== 'available') {
            return response()->json(['error' => 'The vehicle is not available for reservation.'], 400);
        }

        // Set dates with Carbon api
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        // Calculate number of hours the reservation longs
        $hours = ceil($startDate->floatDiffInHours($endDate));

        // Make sure at least one hour is payed
        if ($hours < 1) {
            $hours = 1;
        }

        // Calculate total price 
        $data['amount'] = $hours * $vehicle->hourly_price;

        // Default reservation status is always pending
        $data['status'] = 'pending';

        $reservation = Reservation::create($data);

        return response()->json($reservation, 201);
    }

    // Update reservation data
    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        // Validate recieved data
        $data = $request->validate([
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'status' => 'sometimes|in:pending,confirmed,cancelled,completed',
        ]);

        // Control cases if a status is recieved
        if ($request->has('status')) {
            $oldStatus = $reservation->status;
            $newStatus = $request->status;

            // If status has changed
            if ($oldStatus !== $newStatus) {
                // Anyone can change a completed reservation status
                if ($oldStatus === 'completed') {
                    return response()->json(['error' => 'Completed reservations cannot be modified.'], 400);
                }

                // Validate if new status can be confirmed
                if ($newStatus === 'confirmed') {
                    $vehicle = $reservation->vehicle;
                    // You cannot confirm a reservation with unavailable vehicle
                    if ($vehicle->status !== 'available') {
                        return response()->json(['error' => 'The vehicle is no longer available for reservation.'], 400);
                    }
                    // Change vehicle status when confirming reservation
                    $vehicle->status = 'reserved';
                    $vehicle->save();

                    // Cancel a confirmed reservation
                } elseif ($oldStatus === 'confirmed' && $newStatus === 'cancelled') {
                    $vehicle = $reservation->vehicle;
                    // If vehicle is found the status is changed to available
                    if ($vehicle) {
                        $vehicle->status = 'available';
                        $vehicle->save();
                    }

                    // Return a confirmed reservation into pending
                } elseif ($newStatus === 'pending' && $oldStatus === 'confirmed') {
                    $vehicle = $reservation->vehicle;
                    // If vehicle is found the status is changed to available
                    if ($vehicle) {
                        $vehicle->status = 'available';
                        $vehicle->save();
                    }
                    // Complete a confirmed reservation
                } elseif ($newStatus === 'completed' && $oldStatus === 'confirmed') {
                    $vehicle = $reservation->vehicle;
                    // If vehicle is found the status is changed to available
                    if ($vehicle) {
                        $vehicle->status = 'available';
                        $vehicle->save();
                    }
                }
            }
        }

        // If is recieved a date change
        if ($request->has('start_date') || $request->has('end_date')) {
            // Set dates depending on if is recieved that date
            $startDate = $request->has('start_date') ? Carbon::parse($data['start_date']) : Carbon::parse($reservation->start_date);
            $endDate = $request->has('end_date') ? Carbon::parse($data['end_date']) : Carbon::parse($reservation->end_date);

            // Ensure the end date is after the start date
            if ($endDate <= $startDate) {
                return response()->json(['error' => 'End date must be after start date.'], 400);
            }

            // Recalculate total number of hours
            $hours = ceil($startDate->floatDiffInHours($endDate));

            // Ensure to pay a minimum of one hour
            if ($hours < 1) {
                $hours = 1;
            }

            $vehicle = Vehicle::findOrFail($reservation->vehicle_id);
            // Recalculate total amount of price of reservation
            $data['amount'] = $hours * $vehicle->hourly_price;
        }

        $reservation->update($data);

        return response()->json($reservation);
    }

    // Delete a reservation
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return response()->json(null, 204);
    }

    // Get all reservations associated to current user
    public function userReservations()
    {
        // Make sure the reservation has vehicle and user associated
        $reservations = Reservation::with(['vehicle', 'user'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reservations);
    }

    // Cancel a reservation
    public function cancel($id)
    {
        // Begin a transaction to ensure correct working
        return DB::transaction(function () use ($id) {
            $query = Reservation::where('id', $id)
                // Ensure status is not cancelled nor completed
                ->whereIn('status', ['confirmed', 'pending']);

            // If current user is not admin, the user is set to the current session
            if (Auth::user()->role !== 'admin') {
                $query->where('user_id', Auth::id());
            }

            $reservation = $query->firstOrFail();

            // If actual status is completed, you cannot cancel it
            if ($reservation->status === 'completed') {
                return response()->json(['error' => 'Completed reservations cannot be cancelled.'], 400);
            }

            // If actual status is confirmed, the vehicle status is changeed to available
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

    // Confirm a reservation
    public function confirm($id)
    {
        // Begin a transaction to ensure correct working
        return DB::transaction(function () use ($id) {
            // Ensure the reservation has a vehicle associated
            $query = Reservation::with('vehicle')
                ->where('id', $id);

            // If current user is not admin, the user is set to the current session
            if (Auth::user()->role !== 'admin') {
                $query->where('user_id', Auth::id());
            }

            $reservation = $query->firstOrFail();

            $vehicle = $reservation->vehicle;

            // Make sure ony pending reservations can be confirmed
            if ($reservation->status !== 'pending') {
                return response()->json(['error' => 'Only pending reservations can be confirmed.'], 400);
            }

            // Make sure associated vehicle is available
            if ($vehicle->status !== 'available') {
                return response()->json(['error' => 'The vehicle is no longer available for reservation.'], 400);
            }

            // Change associated vehicle status
            $vehicle->status = 'reserved';
            $vehicle->save();

            $reservation->status = 'confirmed';
            $reservation->save();

            return response()->json($reservation);
        });
    }
}
