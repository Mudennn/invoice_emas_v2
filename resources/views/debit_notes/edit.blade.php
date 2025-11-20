@extends('layouts.main', ['title' => 'Edit Debit Note #' . $debit_note->debit_note_no])

@section('content')

<div class="invoice-form-container  pt-4 px-4">
    <div class="form-header">
        <h1>Edit Debit Note</h1>
    </div>

    <form action="{{ route('debit_notes.update', $debit_note->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @include('debit_notes.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="third-button">Update Debit Note</button>
            <a href="{{ route('debit_notes.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>
@endsection

