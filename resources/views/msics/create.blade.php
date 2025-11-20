@extends('layouts.dashboard', ['title' => 'Create MSIC'])

@section('content')
<div class="relative">
    <div class="d-flex flex-column gap-2 form-header-container">
        <a href="{{ url()->previous() }}" class="back-button mb-4">
            <i class="ph ph-arrow-left" style="font-size: 16px;"></i>
            Back
        </a>
        <h2>New MSIC</h2>
        <p>Create a new MSIC for your customer</p>
    </div>
    <hr>
    <form action="{{ route('msics.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('msics.form')

        <div class="form-button-container">
            <button type="submit" class="primary-button" id="btnSubmit">Create MSIC</button>
        </div>
    </form>
</div>
@endsection