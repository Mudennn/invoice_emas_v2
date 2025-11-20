@extends('layouts.main', ['title' => 'Create Invoice'])

@section('content')

<div class="invoice-form-container pt-4 px-4">
    <div class="form-header">
        <h1>Create Invoice</h1>
    </div>

    <form action="{{ route('invoices.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('invoices.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="form-primary-button">Create Invoice</button>
            <a href="{{ route('invoices.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div> 

@endsection

    