@extends('layouts.main', ['title' => 'Edit Company Profile'])

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Edit Company Profile</h1>
    </div>

    <form action="{{ route('company_profiles.update', $company_profile->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @include('company_profiles.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="third-button">Update Profile</button>
            <a href="{{ route('company_profiles.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>
@endsection

