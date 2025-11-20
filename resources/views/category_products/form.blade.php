<input type="hidden" name="id" value="{{ $category_product->id }}">

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
    <input type="text" name="name" value="{{ old('name') ?? $category_product->name }}" class="form-control"
        {{ $ro }}>

    @error('name')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>
