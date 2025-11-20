@extends('layouts.main', ['title' => 'Delete Payment'])

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Delete Payment</h1>
    </div>

    <form action="{{ route('payments.destroy', $payment->id) }}" method="POST">

        @csrf
        @method('DELETE')
        @include('payments.form')

        <div class="alert alert-danger">
            <p class="text-danger">Are you sure you want to delete this? This action cannot be undone.</p>
        </div>
        <hr>

        <div class="form-button-container">
            <button type="submit" class="form-delete-button">Delete Payment</button>
            <a href="{{ route('payments.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>

@endsection

