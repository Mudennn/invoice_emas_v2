<input type="hidden" name="id" value="{{ $invoice->id }}">

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="list-unstyled mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="invoice-row-form">
    <div class="invoice-row-form-left">
        <div class="input-form">
            <label class="form-label">Company Name</label>
            <select name="company_name" class="form-control form-select company-select2" {{ $ro }}>
                <option value="">Select Company</option>
                @foreach ($allCompanies as $company)
                    <option value="{{ $company }}"
                        {{ (old('company_name') ?? $invoice->company_name) == $company ? 'selected' : '' }}>
                        {{ $company }}
                    </option>
                @endforeach
                <option value="Other"
                    {{ (old('company_name') ?? $invoice->company_name) == 'Other' ? 'selected' : '' }}>Other</option>
            </select>

            <!-- Add modal trigger button (initially hidden) -->
            <button type="button" id="otherCompanyDetailsBtn" class="primary-button mt-1" style="display: none;"
                data-bs-toggle="modal" data-bs-target="#otherCompanyModal">
                Add Company Details
            </button>

            @error('company_name')
                <span class="text-danger font-weight-bold small"># {{ $message }}</span>
            @enderror
        </div>

        <div class="input-form">
            <label class="form-label">Invoice No</label>
            <input type="text" name="invoice_no" value="{{ old('invoice_no') ?? $invoice->invoice_no }}"
                class="form-control" {{ $ro }}>

            @error('invoice_no')
                <span class="text-danger font-weight-bold small"># {{ $message }}</span>
            @enderror
        </div>
        <div class="input-form">
            <label class="form-label">Invoice Date</label>
            <input type="date" name="invoice_date" class="form-control" placeholder="12/2/2025"
                value="{{ old('invoice_date', isset($invoice->invoice_date) ? \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
                {{ $ro }}>

            @error('invoice_date')
                <span class="text-danger font-weight-bold small"># {{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="invoice-row-form-right">

        <div class="input-form">
            <label class="form-label">Goods Received By</label>
            <input type="text" name="goods_received_by"
                value="{{ old('goods_received_by') ?? $invoice->goods_received_by }}" class="form-control"
                {{ $ro }}>

            @error('goods_received_by')
                <span class="text-danger font-weight-bold small"># {{ $message }}</span>
            @enderror
        </div>

        <div class="input-form">
            <label class="form-label">Payment Received By</label>
            <input type="text" name="payment_received_by"
                value="{{ old('payment_received_by') ?? $invoice->payment_received_by }}" class="form-control"
                {{ $ro }}>

            @error('payment_received_by')
                <span class="text-danger font-weight-bold small"># {{ $message }}</span>
            @enderror
        </div>
    </div>
</div>


