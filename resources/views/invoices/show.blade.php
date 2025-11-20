@extends('layouts.main', ['title' => 'Delete Invoice #' . $invoice->invoice_no])

@section('content')

<div class="invoice-form-container pt-4 px-4">
    <div class="form-header">
        <h1>Delete Invoice</h1>
    </div>

    <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST">

        @csrf
        @method('DELETE')
        @include('invoices.form')

        <div class="alert alert-danger">
            <p class="text-danger">Are you sure you want to delete this? This action cannot be undone.</p>
        </div>
        <hr>

        <div class="form-button-container">
            <button type="submit" class="form-delete-button">Delete Invoice</button>
            <a href="{{ route('invoices.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>

@endsection

