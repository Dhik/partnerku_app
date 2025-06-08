@extends('partner::auth.auth-page', ['auth_type' => 'login'])

@section('auth_header', 'Verify Your Email Address')

@section('auth_body')
    @if(session('resent'))
        <div class="alert alert-success" role="alert">
            A fresh verification link has been sent to your email address.
        </div>
    @endif

    <p class="text-center mb-4">
        Before proceeding, please check your email for a verification link.
        If you did not receive the email, you can request another one below.
    </p>

    <form class="text-center" method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane mr-2"></i>
            Click here to request another verification email
        </button>
    </form>
@stop