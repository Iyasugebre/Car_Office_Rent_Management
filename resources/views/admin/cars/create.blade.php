@extends('layouts.admin')

@section('title', 'Add New Car')

@section('content')
    <div class="table-container">
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 2rem; max-width: 800px; margin: 0 auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h3 style="margin: 0; font-size: 1.25rem;">Car Details</h3>
                <a href="{{ route('admin.cars.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem;">Cancel</a>
            </div>

            <form action="{{ route('admin.cars.store') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Make</label>
                        <input type="text" name="make" placeholder="e.g. Toyota" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Model</label>
                        <input type="text" name="model" placeholder="e.g. Corolla" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Year</label>
                        <input type="number" name="year" value="2024" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Plate Number</label>
                        <input type="text" name="plate_number" placeholder="ABC-1234" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Office/Location</label>
                        <select name="office_id" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);" required>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}">{{ $office->name }} ({{ $office->city }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Price per Day ($)</label>
                        <input type="number" step="0.01" name="price_per_day" value="50.00" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Status</label>
                        <select name="status" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);" required>
                            <option value="available">Available</option>
                            <option value="rented">Rented</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                    <button type="submit" style="background: var(--primary-color); color: white; padding: 0.75rem 2rem; border-radius: 0.5rem; border: none; font-weight: 600; cursor: pointer;">
                        Save Car
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
