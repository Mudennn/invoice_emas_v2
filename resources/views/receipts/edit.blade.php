@extends('layouts.main', ['title' => 'Edit Receipt #' . $receipt->receipt_no])

@section('content')

<div class="form-container pt-4 px-4">
    <div class="form-header">
        <h1>Edit Receipt</h1>
    </div>

    <form action="{{ route('receipts.update', $receipt->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @include('receipts.form')
        
        <hr>
        <div class="form-button-container">
            <button type="submit" class="third-button">Update Receipt</button>
            <a href="{{ route('receipts.index') }}" class="form-secondary-button">Cancel</a>
        </div>
    </form>
</div>
@endsection
