<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Office;
use Illuminate\Http\Request;

class CarManagerController extends Controller
{
    public function index()
    {
        $cars = Car::with('office')->latest()->paginate(10);
        return view('admin.cars.index', compact('cars'));
    }

    public function create()
    {
        $offices = Office::all();
        return view('admin.cars.create', compact('offices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'office_id' => 'required|exists:offices,id',
            'make' => 'required|string',
            'model' => 'required|string',
            'year' => 'required|integer',
            'plate_number' => 'required|string|unique:cars,plate_number',
            'price_per_day' => 'required|numeric',
            'status' => 'required|string',
            'mileage' => 'nullable|integer|min:0',
            'last_service_date' => 'nullable|date',
            'registration_number' => 'nullable|string',
            'bolo_expiry_date' => 'nullable|date',
            'inspection_expiry_date' => 'nullable|date',
            'bolo_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'inspection_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('bolo_certificate')) {
            $validated['bolo_certificate_path'] = $request->file('bolo_certificate')->store('certificates/bolo', 'public');
        }
        if ($request->hasFile('inspection_certificate')) {
            $validated['inspection_certificate_path'] = $request->file('inspection_certificate')->store('certificates/inspection', 'public');
        }

        $validated['last_service_mileage'] = $validated['mileage'] ?? 0;

        Car::create($validated);
        return redirect()->route('admin.cars.index')->with('success', 'Car created successfully.');
    }

    public function edit(Car $car)
    {
        $offices = Office::all();
        return view('admin.cars.edit', compact('car', 'offices'));
    }

    public function update(Request $request, Car $car)
    {
        $validated = $request->validate([
            'office_id' => 'required|exists:offices,id',
            'make' => 'required|string',
            'model' => 'required|string',
            'year' => 'required|integer',
            'plate_number' => 'required|string|unique:cars,plate_number,' . $car->id,
            'price_per_day' => 'required|numeric',
            'status' => 'required|string',
            'mileage' => 'nullable|integer|min:0',
            'last_service_date' => 'nullable|date',
            'registration_number' => 'nullable|string',
            'bolo_expiry_date' => 'nullable|date',
            'inspection_expiry_date' => 'nullable|date',
            'bolo_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'inspection_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('bolo_certificate')) {
            $validated['bolo_certificate_path'] = $request->file('bolo_certificate')->store('certificates/bolo', 'public');
        }
        if ($request->hasFile('inspection_certificate')) {
            $validated['inspection_certificate_path'] = $request->file('inspection_certificate')->store('certificates/inspection', 'public');
        }

        $car->update($validated);
        return redirect()->route('admin.cars.index')->with('success', 'Car updated successfully.');
    }

    public function destroy(Car $car)
    {
        $car->delete();
        return redirect()->route('admin.cars.index')->with('success', 'Car deleted successfully.');
    }
}
