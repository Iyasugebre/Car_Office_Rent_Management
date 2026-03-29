<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Office;
use App\Models\Booking;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_cars' => Car::count(),
            'total_offices' => Office::count(),
            'active_bookings' => Booking::where('status', 'active')->count(),
            'total_bookings' => Booking::count(),
            'total_revenue' => Booking::sum('total_price'),
        ];

        $recent_bookings = Booking::with(['car', 'office'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_bookings'));
    }
}
