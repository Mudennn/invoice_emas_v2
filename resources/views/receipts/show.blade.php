@extends('layouts.main', ['title' => 'Delete Receipt #' . $receipt->receipt_no])

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Delete Receipt</h1>
    </div>

    <form action="{{ route('receipts.destroy', $receipt->id) }}" method="POST">

        @csrf
        @method('DELETE')
        @include('receipts.form')

        <div class="alert alert-danger">
            <p class="text-danger">Are you sure you want to delete this? This action cannot be undone.</p>
        </div>
        <hr>

        <div class="form-button-container">
            <button type="submit" class="form-delete-button">Delete Receipt</button>
            <a href="{{ route('receipts.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>

@endsection

