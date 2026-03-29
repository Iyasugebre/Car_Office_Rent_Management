@extends('layouts.admin')

@section('title', 'Branch Requests')

@section('content')
    <div class="table-container">
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h3 style="margin: 0; font-size: 1.25rem;">Enterprise Expansion Proposals</h3>
                <a href="{{ route('admin.branch-requests.create') }}" class="menu-item" style="background: var(--primary-color); color: white; padding: 0.5rem 1.25rem; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-feather="plus" style="width: 16px; height: 16px;"></i> New Branch Request
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
                        <th>Reference</th>
                        <th>Branch Name</th>
                        <th>Location</th>
                        <th>Proposed Office</th>
                        <th>Estimated Rent</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr>
                            <td style="font-weight: 600; color: var(--primary-color);">{{ $request->reference_number }}</td>
                            <td>{{ $request->branch_name }}</td>
                            <td>
                                <span style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
                                    <i data-feather="map-pin" style="width: 14px; height: 14px; color: var(--text-muted);"></i>
                                    {{ $request->location }}
                                </span>
                            </td>
                            <td style="font-size: 0.875rem; color: var(--text-muted);">{{ Str::limit($request->proposed_office, 30) }}</td>
                            <td style="font-weight: 600;">${{ number_format($request->estimated_rent, 2) }}</td>
                            <td>
                                <span style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: {{ $request->priority == 'high' ? 'var(--danger)' : ($request->priority == 'medium' ? 'var(--warning)' : 'var(--success)') }}">
                                    {{ $request->priority }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ strtolower($request->status) }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="{{ route('admin.branch-requests.show', $request) }}" style="color: var(--secondary-color); padding: 0.25rem;">
                                        <i data-feather="eye" style="width: 18px; height: 18px;"></i>
                                    </a>
                                    <form action="{{ route('admin.branch-requests.destroy', $request) }}" method="POST" onsubmit="return confirm('Delete this proposal?');">
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
                            <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 3rem;">No expansion proposals found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top: 1.5rem;">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
@endsection
