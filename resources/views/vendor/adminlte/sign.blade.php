@extends('partner::master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@section('partner_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('body')
    <div class="wrapper">
        @yield('content')
    </div>
@stop

@section('partner_js')
    @stack('js')
    @yield('js')
@stop