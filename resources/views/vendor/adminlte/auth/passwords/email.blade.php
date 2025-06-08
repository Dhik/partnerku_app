@extends('partner::auth.auth-page', ['auth_type' => 'login'])

@php( $password_email_url = View::getSection('password_email_url') ?? config('partner.password_email_url', 'password/email') )

@if (config('partner.use_route_url', false))
    @php( $password_email_url = $password_email_url ? route($password_email_url) : '' )
@else
    @php( $password_email_url = $password_email_url ? url($password_email_url) : '' )
@endif

@section('auth_header', 'Reset Password')

@section('auth_body')
    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ $password_email_url }}" method="post">
        @csrf

        {{-- Email field --}}
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <input type="email" name="email" id="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" 
                       placeholder="Enter your email address" 
                       required autofocus>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
            @error('email')
                <div class="invalid-feedback d-block">
                    <strong>{{ $message }}</strong>
                </div>
            @enderror
        </div>

        {{-- Send reset link button --}}
        <button type="submit" class="btn btn-primary btn-block">
            <span class="fas fa-share-square mr-2"></span>
            Send Password Reset Link
        </button>
    </form>
@stop
