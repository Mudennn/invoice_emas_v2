@extends('layouts.dashboard', ['title' => 'Edit MSIC #' . $msic->msic_code])

@section('content')

<div class="form-container">
    <div>
        <div class="d-flex flex-column gap-2 form-header-container">
            <a href="{{ url()->previous() }}" class="back-button mb-4">
                <i class="ph ph-arrow-left" style="font-size: 16px;"></i>
                Back
            </a>
            <h2>Edit MSIC</h2>
            <p>Edit the MSIC for your customer</p>
        </div>
        <hr>
        <form action="{{ route('msics.update', $msic->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            @include('msics.form')
            <hr>
            <div class="form-button-container">
                <button type="submit" class="primary-button" id="btnSubmit">Update MSIC</button>
            </div>
        </form>
    </div>
</div>
@endsection
