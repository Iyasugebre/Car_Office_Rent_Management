@extends('layouts.admin')

@section('title', 'Agreement Preparation')

@section('content')
    <div class="table-container">
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 2.5rem; max-width: 900px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem;">
                <div>
                    <h3 style="margin: 0 0 0.5rem; font-size: 1.5rem; color: var(--dark-bg);">Contract Preparation</h3>
                    <p style="margin: 0; color: var(--text-muted); font-size: 0.875rem;">Record the binding agreement for the branch opening.</p>
                </div>
                <a href="{{ route('admin.agreements.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem; font-weight: 600;">Cancel</a>
            </div>

            <form action="{{ route('admin.agreements.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                @if($branchRequest)
                    <input type="hidden" name="branch_request_id" value="{{ $branchRequest->id }}">
                    <div style="background: #f8fafc; border: 1px dashed var(--primary-color); padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
                        <i data-feather="check-circle" style="color: var(--primary-color);"></i>
                        <div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Linked Proposal</span>
                            <div style="font-weight: 700;">{{ $branchRequest->reference_number }} - {{ $branchRequest->branch_name }}</div>
                        </div>
                    </div>
                @else
                    <div style="margin-bottom: 2rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Select Branch Request</label>
                        <select name="branch_request_id" style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd;" required>
                            <option value="">-- Choose Approved Request --</option>
                            @foreach(\App\Models\BranchRequest::where('status', 'approved')->get() as $req)
                                <option value="{{ $req->id }}">{{ $req->reference_number }} - {{ $req->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <!-- Contract Identity -->
                    <div style="grid-column: span 2;">
                        <h4 style="font-size: 0.875rem; text-transform: uppercase; color: var(--primary-color); margin-bottom: 1rem; letter-spacing: 0.05em;">1. Agreement Identity</h4>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Internal Agreement ID</label>
                        <input type="text" name="agreement_id" placeholder="e.g. CONT-2024-X101" style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd;" required>
                        @error('agreement_id') <span style="font-size: 0.75rem; color: var(--danger);">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Landlord/Owner Full Name</label>
                        <input type="text" name="landlord_name" placeholder="Name of the person/entity signing the contract" value="{{ $branchRequest->landlord_details ?? '' }}" style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd;" required>
                    </div>

                    <!-- Location & Document -->
                    <div style="grid-column: span 2; margin-top: 1rem;">
                        <h4 style="font-size: 0.875rem; text-transform: uppercase; color: var(--primary-color); margin-bottom: 1rem; letter-spacing: 0.05em;">2. Property & Digital File</h4>
                    </div>
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Final Property Address (As per contract)</label>
                        <textarea name="property_address" style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd; min-height: 80px;" required>{{ $branchRequest->proposed_office ?? '' }}</textarea>
                    </div>
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Upload Scanned Contract (PDF/Image)</label>
                        <div style="border: 2px dashed var(--border-color); padding: 2rem; text-align: center; border-radius: 1rem; background: #fafafa; cursor: pointer; position: relative;">
                            <i data-feather="upload-cloud" style="width: 40px; height: 40px; color: var(--text-muted); margin-bottom: 1rem;"></i>
                            <p style="margin: 0; font-size: 0.875rem; color: var(--text-muted);">Click to browse files or drag and drop here.</p>
                            <input type="file" name="contract_file" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;" required>
                        </div>
                        @error('contract_file') <span style="font-size: 0.75rem; color: var(--danger);">{{ $message }}</span> @enderror
                    </div>

                    <!-- Financials -->
                    <div style="grid-column: span 2; margin-top: 1rem;">
                        <h4 style="font-size: 0.875rem; text-transform: uppercase; color: var(--primary-color); margin-bottom: 1rem; letter-spacing: 0.05em;">3. Financial & Schedule</h4>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Actual Monthly Rent ($)</label>
                        <input type="number" step="0.01" name="monthly_rent" value="{{ $branchRequest->estimated_rent ?? '' }}" style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd;" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Payment Schedule</label>
                        <select name="payment_schedule" style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd;" required>
                            <option value="Monthly">Monthly</option>
                            <option value="Quarterly">Quarterly</option>
                            <option value="Bi-Annual">Bi-Annual</option>
                            <option value="Annually">Annually</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Agreement Start Date</label>
                        <input type="date" name="start_date" style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd;" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Agreement End Date</label>
                        <input type="date" name="end_date" style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd;" required>
                    </div>
                </div>

                <div style="margin-top: 3rem; display: flex; justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 2rem;">
                    <button type="submit" style="background: var(--dark-bg); color: white; padding: 1rem 3rem; border-radius: 0.5rem; border: none; font-weight: 700; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                        Save & Activate Agreement
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
