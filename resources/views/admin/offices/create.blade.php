@extends('layouts.admin')

@section('title', 'Add New Office/Branch')

@section('content')
    <div class="table-container">
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 2rem; max-width: 800px; margin: 0 auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h3 style="margin: 0; font-size: 1.25rem;">Office Location Details</h3>
                <a href="{{ route('admin.offices.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem;">Cancel</a>
            </div>

            <form action="{{ route('admin.offices.store') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Office/Branch Name</label>
                        <input type="text" name="name" placeholder="e.g. Skyline Corporate Center" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Location Type</label>
                        <select name="type" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);" required>
                            <option value="branch">Main Branch</option>
                            <option value="rental_unit">Rental Office Unit</option>
                            <option value="warehouse">Storage/Warehouse</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">City</label>
                        <input type="text" name="city" placeholder="Addis Ababa" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);" required>
                    </div>
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Address Detail</label>
                        <input type="text" name="address" placeholder="e.g. Bole Road, House #123" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Contact Phone</label>
                        <input type="text" name="phone" placeholder="0111-223344" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Price per Month ($) - If applicable</label>
                        <input type="number" step="0.01" name="price_per_month" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Initial Status</label>
                        <select name="status" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);" required>
                            <option value="available">Available</option>
                            <option value="unavailable">Unavailable</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                    <button type="submit" style="background: var(--primary-color); color: white; padding: 0.75rem 2rem; border-radius: 0.5rem; border: none; font-weight: 600; cursor: pointer;">
                        Create Office
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
