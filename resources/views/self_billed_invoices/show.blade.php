@extends('layouts.main', ['title' => 'Delete Self Billed Invoice #' . $selfBilledInvoice->self_billed_invoice_no])

@section('content')

<div class="invoice-form-container pt-4 px-4">
    <div class="form-header">
        <h1>Delete Self Billed Invoice</h1>
    </div>

    <form action="{{ route('self_billed_invoices.destroy', $selfBilledInvoice->id) }}" method="POST">

        @csrf
        @method('DELETE')
        @include('self_billed_invoices.form')

        <div class="alert alert-danger">
            <p class="text-danger">Are you sure you want to delete this? This action cannot be undone.</p>
        </div>
        <hr>

        <div class="form-button-container">
            <button type="submit" class="form-delete-button">Delete Self Billed Invoice</button>
            <a href="{{ route('self_billed_invoices.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>

@endsection

