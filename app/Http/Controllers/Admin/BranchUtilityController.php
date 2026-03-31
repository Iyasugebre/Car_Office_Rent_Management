<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BranchUtility;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BranchUtilityController extends Controller
{
    /**
     * Display a listing of branch utilities.
     */
    public function index()
    {
        $offices = Office::with('utilities')->where('type', 'branch')->get();
        return view('admin.utilities.index', compact('offices'));
    }

    /**
     * Store a new utility account for a branch.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'office_id' => 'required|exists:offices,id',
            'utility_type' => 'required|in:electricity,water,telephone,internet',
            'provider' => 'required|string|max:255',
            'account_number' => 'required|string|max:100',
            'payment_cycle' => 'required|in:monthly,quarterly,annual',
            'next_due_at' => 'required|date',
        ]);

        BranchUtility::create($validated);

        return redirect()->route('admin.branch-utilities.index')
            ->with('success', 'Utility account registered successfully.');
    }

    /**
     * Update utility details.
     */
    public function update(Request $request, BranchUtility $utility)
    {
        $validated = $request->validate([
            'provider' => 'required|string|max:255',
            'account_number' => 'required|string|max:100',
            'payment_cycle' => 'required|in:monthly,quarterly,annual',
            'next_due_at' => 'required|date',
            'is_active' => 'boolean',
        ]);

        $utility->update($validated);

        return redirect()->route('admin.branch-utilities.index')
            ->with('success', 'Utility account updated.');
    }

    /**
     * Record a payment for a utility bill.
     */
    public function recordPayment(BranchUtility $utility)
    {
        $lastPaid = now();
        
        // Calculate next due date based on cycle
        $nextDue = match($utility->payment_cycle) {
            'monthly' => Carbon::parse($utility->next_due_at)->addMonth(),
            'quarterly' => Carbon::parse($utility->next_due_at)->addMonths(3),
            'annual' => Carbon::parse($utility->next_due_at)->addYear(),
            default => Carbon::parse($utility->next_due_at)->addMonth(),
        };

        $utility->update([
            'last_paid_at' => $lastPaid,
            'next_due_at' => $nextDue,
        ]);

        return redirect()->route('admin.branch-utilities.index')
            ->with('success', "Payment recorded for {$utility->utility_type}. Next due: {$nextDue->format('M d, Y')}");
    }

    /**
     * Delete a utility account.
     */
    public function destroy(BranchUtility $utility)
    {
        $utility->delete();
        return redirect()->route('admin.branch-utilities.index')
            ->with('success', 'Utility account removed.');
    }
}
