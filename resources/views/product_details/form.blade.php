<input type="hidden" name="id" value="{{ $product_detail->id }}">

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
    <input type="text" name="name" value="{{ old('name') ?? $product_detail->name }}" class="form-control"
        {{ $ro }}>

    @error('name')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Code</label>
    <input type="text" name="code" value="{{ old('code') ?? $product_detail->code }}" class="form-control"
        {{ $ro }} placeholder="WO-00001">
        <span class="text-danger small italic">*Only insert number</span>

    @error('code')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Category</label>
    <select name="category" class="form-control form-select" {{ $ro }}>
        <option value="">Select Category</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" 
                {{ (old('category') ?? $product_detail->category) == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    @error('category')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>
