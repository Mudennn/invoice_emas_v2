@extends('layouts.main', ['title' => 'Company Profiles'])

@section('content')

    @foreach ($company_profiles as $index => $company_profile)
        <div class="profile-detail-content">
            <div class="profile-header">
                <h1>Company Profile</h1>
                <a href="{{ route('company_profiles.edit', $company_profile->id) }}" class="primary-button"> Edit</a>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="name-box">

                @if ($company_profile->getFirstMediaUrl('is_image'))
                    <img src="{{ $company_profile->getFirstMediaUrl('is_image') }}" alt=""
                        style="width: 80px; height: 80px; object-fit: cover" class="rounded-circle">
                @else
                    <div
                        style="width: 80px; height: 80px; border-radius: 50%; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center;">
                        <span class="material-symbols-outlined">
                            person
                        </span>
                    </div>
                @endif
                <div class="id">
                    <h3>{{ $company_profile->company_name }}</h3>
                </div>
            </div>
            <div class="personal-information">
                <h3>Personal Information</h3>
                <div class="personal-information-details">
                    <div class="email-phone-container">
                        <div class="email-detail">
                            <h4>Email</h4>
                            <p>{{ $company_profile->email }}</p>
                        </div>

                        <div class="phone-detail">
                            <h4>Phone</h4>
                            <p>{{ $company_profile->contact }}</p>
                        </div>

                    </div>
                </div>

            </div>
            <div class="address-information">
                <h3>Business Information</h3>
                <div class="address-information-details">
                    <div class="city-state-container">
                        @if ($company_profile->business_registration_no)
                        <div class="address-detail">
                            <h4>Business Registration Number</h4>
                            <p>{{ $company_profile->business_registration_no }}</p>
                        </div>

                        @elseif ($company_profile->identification_number)
                            <div class="address-detail">
                                <h4>Identification Number</h4>
                                <p>{{ $company_profile->identification_number }}</p>
                            </div>
                        @elseif ($company_profile->passport_number)
                            <div class="address-detail">
                                <h4>Passport Number</h4>
                                <p>{{ $company_profile->passport_number }}</p>
                            </div>
                    @endif

    
                        <div class="state-detail">
                            <h4>SST Registration No</h4>
                            <p>{{ $company_profile->sst_registration_no }}</p>
                        </div>
                    </div>
                   
                    <div class="city-state-container">

                        <div class="city-detail">
                            <h4>MSIC Code</h4>
                            <p>{{ $company_profile->msic_code }}</p>
                        </div>
                        <div class="state-detail">
                            <h4>TIN</h4>
                            <p>{{ $company_profile->tin }}</p>
                        </div>
                    </div>
                </div>

            </div>
           
            <div class="address-information">
                <h3>Address Information</h3>
                <div class="address-information-details">
                    <div class="address-postcode-container">

                        <div class="address-detail">
                            <h4>Address</h4>
                            <p>{{ $company_profile->address_line_1 }} <br> {{ $company_profile->address_line_2 }} </p>
                        </div>

                        <div class="postcode-detail">
                            <h4>Postocede</h4>
                            <p>{{ $company_profile->postcode }}</p>
                        </div>
                    </div>

                    <div class="city-state-container">

                        <div class="city-detail">
                            <h4>City</h4>
                            <p>{{ $company_profile->city }}</p>
                        </div>
                        <div class="state-detail">
                            <h4>State</h4>
                            <p>{{ $company_profile->s_state }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endforeach
@endsection
