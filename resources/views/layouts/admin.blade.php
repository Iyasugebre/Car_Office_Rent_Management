<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Car & Office Rent</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;&display=swap" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header" style="font-size: 1rem;">
                <i data-feather="grid"></i>
                <span>CAR & OFFICE RENT - ERP</span>
            </div>
            <nav class="sidebar-menu">
                <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i data-feather="home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.cars.index') }}" class="menu-item {{ request()->routeIs('admin.cars.*') ? 'active' : '' }}">
                    <i data-feather="truck"></i>
                    <span>Manage Cars</span>
                </a>
                <a href="{{ route('admin.services.index') }}" class="menu-item {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                    <i data-feather="tool"></i>
                    <span>Maintenance</span>
                </a>
                @php
                    $overdueVehicles = \App\Models\Car::where('status', '!=', 'decommissioned')->get()->filter(fn($c) => $c->service_health === 'overdue')->count();
                @endphp
                <a href="{{ route('admin.service-tracker.index') }}" class="menu-item {{ request()->routeIs('admin.service-tracker.*') ? 'active' : '' }}" style="position:relative;">
                    <i data-feather="activity"></i>
                    <span>Service Tracker</span>
                    @if($overdueVehicles > 0)
                        <span style="margin-left:auto; background:#ef4444; color:white; font-size:.625rem; font-weight:800; min-width:18px; height:18px; border-radius:999px; display:flex; align-items:center; justify-content:center; padding:0 .3rem;">{{ $overdueVehicles }}</span>
                    @endif
                </a>

                @php
                    $overdueLegal = \App\Models\Car::where('status', '!=', 'decommissioned')->get()->filter(fn($c) => $c->bolo_status === 'overdue' || $c->inspection_status === 'overdue')->count();
                @endphp
                <a href="{{ route('admin.legal-tracker.index') }}" class="menu-item {{ request()->routeIs('admin.legal-tracker.*') ? 'active' : '' }}" style="position:relative;">
                    <i data-feather="shield"></i>
                    <span>Bolo & Inspection</span>
                    @if($overdueLegal > 0)
                        <span style="margin-left:auto; background:#ef4444; color:white; font-size:.625rem; font-weight:800; min-width:18px; height:18px; border-radius:999px; display:flex; align-items:center; justify-content:center; padding:0 .3rem;">{{ $overdueLegal }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.offices.index') }}" class="menu-item {{ request()->routeIs('admin.offices.index') ? 'active' : '' }}">
                    <i data-feather="briefcase"></i>
                    <span>Manage Offices</span>
                </a>
                <a href="{{ route('admin.branch-utilities.index') }}" class="menu-item {{ request()->routeIs('admin.branch-utilities.*') ? 'active' : '' }}">
                    <i data-feather="zap"></i>
                    <span>Branch Utilities</span>
                </a>
                <a href="{{ route('admin.bookings.index') }}" class="menu-item {{ request()->routeIs('admin.bookings.index') ? 'active' : '' }}">
                    <i data-feather="calendar"></i>
                    <span>Bookings</span>
                </a>
                <a href="{{ route('admin.branch-requests.index') }}" class="menu-item {{ request()->routeIs('admin.branch-requests.index') ? 'active' : '' }}">
                    <i data-feather="plus-circle"></i>
                    <span>Branch Requests</span>
                </a>
                <a href="{{ route('admin.agreements.index') }}" class="menu-item {{ request()->routeIs('admin.agreements.index') ? 'active' : '' }}">
                    <i data-feather="file-text"></i>
                    <span>Lease Agreements</span>
                </a>
                @php
                    $pendingRenewals = \App\Models\AgreementRenewal::where('status', 'pending_approval')->count();
                @endphp
                <a href="{{ route('admin.renewals.index') }}" class="menu-item {{ request()->routeIs('admin.renewals.*') ? 'active' : '' }}" style="position:relative;">
                    <i data-feather="refresh-cw"></i>
                    <span>Renewals & Amendments</span>
                    @if($pendingRenewals > 0)
                        <span style="margin-left:auto; background:#ef4444; color:white; font-size:.625rem; font-weight:800; min-width:18px; height:18px; border-radius:999px; display:flex; align-items:center; justify-content:center; padding:0 .3rem;">{{ $pendingRenewals }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.notifications.index') }}" class="menu-item {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}" style="position:relative; margin-top:2rem; border-top:1px solid #e2e8f0; padding-top:1.5rem;">
                    <i data-feather="bell"></i>
                    <span>Alert Center</span>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span style="margin-left:auto; background:#f59e0b; color:white; font-size:.625rem; font-weight:800; min-width:18px; height:18px; border-radius:999px; display:flex; align-items:center; justify-content:center; padding:0 .3rem;">{{ auth()->user()->unreadNotifications->count() }}</span>
                    @endif
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h2 style="margin: 0; font-size: 1.25rem;">@yield('title', 'Dashboard')</h2>
                </div>
                <div class="user-profile">
                    @php
                        $unreadNotifications = auth()->user()->unreadNotifications;
                        $criticalCount = $unreadNotifications->whereIn('data.urgency', ['critical', 'warning'])->count();
                        $unreadCount = $unreadNotifications->count();
                    @endphp

                    {{-- Notification Bell --}}
                    <div style="position: relative;">
                        <button onclick="toggleNotifications()" style="background: none; border: none; cursor: pointer; position: relative; padding: 0.5rem; color: var(--text-muted);">
                            <i data-feather="bell" style="width: 20px; height: 20px; {{ $criticalCount > 0 ? 'color: #ef4444;' : '' }}"></i>
                            @if($unreadCount > 0)
                                <span style="position: absolute; top: 2px; right: 2px; background: {{ $criticalCount > 0 ? '#ef4444' : '#f59e0b' }}; color: white; font-size: 0.625rem; font-weight: 800; width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; line-height: 1;">{{ $unreadCount }}</span>
                            @endif
                        </button>

                        {{-- Dropdown Panel --}}
                        <div id="notification-panel" style="display: none; position: absolute; right: 0; top: calc(100% + 0.5rem); width: 360px; background: white; border-radius: 1rem; border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.15); z-index: 1000; overflow: hidden;">
                            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 700; font-size: 0.875rem;"><a href="{{ route('admin.notifications.index') }}" style="color:var(--text-main); text-decoration:none;">System Alerts</a></span>
                                @if($unreadCount > 0)
                                <form action="{{ route('admin.notifications.markAllRead') }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" style="background: none; border: none; color: var(--primary-color); font-size: 0.75rem; cursor: pointer; font-weight: 600;">Mark all read</button>
                                </form>
                                @endif
                            </div>
                            <div style="max-height: 380px; overflow-y: auto;">
                                @forelse(auth()->user()->unreadNotifications->take(8) as $notification)
                                    @php
                                        $d = $notification->data;
                                        $urgencyColor = match($d['urgency'] ?? 'warning') {
                                            'critical' => ['bg' => '#fef2f2', 'text' => '#991b1b', 'border' => '#fecaca'],
                                            'warning'  => ['bg' => '#fff7ed', 'text' => '#9a3412', 'border' => '#fed7aa'],
                                            default    => ['bg' => '#fffbeb', 'text' => '#854d0e', 'border' => '#fde68a'],
                                        };
                                        $isAgreement = ($d['type'] ?? '') === 'agreement_expiry';
                                        $isMaintenance = ($d['type'] ?? '') === 'vehicle_maintenance';
                                        $isLegalExpiry = ($d['type'] ?? '') === 'legal_expiry';
                                        $isMaintenanceRequest = ($d['type'] ?? '') === 'maintenance_request';
                                        $isRentPayment = ($d['type'] ?? '') === 'rent_payment_due';
                                    @endphp

                                    @if($isAgreement)
                                        <a href="{{ route('admin.agreements.show', $d['agreement_id']) }}" style="display: block; padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6; text-decoration: none; transition: background 0.15s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                                <div style="background: {{ $urgencyColor['bg'] }}; border: 1px solid {{ $urgencyColor['border'] }}; color: {{ $urgencyColor['text'] }}; padding: 0.375rem; border-radius: 0.5rem; flex-shrink: 0;">
                                                    <i data-feather="clock" style="width: 14px; height: 14px;"></i>
                                                </div>
                                                <div>
                                                    <div style="font-size: 0.8125rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">{{ $d['branch_name'] ?? 'Branch' }}</div>
                                                    <div style="font-size: 0.75rem; color: #6b7280;">{{ $d['message'] ?? '' }}</div>
                                                    <span style="display: inline-block; margin-top: 0.375rem; font-size: 0.6875rem; font-weight: 700; color: {{ $urgencyColor['text'] }}; background: {{ $urgencyColor['bg'] }}; padding: 0.125rem 0.5rem; border-radius: 999px; border: 1px solid {{ $urgencyColor['border'] }};">{{ $d['days_remaining'] ?? 0 }} days left</span>
                                                </div>
                                            </div>
                                        </a>
                                    @elseif($isMaintenance)
                                        <a href="{{ route('admin.services.index') }}" style="display: block; padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6; text-decoration: none; transition: background 0.15s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                                <div style="background: {{ $urgencyColor['bg'] }}; border: 1px solid {{ $urgencyColor['border'] }}; color: {{ $urgencyColor['text'] }}; padding: 0.375rem; border-radius: 0.5rem; flex-shrink: 0;">
                                                    <i data-feather="tool" style="width: 14px; height: 14px;"></i>
                                                </div>
                                                <div>
                                                    <div style="font-size: 0.8125rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Vehicle: {{ $d['plate_number'] ?? 'Unknown' }}</div>
                                                    <div style="font-size: 0.75rem; color: #6b7280;">{{ $d['message'] ?? 'Maintenance required' }}</div>
                                                    <span style="display: inline-block; margin-top: 0.375rem; font-size: 0.6875rem; font-weight: 700; color: {{ $urgencyColor['text'] }}; background: {{ $urgencyColor['bg'] }}; padding: 0.125rem 0.5rem; border-radius: 999px; border: 1px solid {{ $urgencyColor['border'] }};">{{ $d['service_type'] ?? 'Service' }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    @elseif($isLegalExpiry)
                                        <a href="{{ route('admin.legal-tracker.index') }}" style="display: block; padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6; text-decoration: none; transition: background 0.15s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                                <div style="background: {{ $urgencyColor['bg'] }}; border: 1px solid {{ $urgencyColor['border'] }}; color: {{ $urgencyColor['text'] }}; padding: 0.375rem; border-radius: 0.5rem; flex-shrink: 0;">
                                                    <i data-feather="shield" style="width: 14px; height: 14px;"></i>
                                                </div>
                                                <div>
                                                    <div style="font-size: 0.8125rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Vehicle: {{ $d['plate_number'] ?? 'Unknown' }}</div>
                                                    <div style="font-size: 0.75rem; color: #6b7280;">{{ $d['message'] ?? 'Legal document expiry' }}</div>
                                                    <span style="display: inline-block; margin-top: 0.375rem; font-size: 0.6875rem; font-weight: 700; color: {{ $urgencyColor['text'] }}; background: {{ $urgencyColor['bg'] }}; padding: 0.125rem 0.5rem; border-radius: 999px; border: 1px solid {{ $urgencyColor['border'] }};">{{ ucfirst($d['legal_type'] ?? '') }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    @elseif($isMaintenanceRequest)
                                        <a href="{{ route('admin.services.show', $d['request_id']) }}" style="display: block; padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6; text-decoration: none; transition: background 0.15s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                                <div style="background: {{ $urgencyColor['bg'] }}; border: 1px solid {{ $urgencyColor['border'] }}; color: {{ $urgencyColor['text'] }}; padding: 0.375rem; border-radius: 0.5rem; flex-shrink: 0;">
                                                    <i data-feather="settings" style="width: 14px; height: 14px;"></i>
                                                </div>
                                                <div>
                                                    <div style="font-size: 0.8125rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Vehicle: {{ $d['plate_number'] ?? 'Unknown' }}</div>
                                                    <div style="font-size: 0.75rem; color: #6b7280;">{{ $d['message'] ?? 'Maintenance Status Update' }}</div>
                                                    <span style="display: inline-block; margin-top: 0.375rem; font-size: 0.6875rem; font-weight: 700; color: {{ $urgencyColor['text'] }}; background: {{ $urgencyColor['bg'] }}; padding: 0.125rem 0.5rem; border-radius: 999px; border: 1px solid {{ $urgencyColor['border'] }};">{{ ucfirst($d['status'] ?? '') }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    @elseif($isRentPayment)
                                        <a href="{{ route('admin.agreements.index') }}" style="display: block; padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6; text-decoration: none; transition: background 0.15s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                                <div style="background: {{ $urgencyColor['bg'] }}; border: 1px solid {{ $urgencyColor['border'] }}; color: {{ $urgencyColor['text'] }}; padding: 0.375rem; border-radius: 0.5rem; flex-shrink: 0;">
                                                    <i data-feather="dollar-sign" style="width: 14px; height: 14px;"></i>
                                                </div>
                                                <div>
                                                    <div style="font-size: 0.8125rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Office Rent Alert</div>
                                                    <div style="font-size: 0.75rem; color: #6b7280;">{{ $d['message'] ?? '' }}</div>
                                                    <span style="display: inline-block; margin-top: 0.375rem; font-size: 0.6875rem; font-weight: 700; color: {{ $urgencyColor['text'] }}; background: {{ $urgencyColor['bg'] }}; padding: 0.125rem 0.5rem; border-radius: 999px; border: 1px solid {{ $urgencyColor['border'] }};">Due in {{ $d['days_until_due'] ?? 0 }} days</span>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                @empty
                                    <div style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">
                                        <i data-feather="check-circle" style="display: block; margin: 0 auto 0.5rem;"></i>
                                        No pending alerts
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <span>{{ auth()->user()->name ?? 'Admin' }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; font-weight: 500;">
                            <i data-feather="log-out" style="width: 16px; height: 16px;"></i>
                            Logout
                        </button>
                    </form>
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="user"></i>
                    </div>
                </div>
            </header>

            <div class="content-body">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        feather.replace();

        function toggleNotifications() {
            const panel = document.getElementById('notification-panel');
            panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        }

        document.addEventListener('click', function(e) {
            const panel = document.getElementById('notification-panel');
            if (panel && !panel.contains(e.target) && !e.target.closest('button[onclick="toggleNotifications()"]')) {
                panel.style.display = 'none';
            }
        });
    </script>
</body>
</html>
