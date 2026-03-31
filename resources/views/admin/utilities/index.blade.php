@extends('layouts.admin')

@section('title', 'Branch Utility Management')

@section('content')
    <div class="table-container">
        {{-- Header Section --}}
        <div style="background: white; border-radius: 1.5rem; border: 1px solid var(--border-color); padding: 2rem; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="margin: 0; font-size: 1.75rem; font-weight: 700;">Utility Billing</h1>
                    <p style="margin: 0.25rem 0 0; color: var(--text-muted);">Manage electricity, water, telephone, and internet accounts for all branches.</p>
                </div>
                <button onclick="toggleModal('addUtilityModal')" class="menu-item" style="background: var(--primary-color); color: white; padding: 0.75rem 1.5rem; font-weight: 600; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-feather="plus-circle" style="width: 18px;"></i> Register Utility
                </button>
            </div>
        </div>

        @if(session('success'))
            <div style="background: #ecfdf5; color: #065f46; padding: 1rem; border-radius: 0.75rem; margin-bottom: 2rem; border: 1px solid #10b981;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Branch Utility List --}}
        @forelse($offices as $office)
            <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); margin-bottom: 2rem; overflow: hidden;">
                <div style="background: #f8fafc; padding: 1.25rem 2rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="background: white; padding: 0.5rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
                            <i data-feather="map-pin" style="width: 16px; color: var(--primary-color);"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 1.125rem;">Branch: {{ $office->name }}</h3>
                    </div>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Utility Type</th>
                            <th>Provider & Account</th>
                            <th>Cycle</th>
                            <th>Next Due</th>
                            <th>Last Payment</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($office->utilities as $utility)
                            @php
                                $isDue = $utility->next_due_at && $utility->next_due_at->isPast();
                                $isSoon = $utility->next_due_at && $utility->next_due_at->diffInDays(now()) <= 7 && !$isDue;
                            @endphp
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        @php
                                            $icon = match($utility->utility_type) {
                                                'electricity' => 'zap',
                                                'water' => 'droplet',
                                                'telephone' => 'phone',
                                                'internet' => 'wifi',
                                                default => 'activity'
                                            };
                                            $iconColor = match($utility->utility_type) {
                                                'electricity' => '#eab308',
                                                'water' => '#3b82f6',
                                                'telephone' => '#8b5cf6',
                                                'internet' => '#ec4899',
                                                default => '#6b7280'
                                            };
                                        @endphp
                                        <div style="background: {{ $iconColor }}15; color: {{ $iconColor }}; padding: 0.5rem; border-radius: 999px;">
                                            <i data-feather="{{ $icon }}" style="width: 14px; height: 14px;"></i>
                                        </div>
                                        <span style="font-weight: 600;">{{ ucfirst($utility->utility_type) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 0.875rem; font-weight: 500;">{{ $utility->provider }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;">{{ $utility->account_number }}</div>
                                </td>
                                <td><span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600; color: #64748b;">{{ $utility->payment_cycle }}</span></td>
                                <td>
                                    @if($utility->next_due_at)
                                        <div style="color: {{ $isDue ? '#ef4444' : ($isSoon ? '#f59e0b' : '#1e293b') }}; font-weight: 600;">
                                            {{ $utility->next_due_at->format('M d, Y') }}
                                        </div>
                                        <div style="font-size: 0.6875rem; color: var(--text-muted);">
                                            {{ $isDue ? 'OVERDUE' : ($isSoon ? 'Due in ' . now()->diffInDays($utility->next_due_at) . ' days' : 'Scheduled') }}
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span style="font-size: 0.8125rem; color: var(--text-muted);">
                                        {{ $utility->last_paid_at ? $utility->last_paid_at->format('M d, Y') : 'Never' }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                        <form action="{{ route('admin.branch-utilities.recordPayment', $utility) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="menu-item" style="padding: 0.375rem 0.75rem; font-size: 0.75rem; background: #ecfdf5; color: #065f46; border: 1px solid #10b981;">
                                                <i data-feather="check" style="width: 14px;"></i> Log Payment
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.branch-utilities.destroy', $utility) }}" method="POST" onsubmit="return confirm('Remove this utility account?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: none; border: none; color: #94a3b8; cursor: pointer; padding: 0.375rem;" title="Delete">
                                                <i data-feather="trash-2" style="width: 16px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">No utilities registered for this branch.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @empty
            <div style="text-align: center; padding: 5rem 2rem; background: white; border-radius: 1.5rem; border: 1px dashed var(--border-color);">
                No branches found.
            </div>
        @endforelse
    </div>

    {{-- Add Utility Modal --}}
    <div id="addUtilityModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 2000; align-items: center; justify-content: center;">
        <div style="background: white; width: 480px; border-radius: 1.5rem; padding: 2.5rem; position: relative; box-shadow: 0 20px 50px rgba(0,0,0,0.2);">
            <button onclick="toggleModal('addUtilityModal')" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; cursor: pointer; color: #64748b;">
                <i data-feather="x"></i>
            </button>
            <h3 style="margin: 0 0 1.5rem; font-size: 1.5rem;">Register New Utility</h3>
            
            <form action="{{ route('admin.branch-utilities.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.5rem;">Branch</label>
                        <select name="office_id" required style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #cbd5e1;">
                            <option value="">Select Branch</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}">{{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.5rem;">Utility Type</label>
                        <select name="utility_type" required style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #cbd5e1;">
                            <option value="electricity">Electricity</option>
                            <option value="water">Water</option>
                            <option value="telephone">Telephone</option>
                            <option value="internet">Internet</option>
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.5rem;">Provider</label>
                            <input type="text" name="provider" placeholder="e.g. Nile Telecom" required style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #cbd5e1;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.5rem;">Account / ID</label>
                            <input type="text" name="account_number" placeholder="e.g. 10023445" required style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #cbd5e1;">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.5rem;">Payment Cycle</label>
                            <select name="payment_cycle" required style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #cbd5e1;">
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="annual">Annual</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.5rem;">First Due Date</label>
                            <input type="date" name="next_due_at" required style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #cbd5e1;">
                        </div>
                    </div>
                </div>

                <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                    <button type="button" onclick="toggleModal('addUtilityModal')" style="flex: 1; padding: 0.875rem; background: #f1f5f9; border: none; border-radius: 0.75rem; font-weight: 600; cursor: pointer;">Cancel</button>
                    <button type="submit" style="flex: 1; padding: 0.875rem; background: var(--primary-color); color: white; border: none; border-radius: 0.75rem; font-weight: 600; cursor: pointer;">Register</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleModal(id) {
            const modal = document.getElementById(id);
            modal.style.display = modal.style.display === 'none' ? 'flex' : 'none';
        }
        document.addEventListener('DOMContentLoaded', function() { feather.replace(); });
    </script>
@endsection
