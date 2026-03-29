@extends('layouts.admin')

@section('title', 'Service & Maintenance')

@section('content')
    <div class="table-container">
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h3 style="margin: 0; font-size: 1.25rem;">Vehicle Maintenance History</h3>
                <a href="{{ route('admin.services.create') }}" class="menu-item" style="background: var(--primary-color); color: white; padding: 0.5rem 1.25rem; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-feather="tool" style="width: 16px; height: 16px;"></i> Submit Request
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
                        <th>Vehicle Plate</th>
                        <th>Service Type</th>
                        <th>Urgency</th>
                        <th>Problem</th>
                        <th>Status</th>
                        <th>Requested By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                        <tr>
                            <td style="font-weight: 600; color: var(--primary-color);">{{ $service->car->plate_number ?? 'Unknown' }}</td>
                            <td style="text-transform: capitalize;">{{ $service->service_type }}</td>
                            <td>
                                <span style="font-size: 0.75rem; font-weight: 600; color: {{ $service->urgency_color }}; background: {{ $service->urgency_color }}18; padding: 0.2rem 0.6rem; border-radius: 999px; text-transform: uppercase;">
                                    {{ $service->urgency_level }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 0.875rem; color: var(--text-muted);">
                                    {{ Str::limit($service->problem_description, 30) }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 0.75rem; font-weight: 600; color: {{ $service->status_color }}; background: {{ $service->status_color }}18; padding: 0.375rem 0.75rem; border-radius: 0.5rem; border: 1px solid {{ $service->status_color }}40; text-transform: capitalize;">
                                    {{ str_replace('_', ' ', $service->status) }}
                                </span>
                            </td>
                            <td>
                                <div style="font-size: 0.875rem; font-weight: 500;">{{ $service->requester->name ?? 'N/A' }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $service->created_at->format('M d, Y') }}</div>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <a href="{{ route('admin.services.show', $service) }}" style="color: var(--primary-color); padding: 0.25rem;" title="View Details">
                                        <i data-feather="eye" style="width: 18px; height: 18px;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 3rem;">No maintenance requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top: 1.5rem;">
                {{ $services->links() }}
            </div>
        </div>
    </div>
@endsection
