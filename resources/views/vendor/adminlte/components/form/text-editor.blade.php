@extends('partner::components.form.input-group-component')

{{-- Set errors bag internally --}}
@php($setErrorsBag($errors ?? null))

{{-- Set input group item section --}}
@section('input_group_item')
    {{-- Summernote Textarea --}}
    <textarea id="{{ $id }}" name="{{ $name }}"
        {{ $attributes->merge(['class' => $makeItemClass()]) }}
    >{{ $getOldValue($errorKey, $slot) }}</textarea>
@overwrite

{{-- Add plugin initialization and configuration code --}}
@push('js')
<script>
    $(() => {
        let usrCfg = @json($config);

        // Check for placeholder attribute.
        @isset($attributes['placeholder'])
            usrCfg['placeholder'] = "{{ $attributes['placeholder'] }}";
        @endisset

        // Initialize the plugin.
        $('#{{ $id }}').summernote(usrCfg);

        // Check for disabled attribute.
        @isset($attributes['disabled'])
            $('#{{ $id }}').summernote('disable');
        @endisset
    })
</script>
@endpush

{{-- Setup the font size of the plugin when using sm/lg sizes --}}
@once
@push('css')
<style type="text/css">
    {{-- SM size setup --}}
    .input-group-sm .note-editor {
        font-size: .875rem;
        line-height: 1;
    }

    {{-- LG size setup --}}
    .input-group-lg .note-editor {
        font-size: 1.25rem;
        line-height: 1.5;
    }

    {{-- Setup custom invalid style  --}}
    .partner-invalid-itegroup .note-editor {
        box-shadow: 0 .25rem 0.5rem rgba(239,68,68,.25);
        border-color: #ef4444 !important;
    }
</style>
@endpush
@endonce