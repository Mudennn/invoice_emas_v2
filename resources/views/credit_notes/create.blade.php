@extends('layouts.main', ['title' => 'Create Credit Note'])

@section('content')


<div class="invoice-form-container pt-4 px-4">
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="form-header">
        <h1>Create Credit Note</h1>
    </div>

    <form action="{{ route('credit_notes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('credit_notes.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="form-primary-button">Create Credit Note</button>
            <a href="{{ route('credit_notes.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>

@endsection

    