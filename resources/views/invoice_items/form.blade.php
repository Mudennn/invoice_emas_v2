<input type="hidden" name="id" value="{{ $invoice_item->id }}">

<div class="input-form">
    <label class="form-label">Id</span>
        <input type="text" name="id" value="{{ old('id') ?? $invoice_item->id }}" class="form-control"
            {{ $ro }}>

        @error('id')
            <span class="text-danger font-weight-bold small"># {{ $message }}</span>
        @enderror
</div>

<div class="input-form">
    <label class="form-label">Invoice</span>
        <input type="text" name="invoice_id" value="{{ old('invoice_id') ?? $invoice_item->invoice_id }}"
            class="form-control" {{ $ro }}>

        @error('invoice_id')
            <span class="text-danger font-weight-bold small"># {{ $message }}</span>
        @enderror
</div>

<div class="input-form">
    <label class="form-label">Reference No</span>
        <input type="text" name="reference_no" value="{{ old('reference_no') ?? $invoice_item->reference_no }}"
            class="form-control" {{ $ro }}>

        @error('reference_no')
            <span class="text-danger font-weight-bold small"># {{ $message }}</span>
        @enderror
</div>

<div class="input-form">
    <label class="form-label">Particulars</span>
        <input type="text" name="particulars" value="{{ old('particulars') ?? $invoice_item->particulars }}"
            class="form-control" {{ $ro }}>

        @error('particulars')
            <span class="text-danger font-weight-bold small"># {{ $message }}</span>
        @enderror
</div>

<div class="input-form">
    <label class="form-label">Weight</span>
        <input type="text" name="weight" value="{{ old('weight') ?? $invoice_item->weight }}" class="form-control"
            {{ $ro }}>

        @error('weight')
            <span class="text-danger font-weight-bold small"># {{ $message }}</span>
        @enderror
</div>

<div class="input-form">
    <label class="form-label">Wastage</span>
        <input type="text" name="wastage" value="{{ old('wastage') ?? $invoice_item->wastage }}"
            class="form-control" {{ $ro }}>

        @error('wastage')
            <span class="text-danger font-weight-bold small"># {{ $message }}</span>
        @enderror
</div>

<div class="input-form">
    <label class="form-label">Total Weight</span>
        <input type="text" name="total_weight" value="{{ old('total_weight') ?? $invoice_item->total_weight }}"
            class="form-control" {{ $ro }}>

        @error('total_weight')
            <span class="text-danger font-weight-bold small"># {{ $message }}</span>
        @enderror
</div>

<div class="input-form">
    <label class="form-label">Gold</span>
        <input type="text" name="gold" value="{{ old('gold') ?? $invoice_item->gold }}" class="form-control"
            {{ $ro }}>

        @error('gold')
            <span class="text-danger font-weight-bold small"># {{ $message }}</span>
        @enderror
</div>

<div class="input-form">
    <label class="form-label">Workmanship</span>
        <input type="text" name="workmanship" value="{{ old('workmanship') ?? $invoice_item->workmanship }}"
            class="form-control" {{ $ro }}>

        @error('workmanship')
            <span class="text-danger font-weight-bold small"># {{ $message }}</span>
        @enderror
</div>

<div class="input-form">
    <label class="form-label">Total</span>
        <input type="text" name="total" value="{{ old('total') ?? $invoice_item->total }}" class="form-control"
            {{ $ro }}>

        @error('total')
            <span class="text-danger font-weight-bold small"># {{ $message }}</span>
        @enderror
</div>

<div class="input-form">
    <label class="form-label">Grand Total</span>
        <input type="text" name="grand_total" value="{{ old('grand_total') ?? $invoice_item->grand_total }}"
            class="form-control" {{ $ro }}>

        @error('grand_total')
            <span class="text-danger font-weight-bold small"># {{ $message }}</span>
        @enderror
</div>

<div class="input-form">
    <label class="form-label">Remark</span>
        <input type="text" name="remark" value="{{ old('remark') ?? $invoice_item->remark }}" class="form-control"
            {{ $ro }}>

        @error('remark')
            <span class="text-danger font-weight-bold small"># {{ $message }}</span>
        @enderror
</div>
