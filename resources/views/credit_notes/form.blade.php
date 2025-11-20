<input type="hidden" name="id" value="{{ $credit_note->id }}">
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

<div class="credit-note-form-container">
    <div class="left-form">
        <div class="input-form">
            <label class="form-label">Credit Note No</label>
            <input type="text" name="credit_note_no"
                value="{{ old('credit_note_no') ?? $credit_note->credit_note_no }}" class="form-control"
                {{ $ro }} readonly>

            @error('credit_note_no')
                <span class="text-danger font-weight-bold small"># {{ $message }}</span>
            @enderror
        </div>

        <div class="input-form">
            <label class="form-label">Invoice No</label>
            <select name="invoice_no" id="invoice_no" class="form-control form-select invoice-select2"
                {{ $ro }}>
                <option value="">Select Invoice</option>
                @foreach ($invoices ?? [] as $invoice)
                    <option value="{{ $invoice->invoice_no }}"
                        {{ (old('invoice_no') ?? $credit_note->invoice_no) == $invoice->invoice_no ? 'selected' : '' }}>
                        {{ $invoice->invoice_no }}
                    </option>
                @endforeach
            </select>

            @error('invoice_no')
                <span class="text-danger font-weight-bold small"># {{ $message }}</span>
            @enderror
        </div>

        <div class="input-form">
            <label class="form-label">Reason</label>
            <select name="reason" id="reason" class="form-control form-select" {{ $ro }}>
                <option value=""> {{ 'Choose :' }}</option>
                @foreach ($reasons as $reason)
                    @if ($credit_note->reason)
                        <option value="{{ $reason->id }}"
                            {{ $reason->id == $credit_note->reason ? 'selected' : '' }}>
                            {{ $reason->selection_data }}</option>
                    @else
                        <option value="{{ $reason->id }}" {{ $reason->id == old('reason') ? 'selected' : '' }}>
                            {{ $reason->selection_data }}</option>
                    @endif
                @endforeach
            </select>

            @error('reason')
                <span class="text-danger font-weight-bold small"># {{ $message }}</span>
            @enderror
        </div>

        <div class="input-form">
            <label class="form-label">Date</label>
            <input type="date" name="date"
                value="{{ old('date', isset($credit_note->date) ? \Carbon\Carbon::parse($credit_note->date)->format('Y-m-d') : now()->timezone('Asia/Kuala_Lumpur')->format('Y-m-d')) }}"
                class="form-control" {{ $ro }}>

            @error('date')
                <span class="text-danger font-weight-bold small"># {{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="right-form">
        <div class="invoice-detailss">
            <div class="mb-4">
                <h2 class="fw-bold">Customer:</h2>
                <p id="company_name"></p>
            </div>
            <div class="mb-4">
                <h2 class="fw-bold">Billing Address:</h2>
                <p id="customer_address"></p>
            </div>
        </div>
    </div>
</div>

