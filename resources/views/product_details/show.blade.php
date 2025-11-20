@extends('layouts.main', ['title' => 'Delete Product Detail #' . $product_detail->name])

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Delete Product Detail</h1>
    </div>

    <form action="{{ route('product_details.destroy', $product_detail->id) }}" method="POST">

        @csrf
        @method('DELETE')
        @include('product_details.form')

        <div class="alert alert-danger">
            <p class="text-danger">Are you sure you want to delete this? This action cannot be undone.</p>
        </div>
        <hr>

        <div class="form-button-container">
            <button type="submit" class="form-delete-button">Delete Product Detail</button>
            <a href="{{ route('product_details.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>
@endsection

