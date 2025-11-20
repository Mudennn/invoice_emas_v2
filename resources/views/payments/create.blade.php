@extends('layouts.main', ['title' => 'Create Payment'])

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Create Payment</h1>
    </div>

    <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('payments.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="form-primary-button">Create Payment</button>
            <a href="{{ route('payments.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div> 

@endsection

    