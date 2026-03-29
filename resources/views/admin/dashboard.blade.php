@extends('layouts.admin')

@section('title', 'Admin Overview')

@section('content')
    @php
        // Agreements expiring within 90 days
        $expiringAgreements = \App\Models\Agreement::where('status', 'active')
            ->where('legal_status', 'approved')
            ->get()
            ->filter(fn($a) => \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($a->end_date), false) <= 90)
            ->sortBy(fn($a) => \Carbon\Carbon::parse($a->end_date))
            ->take(5);
    @endphp

    <div class="dashboard-grid">
        <div class="stat-card">
            <div class="stat-label">Total Cars</div>
            <div class="stat-value" style="color: var(--primary-color);">{{ $stats['total_cars'] }}</div>
            <div style="font-size: 0.75rem; color: var(--success); margin-top: 0.5rem;">Fleet inventory</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Offices</div>
            <div class="stat-value" style="color: var(--secondary-color);">{{ $stats['total_offices'] }}</div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Active locations</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Active Bookings</div>
            <div class="stat-value" style="color: var(--warning);">{{ $stats['active_bookings'] }}</div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Check for updates</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value" style="color: var(--success); font-family: monospace;">${{ number_format($stats['total_revenue'], 2) }}</div>
            <div style="font-size: 0.75rem; color: var(--success); margin-top: 0.5rem;">+15% from last month</div>
        </div>

        <div class="stat-card" style="border: 1px solid {{ $expiringAgreements->count() > 0 ? '#fca5a5' : 'var(--border-color)' }}; background: {{ $expiringAgreements->count() > 0 ? '#fef2f2' : 'white' }};">
            <div class="stat-label" style="{{ $expiringAgreements->count() > 0 ? 'color: #991b1b;' : '' }}">Expiring Contracts</div>
            <div class="stat-value" style="color: {{ $expiringAgreements->count() > 0 ? '#ef4444' : 'var(--text-muted)' }};">{{ $expiringAgreements->count() }}</div>
            <div style="font-size: 0.75rem; color: {{ $expiringAgreements->count() > 0 ? '#b91c1c' : 'var(--text-muted)' }}; margin-top: 0.5rem;">
                {{ $expiringAgreements->count() > 0 ? 'Action required within 90 days' : 'All contracts on track' }}
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
        {{-- Recent Bookings --}}
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="margin: 0; font-size: 1.125rem;">Recent Bookings</h3>
                <a href="{{ route('admin.bookings.index') }}" style="color: var(--primary-color); text-decoration: none; font-size: 0.875rem; font-weight: 600;">View All</a>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_bookings as $booking)
                        <tr>
                            <td style="font-weight: 500;">#{{ substr($booking->id, 0, 8) }}</td>
                            <td>{{ $booking->user->name ?? 'Guest' }}</td>
                            <td style="font-family: monospace;">${{ number_format($booking->total_price, 2) }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($booking->status) }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem;">No recent bookings.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Expiry Alerts Widget --}}
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <i data-feather="alert-triangle" style="color: #f59e0b; width: 18px; height: 18px;"></i>
                    <h3 style="margin: 0; font-size: 1.125rem;">Expiring Contracts</h3>
                </div>
                <a href="{{ route('admin.agreements.index') }}" style="color: var(--primary-color); text-decoration: none; font-size: 0.875rem; font-weight: 600;">View All</a>
            </div>

            @forelse($expiringAgreements as $agreement)
                @php
                    $daysLeft = max(0, \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($agreement->end_date), false));
                    $barColor = match(true) {
                        $daysLeft <= 30 => '#ef4444',
                        $daysLeft <= 60 => '#f97316',
                        default         => '#f59e0b',
                    };
                    $totalDays = \Carbon\Carbon::parse($agreement->start_date)->diffInDays(\Carbon\Carbon::parse($agreement->end_date));
                    $progress  = $totalDays > 0 ? min(100, round((($totalDays - $daysLeft) / $totalDays) * 100)) : 100;
                @endphp
                <a href="{{ route('admin.agreements.show', $agreement) }}" style="display: block; padding: 0.875rem; border-radius: 0.75rem; border: 1px solid #e5e7eb; margin-bottom: 0.75rem; text-decoration: none; transition: all 0.15s;" onmouseover="this.style.borderColor='{{ $barColor }}';this.style.background='#fafafa'" onmouseout="this.style.borderColor='#e5e7eb';this.style.background='white'">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.875rem; font-weight: 600; color: #111827;">{{ $agreement->agreement_id }}</span>
                        <span style="font-size: 0.6875rem; font-weight: 700; color: {{ $barColor }}; background: {{ $barColor }}18; padding: 0.15rem 0.5rem; border-radius: 999px; border: 1px solid {{ $barColor }}40;">
                            {{ $daysLeft > 0 ? $daysLeft . ' days left' : 'Expired' }}
                        </span>
                    </div>
                    <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.5rem;">{{ $agreement->branchRequest->branch_name ?? $agreement->landlord_name }}</div>
                    <div style="background: #e5e7eb; border-radius: 999px; height: 5px; overflow: hidden;">
                        <div style="background: {{ $barColor }}; height: 100%; width: {{ $progress }}%; border-radius: 999px;"></div>
                    </div>
                </a>
            @empty
                <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
                    <i data-feather="check-circle" style="display: block; margin: 0 auto 0.75rem; width: 28px; height: 28px; color: #10b981;"></i>
                    <div style="font-size: 0.875rem;">No contracts expiring in the next 90 days</div>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
@endsection
