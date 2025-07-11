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

{{-- Add plugin initialization and configuration code --}}
@push('js')
<script>
    $(() => {
        $('#{{ $id }}').select2( @json($config) );

        // Add support to auto select old submitted values in case of validation errors.
        @if($errors->any() && $enableOldSupport)
            let oldOptions = @json(collect($getOldValue($errorKey)));

            $('#{{ $id }} option').each(function() {
                let value = $(this).val() || $(this).text();
                $(this).prop('selected', oldOptions.includes(value));
            });

            $('#{{ $id }}').trigger('change');
        @endif
    })
</script>
@endpush

{{-- CSS workarounds for the Select2 plugin --}}
@once
@push('css')
<style type="text/css">
    {{-- SM size setup --}}
    .input-group-sm .select2-selection--single {
        height: calc(1.8125rem + 2px) !important
    }
    .input-group-sm .select2-selection--single .select2-selection__rendered,
    .input-group-sm .select2-selection--single .select2-selection__placeholder {
        font-size: .875rem !important;
        line-height: 2.125;
    }
    .input-group-sm .select2-selection--multiple {
        min-height: calc(1.8125rem + 2px) !important
    }
    .input-group-sm .select2-selection--multiple .select2-selection__rendered {
        font-size: .875rem !important;
        line-height: normal;
    }

    {{-- LG size setup --}}
    .input-group-lg .select2-selection--single {
        height: calc(2.875rem + 2px) !important;
    }
    .input-group-lg .select2-selection--single .select2-selection__rendered,
    .input-group-lg .select2-selection--single .select2-selection__placeholder {
        font-size: 1.25rem !important;
        line-height: 2.25;
    }
    .input-group-lg .select2-selection--multiple {
        min-height: calc(2.875rem + 2px) !important
    }
    .input-group-lg .select2-selection--multiple .select2-selection__rendered {
        font-size: 1.25rem !important;
        line-height: 1.7;
    }

    {{-- Enhance the plugin to support readonly attribute --}}
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #e9ecef;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-search__field {
        display: none;
    }
</style>
@endpush
@endonce