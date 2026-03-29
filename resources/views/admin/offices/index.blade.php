@extends('layouts.admin')

@section('title', 'Manage Offices')

@section('content')
    <div class="table-container">
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h3 style="margin: 0; font-size: 1.25rem;">Office & Branch Locations</h3>
                <a href="{{ route('admin.offices.create') }}" class="menu-item" style="background: var(--primary-color); color: white; padding: 0.5rem 1.25rem; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-feather="plus" style="width: 16px; height: 16px;"></i> Add New Office
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
                        <th>Office Name</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Price/Month</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offices as $office)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $office->name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $office->phone }}</div>
                            </td>
                            <td>
                                <span style="font-size: 0.875rem; color: var(--text-muted); text-transform: capitalize;">{{ str_replace('_', ' ', $office->type) }}</span>
                            </td>
                            <td>
                                <div style="font-size: 0.875rem;">{{ $office->city }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $office->address }}</div>
                            </td>
                            <td style="font-weight: 600;">
                                @if($office->price_per_month)
                                    ${{ number_format($office->price_per_month, 2) }}
                                @else
                                    <span style="color: var(--text-muted); font-weight: normal;">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge status-{{ strtolower($office->status) }}">
                                    {{ ucfirst($office->status) }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="{{ route('admin.offices.edit', $office) }}" style="color: var(--secondary-color); padding: 0.25rem;">
                                        <i data-feather="edit-2" style="width: 18px; height: 18px;"></i>
                                    </a>
                                    <form action="{{ route('admin.offices.destroy', $office) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this office?');">
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
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 3rem;">No office locations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top: 1.5rem;">
                {{ $offices->links() }}
            </div>
        </div>
    </div>
@endsection
