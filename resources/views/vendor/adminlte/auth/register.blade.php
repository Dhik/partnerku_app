@extends('partner::auth.auth-page', ['auth_type' => 'register'])

@php( $login_url = View::getSection('login_url') ?? config('partner.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('partner.register_url', 'register') )

@if (config('partner.use_route_url', false))
    @php( $login_url = $login_url ? route($login_url) : '' )
    @php( $register_url = $register_url ? route($register_url) : '' )
@else
    @php( $login_url = $login_url ? url($login_url) : '' )
    @php( $register_url = $register_url ? url($register_url) : '' )
@endif

@section('auth_header', 'Create New Account')

@section('auth_body')
    <form action="{{ $register_url }}" method="post">
        @csrf

        {{-- Name field --}}
        <div class="form-group">
            <label for="name" class="form-label">Full Name</label>
            <div class="input-group">
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" 
                       placeholder="Enter your full name" 
                       required autofocus>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-user"></span>
                    </div>
                </div>
            </div>
            @error('name')
                <div class="invalid-feedback d-block">
                    <strong>{{ $message }}</strong>
                </div>
            @enderror
        </div>

        {{-- Email field --}}
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <input type="email" name="email" id="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" 
                       placeholder="Enter your email address" 
                       required>
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
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <input type="password" name="password" id="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Enter your password" 
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

        {{-- Confirm password field --}}
        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="input-group">
                <input type="password" name="password_confirmation" id="password_confirmation"
                       class="form-control @error('password_confirmation') is-invalid @enderror"
                       placeholder="Confirm your password" 
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

        {{-- Register button --}}
        <button type="submit" class="btn btn-primary btn-block">
            <span class="fas fa-user-plus mr-2"></span>
            Create Account
        </button>
    </form>
@stop

@section('auth_footer')
    <div class="register-link">
        <span class="text-muted">Already have an account?</span>
        <a href="{{ $login_url }}" class="auth-link">
            Sign in here
        </a>
    </div>
@stop