<!-- Credit Note Items -->
<div class="mt-4">
    <div class="card-header d-flex justify-content-between align-items-center pb-3">
        <h5 class="mb-0">Credit Note Items</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive" style="overflow-y: auto;" id="credit_notes-items">
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
                    @if (isset($credit_note->creditItems) && count($credit_note->creditItems) > 0)
                        @foreach ($credit_note->creditItems->where('status', '0') as $index => $item)
                            <tr class="credit-note-item" data-item-id="{{ $item->id }}"
                                data-item-type="{{ $item->item_type ?? 'single-with-gold' }}">
                                <input type="hidden" name="items[{{ $index }}][id]"
                                    value="{{ $item->id }}">
                                <input type="hidden" name="items[{{ $index }}][invoice_id]"
                                    value="{{ $item->invoice_id }}">
                                <input type="hidden" name="items[{{ $index }}][item_type]"
                                    value="{{ $item->item_type ?? 'single-with-gold' }}">

                                <td>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex flex-column gap-2">
                                            <label class="small text-muted">Reference No</label>
                                            <div class="d-flex flex-column gap-2">
                                                <input type="text" name="items[{{ $index }}][reference_no]"
                                                    class="form-control reference-input"
                                                    value="{{ old("items.$index.reference_no") ?? $item->reference_no }}"
                                                    placeholder="Enter Reference No" {{ $ro }}>
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
                                                    <input type="number" step="0.01"
                                                        name="items[{{ $index }}][quantity]"
                                                        class="form-control quantity-input"
                                                        placeholder="Enter quantity"
                                                        value="{{ old("items.$index.quantity") ?? $item->quantity }}"
                                                        {{ $ro }}>
                                                </div>
                                                <div class="d-flex flex-column gap-2 w-50">
                                                    <label class="small text-muted">Pair</label>
                                                    <select name="items[{{ $index }}][pair]"
                                                        class="form-control form-select" {{ $ro }}>
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
                                                        placeholder="Enter value or FOC" {{ $ro }}>
                                                    {{-- <button type="button" class="btn btn-outline-secondary foc-btn" {{ $ro }}>FOC</button> --}}
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
                                            
                                            {{-- <div>
                                                <label class="small text-muted">Pure Gold</label>
                                                <select name="items[{{ $index }}][pure_gold]" class="form-control form-select pure-gold-input" {{ $ro }}>
                                                    <option value="">Choose:</option>
                                                    <option value="916" {{ (old("items.$index.pure_gold") ?? $item->pure_gold) == '916' ? 'selected' : '' }}>916</option>
                                                    <option value="835" {{ (old("items.$index.pure_gold") ?? $item->pure_gold) == '835' ? 'selected' : '' }}>835</option>
                                                    <option value="750W" {{ (old("items.$index.pure_gold") ?? $item->pure_gold) == '750W' ? 'selected' : '' }}>750W</option>
                                                    <option value="750R" {{ (old("items.$index.pure_gold") ?? $item->pure_gold) == '750R' ? 'selected' : '' }}>750R</option>
                                                    <option value="750Y" {{ (old("items.$index.pure_gold") ?? $item->pure_gold) == '750Y' ? 'selected' : '' }}>750Y</option>
                                                    <option value="375W" {{ (old("items.$index.pure_gold") ?? $item->pure_gold) == '375W' ? 'selected' : '' }}>375W</option>
                                                    <option value="375R" {{ (old("items.$index.pure_gold") ?? $item->pure_gold) == '375R' ? 'selected' : '' }}>375R</option>
                                                </select>
                                            </div> --}}
                                        @else
                                            <div class="d-flex gap-2 align-items-end">
                                                <div class="d-flex flex-column gap-2">
                                                    <label class="small text-muted">Quantity</label>
                                                    <input type="number" step="0.01"
                                                        name="items[{{ $index }}][quantity]"
                                                        class="form-control quantity-input"
                                                        placeholder="Enter quantity"
                                                        value="{{ old("items.$index.quantity") ?? $item->quantity }}"
                                                        {{ $ro }}>
                                                </div>
                                                <div class="d-flex flex-column gap-2 w-50">
                                                    <label class="small text-muted">Pair</label>
                                                    <select name="items[{{ $index }}][pair]"
                                                        class="form-control form-select" {{ $ro }}>
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
                    {{-- <tr>
                        <th colspan="3" class="text-end">Subtotal (RM):</th>
                        <th class="text-end">
                            <input type="number" step="0.01" name="subtotal"
                                class="form-control text-end subtotal-input"
                                value="{{ old('subtotal') ?? ($credit_note->creditItems->sum('total') ?? '0.00') }}"
                                readonly>
                        </th>
                        <th></th>
                        @if (!isset($ro) || $ro === '')
                            <th></th>
                        @endif
                    </tr> --}}
                    {{-- <tr>
                        <th colspan="3" class="text-end">SST (8%) (RM):</th>
                        <th class="text-end">
                            <input type="number" step="0.01" name="sst"
                                class="form-control text-end sst-input"
                                value="{{ old('sst') ?? ($credit_note->creditItems->first()->sst ?? '0.00') }}"
                                readonly>
                        </th>
                        <th></th>
                        @if (!isset($ro) || $ro === '')
                            <th></th>
                        @endif
                    </tr> --}}
                    {{-- <tr>
                        <th colspan="3" class="text-end">Grand Total (RM):</th>
                        <th class="text-end">
                            <input type="number" step="0.01" name="grand_total"
                                class="form-control text-end grand-total-input"
                                value="{{ old('grand_total') ?? ($credit_note->creditItems->first()->grand_total ?? '0.00') }}"
                                readonly>
                        </th>
                        <th></th>
                        @if (!isset($ro) || $ro === '')
                            <th></th>
                        @endif
                    </tr> --}}
                    <tr>
                        <th colspan="3" class="text-end">Subtotal (RM):</th>
                        <th class="text-end">
                            <input type="number" step="0.01" name="subtotal"
                                class="form-control text-end subtotal-input"
                                value="{{ old('subtotal') ?? ($credit_note->creditItems->first()->subtotal ?? '0.00') }}"
                                readonly>
                        </th>
                        <th><input type="number" step="0.01" name="total_return_balance"
                            class="form-control text-end total-return-balance-input"
                            value="{{ old('remark_total') ?? ($credit_note->creditItems->first()->remark_total ?? '0.00') }}" 
                            readonly></th>
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
                    <li><a class="dropdown-item" href="#" data-item-type="multiple-with-gold">Multiple
                            Particulars
                            with Gold Price</a></li>
                    <li><a class="dropdown-item" href="#" data-item-type="multiple-without-gold">Multiple
                            Particulars without Gold Price</a></li>
                </ul>
            </div>
        @endif
    </div>
</div>

<div class="input-form">
    <label class="form-label">Note</label>
    <textarea name="note" class="form-control" {{ $ro }}>{{ old('note') ?? $credit_note->note }}</textarea>

    @error('note')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
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
