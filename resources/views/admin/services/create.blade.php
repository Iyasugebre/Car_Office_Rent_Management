@extends('layouts.admin')

@section('title', 'Submit Maintenance Request')

@section('content')
<div class="form-container">
    <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 2rem;">
        <div style="margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1.25rem;">Vehicle Service & Maintenance Request</h3>
            <p style="margin: 0.5rem 0 0; color: var(--text-muted); font-size: 0.875rem;">Driver or department submit a new maintenance issue here.</p>
        </div>

        @if($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;">
            <ul style="margin: 0; padding-left: 1.5rem; font-size: 0.875rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.services.store') }}" method="POST">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label>Select Vehicle (Plate #) <span style="color: red;">*</span></label>
                    <select name="car_id" required style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
                        <option value="">-- Choose Vehicle --</option>
                        @foreach($cars as $car)
                            <option value="{{ $car->id }}" {{ old('car_id') == $car->id ? 'selected' : '' }}>
                                {{ $car->plate_number }} ({{ $car->make }} {{ $car->model }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Service Type <span style="color: red;">*</span></label>
                    <select name="service_type" required style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
                        <option value="">-- Choose Type --</option>
                        <option value="routine" {{ old('service_type') == 'routine' ? 'selected' : '' }}>Routine Maintenance</option>
                        <option value="repair" {{ old('service_type') == 'repair' ? 'selected' : '' }}>Repair</option>
                        <option value="inspection" {{ old('service_type') == 'inspection' ? 'selected' : '' }}>Inspection</option>
                        <option value="other" {{ old('service_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label>Urgency Level <span style="color: red;">*</span></label>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" name="urgency_level" value="low" required {{ old('urgency_level') == 'low' ? 'checked' : '' }}> Low
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" name="urgency_level" value="medium" {{ old('urgency_level') == 'medium' ? 'checked' : '' }}> Medium
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: #ea580c; font-weight: 600;">
                        <input type="radio" name="urgency_level" value="high" {{ old('urgency_level') == 'high' ? 'checked' : '' }}> High
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: #dc2626; font-weight: 700;">
                        <input type="radio" name="urgency_level" value="critical" {{ old('urgency_level') == 'critical' ? 'checked' : '' }}> Critical
                    </label>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label>Problem Description <span style="color: red;">*</span></label>
                <textarea name="problem_description" required rows="4" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color); font-family: inherit;" placeholder="Describe the issue or service requested...">{{ old('problem_description') }}</textarea>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary" style="background: var(--dark-bg); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600;">Submit Request</button>
                <a href="{{ route('admin.services.index') }}" style="padding: 0.75rem 1.5rem; border: 1px solid var(--border-color); border-radius: 0.5rem; text-decoration: none; color: var(--text-main);">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
