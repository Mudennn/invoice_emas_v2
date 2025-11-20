
@extends('layouts.main')

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Edit Gold Price</h1>
    </div>

    <form action="{{ route('gold_prices.update', $gold_price->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @include('gold_prices.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="third-button">Update Price</button>
            <a href="{{ route('gold_prices.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>

@endsection

