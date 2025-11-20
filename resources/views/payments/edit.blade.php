@extends('layouts.main', ['title' => 'Edit Payment'])

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Edit Payment</h1>
    </div>

    <form action="{{ route('payments.update', $payment->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @include('payments.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="third-button">Update Payment</button>
            <a href="{{ route('payments.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>
@endsection

