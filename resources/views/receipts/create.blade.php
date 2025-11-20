@extends('layouts.main', ['title' => 'Create Receipt'])

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Create Receipt</h1>
    </div>

    <form action="{{ route('receipts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('receipts.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="form-primary-button">Create Receipt</button>
            <a href="{{ route('receipts.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div> 

@endsection

    