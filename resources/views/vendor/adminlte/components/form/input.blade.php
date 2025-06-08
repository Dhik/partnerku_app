@extends('partner::components.form.input-group-component')

{{-- Set errors bag internally --}}
@php($setErrorsBag($errors ?? null))

{{-- Set input group item section --}}
@section('input_group_item')
    {{-- Input --}}
    <input id="{{ $id }}" name="{{ $name }}"
        value="{{ $getOldValue($errorKey, $attributes->get('value')) }}"
        {{ $attributes->merge(['class' => $makeItemClass()]) }}>
@overwrite