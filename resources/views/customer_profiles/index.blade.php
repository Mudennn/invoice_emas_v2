@extends('layouts.main', ['title' => 'Customer Profiles'])

@section('content')
    <div class="table-header">
        <h1>Customer Profiles</h1>
        {{-- Create Customer Profile --}}
        <a href="{{ route('customer_profiles.create') }}" class="primary-button">Create Profile</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    
    <div class="card">
        <div class="card-body">
        <div class="table-responsive" style="min-height: 200px; overflow-y: auto;">
            <table class="table table-hover table-bordered align-middle text-nowrap" id="invoiceTable">
                <thead class="thead-light">
                    <tr>
                        <th scope="col" style="width: 5%;" class="text-center">No</th>
                        <th scope="col" style="width: 20%;">Company Name</th>
                        <th scope="col" style="width: 5%;">Type</th>
                        <th scope="col" style="width: 20%;">Address</th>
                        <th scope="col" style="width: 10%;">Contact Person 1</th>
                        <th scope="col" style="width: 10%;">Contact Person 2</th>
                        <th scope="col" style="width: 10%;">Contact Person 3</th>

                        <th scope="col" style="width: 5%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($all_profiles as $index => $profile)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-wrap">
                                <p class="fw-bold" style="color: #000 !important">{{ $profile->company_name }}</p>
                                <br>
                                <p>TIN: {{ $profile->tin }}</p>
                                <br>
                                <p>SST Registration No: {{ $profile->sst_registration_no }}</p>
                                <br>
                                @if($profile->business_registration_no)
                                    <p>Business Registration No: {{ $profile->business_registration_no }}</p>
                                @elseif ($profile->identification_number)
                                    <p>Identification Number: {{ $profile->identification_number }}</p>
                                @elseif ($profile->passport_number)
                                    <p>Passport Number: {{ $profile->passport_number }}</p>
                                @endif
                            </td>
                            <td>
                                @if($profile->profile_type == 'Main Client')
                                    <span class="badge text-bg-primary">

                                        {{ ucfirst($profile->profile_type) }}
                                    </span>
                                @else
                                    <span class="badge text-bg-secondary">
                                        {{ ucfirst($profile->profile_type) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-wrap">
                                @if($profile->profile_type === 'customer')
                                    {{ $profile->address_line_1 }} 
                                    <br> {{ $profile->address_line_2 }} 
                                    <br> {{ $profile->postcode }} 
                                    <br> {{ $profile->city }}, {{ $profile->s_state }}
                                @else
                                    @if($profile->address_line_1 || $profile->address_line_2 || $profile->postcode || $profile->city || $profile->state)
                                        {{ $profile->address_line_1 ?: '' }}
                                        @if($profile->address_line_2)
                                            <br> {{ $profile->address_line_2 }}
                                        @endif
                                        @if($profile->postcode)
                                            <br> {{ $profile->postcode }}
                                        @endif
                                        @if($profile->city || $profile->s_state)
                                            <br> {{ $profile->city }}, {{ $profile->s_state }}
                                        @endif
                                    @else
                                        -
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($profile->profile_type === 'customer')
                                    Name: {{ $profile->contact_name_1 ?: '-' }} 
                                    <br> Phone: {{ $profile->contact_1 ?: '-' }} 
                                    <br> Email: {{ $profile->email_1 ?: '-' }}
                                @else
                                    Name: {{ $profile->contact_name_1 ?: '-' }} 
                                    <br> Phone: {{ $profile->contact_1 ?: '-' }} 
                                    <br> Email: {{ $profile->email_1 ?: '-' }}
                                @endif
                            </td>
                            <td>
                                @if($profile->profile_type === 'customer')
                                    Name: {{ $profile->contact_name_2 ?: '-' }} 
                                    <br> Phone: {{ $profile->contact_2 ?: '-' }} 
                                    <br> Email: {{ $profile->email_2 ?: '-' }}
                                @else
                                    Name: {{ $profile->contact_name_2 ?: '-' }} 
                                    <br> Phone: {{ $profile->contact_2 ?: '-' }} 
                                    <br> Email: {{ $profile->email_2 ?: '-' }}
                                @endif
                            </td>
                            <td>
                                @if($profile->profile_type === 'customer')
                                    Name: {{ $profile->contact_name_3 ?: '-' }} 
                                    <br> Phone: {{ $profile->contact_3 ?: '-' }} 
                                    <br> Email: {{ $profile->email_3 ?: '-' }}
                                @else
                                    Name: {{ $profile->contact_name_3 ?: '-' }} 
                                    <br> Phone: {{ $profile->contact_3 ?: '-' }} 
                                    <br> Email: {{ $profile->email_3 ?: '-' }}
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn hide-arrow p-0 border-0" type="button" id="dropdownMenuButton1"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="material-symbols-outlined"style="font-size: 18px; color: #646e78;">
                                            more_vert
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        @if($profile->profile_type === 'Main Client')
                                            <li>
                                                <a href="{{ route('customer_profiles.edit', $profile->id) }}" 
                                                   class="dropdown-item"
                                                   style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;">
                                                    <span class="material-symbols-outlined" style="font-size: 14px">
                                                        edit
                                                    </span> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('customer_profiles.destroy', $profile->id) }}" 
                                                   class="dropdown-item text-danger"
                                                   style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;">
                                                    <span class="material-symbols-outlined" style="font-size: 14px">
                                                        delete
                                                    </span>Delete
                                                </a>
                                            </li>
                                        @else
                                            <li>
                                                <a href="{{ route('others.edit', $profile->id) }}" 
                                                   class="dropdown-item"
                                                   style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;">
                                                    <span class="material-symbols-outlined" style="font-size: 14px">
                                                        edit
                                                    </span> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" 
                                                   onclick="confirmDelete('{{ $profile->id }}')"
                                                   class="dropdown-item text-danger"
                                                   style="display: flex; justify-content: start; align-items: center; gap:8px; font-size:14px;">
                                                    <span class="material-symbols-outlined" style="font-size: 14px">
                                                        delete
                                                    </span>Delete
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        </div>
    </div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-danger">Are you sure you want to delete this profile? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <a href="#" id="confirmDeleteButton" class="form-delete-button mb-1">Delete</a>
                <button type="button" class="form-secondary-button" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(profileId) {
        const modal = document.getElementById('deleteModal');
        const confirmBtn = document.getElementById('confirmDeleteButton');
        confirmBtn.href = "{{ route('others.destroy', '') }}/" + profileId;
        
        const deleteModal = new bootstrap.Modal(modal);
        deleteModal.show();
    }
</script>
@endpush
@endsection
