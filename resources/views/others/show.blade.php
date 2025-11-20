@extends('layouts.main', ['title' => 'Delete Other Profile #' . $other->company_name])

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Delete Profile</h1>
    </div>

    <form action="{{ route('others.destroy', $other->id) }}" method="POST">

        @csrf
        @method('DELETE')
        @include('others.form')

        <div class="alert alert-danger">
            <p class="text-danger">Are you sure you want to delete this? This action cannot be undone.</p>
        </div>
        <hr>

        <div class="form-button-container">
            <button type="submit" class="form-delete-button">Delete Profile</button>
            <a href="{{ route('customer_profiles.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>
@endsection

