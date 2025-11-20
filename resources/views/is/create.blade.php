@extends('layouts.main', ['title' => 'Create IS'])

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Create IS</h1>
    </div>

    <form action="{{ route('is.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('is.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="form-primary-button">Create is</button>
            <a href="{{ route('is.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div> 

@endsection

    