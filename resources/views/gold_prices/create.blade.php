
@extends('layouts.main')

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Create Gold Price</h1>
    </div>

    <form action="{{ route('gold_prices.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('gold_prices.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="form-primary-button">Create Price</button>
            <a href="{{ route('gold_prices.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div> 

@endsection

    