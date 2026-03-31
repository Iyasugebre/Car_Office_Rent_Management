@extends('layouts.admin')

@section('title', 'Edit Car: ' . $car->plate_number)

@section('content')
    <div class="table-container">
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 2rem; max-width: 800px; margin: 0 auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h3 style="margin: 0; font-size: 1.25rem;">Edit Car Details</h3>
                <a href="{{ route('admin.cars.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem;">Cancel</a>
            </div>

            <form action="{{ route('admin.cars.update', $car) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Make</label>
                        <input type="text" name="make" value="{{ $car->make }}" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Model</label>
                        <input type="text" name="model" value="{{ $car->model }}" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Year</label>
                        <input type="number" name="year" value="{{ $car->year }}" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Plate Number</label>
                        <input type="text" name="plate_number" value="{{ $car->plate_number }}" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Registration Number</label>
                        <input type="text" name="registration_number" value="{{ $car->registration_number }}" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Office/Location</label>
                        <select name="office_id" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);" required>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}" {{ $car->office_id == $office->id ? 'selected' : '' }}>{{ $office->name }} ({{ $office->city }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Price per Day ($)</label>
                        <input type="number" step="0.01" name="price_per_day" value="{{ $car->price_per_day }}" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Status</label>
                        <select name="status" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);" required>
                            <option value="available" {{ $car->status == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="rented" {{ $car->status == 'rented' ? 'selected' : '' }}>Rented</option>
                            <option value="maintenance" {{ $car->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Current Mileage (km)</label>
                        <input type="number" name="mileage" value="{{ $car->mileage }}" min="0" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Last Service Date</label>
                        <input type="date" name="last_service_date" value="{{ $car->last_service_date ? $car->last_service_date->format('Y-m-d') : '' }}" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
                    </div>

                    <div style="grid-column: span 2; border-top: 1px solid #eee; padding-top: 1.5rem; margin-top: 1rem;">
                         <h4 style="margin: 0 0 1rem 0;">Legal Tracking (Bolo & Inspection)</h4>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Bolo Expiry Date</label>
                        <input type="date" name="bolo_expiry_date" value="{{ $car->bolo_expiry_date ? $car->bolo_expiry_date->format('Y-m-d') : '' }}" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">
                            Bolo Certificate 
                            @if($car->bolo_certificate_path)
                                <span style="color: var(--primary-color); font-size: 0.75rem;">(File uploaded)</span>
                            @endif
                        </label>
                        <input type="file" name="bolo_certificate" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Inspection Expiry Date</label>
                        <input type="date" name="inspection_expiry_date" value="{{ $car->inspection_expiry_date ? $car->inspection_expiry_date->format('Y-m-d') : '' }}" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">
                            Inspection Certificate
                            @if($car->inspection_certificate_path)
                                <span style="color: var(--primary-color); font-size: 0.75rem;">(File uploaded)</span>
                            @endif
                        </label>
                        <input type="file" name="inspection_certificate" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                    </div>
                </div>

                <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                    <button type="submit" style="background: var(--primary-color); color: white; padding: 0.75rem 2rem; border-radius: 0.5rem; border: none; font-weight: 600; cursor: pointer;">
                        Update Car
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
