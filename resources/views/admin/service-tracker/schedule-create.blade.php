@extends('layouts.admin')

@section('title', 'Create Service Schedule Rule')

@section('content')
    <div class="table-container">
        <div style="max-width: 640px; margin: 0 auto;">
            <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="margin: 0; font-size: 1.25rem;">New Service Schedule Rule</h3>
                    <a href="{{ route('admin.service-tracker.schedules') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.8125rem; display: flex; align-items: center; gap: 0.375rem;">
                        <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i> Back
                    </a>
                </div>

                @if($errors->any())
                    <div style="background: #fef2f2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #fca5a5; font-size: 0.8125rem;">
                        <ul style="margin: 0; padding-left: 1.25rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.service-tracker.schedules.store') }}" method="POST">
                    @csrf
                    <div style="display: grid; gap: 1rem;">
                        <div>
                            <label style="font-size: 0.8125rem; font-weight: 600; color: #374151; display: block; margin-bottom: 0.375rem;">Rule Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Every 10,000 km Oil Change" style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; box-sizing: border-box;">
                        </div>

                        <div>
                            <label style="font-size: 0.8125rem; font-weight: 600; color: #374151; display: block; margin-bottom: 0.375rem;">Schedule Type *</label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <label style="display: flex; align-items: center; gap: 0.625rem; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 0.625rem; cursor: pointer; transition: all 0.2s;" onclick="this.querySelector('input').checked=true; document.getElementById('mileage-fields').style.display='block'; document.getElementById('time-fields').style.display='none'; this.style.borderColor='var(--primary-color)'; this.style.background='#eef2ff'; this.parentElement.children[1].style.borderColor='#e5e7eb'; this.parentElement.children[1].style.background='white';">
                                    <input type="radio" name="schedule_type" value="mileage" {{ old('schedule_type', 'mileage') === 'mileage' ? 'checked' : '' }} style="accent-color: var(--primary-color);">
                                    <div>
                                        <div style="font-weight: 600; font-size: 0.8125rem; color: #111827;">Mileage Based</div>
                                        <div style="font-size: 0.6875rem; color: #6b7280;">Trigger by km driven</div>
                                    </div>
                                </label>
                                <label style="display: flex; align-items: center; gap: 0.625rem; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 0.625rem; cursor: pointer; transition: all 0.2s;" onclick="this.querySelector('input').checked=true; document.getElementById('time-fields').style.display='block'; document.getElementById('mileage-fields').style.display='none'; this.style.borderColor='var(--primary-color)'; this.style.background='#eef2ff'; this.parentElement.children[0].style.borderColor='#e5e7eb'; this.parentElement.children[0].style.background='white';">
                                    <input type="radio" name="schedule_type" value="time_based" {{ old('schedule_type') === 'time_based' ? 'checked' : '' }} style="accent-color: var(--primary-color);">
                                    <div>
                                        <div style="font-weight: 600; font-size: 0.8125rem; color: #111827;">Time Based</div>
                                        <div style="font-size: 0.6875rem; color: #6b7280;">Trigger by months</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div id="mileage-fields" style="{{ old('schedule_type') === 'time_based' ? 'display:none;' : '' }}">
                            <label style="font-size: 0.8125rem; font-weight: 600; color: #374151; display: block; margin-bottom: 0.375rem;">Mileage Interval (km) *</label>
                            <input type="number" name="mileage_interval" value="{{ old('mileage_interval', 5000) }}" min="100" placeholder="5000" style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; font-family: monospace; box-sizing: border-box;">
                        </div>

                        <div id="time-fields" style="{{ old('schedule_type') !== 'time_based' ? 'display:none;' : '' }}">
                            <label style="font-size: 0.8125rem; font-weight: 600; color: #374151; display: block; margin-bottom: 0.375rem;">Month Interval *</label>
                            <input type="number" name="month_interval" value="{{ old('month_interval', 3) }}" min="1" placeholder="3" style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; font-family: monospace; box-sizing: border-box;">
                        </div>

                        <div>
                            <label style="font-size: 0.8125rem; font-weight: 600; color: #374151; display: block; margin-bottom: 0.375rem;">Service Category *</label>
                            <select name="service_category" required style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                                <option value="routine" {{ old('service_category') === 'routine' ? 'selected' : '' }}>Routine — Oil change, filters, fluids</option>
                                <option value="inspection" {{ old('service_category') === 'inspection' ? 'selected' : '' }}>Inspection — Brakes, tyres, lights</option>
                                <option value="major" {{ old('service_category') === 'major' ? 'selected' : '' }}>Major — Full mechanical overhaul</option>
                            </select>
                        </div>

                        <div>
                            <label style="font-size: 0.8125rem; font-weight: 600; color: #374151; display: block; margin-bottom: 0.375rem;">Description</label>
                            <textarea name="description" rows="3" placeholder="What this service covers..." style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; resize: vertical; box-sizing: border-box;">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <button type="submit" style="margin-top: 1.25rem; background: var(--primary-color); color: white; border: none; padding: 0.75rem 2rem; border-radius: 0.5rem; font-size: 0.9375rem; font-weight: 600; cursor: pointer; width: 100%;">
                        Create Schedule Rule
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() { feather.replace(); });
    </script>
@endsection
