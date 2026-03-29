@extends('layouts.admin')

@section('title', 'Renew / Amend / Terminate Agreement')

@section('content')
<div class="table-container">
    <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 2.5rem; max-width: 860px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(0,0,0,.08);">

        {{-- Header --}}
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:2rem; padding-bottom:1.5rem; border-bottom:1px solid var(--border-color);">
            <div>
                <h3 style="margin:0 0 .375rem; font-size:1.375rem; color:var(--dark-bg);">Agreement Action</h3>
                <p style="margin:0; font-size:.875rem; color:var(--text-muted);">
                    Contract <strong style="color:var(--primary-color);">{{ $agreement->agreement_id }}</strong>
                    &mdash; {{ $agreement->branchRequest->branch_name ?? $agreement->landlord_name }}
                </p>
            </div>
            <a href="{{ route('admin.agreements.show', $agreement) }}" style="font-size:.875rem; color:var(--text-muted); text-decoration:none; font-weight:600;">Cancel</a>
        </div>

        <form action="{{ route('admin.renewals.store', $agreement) }}" method="POST" id="renewal-form">
            @csrf

            {{-- Action selector --}}
            <div style="margin-bottom:2rem;">
                <label style="display:block; font-size:.875rem; font-weight:700; margin-bottom:.875rem;">Select Action</label>
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;">
                    @foreach([
                        ['renew',     '🔄 Renew',    'Extend the agreement with updated terms.', '#10b981', '#f0fdf4', '#bbf7d0'],
                        ['amend',     '✏️ Amend',    'Modify specific terms without full renewal.', '#3b82f6', '#eff6ff', '#bfdbfe'],
                        ['terminate', '⛔ Terminate','End the agreement and close the branch.', '#ef4444', '#fef2f2', '#fecaca'],
                    ] as [$val, $label, $desc, $color, $bg, $border])
                        <label style="cursor:pointer;">
                            <input type="radio" name="action" value="{{ $val }}" class="action-radio" data-color="{{ $color }}" data-bg="{{ $bg }}" required style="opacity:0;position:absolute;">
                            <div class="action-card" id="card-{{ $val }}" style="padding:1.25rem; border-radius:.875rem; border:2px solid #e5e7eb; text-align:center; transition:all .2s;">
                                <div style="font-size:1.125rem; font-weight:700; margin-bottom:.375rem;">{{ $label }}</div>
                                <div style="font-size:.75rem; color:#6b7280;">{{ $desc }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Reason --}}
            <div style="margin-bottom:1.5rem;">
                <label style="display:block; font-size:.875rem; font-weight:600; margin-bottom:.5rem;">Reason / Justification <span style="color:#ef4444;">*</span></label>
                <textarea name="reason" rows="3" placeholder="Why is this action being taken?" style="width:100%; padding:.875rem; border-radius:.5rem; border:1px solid var(--border-color); font-size:.875rem;" required></textarea>
            </div>

            {{-- New terms section (hidden for terminate) --}}
            <div id="new-terms" style="display:none;">
                <div style="background:#f8fafc; border:1px solid var(--border-color); border-radius:1rem; padding:1.5rem; margin-bottom:1.5rem;">
                    <h4 style="font-size:.875rem; text-transform:uppercase; color:var(--primary-color); letter-spacing:.05em; margin:0 0 1.25rem;">Updated Contract Terms</h4>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">
                        <div>
                            <label style="display:block; font-size:.8125rem; font-weight:600; margin-bottom:.375rem;">New Monthly Rent ($)</label>
                            <input type="number" step="0.01" name="new_monthly_rent" placeholder="{{ $agreement->monthly_rent }}"
                                style="width:100%; padding:.75rem; border-radius:.5rem; border:1px solid var(--border-color);">
                            <div style="font-size:.7rem; color:var(--text-muted); margin-top:.25rem;">Current: ${{ number_format($agreement->monthly_rent, 2) }}</div>
                        </div>
                        <div>
                            <label style="display:block; font-size:.8125rem; font-weight:600; margin-bottom:.375rem;">New Payment Schedule</label>
                            <select name="new_payment_schedule" style="width:100%; padding:.75rem; border-radius:.5rem; border:1px solid var(--border-color);">
                                <option value="">— Keep current ({{ $agreement->payment_schedule }}) —</option>
                                @foreach(['Monthly','Quarterly','Bi-Annual','Annually'] as $s)
                                    <option value="{{ $s }}" {{ $agreement->payment_schedule == $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-size:.8125rem; font-weight:600; margin-bottom:.375rem;">New Start Date</label>
                            <input type="date" name="new_start_date" value="{{ $agreement->end_date }}"
                                style="width:100%; padding:.75rem; border-radius:.5rem; border:1px solid var(--border-color);">
                        </div>
                        <div>
                            <label style="display:block; font-size:.8125rem; font-weight:600; margin-bottom:.375rem;">New End Date</label>
                            <input type="date" name="new_end_date"
                                style="width:100%; padding:.75rem; border-radius:.5rem; border:1px solid var(--border-color);" required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Amendment notes --}}
            <div style="margin-bottom:2rem;">
                <label style="display:block; font-size:.875rem; font-weight:600; margin-bottom:.5rem;">Additional Notes</label>
                <textarea name="amendment_notes" rows="2" placeholder="Any additional amendment details or special conditions..."
                    style="width:100%; padding:.875rem; border-radius:.5rem; border:1px solid var(--border-color); font-size:.875rem;"></textarea>
            </div>

            {{-- Current agreement summary --}}
            <div style="background:#f8fafc; border-radius:.875rem; padding:1rem 1.25rem; margin-bottom:2rem; display:flex; gap:2rem; flex-wrap:wrap; font-size:.8125rem;">
                <div><span style="color:var(--text-muted);">Landlord:</span> <strong>{{ $agreement->landlord_name }}</strong></div>
                <div><span style="color:var(--text-muted);">Current Rent:</span> <strong>${{ number_format($agreement->monthly_rent,2) }}/mo</strong></div>
                <div><span style="color:var(--text-muted);">Expires:</span> <strong>{{ $agreement->end_date }}</strong></div>
            </div>

            <div style="display:flex; justify-content:flex-end; border-top:1px solid var(--border-color); padding-top:1.5rem;">
                <button type="submit" id="submit-btn" style="background:var(--dark-bg); color:white; padding:1rem 2.5rem; border-radius:.625rem; border:none; font-weight:700; cursor:pointer; font-size:.9375rem;">
                    Submit for Management Approval
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.action-radio').forEach(function(radio) {
    radio.addEventListener('change', function() {
        // Reset all cards
        document.querySelectorAll('.action-card').forEach(c => {
            c.style.borderColor = '#e5e7eb';
            c.style.background  = 'white';
        });
        // Highlight selected
        const card = document.getElementById('card-' + this.value);
        if (card) {
            card.style.borderColor = this.dataset.color;
            card.style.background  = this.dataset.bg;
        }
        // Show/hide new terms
        const hasTerms = (this.value === 'renew' || this.value === 'amend');
        document.getElementById('new-terms').style.display = hasTerms ? 'block' : 'none';

        // Change button color
        const btn = document.getElementById('submit-btn');
        btn.style.background = this.dataset.color;

        // Make new_end_date required only when showing
        document.querySelector('[name="new_end_date"]').required = hasTerms;
    });
});
</script>
@endsection
