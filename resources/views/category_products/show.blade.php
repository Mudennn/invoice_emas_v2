@extends('layouts.main', ['title' => 'Delete Product Category #' . $category_product->name])

@section('content')
    <div class="form-container pt-4 px-4">
        <div class="form-header">
            <h1>Delete Category Product</h1>
        </div>

        <form action="{{ route('category_products.destroy', $category_product->id) }}" method="POST">

            @csrf
            @method('DELETE')
            @include('category_products.form')

            <div class="alert alert-danger">
                <p class="text-danger">Are you sure you want to delete this? This action cannot be undone.</p>
            </div>
            <hr>

            <div class="form-button-container">
                <button type="submit" class="form-delete-button">Delete Category Product</button>
                <a href="{{ route('category_products.index') }}}" class="form-secondary-button">Cancel</a>
            </div>
        </form>
    </div>
@endsection
