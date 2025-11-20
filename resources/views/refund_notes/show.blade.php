@extends('layouts.main', ['title' => 'Delete Refund Note #' . $refund_note->refund_note_no])

@section('content')

<div class="invoice-form-container  pt-4 px-4">
    <div class="form-header">
        <h1>Delete Refund Note</h1>
    </div>

    <form action="{{ route('refund_notes.destroy', $refund_note->id) }}" method="POST">
        
        @csrf
        @method('DELETE')
        @include('refund_notes.form')

        <div class="alert alert-danger">
            <p class="text-danger">Are you sure you want to delete this? This action cannot be undone.</p>
        </div>
        <hr>

        <div class="form-button-container">
            <button type="submit" class="form-delete-button">Delete Refund Note</button>
            <a href="{{ route('refund_notes.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>
@endsection

