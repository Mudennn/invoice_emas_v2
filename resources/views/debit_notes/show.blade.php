@extends('layouts.main', ['title' => 'Delete Debit Note #' . $debit_note->debit_note_no])

@section('content')

<div class="invoice-form-container  pt-4 px-4">
    <div class="form-header">
        <h1>Delete Debit Note</h1>
    </div>

    <form action="{{ route('debit_notes.destroy', $debit_note->id) }}" method="POST">
        
        @csrf
        @method('DELETE')
        @include('debit_notes.form')

        <div class="alert alert-danger">
            <p class="text-danger">Are you sure you want to delete this? This action cannot be undone.</p>
        </div>
        <hr>

        <div class="form-button-container">
            <button type="submit" class="form-delete-button">Delete Debit Note</button>
            <a href="{{ route('debit_notes.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>
@endsection

