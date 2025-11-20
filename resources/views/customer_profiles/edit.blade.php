@extends('layouts.main', ['title' => 'Edit Customer Profile #' . $customer_profile->company_name])

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Edit Customer Profile</h1>
    </div>

    <form action="{{ route('customer_profiles.update', $customer_profile->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @include('customer_profiles.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="third-button">Update Profile</button>
            <a href="{{ route('customer_profiles.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>

@endsection

