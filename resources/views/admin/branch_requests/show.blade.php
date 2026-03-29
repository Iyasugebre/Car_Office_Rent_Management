@extends('layouts.admin')

@section('title', 'Review Branch Request')

@section('content')
    <div class="table-container">
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 2.5rem; max-width: 900px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem;">
                <div>
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.875rem; font-weight: 700; color: var(--primary-color); background: rgba(99, 102, 241, 0.1); padding: 0.25rem 0.75rem; border-radius: 0.5rem;">{{ $branchRequest->reference_number }}</span>
                        <h3 style="margin: 0; font-size: 1.5rem; color: var(--dark-bg);">{{ $branchRequest->branch_name }}</h3>
                    </div>
                    <p style="margin: 0; color: var(--text-muted); font-size: 0.875rem;">Submitted by {{ $branchRequest->requester->name }} on {{ $branchRequest->created_at->format('M d, Y') }}</p>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <span class="status-badge status-{{ strtolower($branchRequest->status) }}" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                        {{ ucfirst($branchRequest->status) }}
                    </span>
                    @if($branchRequest->status == 'approved')
                        <a href="{{ route('admin.agreements.create', ['branch_request_id' => $branchRequest->id]) }}" class="menu-item" style="background: var(--primary-color); color: white; padding: 0.5rem 1.25rem; font-size: 0.875rem; border-radius: 0.5rem;">
                            <i data-feather="file-text" style="width: 16px; height: 16px;"></i> Prepare Agreement
                        </a>
                    @endif
                </div>
            </div>

            <!-- Content Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
                <!-- Column 1: Property Details -->
                <div>
                    <h4 style="font-size: 0.875rem; text-transform: uppercase; color: var(--primary-color); margin-bottom: 1.25rem; letter-spacing: 0.05em;">Property Information</h4>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Location</label>
                        <div style="font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                            <i data-feather="map-pin" style="width: 14px; height: 14px;"></i>
                            {{ $branchRequest->location }}
                        </div>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Proposed Office Space</label>
                        <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; font-size: 0.875rem; line-height: 1.6;">
                            {{ $branchRequest->proposed_office }}
                        </div>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Landlord/Owner Details</label>
                        <div style="font-size: 0.875rem; white-space: pre-line;">
                            {{ $branchRequest->landlord_details }}
                        </div>
                    </div>
                </div>

                <!-- Column 2: Financials & Admin -->
                <div>
                    <h4 style="font-size: 0.875rem; text-transform: uppercase; color: var(--primary-color); margin-bottom: 1.25rem; letter-spacing: 0.05em;">Financials & Priority</h4>

                    <div style="background: #f1f5f9; padding: 1.5rem; border-radius: 1rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <label style="display: block; font-size: 0.75rem; color: var(--text-muted);">Estimated Rent</label>
                            <div style="font-size: 1.5rem; font-weight: 800; color: var(--dark-bg); font-family: monospace;">
                                ${{ number_format($branchRequest->estimated_rent, 2) }} <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal;">/ month</span>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <label style="display: block; font-size: 0.75rem; color: var(--text-muted);">Priority Level</label>
                            <div style="font-size: 1rem; font-weight: 700; color: {{ $branchRequest->priority == 'high' ? 'var(--danger)' : 'var(--text-main)' }}; text-transform: uppercase;">
                                {{ $branchRequest->priority }}
                            </div>
                        </div>
                    </div>

                    <h4 style="font-size: 0.875rem; text-transform: uppercase; color: var(--primary-color); margin-bottom: 1.25rem; letter-spacing: 0.05em;">Workflow Actions</h4>
                    
                    <form action="{{ route('admin.branch-requests.update', $branchRequest) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.5rem;">Update Status</label>
                            <select name="status" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd;">
                                <option value="submitted" {{ $branchRequest->status == 'submitted' ? 'selected' : '' }}>Submitted (Pending)</option>
                                <option value="review" {{ $branchRequest->status == 'review' ? 'selected' : '' }}>Under Review</option>
                                <option value="approved" {{ $branchRequest->status == 'approved' ? 'selected' : '' }}>Approve Proposal</option>
                                <option value="rejected" {{ $branchRequest->status == 'rejected' ? 'selected' : '' }}>Reject Proposal</option>
                                <option value="active" {{ $branchRequest->status == 'active' ? 'selected' : '' }}>Mark as Active (Opening)</option>
                            </select>
                        </div>

                        <div style="margin-bottom: 2rem;">
                            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.5rem;">Administrator Remarks</label>
                            <textarea name="remarks" placeholder="Enter notes or feedback for the requester..." style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd; min-height: 80px;">{{ $branchRequest->remarks }}</textarea>
                        </div>

                        <button type="submit" style="width: 100%; background: var(--dark-bg); color: white; padding: 1rem; border-radius: 0.5rem; border: none; font-weight: 700; cursor: pointer; transition: background 0.2s;">
                            Apply Changes
                        </button>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div style="margin-top: 3rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <a href="{{ route('admin.branch-requests.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i> Back to list
                </a>
                <div style="font-size: 0.75rem; color: var(--text-muted);">
                    Last updated {{ $branchRequest->updated_at->diffForHumans() }}
                </div>
            </div>
        </div>
    </div>
@endsection
