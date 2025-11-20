
@extends('layouts.main')

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Delete Profile</h1>
    </div>

    <form action="{{ route('company_profiles.destroy', $company_profile->id) }}" method="POST">

        @csrf
        @method('DELETE')
        @include('company_profiles.form')

        <div class="alert alert-danger">
            <p class="text-danger">Are you sure you want to delete this? This action cannot be undone.</p>
        </div>
        <hr>

        <div class="form-button-container">
            <button type="submit" class="form-delete-button">Delete Profile</button>
            <a href="{{ route('company_profiles.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>
@endsection

