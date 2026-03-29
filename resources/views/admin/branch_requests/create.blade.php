@extends('layouts.admin')

@section('title', 'Submit Branch Request')

@section('content')
    <div class="table-container">
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 2.5rem; max-width: 900px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem;">
                <div>
                    <h3 style="margin: 0 0 0.5rem; font-size: 1.5rem; color: var(--dark-bg);">New Branch Proposal</h3>
                    <p style="margin: 0; color: var(--text-muted); font-size: 0.875rem;">Submit this form for management review and approval of a new branch location.</p>
                </div>
                <a href="{{ route('admin.branch-requests.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem; font-weight: 600;">Cancel</a>
            </div>

            <form action="{{ route('admin.branch-requests.store') }}" method="POST">
                @csrf
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <!-- Branch Basics -->
                    <div style="grid-column: span 2;">
                        <h4 style="font-size: 0.875rem; text-transform: uppercase; color: var(--primary-color); margin-bottom: 1rem; letter-spacing: 0.05em;">1. Branch Identification</h4>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Branch Name</label>
                        <input type="text" name="branch_name" placeholder="e.g. Bole Medhanialem Branch" style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd;" required>
                        @error('branch_name') <span style="font-size: 0.75rem; color: var(--danger);">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">General Location (City/Area)</label>
                        <input type="text" name="location" placeholder="e.g. Addis Ababa / Bole Area" style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd;" required>
                        @error('location') <span style="font-size: 0.75rem; color: var(--danger);">{{ $message }}</span> @enderror
                    </div>

                    <!-- Office & Landlord -->
                    <div style="grid-column: span 2; margin-top: 1rem;">
                        <h4 style="font-size: 0.875rem; text-transform: uppercase; color: var(--primary-color); margin-bottom: 1rem; letter-spacing: 0.05em;">2. Property & Owner Details</h4>
                    </div>
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Proposed Office (Building/Building Unit Details)</label>
                        <textarea name="proposed_office" placeholder="Detail the specific building name, floor, and unit number under consideration." style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd; min-height: 80px;" required></textarea>
                    </div>
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Landlord/Owner Details</label>
                        <textarea name="landlord_details" placeholder="Full name and contact phone/email of the property owner/representative." style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd; min-height: 80px;" required></textarea>
                    </div>

                    <!-- Financial & Priority -->
                    <div style="grid-column: span 2; margin-top: 1rem;">
                        <h4 style="font-size: 0.875rem; text-transform: uppercase; color: var(--primary-color); margin-bottom: 1rem; letter-spacing: 0.05em;">3. Financial & Urgency</h4>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Estimated Monthly Rent ($)</label>
                        <input type="number" step="0.01" name="estimated_rent" style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd;" required>
                        @error('estimated_rent') <span style="font-size: 0.75rem; color: var(--danger);">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Priority Level</label>
                        <select name="priority" style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd;" required>
                            <option value="low">Low - Routine expansion</option>
                            <option value="medium" selected>Medium - Targeted growth</option>
                            <option value="high">High - Immediate requirement</option>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 3rem; display: flex; justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 2rem;">
                    <button type="submit" style="background: var(--primary-color); color: white; padding: 1rem 3rem; border-radius: 0.5rem; border: none; font-weight: 700; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.2s;">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function() {
            this.querySelector('button[type="submit"]').disabled = true;
            this.querySelector('button[type="submit"]').innerText = 'Submitting...';
        });
    </script>
@endsection
