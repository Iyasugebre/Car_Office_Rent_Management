@extends('layouts.admin')

@section('title', $car->make . ' ' . $car->model . ' — Service History')

@section('content')
    <div class="table-container">
        @if(session('success'))
            <div style="background: #ecfdf5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #10b981;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Vehicle Header Card --}}
        @php
            $health = $car->service_health;
            $hc = match($health) {
                'overdue' => ['bg' => '#fef2f2', 'border' => '#fca5a5', 'text' => '#991b1b', 'dot' => '#ef4444', 'label' => 'SERVICE OVERDUE'],
                'warning' => ['bg' => '#fff7ed', 'border' => '#fed7aa', 'text' => '#9a3412', 'dot' => '#f59e0b', 'label' => 'APPROACHING SERVICE'],
                default   => ['bg' => '#f0fdf4', 'border' => '#bbf7d0', 'text' => '#166534', 'dot' => '#22c55e', 'label' => 'ON TRACK'],
            };
        @endphp
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 1.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                        <h2 style="margin: 0; font-size: 1.5rem;">{{ $car->make }} {{ $car->model }}</h2>
                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; background: {{ $hc['bg'] }}; padding: 0.25rem 0.75rem; border-radius: 999px; border: 1px solid {{ $hc['border'] }}; font-size: 0.6875rem; font-weight: 700; color: {{ $hc['text'] }};">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: {{ $hc['dot'] }}; {{ $health === 'overdue' ? 'animation: pulse 2s infinite;' : '' }}"></span>
                            {{ $hc['label'] }}
                        </span>
                    </div>
                    <div style="display: flex; gap: 1.5rem; font-size: 0.875rem; color: #6b7280;">
                        <span><strong>Plate:</strong> {{ $car->plate_number }}</span>
                        <span><strong>Year:</strong> {{ $car->year }}</span>
                        <span><strong>Location:</strong> {{ $car->office->name ?? 'N/A' }}</span>
                        <span><strong>Status:</strong> {{ ucfirst($car->status) }}</span>
                    </div>
                </div>
                <a href="{{ route('admin.service-tracker.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-feather="arrow-left" style="width: 16px; height: 16px;"></i> Back to Fleet
                </a>
            </div>

            {{-- Key Metrics --}}
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-top: 1.25rem;">
                <div style="background: #f8fafc; border-radius: 0.75rem; padding: 1rem; border: 1px solid #e2e8f0;">
                    <div style="font-size: 0.6875rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 500;">Current Mileage</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #111827; font-family: monospace;">{{ number_format($car->mileage) }} km</div>
                </div>
                <div style="background: #f8fafc; border-radius: 0.75rem; padding: 1rem; border: 1px solid #e2e8f0;">
                    <div style="font-size: 0.6875rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 500;">Km Since Service</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: {{ $car->km_since_service >= 5000 ? '#ef4444' : ($car->km_since_service >= 4000 ? '#f59e0b' : '#111827') }}; font-family: monospace;">{{ number_format($car->km_since_service) }} km</div>
                </div>
                <div style="background: #f8fafc; border-radius: 0.75rem; padding: 1rem; border: 1px solid #e2e8f0;">
                    <div style="font-size: 0.6875rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 500;">Last Serviced</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $car->last_service_date ? $car->last_service_date->format('M d') : '—' }}</div>
                    @if($car->days_since_service !== null)
                        <div style="font-size: 0.75rem; color: #6b7280;">{{ $car->days_since_service }} days ago</div>
                    @endif
                </div>
                <div style="background: #f8fafc; border-radius: 0.75rem; padding: 1rem; border: 1px solid #e2e8f0;">
                    <div style="font-size: 0.6875rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 500;">Service Count</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $car->serviceLogs->count() }}</div>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            {{-- Overdue Alerts + Quick Actions --}}
            <div>
                {{-- Overdue Schedules --}}
                @if($overdueSchedules->count() > 0)
                    <div style="background: #fef2f2; border-radius: 1rem; border: 1px solid #fca5a5; padding: 1.25rem; margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                            <i data-feather="alert-triangle" style="width: 18px; height: 18px; color: #ef4444;"></i>
                            <h4 style="margin: 0; color: #991b1b; font-size: 0.9375rem;">Overdue Service Schedules</h4>
                        </div>
                        @foreach($overdueSchedules as $sched)
                            <div style="background: white; border-radius: 0.625rem; padding: 0.875rem; margin-bottom: 0.5rem; border: 1px solid #fecaca;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <div style="font-size: 0.8125rem; font-weight: 600; color: #111827;">{{ $sched->name }}</div>
                                        <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.125rem;">
                                            @if($sched->schedule_type === 'mileage')
                                                {{ number_format($sched->overdueAmount($car)) }} km overdue
                                            @else
                                                {{ $sched->overdueAmount($car) }} days overdue
                                            @endif
                                        </div>
                                    </div>
                                    <span style="background: {{ $sched->category_color }}18; color: {{ $sched->category_color }}; font-size: 0.6875rem; font-weight: 700; padding: 0.2rem 0.625rem; border-radius: 999px; border: 1px solid {{ $sched->category_color }}30;">{{ ucfirst($sched->service_category) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Quick Mileage Update --}}
                <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 1.25rem; margin-bottom: 1.5rem;">
                    <h4 style="margin: 0 0 1rem; font-size: 0.9375rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i data-feather="activity" style="width: 16px; height: 16px; color: var(--primary-color);"></i>
                        Update Mileage
                    </h4>
                    <form action="{{ route('admin.service-tracker.update-mileage', $car) }}" method="POST" style="display: flex; gap: 0.75rem;">
                        @csrf
                        <input type="number" name="mileage" min="{{ $car->mileage }}" value="{{ $car->mileage }}" required style="flex: 1; padding: 0.625rem 0.875rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; font-family: monospace;">
                        <button type="submit" style="background: var(--primary-color); color: white; border: none; padding: 0.625rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; cursor: pointer;">Update</button>
                    </form>
                    @error('mileage')
                        <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.375rem;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Schedule Status --}}
                <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 1.25rem;">
                    <h4 style="margin: 0 0 1rem; font-size: 0.9375rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i data-feather="clock" style="width: 16px; height: 16px; color: var(--primary-color);"></i>
                        Active Schedule Rules
                    </h4>
                    @foreach($schedules as $sched)
                        @php
                            $isDue = $sched->isDueForCar($car);
                            $statusColor = $isDue ? '#ef4444' : '#22c55e';
                        @endphp
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.625rem 0; border-bottom: 1px solid #f3f4f6;">
                            <div>
                                <div style="font-size: 0.8125rem; font-weight: 500; color: #111827;">{{ $sched->name }}</div>
                                <div style="font-size: 0.6875rem; color: #6b7280;">
                                    @if($sched->schedule_type === 'mileage')
                                        Every {{ number_format($sched->mileage_interval) }} km
                                    @else
                                        Every {{ $sched->month_interval }} month(s)
                                    @endif
                                </div>
                            </div>
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $statusColor }};"></span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Log Service + Service History --}}
            <div>
                {{-- Log New Service --}}
                <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 1.25rem; margin-bottom: 1.5rem;">
                    <h4 style="margin: 0 0 1rem; font-size: 0.9375rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i data-feather="plus-circle" style="width: 16px; height: 16px; color: var(--primary-color);"></i>
                        Log Service Entry
                    </h4>
                    <form action="{{ route('admin.service-tracker.log-service', $car) }}" method="POST">
                        @csrf
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                            <div>
                                <label style="font-size: 0.75rem; font-weight: 500; color: #374151; display: block; margin-bottom: 0.25rem;">Service Type *</label>
                                <select name="service_type" required style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.8125rem;">
                                    <option value="routine">Routine</option>
                                    <option value="inspection">Inspection</option>
                                    <option value="major">Major Service</option>
                                    <option value="repair">Repair</option>
                                </select>
                            </div>
                            <div>
                                <label style="font-size: 0.75rem; font-weight: 500; color: #374151; display: block; margin-bottom: 0.25rem;">Schedule Rule</label>
                                <select name="schedule_id" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.8125rem;">
                                    <option value="">— None —</option>
                                    @foreach($schedules as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="font-size: 0.75rem; font-weight: 500; color: #374151; display: block; margin-bottom: 0.25rem;">Mileage at Service *</label>
                                <input type="number" name="mileage_at_service" value="{{ $car->mileage }}" required min="0" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.8125rem; box-sizing: border-box;">
                            </div>
                            <div>
                                <label style="font-size: 0.75rem; font-weight: 500; color: #374151; display: block; margin-bottom: 0.25rem;">Service Date *</label>
                                <input type="date" name="service_date" value="{{ now()->format('Y-m-d') }}" required style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.8125rem; box-sizing: border-box;">
                            </div>
                            <div>
                                <label style="font-size: 0.75rem; font-weight: 500; color: #374151; display: block; margin-bottom: 0.25rem;">Service Provider</label>
                                <input type="text" name="service_provider" placeholder="e.g. AutoCare Ltd." style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.8125rem; box-sizing: border-box;">
                            </div>
                            <div>
                                <label style="font-size: 0.75rem; font-weight: 500; color: #374151; display: block; margin-bottom: 0.25rem;">Cost</label>
                                <input type="number" name="cost" step="0.01" min="0" placeholder="0.00" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.8125rem; box-sizing: border-box;">
                            </div>
                        </div>
                        <div style="margin-top: 0.75rem;">
                            <label style="font-size: 0.75rem; font-weight: 500; color: #374151; display: block; margin-bottom: 0.25rem;">Description</label>
                            <textarea name="description" rows="2" placeholder="Service details..." style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.8125rem; resize: vertical; box-sizing: border-box;"></textarea>
                        </div>
                        <div style="margin-top: 0.75rem;">
                            <label style="font-size: 0.75rem; font-weight: 500; color: #374151; display: block; margin-bottom: 0.25rem;">Notes</label>
                            <textarea name="notes" rows="2" placeholder="Additional notes..." style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.8125rem; resize: vertical; box-sizing: border-box;"></textarea>
                        </div>
                        <button type="submit" style="margin-top: 1rem; background: var(--primary-color); color: white; border: none; padding: 0.625rem 1.5rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; width: 100%;">
                            Log Service
                        </button>
                    </form>
                </div>

                {{-- Service History --}}
                <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 1.25rem;">
                    <h4 style="margin: 0 0 1rem; font-size: 0.9375rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i data-feather="list" style="width: 16px; height: 16px; color: var(--primary-color);"></i>
                        Service History
                    </h4>
                    @forelse($car->serviceLogs as $log)
                        <div style="padding: 0.875rem; border: 1px solid #e5e7eb; border-radius: 0.625rem; margin-bottom: 0.625rem; position: relative;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                        <span style="background: {{ $log->service_type_color }}18; color: {{ $log->service_type_color }}; font-size: 0.625rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 999px; border: 1px solid {{ $log->service_type_color }}30; text-transform: uppercase;">{{ $log->service_type }}</span>
                                        @if($log->schedule)
                                            <span style="font-size: 0.6875rem; color: #6b7280;">{{ $log->schedule->name }}</span>
                                        @endif
                                    </div>
                                    @if($log->description)
                                        <div style="font-size: 0.8125rem; color: #374151; margin-top: 0.25rem;">{{ Str::limit($log->description, 80) }}</div>
                                    @endif
                                    <div style="display: flex; gap: 1rem; margin-top: 0.375rem; font-size: 0.6875rem; color: #6b7280;">
                                        <span>{{ number_format($log->mileage_at_service) }} km</span>
                                        @if($log->service_provider)
                                            <span>{{ $log->service_provider }}</span>
                                        @endif
                                        @if($log->cost)
                                            <span style="font-weight: 600; color: #111827;">${{ number_format($log->cost, 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 0.75rem; font-weight: 600; color: #111827;">{{ $log->service_date->format('M d, Y') }}</div>
                                    <span style="display: inline-block; margin-top: 0.25rem; background: {{ $log->status_color }}18; color: {{ $log->status_color }}; font-size: 0.625rem; font-weight: 700; padding: 0.1rem 0.5rem; border-radius: 999px;">{{ ucfirst($log->status) }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.875rem;">
                            <i data-feather="inbox" style="display: block; margin: 0 auto 0.5rem; width: 28px; height: 28px;"></i>
                            No service history yet. Log the first service entry above.
                        </div>
                    @endforelse
                </div>
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
