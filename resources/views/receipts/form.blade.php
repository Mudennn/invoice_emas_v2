@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="list-unstyled mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<input type="hidden" name="id" value="{{ $receipt->id }}">

<div class="input-form">
    <label class="form-label">Receipt No</label>
    <input type="text" name="receipt_no" value="{{ old('receipt_no') ?? $receipt->receipt_no }}"
        class="form-control" {{ $ro }} readonly>

    @error('receipt_no')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Receipt Date</label>
    <input type="date" name="receipt_date" class="form-control" placeholder="12/2/2025"
        value="{{ old('receipt_date', isset($receipt->receipt_date) ? \Carbon\Carbon::parse($receipt->receipt_date)->format('Y-m-d') : now()->timezone('Asia/Kuala_Lumpur')->format('Y-m-d')) }}"
        {{ $ro }}>

    @error('payment_date')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>


<div class="input-form">
    <label class="form-label">Receipt Image</label>

    @if ($receipt->receipt_image == 1)
        <div class="mb-2">
            <img src="{{ $receipt->getFirstMediaUrl('receipt_image') }}" alt=""
                style="width: 80px; height: 80px; object-fit: cover" 
                data-bs-toggle="modal" 
                data-bs-target="#imageModal">
        </div>

        <!-- Image Modal -->
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">Receipt Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ $receipt->getFirstMediaUrl('receipt_image') }}" 
                            alt="Receipt Image"
                            style="max-width: 100%; height: auto;">
                    </div>
                </div>
            </div>
        </div>
    @endif

    <input type="file" name="receipt_image" value="{{ old('receipt_image') ?? $receipt->receipt_image }}" class="form-control" {{ $ro }}>
    

    @error('receipt_image')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>


<div class="input-form">
    <label class="form-label">Receipt Notes</label>
    <textarea name="receipt_note" class="form-control" {{ $ro }}>{{ trim(old('receipt_note') ?? $receipt->receipt_note) }}</textarea>

    @error('receipt_note')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>


