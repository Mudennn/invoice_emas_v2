
@extends('layouts.main')

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Edit Invoice Items</h1>
    </div>

    <form action="{{ route('invoice_items.update', $invoice_item->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @include('invoice_items.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="form-primary-button">Update Invoice Items</button>
            <a href="{{ route('invoice_items.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>
@endsection

