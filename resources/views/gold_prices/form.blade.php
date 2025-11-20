<input type="hidden" name="id" value="{{ $gold_price->id }}">

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="list-unstyled mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="input-form">
    <label class="form-label">Name</label>

    <input type="text" name="name" value="{{ old('name') ?? $gold_price->name }}" class="form-control"
        {{ $ro }}>

    @error('name')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Price</label>

    <input type="text" name="price" value="{{ old('price') ?? $gold_price->price }}" class="form-control"
        {{ $ro }}>

    @error('price')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Date Change</label>

    <input type="date" name="date_change" 
        value="{{ old('date_change', isset($gold_price->date_change) ? $gold_price->date_change : now()->format('Y-m-d')) }}"
        class="form-control timepicker" {{ $ro }}>

    @error('date_change')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>
