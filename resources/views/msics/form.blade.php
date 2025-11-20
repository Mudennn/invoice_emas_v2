@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="list-unstyled mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<input type="hidden" name="id" value="{{ $msic->id }}">

<div class="form-input-container">
    <div class="input-container">
        <div class="left-container">
            <div class="d-flex flex-column flex-md-row gap-4 w-100">
                <div class="w-100">
                    <div class="input-form">
                        <label class="form-label">MSIC Code</label>
                        <input type="text" name="msic_code" value="{{ old('msic_code') ?? $msic->msic_code }}"
                            class="form-control" {{ $ro }}>

                        @error('msic_code')
                            <span class="text-danger font-weight-bold small"># {{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="w-100">
                    <div class="input-form">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" placeholder=""
                            value="{{ old('description', isset($msic->description) ? $msic->description : '') }}"
                            {{ $ro }}>

                        @error('description')
                            <span class="text-danger font-weight-bold small"># {{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column flex-md-row gap-4 w-100">
                <div class="w-100">
                    <div class="input-form">
                        <label class="form-label">Category Reference</label>
                        <input type="text" name="category_reference" class="form-control" placeholder=""
                            value="{{ old('category_reference', isset($msic->category_reference) ? $msic->category_reference : '') }}"
                            {{ $ro }}>

                        @error('category_reference')
                            <span class="text-danger font-weight-bold small"># {{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="w-100"></div>
            </div>
        </div>
        <div class="right-container"></div>
    </div>
</div>
