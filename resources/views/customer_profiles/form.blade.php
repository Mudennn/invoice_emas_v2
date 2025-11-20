<input type="hidden" name="id" value="{{ $customer_profile->id }}">

@if ($errors->any())
    <div class="alert alert-danger mt-4">
        <ul class="list-unstyled mb-0 ">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="input-form">
    <label class="form-label">Company Name <span class="text-danger">*</span></label>
    <select name="company_name" class="form-control form-select" {{ $ro }}>
        <option value="">Choose Company Name</option>
        @if(isset($company_names))
            @foreach ($company_names as $company_name)
                @if ($customer_profile->company_name)
                    <option value="{{ $company_name }}" {{ $company_name == $customer_profile->company_name ? 'selected' : '' }}>
                        {{ $company_name }}</option>
                @else
                    <option value="{{ $company_name }}" {{ $company_name == old('company_name') ? 'selected' : '' }}>
                        {{ $company_name }}</option>
                @endif
            @endforeach
        @endif
        <option value="new_company">+ Add New Company</option>
    </select>
    <input type="text" id="new_company_input" name="new_company_name" class="form-control mt-2" 
           placeholder="Enter new company name" style="display: none;" {{ $ro }}>

    @error('company_name')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const companySelect = document.querySelector('select[name="company_name"]');
    const newCompanyInput = document.getElementById('new_company_input');
    
    if (companySelect && newCompanyInput) {
        companySelect.addEventListener('change', function() {
            if (this.value === 'new_company') {
                newCompanyInput.style.display = 'block';
                newCompanyInput.required = true;
                this.name = 'existing_company_name';
                newCompanyInput.name = 'company_name';
            } else {
                newCompanyInput.style.display = 'none';
                newCompanyInput.required = false;
                this.name = 'company_name';
                newCompanyInput.name = 'new_company_name';
            }
        });
    }
});
</script>

<div class="input-form">
    <label class="form-label">Address Line 1 <span class="text-danger">*</span></label>
    <input type="text" name="address_line_1" value="{{ old('address_line_1') ?? $customer_profile->address_line_1 }}"
        class="form-control" {{ $ro }}>

    @error('address_line_1')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Address Line 2 <span class="text-danger">*</span></label>
    <input type="text" name="address_line_2" value="{{ old('address_line_2') ?? $customer_profile->address_line_2 }}"
        class="form-control" {{ $ro }}>

    @error('address_line_2')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Postcode <span class="text-danger">*</span></label>
    <input type="text" name="postcode" value="{{ old('postcode') ?? $customer_profile->postcode }}"
        class="form-control" {{ $ro }}>

    @error('postcode')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">State <span class="text-danger">*</span></label>
        <select name="state" id="state" class="form-control form-select" {{ $ro }}>
            <option value=""> {{ 'Choose :' }}</option>
            @foreach ($states as $state)
                @if ($customer_profile->state)
                    <option value="{{ $state->id }}" {{ $state->id == $customer_profile->state ? 'selected' : '' }}>
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
    <label class="form-label">City <span class="text-danger">*</span></label>
    <input type="text" name="city" value="{{ old('city') ?? $customer_profile->city }}" class="form-control"
        {{ $ro }}>

    @error('city')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>


<div class="input-icon">
    <label class="form-label">Contact Person 1 <span class="text-danger">*</span></label>

    <div class="input-group flex-nowrap">
        <input type="text" class="form-icon" aria-label="Username" aria-describedby="addon-wrapping"
            name="contact_name_1" value="{{ old('contact_name_1') ?? $customer_profile->contact_name_1 }}"
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
        name="contact_1" value="{{ old('contact_1') ?? $customer_profile->contact_1 }}"
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
        name="email_1" value="{{ old('email_1') ?? $customer_profile->email_1 }}"
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
            name="contact_name_2" value="{{ old('contact_name_2') ?? $customer_profile->contact_name_2 }}"
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
        name="contact_2" value="{{ old('contact_2') ?? $customer_profile->contact_2 }}"
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
        name="email_2" value="{{ old('email_2') ?? $customer_profile->email_2 }}"
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
            name="contact_name_3" value="{{ old('contact_name_3') ?? $customer_profile->contact_name_3 }}"
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
        name="contact_3" value="{{ old('contact_3') ?? $customer_profile->contact_3 }}"
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
        name="email_3" value="{{ old('email_3') ?? $customer_profile->email_3 }}"
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
    <input type="text" name="tin" value="{{ old('tin') ?? $customer_profile->tin }}" class="form-control" {{ $ro }}>

    @error('tin')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">SST Registration No <span class="text-danger">*</span></label>
    <input type="text" name="sst_registration_no" value="{{ old('sst_registration_no') ?? $customer_profile->sst_registration_no }}" class="form-control" {{ $ro }}>

    @error('sst_registration_no')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<p class="text-danger italic">*Only fill one of the following</p>
<div class="input-form">
    <label class="form-label">Business Registration No</label>
    <input type="text" name="business_registration_no" value="{{ old('business_registration_no') ?? $customer_profile->business_registration_no }}" class="form-control" {{ $ro }}>

    @error('business_registration_no')  
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">Identification Number</label> 
    <input type="text" name="identification_number" value="{{ old('identification_number') ?? $customer_profile->identification_number }}" class="form-control" {{ $ro }}>

    @error('identification_number')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>  

<div class="input-form">
    <label class="form-label">Passport Number</label>
    <input type="text" name="passport_number" value="{{ old('passport_number') ?? $customer_profile->passport_number }}" class="form-control" {{ $ro }}>    

    @error('passport_number')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>  




