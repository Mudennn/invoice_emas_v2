@extends('layouts.main', ['title' => 'Add Balance Adjustment'])

@section('content')
    <div class="table-header">
        <h1>Add Gold Balance Adjustment</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('reports.balance-adjustments.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="company_name">Company <span class="text-danger">*</span></label>
                            <select name="company_name" id="company_name" class="form-control @error('company_name') is-invalid @enderror" required>
                                <option value="">Select Company</option>
                                <option value="Habib Jewelry Manufacturing Sdn Bhd" {{ old('company_name') == 'Habib Jewelry Manufacturing Sdn Bhd' ? 'selected' : '' }}>
                                    Habib Jewelry Manufacturing Sdn Bhd
                                </option>
                            </select>
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="effective_date">Effective Date <span class="text-danger">*</span></label>
                            <input type="date" name="effective_date" id="effective_date"
                                   class="form-control @error('effective_date') is-invalid @enderror"
                                   value="{{ old('effective_date') }}" required>
                            @error('effective_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="adjustment_amount">Adjustment Amount <span class="text-danger">*</span></label>
                            <input type="number" name="adjustment_amount" id="adjustment_amount"
                                   class="form-control @error('adjustment_amount') is-invalid @enderror"
                                   value="{{ old('adjustment_amount') }}" step="0.01" min="0" required>
                            @error('adjustment_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="adjustment_type">Adjustment Type <span class="text-danger">*</span></label>
                            <select name="adjustment_type" id="adjustment_type" class="form-control @error('adjustment_type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="starting_balance" {{ old('adjustment_type') == 'starting_balance' ? 'selected' : '' }}>Starting Balance</option>
                                <option value="stock_addition" {{ old('adjustment_type') == 'stock_addition' ? 'selected' : '' }}>Stock Addition</option>
                                <option value="inventory_adjustment" {{ old('adjustment_type') == 'inventory_adjustment' ? 'selected' : '' }}>Inventory Adjustment</option>
                                <option value="correction" {{ old('adjustment_type') == 'correction' ? 'selected' : '' }}>Correction</option>
                            </select>
                            @error('adjustment_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Enter reason for this adjustment...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group d-flex justify-content-end gap-2 mt-4">
                     <a href="{{ route('reports.balance-adjustments') }}" class="form-secondary-button">
                        <span class="material-symbols-outlined">cancel</span>
                        Cancel
                    </a>
                    <button type="submit" class="primary-button">
                        <span class="material-symbols-outlined">save</span>
                        Save Adjustment
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Set minimum date to today for effective_date
                const dateInput = document.getElementById('effective_date');
                if (dateInput && !dateInput.value) {
                    const today = new Date().toISOString().split('T')[0];
                    dateInput.min = '2025-01-01'; // Allow historical dates if needed
                }
            });
        </script>
    @endpush
@endsection