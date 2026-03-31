<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = ServiceRequest::with(['car', 'requester', 'fleetManager'])->latest()->paginate(10);
        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cars = Car::where('status', '!=', 'maintenance')->get();
        return view('admin.services.create', compact('cars'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'problem_description' => 'required|string',
            'service_type' => 'required|in:routine,repair,inspection,other',
            'urgency_level' => 'required|in:low,medium,high,critical',
        ]);

        $validated['requester_id'] = auth()->id();
        $validated['status'] = 'pending';

        $service = ServiceRequest::create($validated);
        $admins = \App\Models\User::where('role', 'admin')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\MaintenanceRequestNotification($service, 'pending', 'New maintenance request submitted and requires approval.'));

        return redirect()->route('admin.services.index')->with('success', 'Service request submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceRequest $service)
    {
        return view('admin.services.show', compact('service'));
    }

    /**
     * Approve or Assign service.
     */
    public function assign(Request $request, ServiceRequest $service)
    {
        $validated = $request->validate([
            'service_provider' => 'required|string|max:255',
        ]);

        $service->update([
            'status' => 'approved',
            'service_provider' => $validated['service_provider'],
            'fleet_manager_id' => auth()->id(),
        ]);

        // Option: Change car status to maintenance
        $service->car->update(['status' => 'maintenance']);
        $service->requester->notify(new \App\Notifications\MaintenanceRequestNotification($service, 'approved', "Service request for {$service->car->plate_number} has been approved."));

        return redirect()->route('admin.services.show', $service)->with('success', 'Service localized to provider and approved.');
    }

    /**
     * Execute and complete the service with optional report.
     */
    public function complete(Request $request, ServiceRequest $service)
    {
        $validated = $request->validate([
            'cost' => 'nullable|numeric|min:0',
            'service_report_file' => 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:10240',
        ]);

        if ($request->hasFile('service_report_file')) {
            $path = $request->file('service_report_file')->store('service_reports', 'public');
            $service->update(['service_report_path' => $path]);
        }

        $service->update([
            'status' => 'completed',
            'cost' => $validated['cost'],
        ]);

        // Car is available again. Also log the maintenance tracker for Periodic Tracking
        $carUpdates = ['status' => 'available'];
        if (in_array($service->service_type, ['routine', 'inspection'])) {
            $carUpdates['last_service_date'] = now();
            $carUpdates['last_service_mileage'] = $service->car->mileage;
        }
        $service->car->update($carUpdates);
        $service->requester->notify(new \App\Notifications\MaintenanceRequestNotification($service, 'completed', "Maintenance for {$service->car->plate_number} successfully completed."));

        return redirect()->route('admin.services.show', $service)->with('success', 'Service completed. Vehicle maintenance history updated.');
    }

    // Add rejecting or cancel feature
    public function destroy(ServiceRequest $service)
    {
        $service->update(['status' => 'rejected', 'fleet_manager_id' => auth()->id()]);
        $service->requester->notify(new \App\Notifications\MaintenanceRequestNotification($service, 'rejected', "Service request for car {$service->car->plate_number} has been rejected."));
        return redirect()->route('admin.services.index')->with('success', 'Service request rejected/cancelled.');
    }
}
