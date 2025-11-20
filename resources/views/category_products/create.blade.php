@extends('layouts.main', ['title' => 'Create Product Category'])

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Create Product Category</h1>
    </div>

    <form action="{{ route('category_products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('category_products.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="form-primary-button">Create Product Category</button>
            <a href="{{ route('category_products.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>

@endsection

    