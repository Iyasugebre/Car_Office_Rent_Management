<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Models\BranchRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AgreementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agreements = Agreement::with('branchRequest')->latest()->paginate(10);
        return view('admin.agreements.index', compact('agreements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $branchRequest = null;
        if ($request->has('branch_request_id')) {
            $branchRequest = BranchRequest::findOrFail($request->branch_request_id);
        }

        return view('admin.agreements.create', compact('branchRequest'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_request_id' => 'required|exists:branch_requests,id',
            'agreement_id' => 'required|string|unique:agreements,agreement_id',
            'landlord_name' => 'required|string|max:255',
            'property_address' => 'required|string',
            'monthly_rent' => 'required|numeric|min:0',
            'payment_schedule' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'contract_file' => 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:10240', // 10MB
        ]);

        if ($request->hasFile('contract_file')) {
            $path = $request->file('contract_file')->store('contracts', 'public');
            $validated['contract_path'] = $path;
        }

        $validated['status'] = 'active';

        Agreement::create($validated);

        return redirect()->route('admin.agreements.index')
            ->with('success', 'Rental agreement prepared and recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Agreement $agreement)
    {
        return view('admin.agreements.show', compact('agreement'));
    }

    /**
     * Authenticate (Approve/Reject) the agreement (Legal Review).
     */
    public function authenticate(Request $request, Agreement $agreement)
    {
        $validated = $request->validate([
            'legal_status' => 'required|in:approved,rejected',
            'legal_remarks' => 'nullable|string',
        ]);

        $agreement->update([
            'legal_status' => $validated['legal_status'],
            'legal_remarks' => $validated['legal_remarks'],
            'legal_reviewer_id' => auth()->id(),
            'legal_reviewed_at' => now(),
            // If approved, set the general status to active and activate the branch
            'status' => $validated['legal_status'] == 'approved' ? 'active' : 'draft',
        ]);

        if ($validated['legal_status'] == 'approved') {
            // Automatic ERP Activation: Create the Office record for the system
            \App\Models\Office::create([
                'name' => $agreement->branchRequest->branch_name,
                'address' => $agreement->property_address,
                'city' => $agreement->branchRequest->location,
                'type' => 'branch',
                'price_per_month' => $agreement->monthly_rent,
                'status' => 'active',
            ]);

            // Mark the original proposal as active/completed
            $agreement->branchRequest->update(['status' => 'active']);
        }

        return redirect()->route('admin.agreements.show', $agreement)
            ->with('success', 'Authentication recorded. ' . ($validated['legal_status'] == 'approved' ? 'Branch has been officially activated and added to inventory.' : 'Agreement Rejected.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agreement $agreement)
    {
        if ($agreement->contract_path) {
            Storage::disk('public')->delete($agreement->contract_path);
        }
        
        $agreement->delete();

        return redirect()->route('admin.agreements.index')
            ->with('success', 'Agreement record removed.');
    }
}
