@extends('layouts.admin')

@section('title', 'Alert Center')

@section('content')
    <div class="table-container">
        {{-- Stats & Header --}}
        <div style="background: white; border-radius: 1.5rem; border: 1px solid var(--border-color); padding: 2rem; margin-bottom: 2rem; box-shadow: var(--card-shadow);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="margin: 0; font-size: 1.75rem; font-weight: 700; color: #111827;">System Alerts</h1>
                    <p style="margin: 0.25rem 0 0; font-size: 0.9375rem; color: var(--text-muted);">Monitor and manage all fleet and office lease compliance notifications.</p>
                </div>
                <div style="display: flex; gap: 1rem;">
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <form action="{{ route('admin.notifications.markAllRead') }}" method="POST">
                            @csrf
                            <button type="submit" class="menu-item" style="background: #f1f5f9; color: #475569; padding: 0.75rem 1.25rem; font-weight: 600; border: none; font-size: 0.8125rem;">
                                <i data-feather="check-square" style="width: 16px; height: 16px;"></i> Mark All Read
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Tabs --}}
            <div style="display: flex; gap: 0.5rem; margin-top: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1px;">
                <a href="{{ route('admin.notifications.index', ['filter' => 'all']) }}" 
                   style="padding: 0.75rem 1.5rem; text-decoration: none; font-weight: 600; font-size: 0.875rem; color: {{ $filter === 'all' ? 'var(--primary-color)' : 'var(--text-muted)' }}; border-bottom: 2px solid {{ $filter === 'all' ? 'var(--primary-color)' : 'transparent' }}; margin-bottom: -1px; transition: all 0.2s;">
                    All Alerts ({{ auth()->user()->notifications()->count() }})
                </a>
                <a href="{{ route('admin.notifications.index', ['filter' => 'vehicles']) }}" 
                   style="padding: 0.75rem 1.5rem; text-decoration: none; font-weight: 600; font-size: 0.875rem; color: {{ $filter === 'vehicles' ? 'var(--primary-color)' : 'var(--text-muted)' }}; border-bottom: 2px solid {{ $filter === 'vehicles' ? 'var(--primary-color)' : 'transparent' }}; margin-bottom: -1px; transition: all 0.2s;">
                    <i data-feather="truck" style="width: 14px; display: inline-block; vertical-align: middle; margin-right: 0.375rem;"></i> Vehicle Alerts
                </a>
                <a href="{{ route('admin.notifications.index', ['filter' => 'office']) }}" 
                   style="padding: 0.75rem 1.5rem; text-decoration: none; font-weight: 600; font-size: 0.875rem; color: {{ $filter === 'office' ? 'var(--primary-color)' : 'var(--text-muted)' }}; border-bottom: 2px solid {{ $filter === 'office' ? 'var(--primary-color)' : 'transparent' }}; margin-bottom: -1px; transition: all 0.2s;">
                    <i data-feather="briefcase" style="width: 14px; display: inline-block; vertical-align: middle; margin-right: 0.375rem;"></i> Office Alerts
                </a>
            </div>
        </div>

        @if(session('success'))
            <div style="background: #ecfdf5; color: #065f46; padding: 1rem; border-radius: 0.75rem; margin-bottom: 2rem; border: 1px solid #10b981; display: flex; align-items: center; gap: 0.75rem;">
                <i data-feather="check-circle" style="width: 20px;"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Notification List --}}
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @forelse($notifications as $notification)
                @php
                    $d = $notification->data;
                    $urgencyColor = match($d['urgency'] ?? 'info') {
                        'critical' => ['bg' => '#fef2f2', 'text' => '#991b1b', 'border' => '#fee2e2', 'icon_bg' => '#ef4444', 'icon' => 'alert-octagon'],
                        'warning'  => ['bg' => '#fff7ed', 'text' => '#9a3412', 'border' => '#ffedd5', 'icon_bg' => '#f59e0b', 'icon' => 'alert-triangle'],
                        default    => ['bg' => '#f0f9ff', 'text' => '#075985', 'border' => '#e0f2fe', 'icon_bg' => '#0ea5e9', 'icon' => 'bell'],
                    };
                    $isUnread = $notification->unread();
                @endphp
                <div style="background: {{ $isUnread ? 'white' : '#f9fafb' }}; border: 1px solid {{ $isUnread ? $urgencyColor['border'] : 'var(--border-color)' }}; border-radius: 1rem; padding: 0.5rem; transition: all 0.2s; position: relative; {{ $isUnread ? 'box-shadow: 0 4px 15px -3px rgba(0,0,0,0.05);' : 'opacity: 0.75;' }}">
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem;">
                        <div style="display: flex; align-items: center; gap: 1.25rem; flex: 1;">
                            <div style="background: {{ $urgencyColor['icon_bg'] }}; color: white; padding: 0.75rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px {{ $urgencyColor['icon_bg'] }}30;">
                                <i data-feather="{{ $urgencyColor['icon'] }}" style="width: 20px; height: 20px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
                                    <span style="font-weight: 700; font-size: 1rem; color: #111827;">{{ ucfirst(str_replace('_', ' ', $d['type'] ?? 'General Alert')) }}</span>
                                    <span style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: {{ $urgencyColor['text'] }}; background: {{ $urgencyColor['bg'] }}; padding: 0.125rem 0.625rem; border-radius: 999px;">{{ $d['urgency'] ?? 'Notice' }}</span>
                                    @if($isUnread)
                                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444; animation: pulse 2s infinite;"></div>
                                    @endif
                                </div>
                                <div style="font-size: 0.9375rem; color: #4b5563; line-height: 1.5;">{{ $d['message'] ?? '' }}</div>
                                <div style="display: flex; align-items: center; gap: 1rem; margin-top: 0.5rem;">
                                    <span style="font-size: 0.75rem; color: #9ca3af; display: flex; align-items: center; gap: 0.375rem;">
                                        <i data-feather="calendar" style="width: 12px;"></i> {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                    @if(isset($d['plate_number']))
                                        <span style="font-size: 0.75rem; color: #9ca3af; background: #f3f4f6; padding: 2px 8px; border-radius: 4px; font-family: monospace;">{{ $d['plate_number'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            @php
                                $actionUrl = match($d['type'] ?? '') {
                                    'vehicle_maintenance' => route('admin.service-tracker.index'),
                                    'legal_expiry' => route('admin.legal-tracker.index'),
                                    'maintenance_request' => route('admin.services.show', $d['request_id'] ?? 0),
                                    'agreement_expiry' => route('admin.agreements.index'),
                                    'rent_payment_due' => route('admin.agreements.index'),
                                    default => '#',
                                };
                            @endphp
                            <a href="{{ $actionUrl }}" class="menu-item" style="padding: 0.5rem 1rem; background: var(--primary-color); color: white; font-size: 0.8125rem; font-weight: 600;">Take Action</a>
                            
                            @if($isUnread)
                                <form action="{{ route('admin.notifications.markRead', $notification->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 0.5rem; transition: color 0.15s;" title="Dismiss" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#9ca3af'">
                                        <i data-feather="x" style="width: 18px; height: 18px;"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 5rem 2rem; background: white; border-radius: 1.5rem; border: 1px dashed var(--border-color);">
                    <div style="background: #f8fafc; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                        <i data-feather="check-circle" style="width: 40px; height: 40px; color: #10b981;"></i>
                    </div>
                    <h3 style="margin: 0; font-size: 1.25rem;">All Clear!</h3>
                    <p style="margin: 0.5rem 0 0; color: var(--text-muted);">There are no pending alerts in this category.</p>
                </div>
            @endforelse

            <div style="margin-top: 1.5rem;">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>

    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(1.1); }
        }
        .pagination { display: flex; gap: 0.25rem; list-style: none; padding: 0; }
        .pagination li a, .pagination li span { padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 0.375rem; color: var(--text-main); text-decoration: none; font-size: 0.8125rem; }
        .pagination li.active span { background: var(--primary-color); color: white; border-color: var(--primary-color); }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() { feather.replace(); });
    </script>
@endsection
