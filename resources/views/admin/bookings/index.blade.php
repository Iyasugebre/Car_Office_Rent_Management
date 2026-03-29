@extends('layouts.admin')

@section('title', 'Manage Bookings')

@section('content')
    <div class="table-container">
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h3 style="margin: 0; font-size: 1.25rem;">Enterprise Rental Transactions</h3>
                <a href="{{ route('admin.bookings.create') }}" class="menu-item" style="background: var(--primary-color); color: white; padding: 0.5rem 1.25rem; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-feather="plus" style="width: 16px; height: 16px;"></i> New Booking
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
                        <th>Booking ID</th>
                        <th>Customer</th>
                        <th>Asset Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td style="font-weight: 500;">#{{ substr($booking->id, 0, 8) }}</td>
                            <td>
                                <div style="font-weight: 600;">{{ $booking->user->name ?? 'John Doe' }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $booking->user->email ?? '' }}</div>
                            </td>
                            <td>
                                @if($booking->car_id)
                                    <span style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
                                        <i data-feather="truck" style="width: 14px; height: 14px;"></i> {{ $booking->car->make }} {{ $booking->car->model }}
                                    </span>
                                @elseif($booking->office_id)
                                    <span style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
                                        <i data-feather="briefcase" style="width: 14px; height: 14px;"></i> {{ $booking->office->name }}
                                    </span>
                                @endif
                            </td>
                            <td style="font-size: 0.875rem;">{{ \Carbon\Carbon::parse($booking->start_date)->toFormattedDateString() }}</td>
                            <td style="font-size: 0.875rem;">{{ \Carbon\Carbon::parse($booking->end_date)->toFormattedDateString() }}</td>
                            <td style="font-family: monospace; font-weight: 600;">${{ number_format($booking->total_price, 2) }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($booking->status) }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <form action="{{ route('admin.bookings.update', $booking) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()" style="padding: 0.25rem; font-size: 0.75rem; border-radius: 0.25rem; border: 1px solid var(--border-color); background: #f8fafc; cursor: pointer;">
                                            <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="active" {{ $booking->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </form>
                                    <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('Delete this booking?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: none; border: none; color: var(--danger); cursor: pointer; padding: 0.25rem;">
                                            <i data-feather="trash-2" style="width: 16px; height: 16px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 3rem;">No booking history found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top: 1.5rem;">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
@endsection
