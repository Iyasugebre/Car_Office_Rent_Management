@extends('layouts.admin')

@section('title', 'Lease Agreements')

@section('content')
    <div class="table-container">
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h3 style="margin: 0; font-size: 1.25rem;">Binding Rental Contracts</h3>
                <a href="{{ route('admin.agreements.create') }}" class="menu-item" style="background: var(--primary-color); color: white; padding: 0.5rem 1.25rem; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-feather="plus" style="width: 16px; height: 16px;"></i> Prepare Agreement
                </a>
            </div>

            @if(session('success'))
                <div style="background: #ecfdf5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #10b981;">
                    {{ session('success') }}
                </div>
            @endif

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Agreement ID</th>
                        <th>Landlord</th>
                        <th>Property</th>
                        <th>Monthly Rent</th>
                        <th>Term</th>
                        <th>Legal Status</th>
                        <th>Overall Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agreements as $agreement)
                        <tr>
                            <td style="font-weight: 600; color: var(--primary-color);">{{ $agreement->agreement_id }}</td>
                            <td>{{ $agreement->landlord_name }}</td>
                            <td>
                                <span style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
                                    <i data-feather="map-pin" style="width: 14px; height: 14px; color: var(--text-muted);"></i>
                                    {{ Str::limit($agreement->property_address, 30) }}
                                </span>
                            </td>
                            <td style="font-weight: 600;">${{ number_format($agreement->monthly_rent, 2) }}</td>
                            <td style="font-size: 0.8125rem; color: var(--text-muted);">
                                @php
                                    $remaining = max(0, \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($agreement->end_date), false));
                                    $badgeColor = match(true) {
                                        $remaining <= 0  => ['bg'=>'#fef2f2','text'=>'#991b1b','border'=>'#fecaca'],
                                        $remaining <= 30 => ['bg'=>'#fef2f2','text'=>'#991b1b','border'=>'#fecaca'],
                                        $remaining <= 60 => ['bg'=>'#fff7ed','text'=>'#9a3412','border'=>'#fed7aa'],
                                        $remaining <= 90 => ['bg'=>'#fffbeb','text'=>'#854d0e','border'=>'#fde68a'],
                                        default          => ['bg'=>'#f0fdf4','text'=>'#166534','border'=>'#bbf7d0'],
                                    };
                                @endphp
                                <span style="font-size: 0.75rem; font-weight: 600; color: {{ $badgeColor['text'] }}; background: {{ $badgeColor['bg'] }}; padding: 0.2rem 0.6rem; border-radius: 999px; border: 1px solid {{ $badgeColor['border'] }}; white-space: nowrap;">
                                    {{ $remaining > 0 ? $remaining . ' days left' : 'Expired' }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge" style="background: {{ $agreement->legal_status == 'approved' ? '#f0fdf4' : ($agreement->legal_status == 'rejected' ? '#fef2f2' : '#fff7ed') }}; color: {{ $agreement->legal_status == 'approved' ? '#166534' : ($agreement->legal_status == 'rejected' ? '#991b1b' : '#9a3412') }}; border: 1px solid currentColor;">
                                    {{ ucfirst($agreement->legal_status) }}
                                </span>
                            </td>
                            <td>
                                @if($agreement->legal_status == 'approved' && $agreement->status == 'active')
                                    <span style="display: flex; align-items: center; gap: 0.375rem; background: #f0fdf4; color: #166534; padding: 0.375rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; border: 1px solid #10b981;">
                                        <i data-feather="zap" style="width: 12px; height: 12px;"></i> Branch Activated
                                    </span>
                                @else
                                    <span class="status-badge status-{{ strtolower($agreement->status) }}">
                                        {{ ucfirst($agreement->status) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <a href="{{ route('admin.agreements.show', $agreement) }}" style="color: var(--primary-color); padding: 0.25rem;" title="View Details">
                                        <i data-feather="eye" style="width: 18px; height: 18px;"></i>
                                    </a>
                                    @if($agreement->contract_path)
                                        <a href="{{ asset('storage/' . $agreement->contract_path) }}" target="_blank" style="color: var(--text-muted); padding: 0.25rem;" title="Download Contract">
                                            <i data-feather="download-cloud" style="width: 18px; height: 18px;"></i>
                                        </a>
                                    @endif
                                    <form action="{{ route('admin.agreements.destroy', $agreement) }}" method="POST" onsubmit="return confirm('Remove this agreement record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: none; border: none; color: var(--danger); cursor: pointer; padding: 0.25rem;">
                                            <i data-feather="trash-2" style="width: 18px; height: 18px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 3rem;">No active agreements found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top: 1.5rem;">
                {{ $agreements->links() }}
            </div>
        </div>
    </div>
@endsection
