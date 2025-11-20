@extends('layouts.main', ['title' => 'Create Customer Profile'])

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Create Customer Profile</h1>
    </div>

    <p class="text-danger">*This is the Main Client profile, if you want to create Other Client profile, please create it in invoice and select "Other" in company name field.</p>
    <form action="{{ route('customer_profiles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('customer_profiles.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="form-primary-button">Create Profile</button>
            <a href="{{ route('customer_profiles.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>

@endsection

    