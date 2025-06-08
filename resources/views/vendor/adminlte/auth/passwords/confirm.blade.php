@extends('partner::master')

@section('partner_css')
    @yield('css')
@stop

@section('classes_body', 'lockscreen')

@php( $password_reset_url = View::getSection('password_reset_url') ?? config('partner.password_reset_url', 'password/reset') )
@php( $dashboard_url = View::getSection('dashboard_url') ?? config('partner.dashboard_url', 'home') )

@if (config('partner.use_route_url', false))
    @php( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

@section('body')
    <div class="lockscreen-wrapper">
        {{-- Partner Logo --}}
        <div class="lockscreen-logo">
            <a href="{{ $dashboard_url }}">
                <img src="{{ asset(config('partner.logo_img', 'img/partner-logo.png')) }}" height="50">
                {!! config('partner.logo', '<b>Partner</b>') !!}
            </a>
        </div>

        {{-- User name --}}
        <div class="lockscreen-name">
            {{ isset(Auth::user()->name) ? Auth::user()->name : Auth::user()->email }}
        </div>

        {{-- Lockscreen item --}}
        <div class="lockscreen-item">
            @if(config('partner.usermenu_image'))
                <div class="lockscreen-image">
                    <img src="{{ Auth::user()->partner_image() }}" alt="{{ Auth::user()->name }}">
                </div>
            @endif

            <form method="POST" action="{{ route('password.confirm') }}"
                  class="lockscreen-credentials @if(!config('partner.usermenu_image'))ml-0 @endif">
                @csrf

                <div class="input-group">
                    <input id="password" type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Enter your password" required autofocus>

                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Password error alert --}}
        @error('password')
            <div class="lockscreen-subitem text-center" role="alert">
                <b class="text-danger">{{ $message }}</b>
            </div>
        @enderror

        {{-- Help block --}}
        <div class="help-block text-center">
            Enter your password to confirm your identity
        </div>

        {{-- Additional links --}}
        <div class="text-center">
            <a href="{{ $password_reset_url }}" class="auth-link">
                Forgot your password?
            </a>
        </div>
    </div>
@stop

@section('partner_js')
    @stack('js')
    @yield('js')
@stop