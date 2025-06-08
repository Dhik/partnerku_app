@extends('partner::auth.auth-page', ['auth_type' => 'login'])

@php( $password_reset_url = View::getSection('password_reset_url') ?? config('partner.password_reset_url', 'password/reset') )

@if (config('partner.use_route_url', false))
    @php( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
@else
    @php( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
@endif

@section('auth_header', 'Reset Password')

@section('auth_body')
    <form action="{{ $password_reset_url }}" method="post">
        @csrf

        {{-- Token field --}}
        <input type="hidden" name="token" value="{{ $token }}">

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

        {{-- Password field --}}
        <div class="form-group">
            <label for="password" class="form-label">New Password</label>
            <div class="input-group">
                <input type="password" name="password" id="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Enter new password" 
                       required>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">
                    <strong>{{ $message }}</strong>
                </div>
            @enderror
        </div>

        {{-- Password confirmation field --}}
        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="input-group">
                <input type="password" name="password_confirmation" id="password_confirmation"
                       class="form-control @error('password_confirmation') is-invalid @enderror"
                       placeholder="Confirm new password" 
                       required>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
            @error('password_confirmation')
                <div class="invalid-feedback d-block">
                    <strong>{{ $message }}</strong>
                </div>
            @enderror
        </div>

        {{-- Reset password button --}}
        <button type="submit" class="btn btn-primary btn-block">
            <span class="fas fa-sync-alt mr-2"></span>
            Reset Password
        </button>
    </form>
@stop