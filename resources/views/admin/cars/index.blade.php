@extends('layouts.admin')

@section('title', 'Manage Cars')

@section('content')
    <div class="table-container">
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h3 style="margin: 0; font-size: 1.25rem;">Fleet Inventory</h3>
                <a href="{{ route('admin.cars.create') }}" class="menu-item" style="background: var(--primary-color); color: white; padding: 0.5rem 1.25rem; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-feather="plus" style="width: 16px; height: 16px;"></i> Add New Car
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
                        <th>Car Model</th>
                        <th>Plate Number</th>
                        <th>Location</th>
                        <th>Price/Day</th>
                        <th>Mileage</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cars as $car)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $car->make }} {{ $car->model }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">Year: {{ $car->year }}</div>
                            </td>
                            <td style="font-family: monospace;">{{ $car->plate_number }}</td>
                            <td>
                                <span style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
                                    <i data-feather="map-pin" style="width: 14px; height: 14px; color: var(--text-muted);"></i>
                                    {{ $car->office->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td style="font-weight: 600;">${{ number_format($car->price_per_day, 2) }}</td>
                            <td>
                                <div style="font-size: 0.875rem;">{{ number_format($car->mileage) }} km</div>
                                @if($car->last_service_date)
                                <div style="font-size: 0.70rem; color: var(--text-muted);">Last Svc: {{ \Carbon\Carbon::parse($car->last_service_date)->format('M d, Y') }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge status-{{ strtolower($car->status) }}">
                                    {{ ucfirst($car->status) }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="{{ route('admin.cars.edit', $car) }}" style="color: var(--secondary-color); padding: 0.25rem;">
                                        <i data-feather="edit-2" style="width: 18px; height: 18px;"></i>
                                    </a>
                                    <form action="{{ route('admin.cars.destroy', $car) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this car?');">
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
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 3rem;">No cars found in inventory.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top: 1.5rem;">
                {{ $cars->links() }}
            </div>
        </div>
    </div>
@endsection
