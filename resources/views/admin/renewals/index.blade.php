@extends('layouts.admin')

@section('title', 'Renewal & Amendment Requests')

@section('content')
<div class="table-container">
    <div style="background:white; border-radius:1rem; border:1px solid var(--border-color); padding:1.5rem;">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
            <h3 style="margin:0; font-size:1.25rem;">Renewal & Amendment Queue</h3>
            <span style="font-size:.8125rem; color:var(--text-muted);">Management approval required</span>
        </div>

        @if(session('success'))
            <div style="background:#ecfdf5; color:#065f46; padding:1rem; border-radius:.5rem; margin-bottom:1.5rem; border:1px solid #10b981;">
                {{ session('success') }}
            </div>
        @endif

        <table class="data-table">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Agreement</th>
                    <th>Branch</th>
                    <th>New Rent</th>
                    <th>New Period</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Decision</th>
                </tr>
            </thead>
            <tbody>
                @forelse($renewals as $renewal)
                    @php
                        $actionColors = [
                            'renew'     => ['bg'=>'#f0fdf4','text'=>'#166534','border'=>'#10b981'],
                            'amend'     => ['bg'=>'#eff6ff','text'=>'#1d4ed8','border'=>'#3b82f6'],
                            'terminate' => ['bg'=>'#fef2f2','text'=>'#991b1b','border'=>'#ef4444'],
                        ][$renewal->action] ?? ['bg'=>'#f3f4f6','text'=>'#374151','border'=>'#9ca3af'];
                    @endphp
                    <tr>
                        <td>
                            <span style="font-weight:700; font-size:.75rem; padding:.25rem .75rem; border-radius:999px; border:1px solid {{ $actionColors['border'] }}; background:{{ $actionColors['bg'] }}; color:{{ $actionColors['text'] }}; white-space:nowrap;">
                                {{ $renewal->actionLabel() }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.agreements.show', $renewal->agreement) }}" style="font-weight:600; color:var(--primary-color); text-decoration:none;">
                                {{ $renewal->agreement->agreement_id }}
                            </a>
                        </td>
                        <td style="font-size:.875rem;">{{ $renewal->agreement->branchRequest->branch_name ?? '—' }}</td>
                        <td style="font-family:monospace;">
                            {{ $renewal->new_monthly_rent ? '$'.number_format($renewal->new_monthly_rent,2) : '—' }}
                        </td>
                        <td style="font-size:.8125rem; color:var(--text-muted);">
                            @if($renewal->new_end_date)
                                {{ $renewal->new_start_date ?? '—' }} → {{ $renewal->new_end_date }}
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @php
                                $sc = match($renewal->status) {
                                    'approved' => 'status-active',
                                    'rejected' => 'status-inactive',
                                    default    => 'status-pending',
                                };
                            @endphp
                            <span class="status-badge {{ $sc }}">{{ ucfirst(str_replace('_', ' ', $renewal->status)) }}</span>
                        </td>
                        <td style="font-size:.8125rem; color:var(--text-muted);">{{ $renewal->created_at->format('M d, Y') }}</td>
                        <td>
                            @if($renewal->status === 'pending_approval')
                                <div style="display:flex; gap:.5rem; align-items:center;">
                                    {{-- Approve --}}
                                    <form action="{{ route('admin.renewals.approve', $renewal) }}" method="POST" style="margin:0;"
                                          onsubmit="return confirm('Approve and activate this {{ $renewal->actionLabel() }}?')">
                                        @csrf
                                        <button type="submit" style="background:#10b981; color:white; border:none; padding:.35rem .75rem; border-radius:.5rem; font-size:.75rem; font-weight:700; cursor:pointer;">
                                            ✓ Approve
                                        </button>
                                    </form>
                                    {{-- Reject --}}
                                    <form action="{{ route('admin.renewals.reject', $renewal) }}" method="POST" style="margin:0;"
                                          onsubmit="return confirm('Reject this request?')">
                                        @csrf
                                        <button type="submit" style="background:#ef4444; color:white; border:none; padding:.35rem .75rem; border-radius:.5rem; font-size:.75rem; font-weight:700; cursor:pointer;">
                                            ✗ Reject
                                        </button>
                                    </form>
                                </div>
                            @elseif($renewal->status === 'approved')
                                <span style="font-size:.75rem; color:#10b981; font-weight:600;">
                                    ✓ By {{ $renewal->approvedBy->name ?? 'Admin' }}<br>
                                    <span style="color:var(--text-muted); font-weight:400;">{{ \Carbon\Carbon::parse($renewal->approved_at)->format('M d') }}</span>
                                </span>
                            @else
                                <span style="font-size:.75rem; color:#ef4444; font-weight:600;">✗ Rejected</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; color:var(--text-muted); padding:3rem;">
                            No renewal or amendment requests found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:1.5rem;">{{ $renewals->links() }}</div>
    </div>
</div>
@endsection
