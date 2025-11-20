
@extends('layouts.main')

@section('content')


<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Create Company Profile</h1>
    </div>

    <form action="{{ route('company_profiles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('company_profiles.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="form-primary-button">Create Profile</button>
            <a href="{{ route('company_profiles.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>

@endsection

    