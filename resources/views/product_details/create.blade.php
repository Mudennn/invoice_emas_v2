@extends('layouts.main', ['title' => 'Create Product Details'])

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Create Product Details</h1>
    </div>

    <form action="{{ route('product_details.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('product_details.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="form-primary-button">Create Product Details</button>
            <a href="{{ route('product_details.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div> 

@endsection

    