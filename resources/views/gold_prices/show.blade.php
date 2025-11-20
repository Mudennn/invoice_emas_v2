
@extends('layouts.main')

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Delete Gold Price</h1>
    </div>

    <form action="{{ route('gold_prices.destroy', $gold_price->id) }}" method="POST">

        @csrf
        @method('DELETE')
        @include('gold_prices.form')

        <div class="alert alert-danger">
            <p class="text-danger">Are you sure you want to delete this? This action cannot be undone.</p>
        </div>
        <hr>

        <div class="form-button-container">
            <button type="submit" class="form-delete-button">Delete Gold Price</button>
            <a href="{{ route('gold_prices.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>
@endsection

