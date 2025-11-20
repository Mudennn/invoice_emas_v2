@extends('layouts.main', ['title' => 'Change Password'])

@section('content')
<div class="invoice-form-container pt-4 px-4 col-md-3 mx-auto">
    <div class="form-header">
        <h1>Change Password</h1>
    </div>

    <div>
            <form method="POST" action="{{ route('password.save') }}">
                @csrf

                <div class="input-form">
                    <label class="form-label">Password</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
        
                    @error('password')
                        <span class="text-danger font-weight-bold small"># {{ $message }}</span>
                    @enderror
                </div>

                <div class="input-form">
                    <label class="form-label">Confirm Password</label>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                </div>

                <hr>
                <div class="py-4">
                    <button type="submit" class="form-primary-button w-100">Change Password</button>
                </div>

            </form>
    </div>

</div>
@endsection
