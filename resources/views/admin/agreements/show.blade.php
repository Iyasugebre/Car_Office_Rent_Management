@extends('layouts.admin')

@section('title', 'Agreement Review & Authentication')

@section('content')
    <div class="table-container">
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 2.5rem; max-width: 1000px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <!-- Header section -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem;">
                <div>
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.8125rem; font-weight: 700; color: var(--primary-color); background: rgba(99, 102, 241, 0.1); padding: 0.25rem 0.75rem; border-radius: 0.5rem;">{{ $agreement->agreement_id }}</span>
                        <h3 style="margin: 0; font-size: 1.5rem; color: var(--dark-bg);">Lease Agreement Detail</h3>
                    </div>
                    <p style="margin: 0; color: var(--text-muted); font-size: 0.875rem;">Linked to Branch Proposal: <strong>{{ $agreement->branchRequest->reference_number }}</strong></p>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <span class="status-badge status-{{ strtolower($agreement->status) }}" style="padding: 0.5rem 1rem;">{{ ucfirst($agreement->status) }}</span>
                    <span class="status-badge status-{{ $agreement->legal_status == 'approved' ? 'success' : ($agreement->legal_status == 'rejected' ? 'danger' : 'warning') }}" style="padding: 0.5rem 1rem; border: 1px solid currentColor; background: transparent;">
                        Legal: {{ ucfirst($agreement->legal_status) }}
                    </span>
                    @if(in_array($agreement->status, ['active', 'draft']))
                    <a href="{{ route('admin.renewals.create', $agreement) }}"
                       style="display:flex; align-items:center; gap:.375rem; background:var(--dark-bg); color:white; padding:.5rem 1.125rem; border-radius:.5rem; text-decoration:none; font-size:.8125rem; font-weight:700; white-space:nowrap;">
                        <i data-feather="refresh-cw" style="width:14px;height:14px;"></i> Renew / Amend / Terminate
                    </a>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div style="background: #ecfdf5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #10b981;">
                    {{ session('success') }}
                </div>
            @endif

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
                <!-- Column 1: Contract Details -->
                <div>
                    <h4 style="font-size: 0.875rem; text-transform: uppercase; color: var(--primary-color); margin-bottom: 1.25rem; letter-spacing: 0.05em;">Agreement Details</h4>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Landlord / Owner</label>
                        <div style="font-weight: 600;">{{ $agreement->landlord_name }}</div>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Property Address</label>
                        <div style="font-size: 0.875rem; color: var(--text-main); line-height: 1.5;">{{ $agreement->property_address }}</div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                        <div>
                            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Rent Amount</label>
                            <div style="font-weight: 700; font-family: monospace; font-size: 1.125rem;">${{ number_format($agreement->monthly_rent, 2) }}</div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Schedule</label>
                            <div style="font-weight: 600;">{{ $agreement->payment_schedule }}</div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                        <div>
                            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Effective From</label>
                            <div style="font-size: 0.875rem;">{{ $agreement->start_date }}</div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Expiry Date</label>
                            <div style="font-size: 0.875rem;">{{ $agreement->end_date }}</div>
                        </div>
                    </div>
                    {{-- Contract Period Tracker --}}
                    @php
                        $start = \Carbon\Carbon::parse($agreement->start_date);
                        $end   = \Carbon\Carbon::parse($agreement->end_date);
                        $today = \Carbon\Carbon::today();
                        $totalDays     = $start->diffInDays($end);
                        $daysRemaining = max(0, $today->diffInDays($end, false));
                        $daysElapsed   = $totalDays - $daysRemaining;
                        $progress      = $totalDays > 0 ? min(100, round(($daysElapsed / $totalDays) * 100)) : 100;
                        $barColor = match(true) {
                            $daysRemaining <= 30  => '#ef4444',
                            $daysRemaining <= 60  => '#f97316',
                            $daysRemaining <= 90  => '#f59e0b',
                            default               => '#10b981',
                        };
                        $urgencyLabel = match(true) {
                            $daysRemaining <= 0   => 'Expired',
                            $daysRemaining <= 30  => 'Critical – Renew Now',
                            $daysRemaining <= 60  => 'Expiring Soon',
                            $daysRemaining <= 90  => 'Upcoming Renewal',
                            default               => 'On Track',
                        };
                    @endphp
                    <div style="margin-top: 2rem; padding: 1.25rem; background: #f8fafc; border-radius: 1rem; border: 1px solid var(--border-color);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                            <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Contract Duration</span>
                            <span style="font-size: 0.75rem; font-weight: 700; color: {{ $barColor }}; background: {{ $barColor }}18; padding: 0.2rem 0.6rem; border-radius: 999px; border: 1px solid {{ $barColor }}40;">{{ $urgencyLabel }}</span>
                        </div>
                        <div style="background: #e2e8f0; border-radius: 999px; height: 8px; overflow: hidden; margin-bottom: 0.75rem;">
                            <div style="background: {{ $barColor }}; height: 100%; width: {{ $progress }}%; border-radius: 999px; transition: width 0.6s ease;"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-muted);">
                            <span>{{ $agreement->start_date }}</span>
                            <span style="font-weight: 700; color: {{ $barColor }};">
                                {{ $daysRemaining > 0 ? $daysRemaining . ' days remaining' : 'Expired' }}
                            </span>
                            <span>{{ $agreement->end_date }}</span>
                        </div>
                        <div style="margin-top: 0.75rem; display: flex; gap: 1rem; font-size: 0.75rem; color: var(--text-muted);">
                            <span>Payment: <strong style="color: var(--dark-bg);">{{ $agreement->payment_schedule }}</strong></span>
                            <span>Total term: <strong style="color: var(--dark-bg);">{{ $totalDays }} days</strong></span>
                        </div>
                    </div>

                        {{-- Branch Activation Banner --}}
                        @if($agreement->legal_status == 'approved')
                        @php
                            $registeredBranch = \App\Models\Office::where('name', $agreement->branchRequest->branch_name)
                                ->where('address', $agreement->property_address)
                                ->first();
                        @endphp
                        <div style="margin-top: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #f0fdf4, #dcfce7); border-radius: 1rem; border: 1px solid #10b981; position: relative; overflow: hidden;">
                            <div style="position: absolute; top: -10px; right: -10px; width: 80px; height: 80px; background: rgba(16, 185, 129, 0.1); border-radius: 50%;"></div>
                            <div style="display: flex; align-items: flex-start; gap: 1rem;">
                                <div style="background: #10b981; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i data-feather="check-circle" style="color: white; width: 20px; height: 20px;"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 800; font-size: 0.875rem; color: #166534; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.375rem;">Branch Officially Registered</div>
                                    <div style="font-size: 0.8125rem; color: #15803d; margin-bottom: 1rem;">
                                        This agreement has been legally authenticated. The branch is now active in the ERP system.
                                    </div>
                                    @if($registeredBranch)
                                    <div style="background: white; padding: 1rem; border-radius: 0.75rem; border: 1px solid #bbf7d0;">
                                        <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.5rem;">Registered As:</div>
                                        <div style="font-weight: 700; color: #166534; margin-bottom: 0.25rem;">{{ $registeredBranch->name }}</div>
                                        <div style="font-size: 0.8125rem; color: #374151;">{{ $registeredBranch->city }} · {{ $registeredBranch->address }}</div>
                                        <div style="font-size: 0.75rem; color: #10b981; font-weight: 600; margin-top: 0.5rem; text-transform: uppercase;">Status: {{ ucfirst($registeredBranch->status) }}</div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                    @if($agreement->contract_path)
                        <div style="margin-top: 2rem; padding: 1.5rem; background: #f8fafc; border-radius: 1rem; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div style="background: white; width: 40px; height: 40px; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border-color);">
                                    <i data-feather="file" style="color: var(--primary-color);"></i>
                                </div>
                                <div>
                                    <div style="font-size: 0.8125rem; font-weight: 600;">Digital Contract Copy</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">Signed Proposal PDF</div>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $agreement->contract_path) }}" target="_blank" class="menu-item" style="padding: 0.5rem 1rem; font-size: 0.75rem; border: 1px solid var(--primary-color);">View</a>
                        </div>
                    @endif
                </div>

                <!-- Column 2: Legal Authentication Panel -->
                <div style="background: #fafafa; border: 1px solid var(--border-color); padding: 2rem; border-radius: 1.5rem;">
                    <h4 style="font-size: 0.875rem; text-transform: uppercase; color: var(--primary-color); margin-bottom: 1.5rem; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem;">
                        <i data-feather="shield" style="width: 16px; height: 16px;"></i> Legal Department Review
                    </h4>

                    @if($agreement->legal_status == 'pending')
                    <form action="{{ route('admin.agreements.authenticate', $agreement) }}" method="POST">
                        @csrf
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.75rem;">Authentication Status</label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <label style="display: block; cursor: pointer;">
                                    <input type="radio" name="legal_status" value="approved" class="hidden-radio" id="st_app" required>
                                    <div for="st_app" class="status-option approve-opt">Approve Agreement</div>
                                </label>
                                <label style="display: block; cursor: pointer;">
                                    <input type="radio" name="legal_status" value="rejected" class="hidden-radio" id="st_rej" required>
                                    <div for="st_rej" class="status-option reject-opt">Reject Agreement</div>
                                </label>
                            </div>
                        </div>

                        <div style="margin-bottom: 2rem;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Legal Remarks & Feedback</label>
                            <textarea name="legal_remarks" placeholder="Provide context for the approval or reasons for rejection..." style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: white; min-height: 120px; font-size: 0.875rem;"></textarea>
                        </div>

                        <button type="submit" style="width: 100%; background: var(--dark-bg); color: white; padding: 1.125rem; border-radius: 0.75rem; border: none; font-weight: 700; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                            Record Authentication
                        </button>
                    </form>
                    @else
                        <!-- Review Summary for authenticated agreements -->
                        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 1.5rem; margin-top: 0.5rem;">
                             <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                                <div style="background: {{ $agreement->legal_status == 'approved' ? '#f0fdf4' : '#fef2f2' }}; color: {{ $agreement->legal_status == 'approved' ? '#166534' : '#991b1b' }}; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i data-feather="{{ $agreement->legal_status == 'approved' ? 'check' : 'x' }}" style="width: 18px; height: 18px;"></i>
                                </div>
                                <div style="font-weight: 700; color: var(--dark-bg);">Authentication {{ ucfirst($agreement->legal_status) }}</div>
                             </div>
                             
                             <div style="margin-bottom: 1rem;">
                                 <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Legal Specialist</label>
                                 <div style="font-size: 0.875rem; font-weight: 500;">{{ $agreement->legalReviewer->name ?? 'Anonymous Admin' }}</div>
                             </div>

                             <div style="margin-bottom: 1rem;">
                                 <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Decision Date</label>
                                 <div style="font-size: 0.875rem; color: var(--text-muted);">{{ \Carbon\Carbon::parse($agreement->legal_reviewed_at)->format('F d, Y @ H:i') }}</div>
                             </div>

                             @if($agreement->legal_remarks)
                             <div>
                                 <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Legal Remarks</label>
                                 <div style="font-size: 0.875rem; font-style: italic; color: var(--text-main);">"{{ $agreement->legal_remarks }}"</div>
                             </div>
                             @endif
                        </div>

                        <div style="margin-top: 1.5rem; text-align: center; color: var(--text-muted); font-size: 0.75rem;">
                            Authentication is permanent. Contact system admin for reversals.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Renewal History Timeline -->
            @if($agreement->renewals->count() > 0)
            <div style="margin-top:2.5rem; border-top:1px solid var(--border-color); padding-top:2rem;">
                <h4 style="font-size:.875rem; text-transform:uppercase; letter-spacing:.05em; color:var(--primary-color); margin:0 0 1.25rem; display:flex; align-items:center; gap:.5rem;">
                    <i data-feather="git-branch" style="width:16px;height:16px;"></i> Renewal & Amendment History
                </h4>
                <div style="position:relative; padding-left:1.75rem;">
                    <div style="position:absolute; left:.5rem; top:0; bottom:0; width:2px; background:var(--border-color); border-radius:1px;"></div>
                    @foreach($agreement->renewals()->latest()->get() as $renewal)
                    @php
                        $rc = match($renewal->action) {
                            'renew'     => ['dot'=>'#10b981','bg'=>'#f0fdf4','text'=>'#166534'],
                            'amend'     => ['dot'=>'#3b82f6','bg'=>'#eff6ff','text'=>'#1d4ed8'],
                            'terminate' => ['dot'=>'#ef4444','bg'=>'#fef2f2','text'=>'#991b1b'],
                            default     => ['dot'=>'#9ca3af','bg'=>'#f3f4f6','text'=>'#374151'],
                        };
                        $sc = match($renewal->status) {
                            'approved' => 'status-active',
                            'rejected' => 'status-inactive',
                            default    => 'status-pending',
                        };
                    @endphp
                    <div style="margin-bottom:1.25rem; position:relative;">
                        <div style="position:absolute; left:-1.35rem; top:.3rem; width:12px; height:12px; border-radius:50%; background:{{ $rc['dot'] }}; border:2px solid white; box-shadow:0 0 0 2px {{ $rc['dot'] }}40;"></div>
                        <div style="background:{{ $rc['bg'] }}; border:1px solid {{ $rc['dot'] }}40; border-radius:.75rem; padding:1rem 1.25rem;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.375rem;">
                                <span style="font-weight:700; font-size:.875rem; color:{{ $rc['text'] }};">{{ $renewal->actionLabel() }}</span>
                                <div style="display:flex; align-items:center; gap:.5rem;">
                                    <span class="status-badge {{ $sc }}" style="font-size:.7rem;">{{ ucfirst(str_replace('_',' ',$renewal->status)) }}</span>
                                    <span style="font-size:.7rem; color:var(--text-muted);">{{ $renewal->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            @if($renewal->reason)
                            <div style="font-size:.8125rem; color:#374151; margin-bottom:.375rem;">{{ $renewal->reason }}</div>
                            @endif
                            @if($renewal->new_monthly_rent || $renewal->new_end_date)
                            <div style="display:flex; gap:1.25rem; font-size:.75rem; color:var(--text-muted); flex-wrap:wrap;">
                                @if($renewal->new_monthly_rent)
                                    <span>New rent: <strong style="color:{{ $rc['text'] }};">\${{ number_format($renewal->new_monthly_rent,2) }}/mo</strong></span>
                                @endif
                                @if($renewal->new_end_date)
                                    <span>New end: <strong style="color:{{ $rc['text'] }};">{{ $renewal->new_end_date }}</strong></span>
                                @endif
                            </div>
                            @endif
                            @if($renewal->approval_remarks)
                            <div style="margin-top:.5rem; font-size:.75rem; font-style:italic; color:var(--text-muted);">&ldquo;{{ $renewal->approval_remarks }}&rdquo;</div>
                            @endif

                            @if($renewal->status === 'pending_approval')
                            <div style="margin-top:.875rem; display:flex; gap:.625rem;">
                                <form action="{{ route('admin.renewals.approve', $renewal) }}" method="POST" style="margin:0;"
                                      onsubmit="return confirm('Approve and activate this {{ $renewal->actionLabel() }}?')">
                                    @csrf
                                    <button type="submit" style="background:#10b981;color:white;border:none;padding:.375rem .875rem;border-radius:.5rem;font-size:.75rem;font-weight:700;cursor:pointer;">✓ Approve</button>
                                </form>
                                <form action="{{ route('admin.renewals.reject', $renewal) }}" method="POST" style="margin:0;"
                                      onsubmit="return confirm('Reject this request?')">
                                    @csrf
                                    <button type="submit" style="background:#ef4444;color:white;border:none;padding:.375rem .875rem;border-radius:.5rem;font-size:.75rem;font-weight:700;cursor:pointer;">✗ Reject</button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Footer -->
            <div style="margin-top: 3rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                <a href="{{ route('admin.agreements.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; font-weight: 500;">
                    <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i> Back to repository
                </a>
            </div>
        </div>
    </div>

    <style>
        .hidden-radio { opacity: 0; position: absolute; }
        .status-option { padding: 1rem; border-radius: 0.75rem; border: 1px solid var(--border-color); text-align: center; font-size: 0.875rem; font-weight: 600; transition: all 0.2s; background: white; }
        .hidden-radio:checked + .status-option.approve-opt { border-color: #10b981; background: #f0fdf4; color: #166534; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); }
        .hidden-radio:checked + .status-option.reject-opt { border-color: #ef4444; background: #fef2f2; color: #991b1b; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1); }
        .status-option:hover { border-color: var(--primary-color); }
    </style>
@endsection
