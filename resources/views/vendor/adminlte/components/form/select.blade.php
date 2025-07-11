@extends('partner::components.form.input-group-component')

{{-- Set errors bag internally --}}
@php($setErrorsBag($errors ?? null))

{{-- Set input group item section --}}
@section('input_group_item')
    {{-- Select --}}
    <select id="{{ $id }}" name="{{ $name }}"
        {{ $attributes->merge(['class' => $makeItemClass()]) }}>
        {{ $slot }}
    </select>
@overwrite

{{-- Support to auto select the old submitted values --}}
@if($errors->any() && $enableOldSupport)
@push('js')
<script>
    $(() => {
        let oldOptions = @json(collect($getOldValue($errorKey)));

        $('#{{ $id }} option').each(function() {
            let value = $(this).val() || $(this).text();
            $(this).prop('selected', oldOptions.includes(value));
        });
    });
</script>
@endpush
@endif