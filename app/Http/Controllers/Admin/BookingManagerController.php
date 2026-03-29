<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingManagerController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'car', 'office'])->latest()->paginate(10);
        return view('admin.bookings.index', compact('bookings'));
    }

    public function create()
    {
        $users = \App\Models\User::all();
        $cars = \App\Models\Car::where('status', 'available')->get();
        $offices = \App\Models\Office::where('status', 'available')->where('type', 'rental_unit')->get();
        return view('admin.bookings.create', compact('users', 'cars', 'offices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'car_id' => 'nullable|exists:cars,id',
            'office_id' => 'nullable|exists:offices,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_price' => 'required|numeric',
        ]);

        $validated['status'] = 'confirmed';

        Booking::create($validated);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking created successfully.');
    }

    public function show(Booking $booking)
    {
        return view('admin.bookings.show', compact('booking'));
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,confirmed,active,completed,cancelled',
        ]);

        $booking->update($validated);
        return redirect()->route('admin.bookings.index')->with('success', 'Booking status updated.');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted.');
    }
}
