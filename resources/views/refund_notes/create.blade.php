@extends('layouts.main', ['title' => 'Create Refund Note'])

@section('content')


<div class="invoice-form-container pt-4 px-4">
    <div class="form-header">
        <h1>Create Refund Note</h1>
    </div>

    <form action="{{ route('refund_notes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('refund_notes.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="form-primary-button">Create Refund Note</button>
            <a href="{{ route('refund_notes.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>

@endsection

    