<!-- New Invoice Items Section -->
<div class="mt-4">
    <div class="card-header d-flex justify-content-between align-items-center pb-3">
        <h5 class="mb-0">Invoice Items</h5>
        @if (!isset($ro) || $ro === '')
            <div class="dropdown">
                <button class="primary-button dropdown-toggle" type="button" id="addItemDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    + Add Item
                </button>
                <ul class="dropdown-menu" aria-labelledby="addItemDropdown">
                    <li><a class="dropdown-item" href="#" data-item-type="single-with-gold">One Particular with
                            Gold Price</a></li>
                    <li><a class="dropdown-item" href="#" data-item-type="single-without-gold">One Particular
                            without Gold Price</a></li>
                    <li><a class="dropdown-item" href="#" data-item-type="multiple-with-gold">Multiple Particulars
                            with Gold Price</a></li>
                    <li><a class="dropdown-item" href="#" data-item-type="multiple-without-gold">Multiple
                            Particulars without Gold Price</a></li>
                </ul>
            </div>
        @endif
    </div>
    <div class="card-body">
        <div class="table-responsive" style="min-height: 200px; overflow-y: auto;" id="invoice-items">
            <table class="table table-hover table-bordered align-middle text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 15%;">Product Details</th>
                        <th style="width: 5%;">Weight Details</th>
                        <th style="width: 10%;">Price Details</th>
                        <th style="width: 10%;">Total</th>
                        <th style="width: 10%;">Remark</th>
                        @if (!isset($ro) || $ro === '')
                            <th style="width: 5%;">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if (isset($invoice->invoiceItems) && count($invoice->invoiceItems) > 0)
                        @foreach ($invoice->invoiceItems->where('status', '0') as $index => $item)
                            <tr class="invoice-item" data-item-id="{{ $item->id }}"
                                data-item-type="{{ $item->item_type }}">
                                <input type="hidden" name="items[{{ $index }}][id]"
                                    value="{{ $item->id }}">
                                <input type="hidden" name="items[{{ $index }}][item_type]"
                                    value="{{ $item->item_type }}">

                                <td>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex flex-column gap-2">
                                            <label class="small text-muted">Reference No</label>
                                            <div class="d-flex flex-column gap-2">
                                                {{--   <select name="items[{{ $index }}][reference_no]"
                                                    class="form-control form-select product-code-select select2-input"
                                                    data-type="reference"
                                                    data-index="{{ $index }}" {{ $ro }}>
                                                    <option value="">Select Code</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->code }}" data-name="{{ $product->name }}"
                                                            {{ (old("items.$index.reference_no") ?? $item->reference_no) == $product->code ? 'selected' : '' }}>
                                                            {{ $product->code }}
                                                        </option>
                                                    @endforeach
                                                </select> --}}
                                                <input type="text" name="items[{{ $index }}][reference_no]"
                                                    class="form-control reference-input"
                                                    value="{{ old("items.$index.reference_no") ?? $item->reference_no }}"
                                                    placeholder="Enter Reference No">
                                                <input type="text"
                                                    name="items[{{ $index }}][custom_reference]"
                                                    class="form-control custom-reference-input"
                                                    value="{{ old("items.$index.custom_reference") ?? $item->custom_reference }}"
                                                    placeholder="Custom Reference" {{ $ro }}>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column gap-2">
                                            <label class="small text-muted">Particulars</label>
                                            @if (str_contains($item->item_type, 'multiple'))
                                                <input type="text" name="items[{{ $index }}][particulars]"
                                                    class="form-control particulars-input"
                                                    value="{{ old("items.$index.particulars") ?? $item->particulars }}"
                                                    placeholder="Enter particulars" {{ $ro }}>
                                            @else
                                                <select name="items[{{ $index }}][particulars]"
                                                    class="form-control form-select product-name-select select2-input"
                                                    data-type="particulars" data-index="{{ $index }}"
                                                    {{ $ro }}>
                                                    <option value="">Select Product</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->name }}"
                                                            data-code="{{ $product->code }}"
                                                            {{ (old("items.$index.particulars") ?? $item->particulars) == $product->name ? 'selected' : '' }}>
                                                            {{ $product->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-2">
                                        <div>
                                            <label class="small text-muted">Weight</label>
                                            <input type="number" step="0.01"
                                                name="items[{{ $index }}][weight]"
                                                value="{{ old("items.$index.weight") ?? $item->weight }}"
                                                class="form-control weight-input" {{ $ro }}>
                                        </div>
                                        <div>
                                            <label class="small text-muted">Wastage</label>
                                            <input type="number" step="0.01"
                                                name="items[{{ $index }}][wastage]"
                                                value="{{ old("items.$index.wastage") ?? $item->wastage }}"
                                                class="form-control wastage-input" {{ $ro }}>
                                        </div>
                                        <div>
                                            <label class="small text-muted">Total Weight</label>
                                            <input type="number" step="0.01"
                                                name="items[{{ $index }}][total_weight]"
                                                value="{{ old("items.$index.total_weight") ?? $item->total_weight }}"
                                                class="form-control" readonly>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-2">
                                        @if (str_contains($item->item_type ?? '', 'with-gold'))
                                            <div class="d-flex flex-column gap-2">
                                                <label class="small text-muted">Gold Price</label>
                                                <input type="number" step="0.01"
                                                    name="items[{{ $index }}][gold]"
                                                    value="{{ old("items.$index.gold") ?? $item->gold }}"
                                                    class="form-control gold-input" placeholder="Enter gold price"
                                                    {{ $ro }}>
                                            </div>
                                        @else
                                            <div class="d-flex flex-column gap-2">
                                                <label class="small text-muted">Pure Gold</label>
                                                <input type="number" step="0.01"
                                                    name="items[{{ $index }}][pure_gold]"
                                                    value="{{ old("items.$index.pure_gold") ?? $item->pure_gold }}"
                                                    class="form-control pure-gold-input" placeholder="Enter pure gold"
                                                    {{ $ro }}>
                                            </div>
                                        @endif
                                        <div class="d-flex flex-column gap-2">
                                            <label class="small text-muted">Workmanship</label>
                                            <input type="number" step="0.01"
                                                name="items[{{ $index }}][workmanship]"
                                                value="{{ old("items.$index.workmanship") ?? $item->workmanship }}"
                                                class="form-control workmanship-input" {{ $ro }}>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[{{ $index }}][total]"
                                        value="{{ old("items.$index.total") ?? $item->total }}"
                                        class="form-control total-input" readonly>
                                    <input type="hidden" name="items[{{ $index }}][grand_total]"
                                        value="{{ $invoice->invoiceItems->where('status', '0')->sum('total') }}"
                                        class="grand-total-input">
                                </td>
                                <td>
                                    <textarea name="items[{{ $index }}][remark]" class="form-control remark-input" rows="2"
                                        style="min-height: 60px; resize: vertical;" {{ $ro }}>{{ old("items.$index.remark") ?? $item->remark }}</textarea>
                                </td>
                                @if (!isset($ro) || $ro === '')
                                    <td>
                                        <button type="button"
                                            class="btn btn-danger btn-sm remove-item">Remove</button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Subtotal (RM):</th>
                        <th class="text-end">
                            <input type="number" step="0.01" name="subtotal"
                                class="form-control text-end subtotal-input"
                                value="{{ old('subtotal') ?? ($invoice->invoiceItems->sum('total') ?? '0.00') }}"
                                readonly>
                        </th>
                        <th></th>
                        @if (!isset($ro) || $ro === '')
                            <th></th>
                        @endif
                    </tr>
                    <tr>
                        <th colspan="3" class="text-end">SST (8%) (RM):</th>
                        <th class="text-end">
                            <input type="number" step="0.01" name="sst"
                                class="form-control text-end sst-input"
                                value="{{ old('sst') ?? ($invoice->invoiceItems->first()->sst ?? '0.00') }}" readonly>
                        </th>
                        <th></th>
                        @if (!isset($ro) || $ro === '')
                            <th></th>
                        @endif
                    </tr>
                    <tr>
                        <th colspan="3" class="text-end">Grand Total (RM):</th>
                        <th class="text-end">
                            <input type="number" step="0.01" name="grand_total"
                                class="form-control text-end grand-total-input"
                                value="{{ old('grand_total') ?? ($invoice->invoiceItems->first()->grand_total ?? '0.00') }}"
                                readonly>
                        </th>
                        <th></th>
                        @if (!isset($ro) || $ro === '')
                            <th></th>
                        @endif
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- After the invoice items section, replace the gold prices table with this list -->
{{-- <div class="mt-4">
    <div class="card-header">
        <h5 class="pb-3">Current Gold Prices</h5>
    </div>
    <div class="card-body gold-prices-table">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle text-nowrap">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Date Changed</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($goldPrices as $goldPrice)
                        <tr>
                            <td>{{ $goldPrice->name }}</td>
                            <td>RM{{ $goldPrice->price }}</td>
                            <td>{{ \Carbon\Carbon::parse($goldPrice->date_change)->format('d-m-Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> --}}

<!-- Add the modal -->
<div class="modal fade" id="otherCompanyModal" tabindex="-1" aria-labelledby="otherCompanyModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="otherCompanyModalLabel">New Company Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Company Name</label>
                        <input type="text" id="other_company_name" name="other_company_name" class="form-control"
                            placeholder="Enter company name"
                            value="{{ old('other_company_name') ?? ($invoice->other_company_name ?? '') }}">
                        <span class="text-danger" id="company-name-error"></span>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Address Line 1</label>
                        <input type="text" name="other_address_line_1" class="form-control"
                            value="{{ old('other_address_line_1') }}">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Address Line 2</label>
                        <input type="text" name="other_address_line_2" class="form-control"
                            value="{{ old('other_address_line_2') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">City</label>
                        <input type="text" name="other_city" class="form-control"
                            value="{{ old('other_city') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">State</label>
                        <select name="other_state" class="form-control form-select">
                            <option value="">Choose:</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}"
                                    {{ old('other_state') == $state->id ? 'selected' : '' }}>
                                    {{ $state->selection_data }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Postcode</label>
                        <input type="text" name="other_postcode" class="form-control"
                            value="{{ old('other_postcode') }}">
                    </div>

                    <!-- Contact Person 1 -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contact Person 1</label>
                        <div class="input-group flex-nowrap">
                            <input type="text" name="other_contact_name_1" class="form-icon"
                                value="{{ old('other_contact_name_1') }}" placeholder="Name">
                            <span class="material-symbols-outlined input-group-text" id="addon-wrapping">
                                person
                            </span>
                        </div>

                        <div class="input-group flex-nowrap mt-2">
                            <input type="text" name="other_contact_1" class="form-icon"
                                value="{{ old('other_contact_1') }}" placeholder="Phone Number">
                            <span class="material-symbols-outlined input-group-text" id="addon-wrapping">
                                call
                            </span>
                        </div>

                        <div class="input-group flex-nowrap mt-2">
                            <input type="email" name="other_email_1" class="form-icon"
                                value="{{ old('other_email_1') }}" placeholder="Email Address">
                            <span class="material-symbols-outlined input-group-text" id="addon-wrapping">
                                mail
                            </span>
                        </div>
                    </div>



                    <!-- Contact Person 2 -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contact Person 2</label>
                        <div class="input-group flex-nowrap">
                            <input type="text" name="other_contact_name_2" class="form-icon"
                                value="{{ old('other_contact_name_2') }}" placeholder="Name">
                            <span class="material-symbols-outlined input-group-text" id="addon-wrapping">
                                person
                            </span>
                        </div>

                        <div class="input-group flex-nowrap mt-2">
                            <input type="text" name="other_contact_2" class="form-icon"
                                value="{{ old('other_contact_2') }}" placeholder="Phone Number">
                            <span class="material-symbols-outlined input-group-text" id="addon-wrapping">
                                call
                            </span>
                        </div>

                        <div class="input-group flex-nowrap mt-2">
                            <input type="email" name="other_email_2" class="form-icon"
                                value="{{ old('other_email_2') }}" placeholder="Email Address">
                            <span class="material-symbols-outlined input-group-text" id="addon-wrapping">
                                mail
                            </span>
                        </div>
                    </div>

                    <!-- Contact Person 3 -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contact Person 3</label>
                        <div class="input-group flex-nowrap">
                            <input type="text" name="other_contact_name_3" class="form-icon"
                                value="{{ old('other_contact_name_3') }}" placeholder="Name">
                            <span class="material-symbols-outlined input-group-text" id="addon-wrapping">
                                person
                            </span>
                        </div>

                        <div class="input-group flex-nowrap mt-2">
                            <input type="text" name="other_contact_3" class="form-icon"
                                value="{{ old('other_contact_3') }}" placeholder="Phone Number">
                            <span class="material-symbols-outlined input-group-text" id="addon-wrapping">
                                call
                            </span>
                        </div>

                        <div class="input-group flex-nowrap mt-2">
                            <input type="email" name="other_email_3" class="form-icon"
                                value="{{ old('other_email_3') }}" placeholder="Email Address">
                            <span class="material-symbols-outlined input-group-text" id="addon-wrapping">
                                mail
                            </span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="saveCompanyBtn" class="form-primary-button">Save</button>
                <button type="button" class="form-secondary-button" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2 for company dropdown
            $('.company-select2').select2({
                placeholder: "Select Company",
                width: '100%'
            }).on('change', function() {
                const value = $(this).val();
                const otherDetailsBtn = document.getElementById('otherCompanyDetailsBtn');

                // Show/hide the "Add Company Details" button
                if (otherDetailsBtn) {
                    otherDetailsBtn.style.display = value === 'Other' ? 'block' : 'none';
                }

                // Clear other company fields if not "Other"
                if (value !== 'Other') {
                    document.querySelectorAll('[name^="other_"]').forEach(input => {
                        input.value = '';
                    });
                }
            });

            // Add flags to prevent infinite loop
            let isUpdatingParticulars = false;

            // Handle particulars change (only for single items)
            $(document).on('change', '.select2-input[data-type="particulars"]', function() {
                if (isUpdatingParticulars) return;
                const selectedOption = this.options[this.selectedIndex];
                const row = $(this).closest('tr');
                const itemType = row.data('item-type');
            });

            // Function to initialize Select2
            function initializeSelect2ForRow(row) {
                $(row).find('.select2-input').each(function() {
                    if ($(this).data('select2')) {
                        $(this).select2('destroy');
                    }
                    $(this).select2({
                        placeholder: "Select Product",
                        width: '100%'
                    });
                });
            }

            // Function to calculate total weight for a row
            function calculateTotalWeight(row) {
                const weight = parseFloat(row.querySelector('.weight-input').value) || 0;
                const wastage = parseFloat(row.querySelector('.wastage-input').value) || 0;

                // Calculate total weight including wastage
                const totalWeight = weight + (weight * wastage / 100);
                row.querySelector('input[name$="[total_weight]"]').value = totalWeight.toFixed(2);
                calculateTotal(row);
            }

            // Function to calculate total for a row
            function calculateTotal(row) {
                const itemType = row.dataset.itemType;
                const workmanship = parseFloat(row.querySelector('.workmanship-input').value) || 0;
                let total = 0;

                if (itemType && itemType.includes('with-gold')) {
                    const goldPrice = parseFloat(row.querySelector('.gold-input')?.value) || 0;
                    total = goldPrice + workmanship;
                } else {
                    // For items without gold, we still calculate total as just workmanship
                    // Pure gold is stored but not used in total calculation
                    total = workmanship;
                }

                row.querySelector('.total-input').value = total.toFixed(2);
                updateDisplayTotals();
            }

            // Only update display values, actual calculations happen in backend
            function updateDisplayTotals() {
                const totals = Array.from(document.querySelectorAll('.total-input'))
                    .map(input => parseFloat(input.value) || 0);
                const subtotal = totals.reduce((sum, value) => sum + value, 0);
                const sst = subtotal * 0.08;
                const grandTotal = subtotal + sst;

                // Just update the display fields
                document.querySelectorAll('.subtotal-input').forEach(input => {
                    input.value = subtotal.toFixed(2);
                });

                document.querySelectorAll('.sst-input').forEach(input => {
                    input.value = sst.toFixed(2);
                });

                document.querySelectorAll('.grand-total-input').forEach(input => {
                    input.value = grandTotal.toFixed(2);
                });
            }

            // Function to setup event listeners for a row
            function setupEventListeners() {
                // Weight and wastage input handlers
                document.querySelectorAll('.weight-input, .wastage-input').forEach(input => {
                    input.removeEventListener('input', weightWastageHandler);
                    input.addEventListener('input', weightWastageHandler);
                });

                // Gold, pure gold and workmanship input handlers
                document.querySelectorAll('.gold-input, .pure-gold-input, .workmanship-input').forEach(input => {
                    input.removeEventListener('input', priceHandler);
                    input.addEventListener('input', priceHandler);
                });

                // Remove item handler
                document.querySelectorAll('.remove-item').forEach(button => {
                    button.removeEventListener('click', removeItemHandler);
                    button.addEventListener('click', removeItemHandler);
                });
            }

            // Event handler for weight and wastage inputs
            function weightWastageHandler(event) {
                const row = event.target.closest('tr');
                calculateTotalWeight(row);
            }

            // Event handler for gold and workmanship inputs
            function priceHandler(event) {
                const row = event.target.closest('tr');
                calculateTotal(row);
            }

            // Event handler for remove button
            function removeItemHandler(event) {
                const row = event.target.closest('tr');
                $(row).find('.select2-input').select2('destroy');
                row.remove();
                updateDisplayTotals();
            }

            // Initialize items from old input if they exist
            const oldItems = @json(old('items', []));
            if (oldItems && oldItems.length > 0) {
                oldItems.forEach((item, index) => {
                    const itemType = item.item_type || 'single-with-gold';
                    const template = `
                    <tr class="invoice-item" data-item-id="" data-item-type="${itemType}">
                        <input type="hidden" name="items[${index}][item_type]" value="${itemType}">
                        <td>
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex flex-column gap-2">
                                    <label class="small text-muted">Reference No</label>
                                    <div class="d-flex flex-column gap-2">
                                        <input type="text" 
                                            name="items[${index}][reference_no]" 
                                            class="form-control reference-input" 
                                            value="${item.reference_no || ''}"
                                            placeholder="Enter Reference No">
                                        <input type="text" 
                                            name="items[${index}][custom_reference]" 
                                            class="form-control custom-reference-input" 
                                            value="${item.custom_reference || ''}"
                                            placeholder="Custom Reference">
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    <label class="small text-muted">Particulars</label>
                                    ${itemType.includes('multiple') ? `
                                                            <input type="text" 
                                                                name="items[${index}][particulars]" 
                                                                class="form-control particulars-input"
                                                                value="${item.particulars || ''}" 
                                                                placeholder="Enter particulars">
                                                        ` : `
                                                            <select name="items[${index}][particulars]" 
                                                                class="form-control form-select product-name-select select2-input"
                                                                data-type="particulars"
                                                                data-index="${index}">
                                                                <option value="">Select Product</option>
                                                                @foreach ($products as $product)
                                                                    <option value="{{ $product->name }}" 
                                                                        data-code="{{ $product->code }}"
                                                                        ${item.particulars === '{{ $product->name }}' ? 'selected' : ''}>
                                                                        {{ $product->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        `}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-2">
                                <div>
                                    <label class="small text-muted">Weight</label>
                                    <input type="number" step="0.01" name="items[${index}][weight]" 
                                        value="${item.weight || ''}"
                                        class="form-control weight-input">
                                </div>
                                <div>
                                    <label class="small text-muted">Wastage</label>
                                    <input type="number" step="0.01" name="items[${index}][wastage]" 
                                        value="${item.wastage || ''}"
                                        class="form-control wastage-input">
                                </div>
                                <div>
                                    <label class="small text-muted">Total Weight</label>
                                    <input type="number" step="0.01" name="items[${index}][total_weight]" 
                                        value="${item.total_weight || ''}"
                                        class="form-control" readonly>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-2">
                                ${itemType.includes('with-gold') ? `
                                                    <div class="d-flex flex-column gap-2">
                                                        <label class="small text-muted">Gold Price</label>
                                                        <input type="number" step="0.01" name="items[${index}][gold]" 
                                                            value="${item.gold || ''}"
                                                            class="form-control gold-input" placeholder="Enter gold price">
                                                    </div>
                                                    ` : `
                                                    <div class="d-flex flex-column gap-2">
                                                        <label class="small text-muted">Pure Gold</label>
                                                        <input type="number" step="0.01" name="items[${index}][pure_gold]" 
                                                            class="form-control pure-gold-input" placeholder="Enter pure gold">
                                                    </div>
                                                    `}
                                <div class="d-flex flex-column gap-2">
                                    <label class="small text-muted">Workmanship</label>
                                    <input type="number" step="0.01" name="items[${index}][workmanship]" 
                                        value="${item.workmanship || ''}"
                                        class="form-control workmanship-input">
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type="number" step="0.01" name="items[${index}][total]" 
                                value="${item.total || ''}"
                                class="form-control total-input" readonly>
                        </td>
                        <td>
                            <textarea name="items[${index}][remark]" class="form-control remark-input" 
                                rows="2" style="min-height: 60px; resize: vertical;">${item.remark || ''}</textarea>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                        </td>
                    </tr>
                    `;
                    document.querySelector('#invoice-items tbody').insertAdjacentHTML('beforeend',
                        template);
                });

                // Re-initialize Select2 for all rows
                document.querySelectorAll('.invoice-item').forEach(row => {
                    initializeSelect2ForRow(row);
                });

                // Setup event listeners
                setupEventListeners();

                // Update totals
                updateDisplayTotals();
            }

            // Add new invoice item
            function addInvoiceItem(itemType) {
                const existingItems = document.querySelectorAll('.invoice-item');
                const newIndex = existingItems.length;

                // Check if single item type and items already exist
                // if ((itemType === 'single-with-gold' || itemType === 'single-without-gold') && existingItems.length > 0) {
                //     alert('You can only add one item for this invoice type.');
                //     return;
                // }

                const template = `
                <tr class="invoice-item" data-item-id="" data-item-type="${itemType}">
                    <input type="hidden" name="items[${newIndex}][item_type]" value="${itemType}">
                    <td>
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex flex-column gap-2">
                                <label class="small text-muted">Reference No</label>
                                <div class="d-flex flex-column gap-2">
                                    <input type="text" 
                                        name="items[${newIndex}][reference_no]" 
                                        class="form-control reference-input" 
                                        placeholder="Enter Reference No">
                                    <input type="text" 
                                        name="items[${newIndex}][custom_reference]" 
                                        class="form-control custom-reference-input" 
                                        placeholder="Custom Reference">
                                </div>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                <label class="small text-muted">Particulars</label>
                                ${itemType.includes('multiple') ? `
                                                        <input type="text" 
                                                            name="items[${newIndex}][particulars]" 
                                                            class="form-control particulars-input" 
                                                            placeholder="Enter particulars">
                                                    ` : `
                                                        <select name="items[${newIndex}][particulars]" 
                                                            class="form-control form-select product-name-select select2-input"
                                                            data-type="particulars"
                                                            data-index="${newIndex}">
                                                            <option value="">Select Product</option>
                                                            @foreach ($products as $product)
                                                                <option value="{{ $product->name }}" data-code="{{ $product->code }}">
                                                                    {{ $product->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    `}
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-2">
                            <div>
                                <label class="small text-muted">Weight</label>
                                <input type="number" step="0.01" name="items[${newIndex}][weight]" 
                                    class="form-control weight-input">
                            </div>
                            <div>
                                <label class="small text-muted">Wastage</label>
                                <input type="number" step="0.01" name="items[${newIndex}][wastage]" 
                                    class="form-control wastage-input">
                            </div>
                            <div>
                                <label class="small text-muted">Total Weight</label>
                                <input type="number" step="0.01" name="items[${newIndex}][total_weight]" 
                                    class="form-control" readonly>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-2">
                            ${itemType.includes('with-gold') ? `
                                                <div class="d-flex flex-column gap-2">
                                                    <label class="small text-muted">Gold Price</label>
                                                    <input type="number" step="0.01" name="items[${newIndex}][gold]" 
                                                        class="form-control gold-input" placeholder="Enter gold price">
                                                </div>
                                                ` : `
                                                <div class="d-flex flex-column gap-2">
                                                    <label class="small text-muted">Pure Gold</label>
                                                    <input type="number" step="0.01" name="items[${newIndex}][pure_gold]" 
                                                        class="form-control pure-gold-input" placeholder="Enter pure gold">
                                                </div>
                                                `}
                            <div class="d-flex flex-column gap-2">
                                <label class="small text-muted">Workmanship</label>
                                <input type="number" step="0.01" name="items[${newIndex}][workmanship]" 
                                    class="form-control workmanship-input">
                            </div>
                        </div>
                    </td>
                    <td><input type="number" step="0.01" name="items[${newIndex}][total]" class="form-control total-input" readonly></td>
                    <td><textarea name="items[${newIndex}][remark]" class="form-control remark-input" rows="2" style="min-height: 60px; resize: vertical;"></textarea></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-item">Remove</button></td>
                </tr>
            `;

                document.querySelector('#invoice-items tbody').insertAdjacentHTML('beforeend', template);
                initializeSelect2();
            }

            // Calculate grand total function
            function calculateGrandTotal() {
                let subtotal = 0;

                // Sum up all item totals
                document.querySelectorAll('.total-input').forEach(input => {
                    subtotal += parseFloat(input.value) || 0;
                });

                // Calculate SST (8%)
                const sst = subtotal * 0.08;
                const grandTotal = subtotal + sst;

                // Update display fields
                document.querySelectorAll('.subtotal-input').forEach(input => {
                    input.value = subtotal.toFixed(2);
                });

                document.querySelectorAll('.sst-input').forEach(input => {
                    input.value = sst.toFixed(2);
                });

                document.querySelectorAll('.grand-total-input').forEach(input => {
                    input.value = grandTotal.toFixed(2);
                });
            }

            // Add button click event listener
            const addButton = document.getElementById('addItemBtn');
            if (addButton) {
                addButton.addEventListener('click', function() {
                    addInvoiceItem();
                    // Initialize gold price ID for new row
                    const newRow = document.querySelector('#invoice-items tr:last-child');
                    if (newRow) {
                        const goldSelect = newRow.querySelector('.gold-input');
                        if (goldSelect) {
                            handleGoldPriceSelection(goldSelect);
                        }
                    }
                    calculateGrandTotal();
                });
            }

            // Remove item
            document.getElementById('invoice-items').addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-item')) {
                    e.target.closest('tr').remove();
                    calculateGrandTotal();
                }
            });

            // Calculate totals on weight and workmanship input
            document.getElementById('invoice-items').addEventListener('input', function(e) {
                const row = e.target.closest('tr');

                if (e.target.matches('.weight-input') || e.target.matches('.wastage-input')) {
                    const weight = parseFloat(row.querySelector('.weight-input').value) || 0;
                    const wastage = parseFloat(row.querySelector('.wastage-input').value) || 0;
                    const totalWeight = weight + wastage;
                    row.querySelector('[name$="[total_weight]"]').value = totalWeight.toFixed(2);
                }

                if (e.target.matches('.weight-input') ||
                    e.target.matches('.wastage-input') ||
                    e.target.matches('.gold-input') ||
                    e.target.matches('.workmanship-input')) {
                    calculateTotal(row);
                    calculateGrandTotal(); // Add this to update totals
                }
            });

            // Calculate initial totals for existing rows
            document.querySelectorAll('.invoice-item').forEach(row => {
                calculateTotal(row);
            });

            // Add handler for gold price selection
            const goldPriceRadios = document.querySelectorAll('.gold-price-radio');
            goldPriceRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        const selectedPrice = this.dataset.price;
                        // You can use the selectedPrice value as needed
                        console.log('Selected gold price:', selectedPrice);
                    }
                });
            });

            // Initial calculation of grand total
            calculateGrandTotal();

            // Add this function to handle auto-fill
            function initializeSelect2() {
                $('.select2-input').each(function() {
                    if ($(this).data('select2')) {
                        $(this).select2('destroy');
                    }
                    $(this).select2({
                        placeholder: "Select Product",
                        width: '100%'
                    });
                });
            }

            // Initialize Select2 on page load
            initializeSelect2();

            // Add input event listener to clear error message
            const otherCompanyNameInput = document.getElementById('other_company_name');
            if (otherCompanyNameInput) {
                otherCompanyNameInput.addEventListener('input', function() {
                    document.getElementById('company-name-error').textContent = '';
                });
            }

            // Save button handler
            const saveCompanyBtn = document.getElementById('saveCompanyBtn');
            if (saveCompanyBtn) {
                saveCompanyBtn.addEventListener('click', async function() {
                    const companyName = document.getElementById('other_company_name').value;

                    try {
                        // Get CSRF token from meta tag or use Laravel's built-in CSRF-TOKEN header
                        const token = document.querySelector('meta[name="csrf-token"]')?.content ||
                            '{{ csrf_token() }}';

                        // Check if company name exists
                        const response = await fetch('/check-company-name', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({
                                company_name: companyName
                            })
                        });

                        const data = await response.json();

                        if (!data.valid) {
                            document.getElementById('company-name-error').textContent = data.message;
                            return;
                        }

                        // If validation passes, close modal and update the company name field
                        document.querySelector('select[name="company_name"]').value = 'Other';
                        const modal = bootstrap.Modal.getInstance(document.getElementById(
                            'otherCompanyModal'));
                        modal.hide();
                    } catch (error) {
                        console.error('Error:', error);
                        document.getElementById('company-name-error').textContent =
                            'An error occurred while validating the company name.';
                    }
                });
            }

            // Update dropdown click handler
            document.querySelector('.dropdown-menu').addEventListener('click', function(e) {
                e.preventDefault();
                if (e.target.classList.contains('dropdown-item')) {
                    const itemType = e.target.dataset.itemType;
                    const existingItems = document.querySelectorAll('.invoice-item');

                    // If there are existing items, check compatibility
                    if (existingItems.length > 0) {
                        const firstItemType = existingItems[0].dataset.itemType;

                        // If trying to add a multiple-type item to single items
                        if (itemType.includes('multiple') && !firstItemType.includes('multiple')) {
                            alert(
                                'Cannot mix multiple particulars with single particulars in the same invoice.'
                            );
                            return;
                        }

                        // If trying to add a single-type item to multiple items
                        if (!itemType.includes('multiple') && firstItemType.includes('multiple')) {
                            alert(
                                'Cannot mix single particulars with multiple particulars in the same invoice.'
                            );
                            return;
                        }
                    }

                    addInvoiceItem(itemType);
                }
            });
        });
    </script>
@endpush
