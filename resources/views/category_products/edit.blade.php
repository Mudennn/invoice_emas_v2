@extends('layouts.main', ['title' => 'Edit Product Category #' . $category_product->name])

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Edit Product Category</h1>
    </div>

    <form action="{{ route('category_products.update', $category_product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @include('category_products.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="third-button">Update Product Category</button>
            <a href="{{ route('category_products.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>
@endsection

