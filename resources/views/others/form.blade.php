<input type="hidden" name="id" value="{{ $other->id }}">
<div class="input-form">
    <label class="form-label">Company Name</label>
    <input type="text" name="company_name" value="{{ old('company_name') ?? $other->company_name }}"
        class="form-control" {{ $ro }}>


    @error('company_name')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Address Line 1</label>
    <input type="text" name="address_line_1" value="{{ old('address_line_1') ?? $other->address_line_1 }}"
        class="form-control" {{ $ro }}>

    @error('address_line_1')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Address Line 2</label>
    <input type="text" name="address_line_2" value="{{ old('address_line_2') ?? $other->address_line_2 }}"
        class="form-control" {{ $ro }}>

    @error('address_line_2')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Postcode</label>
    <input type="text" name="postcode" value="{{ old('postcode') ?? $other->postcode }}"
        class="form-control" {{ $ro }}>

    @error('postcode')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
        <label class="form-label">State</label>
        <select name="state" id="state" class="form-control form-select" {{ $ro }}>
            <option value=""> {{ 'Choose :' }}</option>
            @foreach ($states as $state)
                @if ($other->state)
                    <option value="{{ $state->id }}" {{ $state->id == $other->state ? 'selected' : '' }}>
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
    <label class="form-label">City</label>
    <input type="text" name="city" value="{{ old('city') ?? $other->city }}" class="form-control"
        {{ $ro }}>

    @error('city')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>


<div class="input-icon">
    <label class="form-label">Contact Person 1</label>

    <div class="input-group flex-nowrap">
        <input type="text" class="form-icon" aria-label="Username" aria-describedby="addon-wrapping"
            name="contact_name_1" value="{{ old('contact_name_1') ?? $other->contact_name_1 }}"
            placeholder="Contact Name" class="form-control" {{ $ro }}>
        <span class="material-symbols-outlined input-group-text" id="addon-wrapping">
            person
        </span>
    </div>

    @error('contact_name_1')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <div class="input-group flex-nowrap">
        <input type="text" class="form-icon" aria-label="Username" aria-describedby="addon-wrapping"
        name="contact_1" value="{{ old('contact_1') ?? $other->contact_1 }}"
        placeholder="Contact Number" class="form-control" {{ $ro }}>
        <span class="material-symbols-outlined input-group-text" id="addon-wrapping">
            call
        </span>
    </div>

    @error('contact_1')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">

    <div class="input-group flex-nowrap">
        <input type="text" class="form-icon" aria-label="Username" aria-describedby="addon-wrapping"
        name="email_1" value="{{ old('email_1') ?? $other->email_1 }}"
        placeholder="Email Address" class="form-control" {{ $ro }}>
        <span class="material-symbols-outlined input-group-text" id="addon-wrapping">
            mail
        </span>
    </div>


    @error('email_1')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Contact Person 2</label>

    <div class="input-group flex-nowrap">
        <input type="text" class="form-icon" aria-label="Username" aria-describedby="addon-wrapping"
            name="contact_name_2" value="{{ old('contact_name_2') ?? $other->contact_name_2 }}"
            class="form-control" placeholder="Contact Name" {{ $ro }}>
        <span class="material-symbols-outlined input-group-text" id="addon-wrapping">
            person
        </span>
    </div>

    @error('contact_name_2')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <div class="input-group flex-nowrap">
        <input type="text" class="form-icon" aria-label="Username" aria-describedby="addon-wrapping"
        name="contact_2" value="{{ old('contact_2') ?? $other->contact_2 }}"
        placeholder="Contact Number" class="form-control" {{ $ro }}>
        <span class="material-symbols-outlined input-group-text" id="addon-wrapping">
            call
        </span>
    </div>

    @error('contact_2')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <div class="input-group flex-nowrap">
        <input type="text" class="form-icon" aria-label="Username" aria-describedby="addon-wrapping"
        name="email_2" value="{{ old('email_2') ?? $other->email_2 }}"
        placeholder="Email Address" class="form-control" {{ $ro }}>
        <span class="material-symbols-outlined input-group-text" id="addon-wrapping">
            mail
        </span>
    </div>

    @error('email_2')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>


<div class="input-form">
    <label class="form-label">Contact Person 3</label>

    <div class="input-group flex-nowrap">
        <input type="text" class="form-icon" aria-label="Username" aria-describedby="addon-wrapping"
            name="contact_name_3" value="{{ old('contact_name_3') ?? $other->contact_name_3 }}"
            class="form-control" placeholder="Contact Name" {{ $ro }}>
        <span class="material-symbols-outlined input-group-text" id="addon-wrapping">
            person
        </span>
    </div>
    @error('contact_name_3')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <div class="input-group flex-nowrap">
        <input type="text" class="form-icon" aria-label="Username" aria-describedby="addon-wrapping"
        name="contact_3" value="{{ old('contact_3') ?? $other->contact_3 }}"
        placeholder="Contact Number" class="form-control" {{ $ro }}>
        <span class="material-symbols-outlined input-group-text" id="addon-wrapping">
            call
        </span>
    </div>

    @error('contact_3')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <div class="input-group flex-nowrap">
        <input type="text" class="form-icon" aria-label="Username" aria-describedby="addon-wrapping"
        name="email_3" value="{{ old('email_3') ?? $other->email_3 }}"
        placeholder="Email Address" class="form-control" {{ $ro }}>
        <span class="material-symbols-outlined input-group-text" id="addon-wrapping">
            mail
        </span>
    </div>
    
    @error('email_3')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">TIN <span class="text-danger">*</span></label>
    <input type="text" name="tin" value="{{ old('tin') ?? $other->tin }}" class="form-control" {{ $ro }}>

    @error('tin')   
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>      

<div class="input-form">
    <label class="form-label">SST Registration No <span class="text-danger">*</span></label>
    <input type="text" name="sst_registration_no" value="{{ old('sst_registration_no') ?? $other->sst_registration_no }}" class="form-control" {{ $ro }}>   

    @error('sst_registration_no')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<p class="text-danger italic">*Only fill one of the following</p>
<div class="input-form">
    <label class="form-label">Business Registration No</label>
    <input type="text" name="business_registration_no" value="{{ old('business_registration_no') ?? $other->business_registration_no }}" class="form-control" {{ $ro }}>    

    @error('business_registration_no')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Identification Number</label>
    <input type="text" name="identification_number" value="{{ old('identification_number') ?? $other->identification_number }}" class="form-control" {{ $ro }}>

    @error('identification_number') 
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Passport Number</label>   
    <input type="text" name="passport_number" value="{{ old('passport_number') ?? $other->passport_number }}" class="form-control" {{ $ro }}>

    @error('passport_number')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>














