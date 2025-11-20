@extends('layouts.main', ['title' => 'Create Debit Note'])

@section('content')


<div class="invoice-form-container pt-4 px-4">
    <div class="form-header">
        <h1>Create Debit Note</h1>
    </div>

    <form action="{{ route('debit_notes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('debit_notes.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="form-primary-button">Create Debit Note</button>
            <a href="{{ route('debit_notes.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>

@endsection

    