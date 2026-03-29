<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Models\AgreementRenewal;
use Illuminate\Http\Request;

class AgreementRenewalController extends Controller
{
    /**
     * List all pending renewals/amendments awaiting approval.
     */
    public function index()
    {
        $renewals = AgreementRenewal::with('agreement.branchRequest')
            ->latest()
            ->paginate(10);

        return view('admin.renewals.index', compact('renewals'));
    }

    /**
     * Show the Renew / Amend / Terminate form for an agreement.
     */
    public function create(Agreement $agreement)
    {
        return view('admin.renewals.create', compact('agreement'));
    }

    /**
     * Store a new renewal/amendment/termination record.
     */
    public function store(Request $request, Agreement $agreement)
    {
        $validated = $request->validate([
            'action'               => 'required|in:renew,amend,terminate',
            'reason'               => 'required|string',
            'amendment_notes'      => 'nullable|string',
            'new_monthly_rent'     => 'nullable|numeric|min:0',
            'new_start_date'       => 'nullable|date',
            'new_end_date'         => 'nullable|date|after:new_start_date',
            'new_payment_schedule' => 'nullable|string',
        ]);

        $validated['agreement_id'] = $agreement->id;
        $validated['status']       = 'pending_approval';

        AgreementRenewal::create($validated);

        return redirect()->route('admin.renewals.index')
            ->with('success', ucfirst($validated['action']) . ' request submitted and awaiting management approval.');
    }

    /**
     * Approve a renewal/amendment/termination.
     */
    public function approve(Request $request, AgreementRenewal $renewal)
    {
        $request->validate([
            'approval_remarks' => 'nullable|string',
        ]);

        $renewal->update([
            'status'           => 'approved',
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
            'approval_remarks' => $request->approval_remarks,
        ]);

        $agreement = $renewal->agreement;

        if ($renewal->action === 'renew' || $renewal->action === 'amend') {
            // Apply new terms to the original agreement
            $agreement->update(array_filter([
                'monthly_rent'     => $renewal->new_monthly_rent     ?? $agreement->monthly_rent,
                'start_date'       => $renewal->new_start_date       ?? $agreement->start_date,
                'end_date'         => $renewal->new_end_date         ?? $agreement->end_date,
                'payment_schedule' => $renewal->new_payment_schedule ?? $agreement->payment_schedule,
                'status'           => 'active',
                'legal_status'     => 'approved',
            ]));

            // Update the office rent in the system if rent changed
            if ($renewal->new_monthly_rent) {
                \App\Models\Office::where('name', $agreement->branchRequest->branch_name ?? '')
                    ->update(['price_per_month' => $renewal->new_monthly_rent]);
            }

        } elseif ($renewal->action === 'terminate') {
            // Terminate: deactivate agreement and the linked office branch
            $agreement->update(['status' => 'terminated']);

            \App\Models\Office::where('name', $agreement->branchRequest->branch_name ?? '')
                ->where('address', $agreement->property_address)
                ->update(['status' => 'closed']);
        }

        return redirect()->route('admin.renewals.index')
            ->with('success', $renewal->actionLabel() . ' approved and activated successfully.');
    }

    /**
     * Reject a renewal/amendment/termination request.
     */
    public function reject(Request $request, AgreementRenewal $renewal)
    {
        $request->validate([
            'approval_remarks' => 'nullable|string',
        ]);

        $renewal->update([
            'status'           => 'rejected',
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
            'approval_remarks' => $request->approval_remarks,
        ]);

        return redirect()->route('admin.renewals.index')
            ->with('success', 'Request rejected.');
    }
}
