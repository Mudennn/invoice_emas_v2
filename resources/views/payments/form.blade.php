@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="list-unstyled mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<input type="hidden" name="id" value="{{ $payment->id }}">

<div class="input-form">
    <label class="form-label">Invoice Number</label>
    <select name="invoice_id" class="form-control form-select invoice-select2" {{ $ro }}>
        <option value="">Select Invoice</option>
        @foreach ($invoices ?? [] as $invoice)
            <option value="{{ $invoice->id }}"
                {{ (old('invoice_id') ?? $payment->invoice_id) == $invoice->id ? 'selected' : '' }}>
                {{ $invoice->invoice_no }}
            </option>
        @endforeach
    </select>

    @error('invoice_id')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Total Payment</label>
    <input type="text" name="total_payment" value="{{ old('total_payment') ?? $payment->total_payment }}"
        placeholder="RM" class="form-control" {{ $ro }}>

    @error('total_payment')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Payment Voucher</label>
    <input type="text" name="payment_voucher" value="{{ old('payment_voucher') ?? $payment->payment_voucher }}"
        class="form-control" {{ $ro }}>

    @error('payment_voucher')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Payment Date</label>
    <input type="date" name="payment_date" class="form-control" placeholder="12/2/2025"
        value="{{ old('payment_date', isset($payment->payment_date) ? \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') : now()->timezone('Asia/Kuala_Lumpur')->format('Y-m-d')) }}"
        {{ $ro }}>

    @error('payment_date')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2 for invoice dropdown
            $('.invoice-select2').select2({
                placeholder: "Select Invoice",
                width: '100%'
            });

            // Handle invoice selection change
            $('.invoice-select2').on('change', function() {
                const invoiceId = $(this).val();
                if (invoiceId) {
                    // Fetch invoice details using AJAX
                    $.ajax({
                        url: `/invoices/${invoiceId}/details`,
                        method: 'GET',
                        success: function(response) {
                            if (response.grand_total) {
                                $('input[name="total_payment"]').val(response.grand_total);
                            }
                        },
                        error: function(xhr) {
                            console.error('Error fetching invoice details:', xhr);
                        }
                    });
                } else {
                    $('input[name="total_payment"]').val('');
                }
            });

            // Trigger change event on page load if invoice is selected
            if ($('.invoice-select2').val()) {
                $('.invoice-select2').trigger('change');
            }
        });
    </script>
@endpush
