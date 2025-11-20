@extends('layouts.main', ['title' => 'Edit Invoice #' . $invoice->invoice_no])

@section('content')

<div class="invoice-form-container pt-4 px-4">
    <div class="form-header">
        <h1>Edit Invoice</h1>
    </div>

    <form action="{{ route('invoices.update', $invoice->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @include('invoices.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="third-button">Update Invoice</button>
            <a href="{{ route('invoices.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>

@endsection

