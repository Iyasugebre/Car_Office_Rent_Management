<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BranchRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $requests = BranchRequest::with('requester')->latest()->paginate(10);
        return view('admin.branch_requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.branch_requests.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'proposed_office' => 'required|string',
            'landlord_details' => 'required|string',
            'estimated_rent' => 'required|numeric|min:0',
            'priority' => 'required|in:low,medium,high',
        ]);

        $validated['requester_id'] = Auth::id();
        $validated['status'] = 'submitted';

        BranchRequest::create($validated);

        return redirect()->route('admin.branch_requests.index')
            ->with('success', 'Branch request submitted successfully and is now under review.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BranchRequest $branchRequest)
    {
        return view('admin.branch_requests.show', compact('branchRequest'));
    }

    /**
     * Update the specified resource's status.
     */
    public function update(Request $request, BranchRequest $branchRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:submitted,review,approved,rejected,active',
            'remarks' => 'nullable|string',
        ]);

        $branchRequest->update($validated);

        // Optional: If 'active', we could trigger office creation logic here in a real ERP.

        return redirect()->route('admin.branch_requests.index')
            ->with('success', 'Branch request status updated to ' . $validated['status'] . '.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BranchRequest $branchRequest)
    {
        $branchRequest->delete();

        return redirect()->route('admin.branch_requests.index')
            ->with('success', 'Branch request deleted safely.');
    }
}
