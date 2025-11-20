@extends('layouts.main', ['title' => 'Create Self Billed Invoice'])

@section('content')

<div class="invoice-form-container pt-4 px-4">
    <div class="form-header">
        <h1>Create Self Billed Invoice</h1>
    </div>

    <form action="{{ route('self_billed_invoices.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('self_billed_invoices.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="form-primary-button">Create Self Billed Invoice</button>
            <a href="{{ route('invoices.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div> 

@endsection

    