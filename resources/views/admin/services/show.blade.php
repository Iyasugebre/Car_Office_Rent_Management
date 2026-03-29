@extends('layouts.admin')

@section('title', 'Service Tracking & Execution')

@section('content')
<div class="form-container">
    <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 2.5rem; max-width: 1000px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                    <span style="font-size: 0.8125rem; font-weight: 700; color: {{ $service->status_color }}; background: {{ $service->status_color }}18; padding: 0.25rem 0.75rem; border-radius: 0.5rem; text-transform: uppercase;">
                        Status: {{ str_replace('_', ' ', $service->status) }}
                    </span>
                    <h3 style="margin: 0; font-size: 1.5rem; color: var(--dark-bg);">Maintenance Tracker</h3>
                </div>
                <p style="margin: 0; color: var(--text-muted); font-size: 0.875rem;">Requested on {{ $service->created_at->format('M d, Y') }} by <strong>{{ $service->requester->name }}</strong></p>
            </div>
            <div>
                <span class="status-badge" style="background: {{ $service->urgency_color }}18; color: {{ $service->urgency_color }}; border: 1px solid currentColor;">
                    Urgency: {{ ucfirst($service->urgency_level) }}
                </span>
            </div>
        </div>

        @if(session('success'))
            <div style="background: #ecfdf5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #10b981;">
                {{ session('success') }}
            </div>
        @endif

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
            <!-- Detailed Specs -->
            <div>
                <h4 style="font-size: 0.875rem; text-transform: uppercase; color: var(--primary-color); margin-bottom: 1.25rem; letter-spacing: 0.05em;">Initial Request Info</h4>
                
                <div style="margin-bottom: 1.5rem; background: #f8fafc; padding: 1.25rem; border-radius: 0.75rem; border: 1px solid var(--border-color);">
                    <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1rem;">
                        <div style="background: var(--dark-bg); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i data-feather="truck"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Vehicle Identification</div>
                            <div style="font-weight: 700; font-size: 1rem;">{{ $service->car->plate_number }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-main);">{{ $service->car->make }} {{ $service->car->model }} ({{ $service->car->year }})</div>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Problem / Issue Reported</label>
                    <div style="font-size: 0.875rem; color: var(--text-main); line-height: 1.6; background: white; border: 1px solid var(--border-color); border-radius: 0.5rem; padding: 1rem;">
                        {{ $service->problem_description }}
                    </div>
                </div>
            </div>

            <!-- Fleet Manager Workflow -->
            <div style="background: #fafafa; border: 1px solid var(--border-color); padding: 2rem; border-radius: 1.5rem;">
                <!-- Step 1: Fleet Manager Assingment -->
                @if($service->status === 'pending')
                    <h4 style="font-size: 0.875rem; text-transform: uppercase; color: var(--primary-color); margin-bottom: 1.5rem; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem;">
                        <i data-feather="user-check" style="width: 16px; height: 16px;"></i> Step 1: Assign Provider
                    </h4>

                    <form action="{{ route('admin.services.assign', $service) }}" method="POST">
                        @csrf
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Select Service Provider / Mechanic <span style="color:red;">*</span></label>
                            <input type="text" name="service_provider" required placeholder="e.g. Action Auto Repairs" style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: white;">
                        </div>

                        <button type="submit" style="width: 100%; background: var(--primary-color); color: white; padding: 1rem; border-radius: 0.5rem; border: none; font-weight: 600; cursor: pointer;">
                            Approve & Assign Vehicle
                        </button>
                    </form>

                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" style="margin-top: 1rem; text-align: center;">
                        @csrf @method('DELETE')
                        <button type="submit" style="background: none; border: none; color: #ef4444; font-size: 0.8125rem; font-weight: 600; cursor: pointer; text-decoration: underline;" onsubmit="return confirm('Are you sure you want to reject this request?');">Reject Request</button>
                    </form>

                <!-- Step 2: Mechanic Execution -->
                @elseif($service->status === 'approved' || $service->status === 'in_progress')
                    <h4 style="font-size: 0.875rem; text-transform: uppercase; color: var(--primary-color); margin-bottom: 1.5rem; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem;">
                        <i data-feather="activity" style="width: 16px; height: 16px;"></i> Step 2: Complete Service Execution
                    </h4>
                    
                    <div style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Assigned Provider</div>
                        <div style="font-weight: 700; color: #1e3a8a;">{{ $service->service_provider }}</div>
                    </div>

                    <form action="{{ route('admin.services.complete', $service) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Final Total Cost ($)</label>
                            <input type="number" step="0.01" name="cost" placeholder="0.00" style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: white;">
                        </div>

                        <div style="margin-bottom: 2rem;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Upload Service/Mechanic Report (Optional)</label>
                            <input type="file" name="service_report_file" accept=".pdf,.png,.jpg,.doc,.docx" style="width: 100%; padding: 0.5rem; border-radius: 0.5rem; border: 1px dashed var(--border-color); background: white; font-size: 0.875rem;">
                        </div>

                        <button type="submit" style="width: 100%; background: #10b981; color: white; padding: 1rem; border-radius: 0.75rem; border: none; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                            <i data-feather="check-square" style="width: 18px; height: 18px;"></i> Mark Completed
                        </button>
                    </form>

                <!-- Completion Status -->
                @elseif($service->status === 'completed')
                    <h4 style="font-size: 0.875rem; text-transform: uppercase; color: var(--primary-color); margin-bottom: 1.5rem; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem;">
                        <i data-feather="check-circle" style="width: 16px; height: 16px;"></i> Maintenance Concluded
                    </h4>

                    <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 1.5rem; margin-top: 0.5rem;">
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Service Provider</label>
                            <div style="font-size: 0.875rem; font-weight: 600;">{{ $service->service_provider ?? 'Internal' }}</div>
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Final Invoiced Cost</label>
                            <div style="font-size: 1.125rem; font-family: monospace; font-weight: 700; color: #166534;">
                                ${{ number_format($service->cost, 2) }}
                            </div>
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Cleared By Fleet Manager</label>
                            <div style="font-size: 0.875rem; font-weight: 500;">{{ $service->fleetManager->name ?? 'Admin System' }}</div>
                        </div>

                        @if($service->service_report_path)
                            <a href="{{ asset('storage/' . $service->service_report_path) }}" target="_blank" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; background: #f8fafc; border: 1px solid var(--border-color); border-radius: 0.5rem; text-decoration: none; color: var(--primary-color); font-size: 0.875rem; font-weight: 600; justify-content: center;">
                                <i data-feather="file-text" style="width: 16px; height: 16px;"></i> Open Service Report
                            </a>
                        @endif
                    </div>
                @else
                    <div style="text-align: center; color: #991b1b; padding: 2rem; background: #fef2f2; border-radius: 1rem; font-weight: 600;">
                        This request was heavily rejected / terminated.
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Footer -->
        <div style="margin-top: 3rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
            <a href="{{ route('admin.services.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; font-weight: 500;">
                <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i> Back to Maintenance Logs
            </a>
        </div>
    </div>
</div>
@endsection
