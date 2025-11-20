@extends('layouts.main', ['title' => 'Delete Credit Note #' . $credit_note->credit_note_no])

@section('content')

<div class="invoice-form-container  pt-4 px-4">
    <div class="form-header">
        <h1>Delete Credit Note</h1>
    </div>

    <form action="{{ route('credit_notes.destroy', $credit_note->id) }}" method="POST">

        @csrf
        @method('DELETE')
        @include('credit_notes.form')

        <div class="alert alert-danger">
            <p class="text-danger">Are you sure you want to delete this? This action cannot be undone.</p>
        </div>
        <hr>

        <div class="form-button-container">
            <button type="submit" class="form-delete-button">Delete Credit Note</button>
            <a href="{{ route('credit_notes.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>
@endsection

