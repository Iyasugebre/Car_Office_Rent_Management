@extends('layouts.admin')

@section('title', 'Create New Booking')

@section('content')
    <div class="table-container">
        <div style="background: white; border-radius: 1rem; border: 1px solid var(--border-color); padding: 2.5rem; max-width: 900px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem;">
                <div>
                    <h3 style="margin: 0 0 0.5rem; font-size: 1.5rem; color: var(--dark-bg);">New Rental Booking</h3>
                    <p style="margin: 0; color: var(--text-muted); font-size: 0.875rem;">Create a new booking for a car or office rental unit.</p>
                </div>
                <a href="{{ route('admin.bookings.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem; font-weight: 600;">Cancel</a>
            </div>

            <form action="{{ route('admin.bookings.store') }}" method="POST" id="booking-form">
                @csrf
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <!-- Customer Information -->
                    <div style="grid-column: span 2;">
                        <h4 style="font-size: 0.875rem; text-transform: uppercase; color: var(--primary-color); margin-bottom: 1rem; letter-spacing: 0.05em;">1. Customer Selection</h4>
                    </div>
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Select Customer</label>
                        <select name="user_id" style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd;" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Asset Selection -->
                    <div style="grid-column: span 2; margin-top: 1rem;">
                        <h4 style="font-size: 0.875rem; text-transform: uppercase; color: var(--primary-color); margin-bottom: 1rem; letter-spacing: 0.05em;">2. Asset Selection</h4>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Rental Car (Optional)</label>
                        <select name="car_id" id="car_select" style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd;">
                            <option value="">-- None --</option>
                            @foreach($cars as $car)
                                <option value="{{ $car->id }}" data-price="{{ $car->price_per_day }}" data-type="car">{{ $car->make }} {{ $car->model }} (${{ number_format($car->price_per_day, 2) }}/day)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Rental Office (Optional)</label>
                        <select name="office_id" id="office_select" style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd;">
                            <option value="">-- None --</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}" data-price="{{ $office->price_per_month }}" data-type="office">{{ $office->name }} (${{ number_format($office->price_per_month, 2) }}/month)</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dates & Financials -->
                    <div style="grid-column: span 2; margin-top: 1rem;">
                        <h4 style="font-size: 0.875rem; text-transform: uppercase; color: var(--primary-color); margin-bottom: 1rem; letter-spacing: 0.05em;">3. Schedule & Pricing</h4>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Start Date</label>
                        <input type="date" name="start_date" id="start_date" style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd;" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">End Date</label>
                        <input type="date" name="end_date" id="end_date" style="width: 100%; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border-color); background: #fdfdfd;" required>
                    </div>

                    <div style="grid-column: span 2;">
                        <div style="background: #f1f5f9; padding: 1.5rem; border-radius: 1rem; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <label style="display: block; font-size: 0.75rem; color: var(--text-muted);">Calculated Total</label>
                                <div id="total-display" style="font-size: 1.5rem; font-weight: 800; color: var(--dark-bg); font-family: monospace;">$0.00</div>
                                <input type="hidden" name="total_price" id="total_price_input" value="0">
                            </div>
                            <div style="text-align: right;">
                                <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted);">Pricing based on daily for cars and monthly for offices.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 3rem; display: flex; justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 2rem;">
                    <button type="submit" style="background: var(--primary-color); color: white; padding: 1rem 3rem; border-radius: 0.5rem; border: none; font-weight: 700; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                        Confirm Booking
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const carSelect = document.getElementById('car_select');
        const officeSelect = document.getElementById('office_select');
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        const totalDisplay = document.getElementById('total-display');
        const totalPriceInput = document.getElementById('total_price_input');

        function calculateTotal() {
            const start = new Date(startDate.value);
            const end = new Date(endDate.value);
            
            if (isNaN(start) || isNaN(end) || end < start) {
                totalDisplay.innerText = '$0.00';
                totalPriceInput.value = 0;
                return;
            }

            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) || 1;

            let total = 0;
            
            if (carSelect.value) {
                const price = parseFloat(carSelect.options[carSelect.selectedIndex].dataset.price);
                total += price * diffDays;
            }
            
            if (officeSelect.value) {
                const price = parseFloat(officeSelect.options[officeSelect.selectedIndex].dataset.price);
                total += (price / 30) * diffDays;
            }

            totalDisplay.innerText = '$' + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            totalPriceInput.value = total.toFixed(2);
        }

        [carSelect, officeSelect, startDate, endDate].forEach(el => {
            el.addEventListener('change', calculateTotal);
        });

        // Mutually exclusive selection for now (ERP usually keeps them separate per booking)
        carSelect.addEventListener('change', () => { if(carSelect.value) officeSelect.value = ''; });
        officeSelect.addEventListener('change', () => { if(officeSelect.value) carSelect.value = ''; });
    </script>
@endsection
