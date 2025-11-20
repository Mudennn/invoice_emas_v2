
@extends('layouts.main')

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Delete Invoice Items</h1>
    </div>

    <form action="{{ route('invoice_items.destroy', $invoice_item->id) }}" method="POST">

        @csrf
        @method('DELETE')
        @include('invoice_items.form')

        <div class="alert alert-danger">
            <p class="text-danger">Are you sure you want to delete this? This action cannot be undone.</p>
        </div>
        <hr>

        <div class="form-button-container">
            <button type="submit" class="form-primary-button">Delete Invoice Items</button>
            <a href="{{ route('invoice_items.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>

@endsection

