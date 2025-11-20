<input type="hidden" name="id" value="{{ $company_profile->id }}">

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="list-unstyled mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
{{-- <div class="input-form">
    <label class="form-label">Id</label>
    <input type="text" name="id" value="{{ old('id') ?? $company_profile->id }}" class="form-control"
        {{ $ro }}>

    @error('id')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div> --}}

<div class="input-form">
    <label class="form-label">Profile Image</label>

    @if ($company_profile->is_image == 1)
        <div class="mb-2">
            <img src="{{ $company_profile->getFirstMediaUrl('is_image') }}" alt=""
                style="width: 80px; height: 80px; object-fit: cover" class="rounded-circle">
        </div>
    @endif

    <input type="file" name="is_image" value="{{ old('is_image') ?? $company_profile->is_image }}" class="form-control" {{ $ro }}>


    @error('is_image')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Company Name <span class="text-danger">*</span></label>
    <input type="text" name="company_name" value="{{ old('company_name') ?? $company_profile->company_name }}"
        class="form-control" {{ $ro }}>

    @error('company_name')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Address Line 1 <span class="text-danger">*</span></label>
    <input type="text" name="address_line_1" value="{{ old('address_line_1') ?? $company_profile->address_line_1 }}"
        class="form-control" {{ $ro }}>

    @error('address_line_1')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Address Line 2 <span class="text-danger">*</span></label>
    <input type="text" name="address_line_2" value="{{ old('address_line_2') ?? $company_profile->address_line_2 }}"
        class="form-control" {{ $ro }}>

    @error('address_line_2')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Postcode <span class="text-danger">*</span></label>
    <input type="text" name="postcode" value="{{ old('postcode') ?? $company_profile->postcode }}"
        class="form-control" {{ $ro }}>
    
    @error('postcode')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">City <span class="text-danger">*</span></label>
    <input type="text" name="city" value="{{ old('city') ?? $company_profile->city }}" class="form-control"
        {{ $ro }}>

    @error('city')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">State <span class="text-danger">*</span></label>
        <select name="state" id="state" class="form-control form-select" {{ $ro }}>
            <option value=""> {{ 'Choose :' }}</option>
            @foreach ($states as $state)
                @if ($company_profile->state)
                    <option value="{{ $state->id }}" {{ $state->id == $company_profile->state ? 'selected' : '' }}>
                        {{ $state->selection_data }}</option>
                @else
                    <option value="{{ $state->id }}" {{ $state->id == old('state') ? 'selected' : '' }}>
                        {{ $state->selection_data }}</option>
                @endif
            @endforeach
        </select>

    @error('state')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>



<div class="input-form">
    <label class="form-label">Email <span class="text-danger">*</span></label>
    <input type="text" name="email" value="{{ old('email') ?? $company_profile->email }}" class="form-control"
        {{ $ro }}>


    @error('email')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Contact <span class="text-danger">*</span></label>
    <input type="text" name="contact" value="{{ old('contact') ?? $company_profile->contact }}" class="form-control"
        {{ $ro }}>


    @error('contact')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">TIN <span class="text-danger">*</span></label>
    <input type="text" name="tin" value="{{ old('tin') ?? $company_profile->tin }}" class="form-control"
        {{ $ro }}>
        
    @error('tin')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>  

<div class="input-form">
    <label class="form-label">SST Registration No <span class="text-danger">*</span></label>
    <input type="text" name="sst_registration_no" value="{{ old('sst_registration_no') ?? $company_profile->sst_registration_no }}" class="form-control"
        {{ $ro }}>

    @error('sst_registration_no')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>  

<div class="input-form">
    <label class="form-label">MSIC Code <span class="text-danger">*</span></label>
    <select name="msic_code" id="msic_code" class="form-control form-select" {{ $ro }}>
        <option value="">Choose MSIC Code:</option>
        @foreach ($msics as $msic)
            @if ($company_profile->msic_code)
                <option value="{{ $msic->msic_code }}" {{ $msic->msic_code == $company_profile->msic_code ? 'selected' : '' }}>
                    {{ $msic->msic_code }} - {{ $msic->description }}
                </option>
            @else
                <option value="{{ $msic->msic_code }}" {{ $msic->msic_code == old('msic_code') ? 'selected' : '' }}>
                    {{ $msic->msic_code }} - {{ $msic->description }}
                </option>
            @endif
        @endforeach
    </select>

    @error('msic_code')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<p class="text-danger italic">*Only fill one of the following</p>
<div class="input-form">
    <label class="form-label">Business Registration Number</label>
    <input type="text" name="business_registration_no" value="{{ old('business_registration_no') ?? $company_profile->business_registration_no }}" class="form-control"
        {{ $ro }}>

    @error('business_registration_no')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Identification Number</label>
    <input type="text" name="identification_number" value="{{ old('identification_number') ?? $company_profile->identification_number }}" class="form-control"
        {{ $ro }}>

    @error('identification_number')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Passport Number</label>
    <input type="text" name="passport_number" value="{{ old('passport_number') ?? $company_profile->passport_number }}" class="form-control"
        {{ $ro }}>

    @error('passport_number')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>






