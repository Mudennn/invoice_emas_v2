@extends('layouts.main', ['title' => 'Edit Self Billed Invoice #' . $selfBilledInvoice->self_billed_invoice_no])

@section('content')

<div class="invoice-form-container pt-4 px-4">
    <div class="form-header">
        <h1>Edit Self Billed Invoice</h1>
    </div>

    <form action="{{ route('self_billed_invoices.update', $selfBilledInvoice->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @include('self_billed_invoices.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="third-button">Update Self Billed Invoice</button>
            <a href="{{ route('self_billed_invoices.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>

@endsection

