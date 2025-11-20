@extends('layouts.main', ['title' => 'Edit Product Detail #' . $product_detail->name])

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Edit Product Detail</h1>
    </div>

    <form action="{{ route('product_details.update', $product_detail->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @include('product_details.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="third-button">Update Product Detail</button>
            <a href="{{ route('product_details.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>
@endsection

