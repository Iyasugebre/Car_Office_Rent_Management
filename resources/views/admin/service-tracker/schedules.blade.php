@extends('layouts.admin')

@section('title', 'Service Schedule Rules')

@section('content')
    <div class="table-container">
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h3 style="margin: 0; font-size: 1.25rem;">Service Schedule Rules</h3>
                    <p style="margin: 0.25rem 0 0; font-size: 0.8125rem; color: var(--text-muted);">Configure when vehicles need service based on mileage or time</p>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <a href="{{ route('admin.service-tracker.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.8125rem; display: flex; align-items: center; gap: 0.375rem;">
                        <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i> Back
                    </a>
                    <a href="{{ route('admin.service-tracker.schedules.create') }}" class="menu-item" style="background: var(--primary-color); color: white; padding: 0.5rem 1rem; font-size: 0.8125rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i data-feather="plus" style="width: 15px; height: 15px;"></i> Add Rule
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div style="background: #ecfdf5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #10b981;">
                    {{ session('success') }}
                </div>
            @endif

            <div style="display: grid; gap: 0.75rem;">
                @forelse($schedules as $schedule)
                    <div style="border: 1px solid {{ $schedule->is_active ? 'var(--border-color)' : '#fca5a5' }}; border-radius: 0.875rem; padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; {{ !$schedule->is_active ? 'opacity: 0.6; background: #fafafa;' : '' }}">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="background: {{ $schedule->category_color }}15; color: {{ $schedule->category_color }}; padding: 0.625rem; border-radius: 0.625rem; border: 1px solid {{ $schedule->category_color }}25;">
                                @if($schedule->service_category === 'routine')
                                    <i data-feather="refresh-cw" style="width: 20px; height: 20px;"></i>
                                @elseif($schedule->service_category === 'inspection')
                                    <i data-feather="search" style="width: 20px; height: 20px;"></i>
                                @else
                                    <i data-feather="tool" style="width: 20px; height: 20px;"></i>
                                @endif
                            </div>
                            <div>
                                <div style="font-weight: 600; font-size: 0.9375rem; color: #111827;">{{ $schedule->name }}</div>
                                <div style="font-size: 0.8125rem; color: #6b7280; margin-top: 0.125rem;">
                                    @if($schedule->schedule_type === 'mileage')
                                        <span style="display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <i data-feather="navigation" style="width: 12px; height: 12px;"></i>
                                            Every {{ number_format($schedule->mileage_interval) }} km
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <i data-feather="calendar" style="width: 12px; height: 12px;"></i>
                                            Every {{ $schedule->month_interval }} month(s)
                                        </span>
                                    @endif
                                </div>
                                @if($schedule->description)
                                    <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem; max-width: 500px;">{{ Str::limit($schedule->description, 100) }}</div>
                                @endif
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="background: {{ $schedule->category_color }}15; color: {{ $schedule->category_color }}; font-size: 0.6875rem; font-weight: 700; padding: 0.2rem 0.625rem; border-radius: 999px; border: 1px solid {{ $schedule->category_color }}25; text-transform: uppercase;">{{ $schedule->service_category }}</span>
                            <form action="{{ route('admin.service-tracker.schedules.toggle', $schedule) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" title="{{ $schedule->is_active ? 'Deactivate' : 'Activate' }}" style="background: none; border: none; cursor: pointer; padding: 0.25rem; color: {{ $schedule->is_active ? '#22c55e' : '#ef4444' }};">
                                    <i data-feather="{{ $schedule->is_active ? 'toggle-right' : 'toggle-left' }}" style="width: 24px; height: 24px;"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.service-tracker.schedules.destroy', $schedule) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Delete this schedule rule?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; cursor: pointer; padding: 0.25rem; color: #ef4444;">
                                    <i data-feather="trash-2" style="width: 16px; height: 16px;"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                        <i data-feather="settings" style="display: block; margin: 0 auto 0.75rem; width: 32px; height: 32px;"></i>
                        <div style="font-size: 0.9375rem; font-weight: 500;">No service schedules defined yet.</div>
                        <p style="font-size: 0.8125rem; margin-top: 0.5rem;">Run <code>php artisan maintenance:check</code> to auto-create defaults, or add a custom rule.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() { feather.replace(); });
    </script>
@endsection
