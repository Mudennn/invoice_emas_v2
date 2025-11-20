@extends('layouts.main', ['title' => 'Edit Refund Note #' . $refund_note->refund_note_no])

@section('content')

<div class="invoice-form-container  pt-4 px-4">
    <div class="form-header">
        <h1>Edit Refund Note</h1>
    </div>

    <form action="{{ route('refund_notes.update', $refund_note->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @include('refund_notes.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="third-button">Update Refund Note</button>
            <a href="{{ route('refund_notes.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>
@endsection

