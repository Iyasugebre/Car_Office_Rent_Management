@extends('layouts.admin')

@section('title', 'Update Legal Documents: ' . $car->plate_number)

@section('content')
    <div class="table-container">
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 2rem; max-width: 800px; margin: 0 auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div>
                    <h3 style="margin: 0; font-size: 1.25rem;">Update Legal Records</h3>
                    <p style="margin: 0.25rem 0 0; font-size: 0.8125rem; color: var(--text-muted);">{{ $car->make }} {{ $car->model }} ({{ $car->plate_number }})</p>
                </div>
                <a href="{{ route('admin.legal-tracker.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem;">Cancel</a>
            </div>

            <form action="{{ route('admin.legal-tracker.update', $car) }}" method="POST" enctype="multipart/form-data" class="legal-form">
                @csrf
                @method('PUT')
                
                <div style="display: flex; flex-direction: column; gap: 2rem;">
                    {{-- Common Details --}}
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: #334155;">Vehicle Registration Number</label>
                        <input type="text" name="registration_number" value="{{ $car->registration_number }}" placeholder="e.g. AA 12345" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border-color); font-size: 0.9375rem;">
                        <p style="margin-top: 0.375rem; font-size: 0.75rem; color: var(--text-muted);">Unique registration ID from transport authority.</p>
                    </div>

                    {{-- Bolo Section --}}
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                            <div style="padding: 0.5rem; background: var(--primary-color); color: white; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                                <i data-feather="file-text" style="width: 16px; height: 16px;"></i>
                            </div>
                            <span style="font-weight: 700; color: #1e293b;">Annual Bolo Tracking</span>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.5rem; color: #475569;">Bolo Expiry Date</label>
                                <input type="date" name="bolo_expiry_date" value="{{ $car->bolo_expiry_date ? $car->bolo_expiry_date->format('Y-m-d') : '' }}" style="width: 100%; padding: 0.625rem; border-radius: 0.375rem; border: 1px solid #cbd5e1;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.5rem; color: #475569;">Bolo Certificate Receipt</label>
                                <input type="file" name="bolo_certificate" style="width: 100%; padding: 0.5rem; background: white; border: 1px dashed #cbd5e1; border-radius: 0.375rem; font-size: 0.8125rem;">
                                @if($car->bolo_certificate_path)
                                    <div style="margin-top: 0.5rem; display: flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; color: #10b981; font-weight: 600;">
                                        <i data-feather="check-circle" style="width: 14px;"></i> Document Already Uploaded
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Inspection Section --}}
                    <div style="background: #fdf2f810; border: 1px solid #fce7f3; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                            <div style="padding: 0.5rem; background: var(--secondary-color); color: white; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                                <i data-feather="clipboard" style="width: 16px; height: 16px;"></i>
                            </div>
                            <span style="font-weight: 700; color: #1e293b;">Vehicle Physical Inspection</span>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.5rem; color: #475569;">Inspection Expiry Date</label>
                                <input type="date" name="inspection_expiry_date" value="{{ $car->inspection_expiry_date ? $car->inspection_expiry_date->format('Y-m-d') : '' }}" style="width: 100%; padding: 0.625rem; border-radius: 0.375rem; border: 1px solid #cbd5e1;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.5rem; color: #475569;">Inspection Certificate</label>
                                <input type="file" name="inspection_certificate" style="width: 100%; padding: 0.5rem; background: white; border: 1px dashed #cbd5e1; border-radius: 0.375rem; font-size: 0.8125rem;">
                                @if($car->inspection_certificate_path)
                                    <div style="margin-top: 0.5rem; display: flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; color: #10b981; font-weight: 600;">
                                        <i data-feather="check-circle" style="width: 14px;"></i> Certificate Already Uploaded
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 2.5rem; display: flex; justify-content: flex-end; gap: 1rem;">
                    <a href="{{ route('admin.legal-tracker.index') }}" class="menu-item" style="background: #f1f5f9; color: #475569; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; border: none; text-decoration: none;">Back</a>
                    <button type="submit" style="background: var(--primary-color); color: white; padding: 0.75rem 2rem; border-radius: 0.5rem; border: none; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);">
                        Update Records
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() { feather.replace(); });
    </script>
@endsection
