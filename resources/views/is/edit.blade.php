@extends('layouts.main', ['title' => 'Edit IS #' . $is->is_no])

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Edit IS</h1>
    </div>

    <form action="{{ route('is.update', $is->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @include('is.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="third-button">Update is</button>
            <a href="{{ route('is.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>
@endsection
