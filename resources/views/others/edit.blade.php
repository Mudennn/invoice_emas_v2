@extends('layouts.main', ['title' => 'Edit Other Profile #' . $other->company_name])

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Edit Customer Profile</h1>
    </div>

    <form action="{{ route('others.update', $other->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @include('others.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="third-button">Update Profile</button>
            <a href="{{ route('customer_profiles.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>

@endsection

