@extends('layouts.dashboard', ['title' => 'Delete MSIC #' . $msic->msic_code])

@section('content')
    <div>
        <div class="d-flex flex-column gap-2 form-header-container">
            <a href="{{ url()->previous() }}" class="back-button mb-4">
                <i class="ph ph-arrow-left" style="font-size: 16px;"></i>
                Back
            </a>
            <h2>Delete MSIC</h2>
            <p>Delete the MSIC for your customer</p>
        </div>
        <hr>
        <form action="{{ route('msics.destroy', $msic->id) }}" method="POST">
            @csrf
            @method('DELETE')

            @include('msics.form')
            <hr>
            <div style="padding: 32px 32px 16px 32px;">
                <div class="alert alert-danger" role="alert">
                    Are you sure want to delete?
                </div>
            </div>

            <div class="form-button-container">
                <button type="submit" class="delete-button" id="btnSubmit">Delete MSIC</button>
            </div>
        </form>
    </div>
@endsection


