@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="list-unstyled mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<input type="hidden" name="id" value="{{ $is->id }}">

<div class="input-form">
    <label class="form-label">IS No</label>
    <input type="text" name="is_no" value="{{ old('is_no') ?? $is->is_no }}" class="form-control"
        {{ $ro }}>

    @error('is_no')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>

<div class="input-form">
    <label class="form-label">IS Date</label>
    <input type="date" name="is_date" class="form-control" placeholder="12/2/2025"
        value="{{ old('is_date', isset($is->is_date) ? \Carbon\Carbon::parse($is->is_date)->format('Y-m-d') : now()->timezone('Asia/Kuala_Lumpur')->format('Y-m-d')) }}"
        {{ $ro }}>

    @error('payment_date')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>


<div class="input-form">
    <label class="form-label">Company Name</label>
    <select name="company_name" class="form-control form-select" {{ $ro }}>
        <option value="">Choose Company Name</option>
        @php
            $hasHabibCompany = false;
            if(isset($company_names)) {
                foreach($company_names as $company_name) {
                    if(str_contains($company_name, 'Habib Jewelry Manufacturing')) {
                        $hasHabibCompany = true;
                        break;
                    }
                }
            }
        @endphp
        @if($hasHabibCompany)
            @php
                $isSelected = false;
                if ($is->company_name) {
                    $isSelected = str_contains($is->company_name, 'Habib Jewelry Manufacturing');
                } else {
                    $isSelected = str_contains(old('company_name', ''), 'Habib Jewelry Manufacturing');
                }
            @endphp
            <option value="Habib Jewelry Manufacturing Sdn Bhd" {{ $isSelected ? 'selected' : '' }}>
                Habib Jewelry Manufacturing Sdn Bhd
            </option>
        @endif
        {{-- <option value="new_company">+ Add New Company</option> --}}
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
    <label class="form-label">Weight</label>
    <input type="number" name="weight" value="{{ old('weight') ?? $is->weight }}"
        class="form-control" {{ $ro }}>

    @error('weight')
        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
    @enderror
</div>
