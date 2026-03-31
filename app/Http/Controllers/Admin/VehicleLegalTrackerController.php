<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleLegalTrackerController extends Controller
{
    /**
     * Display a listing of vehicle legal statuses (Bolo & Inspection).
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $query = Car::with('office')->where('status', '!=', 'decommissioned');

        $cars = $query->get()->map(function ($car) {
            $car->bolo_days = $car->bolo_days_remaining;
            $car->inspection_days = $car->inspection_days_remaining;
            $car->bolo_health = $car->bolo_status;
            $car->inspection_health = $car->inspection_status;
            return $car;
        });

        if ($filter === 'overdue') {
            $cars = $cars->filter(fn($c) => $c->bolo_health === 'overdue' || $c->inspection_health === 'overdue');
        } elseif ($filter === 'warning') {
            $cars = $cars->filter(fn($c) => $c->bolo_health === 'warning' || $c->inspection_health === 'warning');
        }

        // Sort by most urgent (overdue first)
        $cars = $cars->sortBy(function ($car) {
            $minDays = min($car->bolo_days ?? 999, $car->inspection_days ?? 999);
            return $minDays;
        });

        $stats = [
            'total' => Car::where('status', '!=', 'decommissioned')->count(),
            'overdue' => $cars->filter(fn($c) => $c->bolo_health === 'overdue' || $c->inspection_health === 'overdue')->count(),
            'warning' => $cars->filter(fn($c) => ($c->bolo_health === 'warning' || $c->inspection_health === 'warning') && !($c->bolo_health === 'overdue' || $c->inspection_health === 'overdue'))->count(),
        ];

        return view('admin.legal.index', compact('cars', 'stats', 'filter'));
    }

    /**
     * Show the form for editing the legal documents of a specific vehicle.
     */
    public function edit(Car $car)
    {
        return view('admin.legal.edit', compact('car'));
    }

    /**
     * Update legal documents and expiry dates.
     */
    public function update(Request $request, Car $car)
    {
        $validated = $request->validate([
            'registration_number' => 'nullable|string|max:255',
            'bolo_expiry_date' => 'nullable|date',
            'inspection_expiry_date' => 'nullable|date',
            'bolo_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'inspection_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('bolo_certificate')) {
            // Delete old if exists
            if ($car->bolo_certificate_path) {
                Storage::disk('public')->delete($car->bolo_certificate_path);
            }
            $validated['bolo_certificate_path'] = $request->file('bolo_certificate')->store('certificates/bolo', 'public');
        }

        if ($request->hasFile('inspection_certificate')) {
            // Delete old if exists
            if ($car->inspection_certificate_path) {
                Storage::disk('public')->delete($car->inspection_certificate_path);
            }
            $validated['inspection_certificate_path'] = $request->file('inspection_certificate')->store('certificates/inspection', 'public');
        }

        $car->update($validated);

        return redirect()->route('admin.legal-tracker.index')
            ->with('success', "Legal records for {$car->plate_number} updated successfully.");
    }
}
