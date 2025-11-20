<input type="hidden" name="id" value="{{ $selfBilledInvoice->id }}">
<input type="hidden" name="deleted_items" id="deleted_items" value="">

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
                        {{ (old('company_name') ?? $selfBilledInvoice->company_name) == $company ? 'selected' : '' }}>
                        {{ $company }}
                    </option>
                @endforeach
                <option value="Other"
                    {{ (old('company_name') ?? $selfBilledInvoice->company_name) == 'Other' ? 'selected' : '' }}>Other</option>
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
            <input type="text" name="self_billed_invoice_no" value="{{ old('self_billed_invoice_no') ?? $selfBilledInvoice->self_billed_invoice_no }}"
                class="form-control" {{ $ro }}>

            @error('self_billed_invoice_no')
                <span class="text-danger font-weight-bold small"># {{ $message }}</span>
            @enderror
        </div>
        <div class="input-form">
            <label class="form-label">Invoice Date</label>
            <input type="date" name="self_billed_invoice_date" class="form-control" placeholder="12/2/2025"
                value="{{ old('self_billed_invoice_date', isset($selfBilledInvoice->self_billed_invoice_date) ? \Carbon\Carbon::parse($selfBilledInvoice->self_billed_invoice_date)->format('Y-m-d') : now()->timezone('Asia/Kuala_Lumpur')->format('Y-m-d')) }}"
                {{ $ro }}>

            @error('self_billed_invoice_date')
                <span class="text-danger font-weight-bold small"># {{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="invoice-row-form-right">

        <div class="input-form">
            <label class="form-label">Goods Received By</label>
            <input type="text" name="goods_received_by"
                value="{{ old('goods_received_by') ?? $selfBilledInvoice->goods_received_by }}" class="form-control"
                {{ $ro }}>

            @error('goods_received_by')
                <span class="text-danger font-weight-bold small"># {{ $message }}</span>
            @enderror
        </div>

        <div class="input-form">
            <label class="form-label">Payment Received By</label>
            <input type="text" name="payment_received_by"
                value="{{ old('payment_received_by') ?? $selfBilledInvoice->payment_received_by }}" class="form-control"
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
    </div>
    <div class="card-body">
        <div class="table-responsive" style="overflow-y: auto;" id="invoice-items">
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
                    @if (isset($selfBilledInvoice->selfBilledInvoiceItems) && count($selfBilledInvoice->selfBilledInvoiceItems) > 0)
                        @foreach ($selfBilledInvoice->selfBilledInvoiceItems->where('status', '0') as $index => $item)
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
                                            @if (str_contains($item->item_type ?? '', 'multiple'))
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
                                        <div class="d-flex gap-2 align-items-end">
                                            <div class="d-flex flex-column gap-2">
                                                <label class="small text-muted">Quantity</label>
                                                <input type="number" step="1" name="items[{{ $index }}][quantity]"
                                                    value="{{ old("items.$index.quantity") ?? $item->quantity }}"
                                                    class="form-control quantity-input" placeholder="Enter quantity"
                                                    {{ $ro }}>
                                            </div>
                                            <div class="d-flex flex-column gap-2 w-50">
                                                <label class="small text-muted">Pair</label>
                                                <select name="items[{{ $index }}][pair]" class="form-control form-select" {{ $ro }}>
                                                    <option value="">Choose:</option>
                                                    @foreach ($pair as $p)
                                                        <option value="{{ $p->id }}"
                                                            {{ old("items.$index.pair", optional($item)->pair) == $p->id ? 'selected' : '' }}>
                                                            {{ $p->selection_data }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                            <div class="d-flex flex-column gap-2">
                                                <label class="small text-muted">Gold Price</label>
                                                <input type="number" step="0.01"
                                                    name="items[{{ $index }}][gold]"
                                                    value="{{ old("items.$index.gold") ?? $item->gold }}"
                                                    class="form-control gold-input" placeholder="Enter gold price"
                                                    {{ $ro }}>
                                            </div>
                                            <div class="d-flex flex-column gap-2">
                                                <label class="small text-muted">Unit Price</label>
                                                <div class="input-group">
                                                    <input type="text"
                                                        name="items[{{ $index }}][unit_price]"
                                                        value="{{ old("items.$index.unit_price") ?? $item->unit_price }}"
                                                        class="form-controll unit-price-input" 
                                                        placeholder="Enter value or FOC" {{ $ro }}>
                                                    {{-- <button type="button" class="btn btn-outline-secondary foc-btn" {{ $ro }}>FOC</button> --}}
                                                </div>
                                            </div>
                                            {{-- <div>
                                                <label class="small text-muted">Pure Gold</label>
                                                <select name="items[{{ $index }}][pure_gold]" class="form-control form-select pure-gold-input" {{ $ro }}>
                                                    <option value="">Choose:</option>
                                                    <option value="916" {{ (old("items.$index.pure_gold") ?? $item->pure_gold) == '916' ? 'selected' : '' }}>916</option>
                                                    <option value="835" {{ (old("items.$index.pure_gold") ?? $item->pure_gold) == '835' ? 'selected' : '' }}>835</option>
                                                    <option value="750" {{ (old("items.$index.pure_gold") ?? $item->pure_gold) == '750' ? 'selected' : '' }}>750</option>
                                                    <option value="375" {{ (old("items.$index.pure_gold") ?? $item->pure_gold) == '375' ? 'selected' : '' }}>375</option>
                                                </select>
                                            </div> --}}
                                            <div>
                                                <label class="small text-muted">KT</label>
                                                <select name="items[{{ $index }}][kt]"
                                                    class="form-control form-select kt-input"
                                                    {{ $ro }}>
                                                    <option value="">Choose:</option>
                                                    <option value="916"
                                                        {{ (old("items.$index.kt") ?? $item->kt) == '916' ? 'selected' : '' }}>
                                                        916</option>
                                                    <option value="835"
                                                        {{ (old("items.$index.kt") ?? $item->kt) == '835' ? 'selected' : '' }}>
                                                        835</option>
                                                    <option value="750W"
                                                        {{ (old("items.$index.kt") ?? $item->kt) == '750W' ? 'selected' : '' }}>
                                                        750W</option>
                                                    <option value="750R"
                                                        {{ (old("items.$index.kt") ?? $item->kt) == '750R' ? 'selected' : '' }}>
                                                        750R</option>
                                                    <option value="750Y"
                                                        {{ (old("items.$index.kt") ?? $item->kt) == '750Y' ? 'selected' : '' }}>
                                                        750Y</option>
                                                    <option value="375W"
                                                        {{ (old("items.$index.kt") ?? $item->kt) == '375W' ? 'selected' : '' }}>
                                                        375W</option>
                                                    <option value="375R"
                                                        {{ (old("items.$index.kt") ?? $item->kt) == '375R' ? 'selected' : '' }}>
                                                        375R</option>
                                                </select>
                                            </div>
                                        @else
                                        <div class="d-flex gap-2 align-items-end">
                                            <div class="d-flex flex-column gap-2">
                                                <label class="small text-muted">Quantity</label>
                                                <input type="number" step="1" name="items[{{ $index }}][quantity]"
                                                    value="{{ old("items.$index.quantity") ?? $item->quantity }}"
                                                    class="form-control quantity-input" placeholder="Enter quantity"
                                                    {{ $ro }}>
                                            </div>
                                            <div class="d-flex flex-column gap-2 w-50">
                                                <label class="small text-muted">Pair</label>
                                                <select name="items[{{ $index }}][pair]" class="form-control form-select" {{ $ro }}>
                                                    <option value="">Choose:</option>
                                                    @foreach ($pair as $p)
                                                        <option value="{{ $p->id }}"
                                                            {{ old("items.$index.pair", optional($item)->pair) == $p->id ? 'selected' : '' }}>
                                                            {{ $p->selection_data }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column gap-2">
                                            <label class="small text-muted">Unit Price</label>
                                            <div class="input-group">
                                                <input type="text"
                                                    name="items[{{ $index }}][unit_price]"
                                                    value="{{ old("items.$index.unit_price") ?? $item->unit_price }}"
                                                    class="form-controll unit-price-input"
                                                    placeholder="Enter unit price" {{ $ro }}>
                                                <button type="button" class="btn btn-outline-secondary foc-btn" {{ $ro }}>FOC</button>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="small text-muted">Pure Gold</label>
                                            <select name="items[{{ $index }}][pure_gold]"
                                                class="form-control form-select pure-gold-input"
                                                {{ $ro }}>
                                                <option value="">Choose:</option>
                                                <option value="916"
                                                    {{ (old("items.$index.pure_gold") ?? $item->pure_gold) == '916' ? 'selected' : '' }}>
                                                    916</option>
                                                <option value="835"
                                                    {{ (old("items.$index.pure_gold") ?? $item->pure_gold) == '835' ? 'selected' : '' }}>
                                                    835</option>
                                                <option value="750W"
                                                    {{ (old("items.$index.pure_gold") ?? $item->pure_gold) == '750W' ? 'selected' : '' }}>
                                                    750W</option>
                                                <option value="750R"
                                                    {{ (old("items.$index.pure_gold") ?? $item->pure_gold) == '750R' ? 'selected' : '' }}>
                                                    750R</option>
                                                <option value="750Y"
                                                    {{ (old("items.$index.pure_gold") ?? $item->pure_gold) == '750Y' ? 'selected' : '' }}>
                                                    750Y</option>
                                                <option value="375W"
                                                    {{ (old("items.$index.pure_gold") ?? $item->pure_gold) == '375W' ? 'selected' : '' }}>
                                                    375W</option>
                                                <option value="375R"
                                                    {{ (old("items.$index.pure_gold") ?? $item->pure_gold) == '375R' ? 'selected' : '' }}>
                                                    375R</option>
                                            </select>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[{{ $index }}][total]"
                                        value="{{ old("items.$index.total") ?? $item->total }}"
                                        class="form-control total-input" readonly>
                                    <input type="hidden" name="items[{{ $index }}][remark_total]"
                                        value="{{ old("items.$index.remark_total") ?? $item->remark_total ?? '0.00' }}"
                                        class="remark-total-input">
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
                               value="{{ old('subtotal') ?? ($selfBilledInvoice->subtotal ?? '0.00') }}"
                                readonly>
                        </th>
                        <th class="text-end">
                            <input type="number" step="0.01" name="total_return_balance"
                                class="form-control text-end total-return-balance-input"
                                value="{{ old('remark_total') ?? ($selfBilledInvoice->remark_total ?? '0.00') }}" 
                                readonly>
                        </th>
                        @if (!isset($ro) || $ro === '')
                            <th></th>
                        @endif
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="mt-2 mb-4 d-flex justify-content-end">
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
</div>

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
                        <label class="form-label">Company Name <span class="text-danger">*</span></label>
                        <input type="text" id="other_company_name" name="other_company_name" class="form-control"
                            placeholder="Enter company name"
                            value="{{ old('other_company_name') ?? ($selfBilledInvoice->other_company_name ?? '') }}">
                        <span class="text-danger" id="company-name-error"></span>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                        <input type="text" name="other_address_line_1" class="form-control"
                            value="{{ old('other_address_line_1') }}">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Address Line 2 <span class="text-danger">*</span></label>
                        <input type="text" name="other_address_line_2" class="form-control"
                            value="{{ old('other_address_line_2') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">City <span class="text-danger">*</span></label>
                        <input type="text" name="other_city" class="form-control"
                            value="{{ old('other_city') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">State <span class="text-danger">*</span></label>
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
                        <label class="form-label">Postcode <span class="text-danger">*</span></label>
                        <input type="text" name="other_postcode" class="form-control"
                            value="{{ old('other_postcode') }}">
                    </div>

                    <!-- Contact Person 1 -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contact Person 1 <span class="text-danger">*</span></label>
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

                    <div class="col-md-6 mb-3">
                        <label class="form-label">TIN <span class="text-danger">*</span></label>
                        <input type="text" name="other_tin" class="form-control" value="{{ old('other_tin') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">SST Registration No <span class="text-danger">*</span></label>
                        <input type="text" name="other_sst_registration_no" class="form-control"
                            value="{{ old('other_sst_registration_no') }}">
                    </div>

                    <p class="text-danger italic">*Only fill one of the following</p>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Business Registration No</label>
                        <input type="text" name="other_business_registration_no" class="form-control"
                            value="{{ old('other_business_registration_no') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Identification Number</label>
                        <input type="text" name="other_identification_number" class="form-control"
                            value="{{ old('other_identification_number') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Passport Number</label>
                        <input type="text" name="other_passport_number" class="form-control"
                            value="{{ old('other_passport_number') }}">
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
    <script>
        // Make form data available to the JavaScript
        window.products = @json($products);
        window.ro = @json($ro);
        window.pair = @json($pair);
        window.goldPrices = @json($goldPrices ?? []);
        window.oldItems = @json(old('items', []));
    </script>
@endpush
