@extends('layouts.main', ['title' => 'Edit Credit Note #' . $credit_note->credit_note_no])

@section('content')

<div class="invoice-form-container  pt-4 px-4">
    <div class="form-header">
        <h1>Edit Credit Note</h1>
    </div>

    <form action="{{ route('credit_notes.update', $credit_note->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @include('credit_notes.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="third-button">Update Credit Note</button>
            <a href="{{ route('credit_notes.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>
@endsection

