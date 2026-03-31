@extends('layouts.admin')

@section('title', 'Bolo & Inspection Tracker')

@section('content')
    <div class="table-container">
        {{-- Stats Row --}}
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
            <a href="{{ route('admin.legal-tracker.index') }}" style="text-decoration: none;">
                <div style="background: white; border-radius: 1rem; border: 1px solid {{ $filter === 'all' ? 'var(--primary-color)' : 'var(--border-color)' }}; padding: 1.25rem; transition: all 0.2s;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Total Fleet</div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--primary-color); margin-top: 0.25rem;">{{ $stats['total'] }}</div>
                </div>
            </a>
            <a href="{{ route('admin.legal-tracker.index', ['filter' => 'overdue']) }}" style="text-decoration: none;">
                <div style="background: {{ $filter === 'overdue' ? '#fef2f2' : 'white' }}; border-radius: 1rem; border: 1px solid {{ $stats['overdue'] > 0 ? '#ef4444' : 'var(--border-color)' }}; padding: 1.25rem; transition: all 0.2s;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444; {{ $stats['overdue'] > 0 ? 'animation: pulse 2s infinite;' : '' }}"></div>
                        <span style="font-size: 0.75rem; color: {{ $stats['overdue'] > 0 ? '#991b1b' : 'var(--text-muted)' }}; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Overdue (Expired)</span>
                    </div>
                    <div style="font-size: 2rem; font-weight: 700; color: #ef4444; margin-top: 0.25rem;">{{ $stats['overdue'] }}</div>
                </div>
            </a>
            <a href="{{ route('admin.legal-tracker.index', ['filter' => 'warning']) }}" style="text-decoration: none;">
                <div style="background: {{ $filter === 'warning' ? '#fff7ed' : 'white' }}; border-radius: 1rem; border: 1px solid {{ $stats['warning'] > 0 ? '#f59e0b' : 'var(--border-color)' }}; padding: 1.25rem; transition: all 0.2s;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Expiring Soon</span>
                    </div>
                    <div style="font-size: 2rem; font-weight: 700; color: #f59e0b; margin-top: 0.25rem;">{{ $stats['warning'] }}</div>
                </div>
            </a>
        </div>

        {{-- Main Card --}}
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h3 style="margin: 0; font-size: 1.25rem;">Fleet Legal Compliance</h3>
                    <p style="margin: 0.25rem 0 0; font-size: 0.8125rem; color: var(--text-muted);">Annual Bolo renewal and Vehicle Inspection tracking</p>
                </div>
            </div>

            @if(session('success'))
                <div style="background: #ecfdf5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #10b981;">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Vehicle List --}}
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Vehicle</th>
                        <th>Bolo Expiry</th>
                        <th>Inspection Expiry</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cars as $car)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $car->make }} {{ $car->model }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;">{{ $car->plate_number }}</div>
                            </td>
                            <td>
                                @if($car->bolo_expiry_date)
                                    @php
                                        $statusColor = match($car->bolo_status) {
                                            'overdue' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'label' => 'EXPIRED'],
                                            'warning' => ['bg' => '#fef3c7', 'text' => '#92400e', 'label' => 'DUE SOON'],
                                            default   => ['bg' => '#ecfdf5', 'text' => '#065f46', 'label' => 'VALID'],
                                        };
                                    @endphp
                                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                        <span style="font-size: 0.875rem; font-weight: 500;">{{ $car->bolo_expiry_date->format('M d, Y') }}</span>
                                        <span class="status-badge" style="background: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }}; width: fit-content; font-size: 0.65rem;">
                                            {{ $statusColor['label'] }} ({{ $car->bolo_days_remaining }}d)
                                        </span>
                                    </div>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.875rem;">Not Set</span>
                                @endif
                            </td>
                            <td>
                                @if($car->inspection_expiry_date)
                                    @php
                                        $statusColor = match($car->inspection_status) {
                                            'overdue' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'label' => 'EXPIRED'],
                                            'warning' => ['bg' => '#fef3c7', 'text' => '#92400e', 'label' => 'DUE SOON'],
                                            default   => ['bg' => '#ecfdf5', 'text' => '#065f46', 'label' => 'VALID'],
                                        };
                                    @endphp
                                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                        <span style="font-size: 0.875rem; font-weight: 500;">{{ $car->inspection_expiry_date->format('M d, Y') }}</span>
                                        <span class="status-badge" style="background: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }}; width: fit-content; font-size: 0.65rem;">
                                            {{ $statusColor['label'] }} ({{ $car->inspection_days_remaining }}d)
                                        </span>
                                    </div>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.875rem;">Not Set</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                                    @if($car->bolo_certificate_path)
                                        <a href="{{ Storage::url($car->bolo_certificate_path) }}" target="_blank" title="View Bolo Certificate" style="color: var(--primary-color);">
                                            <i data-feather="file-text" style="width: 18px; height: 18px;"></i>
                                        </a>
                                    @endif
                                    @if($car->inspection_certificate_path)
                                        <a href="{{ Storage::url($car->inspection_certificate_path) }}" target="_blank" title="View Inspection Certificate" style="color: var(--secondary-color);">
                                            <i data-feather="clipboard" style="width: 18px; height: 18px;"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.legal-tracker.edit', $car) }}" class="menu-item" style="padding: 0.375rem 0.75rem; font-size: 0.75rem; background: #f8fafc; color: #475569; border: 1px solid #e2e8f0;">
                                        <i data-feather="edit" style="width: 14px; height: 14px;"></i> Update Docs
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 3rem; color: var(--text-muted);">No vehicles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() { feather.replace(); });
    </script>
@endsection
