<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Office;
use Illuminate\Http\Request;

class OfficeManagerController extends Controller
{
    public function index()
    {
        $offices = Office::latest()->paginate(10);
        return view('admin.offices.index', compact('offices'));
    }

    public function create()
    {
        return view('admin.offices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'phone' => 'nullable|string',
            'type' => 'required|string',
            'price_per_month' => 'nullable|numeric',
            'status' => 'required|string',
        ]);

        Office::create($validated);
        return redirect()->route('admin.offices.index')->with('success', 'Office created successfully.');
    }

    public function edit(Office $office)
    {
        return view('admin.offices.edit', compact('office'));
    }

    public function update(Request $request, Office $office)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'phone' => 'nullable|string',
            'type' => 'required|string',
            'price_per_month' => 'nullable|numeric',
            'status' => 'required|string',
        ]);

        $office->update($validated);
        return redirect()->route('admin.offices.index')->with('success', 'Office updated successfully.');
    }

    public function destroy(Office $office)
    {
        $office->delete();
        return redirect()->route('admin.offices.index')->with('success', 'Office deleted successfully.');
    }
}
