@extends('layouts.admin')

@section('title', 'Vehicle Service Tracker')

@section('content')
    <div class="table-container">
        {{-- Stats Row --}}
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
            <a href="{{ route('admin.service-tracker.index') }}" style="text-decoration: none;">
                <div style="background: white; border-radius: 1rem; border: 1px solid {{ $filter === 'all' ? 'var(--primary-color)' : 'var(--border-color)' }}; padding: 1.25rem; transition: all 0.2s; {{ $filter === 'all' ? 'box-shadow: 0 0 0 2px rgba(79,70,229,0.15);' : '' }}">
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Total Fleet</div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--primary-color); margin-top: 0.25rem;">{{ $stats['total'] }}</div>
                </div>
            </a>
            <a href="{{ route('admin.service-tracker.index', ['filter' => 'overdue']) }}" style="text-decoration: none;">
                <div style="background: {{ $filter === 'overdue' ? '#fef2f2' : 'white' }}; border-radius: 1rem; border: 1px solid {{ $filter === 'overdue' ? '#ef4444' : ($stats['overdue'] > 0 ? '#fca5a5' : 'var(--border-color)') }}; padding: 1.25rem; transition: all 0.2s;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444; {{ $stats['overdue'] > 0 ? 'animation: pulse 2s infinite;' : '' }}"></div>
                        <span style="font-size: 0.75rem; color: {{ $stats['overdue'] > 0 ? '#991b1b' : 'var(--text-muted)' }}; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Overdue</span>
                    </div>
                    <div style="font-size: 2rem; font-weight: 700; color: #ef4444; margin-top: 0.25rem;">{{ $stats['overdue'] }}</div>
                </div>
            </a>
            <a href="{{ route('admin.service-tracker.index', ['filter' => 'warning']) }}" style="text-decoration: none;">
                <div style="background: {{ $filter === 'warning' ? '#fff7ed' : 'white' }}; border-radius: 1rem; border: 1px solid {{ $filter === 'warning' ? '#f59e0b' : 'var(--border-color)' }}; padding: 1.25rem; transition: all 0.2s;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Approaching</span>
                    </div>
                    <div style="font-size: 2rem; font-weight: 700; color: #f59e0b; margin-top: 0.25rem;">{{ $stats['warning'] }}</div>
                </div>
            </a>
            <a href="{{ route('admin.service-tracker.index', ['filter' => 'good']) }}" style="text-decoration: none;">
                <div style="background: {{ $filter === 'good' ? '#f0fdf4' : 'white' }}; border-radius: 1rem; border: 1px solid {{ $filter === 'good' ? '#22c55e' : 'var(--border-color)' }}; padding: 1.25rem; transition: all 0.2s;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #22c55e;"></div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">On Track</span>
                    </div>
                    <div style="font-size: 2rem; font-weight: 700; color: #22c55e; margin-top: 0.25rem;">{{ $stats['good'] }}</div>
                </div>
            </a>
        </div>

        {{-- Main Card --}}
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h3 style="margin: 0; font-size: 1.25rem;">Fleet Service Status</h3>
                    <p style="margin: 0.25rem 0 0; font-size: 0.8125rem; color: var(--text-muted);">Periodic vehicle service tracking based on mileage &amp; time intervals</p>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <a href="{{ route('admin.service-tracker.schedules') }}" class="menu-item" style="background: #f1f5f9; color: var(--text-main); padding: 0.5rem 1rem; font-size: 0.8125rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i data-feather="settings" style="width: 15px; height: 15px;"></i> Service Rules
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div style="background: #ecfdf5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #10b981;">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Vehicle Grid --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1rem;">
                @forelse($cars as $car)
                    @php
                        $healthColor = match($car->health) {
                            'overdue' => ['bg' => '#fef2f2', 'border' => '#fca5a5', 'text' => '#991b1b', 'dot' => '#ef4444', 'label' => 'Overdue'],
                            'warning' => ['bg' => '#fff7ed', 'border' => '#fed7aa', 'text' => '#9a3412', 'dot' => '#f59e0b', 'label' => 'Approaching'],
                            default   => ['bg' => '#f0fdf4', 'border' => '#bbf7d0', 'text' => '#166534', 'dot' => '#22c55e', 'label' => 'On Track'],
                        };
                    @endphp
                    <a href="{{ route('admin.service-tracker.show', $car) }}" style="display: block; text-decoration: none; background: {{ $healthColor['bg'] }}; border: 1px solid {{ $healthColor['border'] }}; border-radius: 0.875rem; padding: 1.25rem; transition: all 0.2s; position: relative; overflow: hidden;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,0.08)'" onmouseout="this.style.transform='none';this.style.boxShadow='none'">
                        {{-- Header --}}
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                            <div>
                                <div style="font-size: 1rem; font-weight: 700; color: #111827;">{{ $car->make }} {{ $car->model }}</div>
                                <div style="font-size: 0.75rem; color: #6b7280; font-family: monospace; margin-top: 0.125rem;">{{ $car->plate_number }}</div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.375rem; background: {{ $healthColor['dot'] }}15; padding: 0.25rem 0.625rem; border-radius: 999px; border: 1px solid {{ $healthColor['dot'] }}30;">
                                <div style="width: 7px; height: 7px; border-radius: 50%; background: {{ $healthColor['dot'] }}; {{ $car->health === 'overdue' ? 'animation: pulse 2s infinite;' : '' }}"></div>
                                <span style="font-size: 0.6875rem; font-weight: 700; color: {{ $healthColor['text'] }};">{{ $healthColor['label'] }}</span>
                            </div>
                        </div>

                        {{-- Metrics --}}
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                            <div style="background: rgba(255,255,255,0.7); border-radius: 0.5rem; padding: 0.625rem;">
                                <div style="font-size: 0.625rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 500;">Current Mileage</div>
                                <div style="font-size: 1.125rem; font-weight: 700; color: #111827; font-family: monospace;">{{ number_format($car->mileage) }} km</div>
                            </div>
                            <div style="background: rgba(255,255,255,0.7); border-radius: 0.5rem; padding: 0.625rem;">
                                <div style="font-size: 0.625rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 500;">Since Service</div>
                                <div style="font-size: 1.125rem; font-weight: 700; color: #111827; font-family: monospace;">{{ number_format($car->km_since_service) }} km</div>
                            </div>
                        </div>

                        {{-- Last Service --}}
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-size: 0.75rem; color: #6b7280;">
                                <i data-feather="calendar" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle;"></i>
                                Last: {{ $car->last_service_date ? $car->last_service_date->format('M d, Y') : 'Never' }}
                            </div>
                            @if($car->days_since_service !== null)
                                <span style="font-size: 0.6875rem; color: {{ $car->days_since_service > 180 ? '#ef4444' : ($car->days_since_service > 90 ? '#f59e0b' : '#6b7280') }}; font-weight: 600;">{{ $car->days_since_service }}d ago</span>
                            @endif
                        </div>

                        {{-- Overdue alerts --}}
                        @if($car->overdue_schedules->count() > 0)
                            <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid {{ $healthColor['border'] }};">
                                @foreach($car->overdue_schedules->take(2) as $sched)
                                    <div style="font-size: 0.6875rem; color: {{ $healthColor['text'] }}; display: flex; align-items: center; gap: 0.375rem; margin-bottom: 0.25rem;">
                                        <i data-feather="alert-circle" style="width: 11px; height: 11px; flex-shrink: 0;"></i>
                                        {{ $sched->name }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </a>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-muted);">
                        <i data-feather="check-circle" style="display: block; margin: 0 auto 0.75rem; width: 32px; height: 32px; color: #10b981;"></i>
                        <div style="font-size: 0.9375rem; font-weight: 500;">{{ $filter !== 'all' ? 'No vehicles in this category.' : 'No vehicles registered yet.' }}</div>
                    </div>
                @endforelse
            </div>
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
