@extends('partner::components.form.input-group-component')

{{-- Set errors bag internally --}}
@php($setErrorsBag($errors ?? null))

{{-- Set input group item section --}}
@section('input_group_item')
    {{-- Input Slider --}}
    <input id="{{ $id }}" name="{{ $name }}"
        {{ $attributes->merge(['class' => $makeItemClass()]) }}>
@overwrite

{{-- Add plugin initialization and configuration code --}}
@push('js')
<script>
    $(() => {
        let usrCfg = @json($config);

        // Check for disabled attribute
        @if($attributes->has('disabled'))
            usrCfg.enabled = false;
        @endif

        // Check for min, max and step attributes
        @if($attributes->has('min'))
            usrCfg.min = Number( @json($attributes['min']) );
        @endif

        @if($attributes->has('max'))
            usrCfg.max = Number( @json($attributes['max']) );
        @endif

        @if($attributes->has('step'))
            usrCfg.step = Number( @json($attributes['step']) );
        @endif

        // Check for value attribute
        @if($attributes->has('value') || ($errors->any() && $enableOldSupport))
            let value = @json($getOldValue($errorKey, $attributes['value']));
            if (value) {
                value = value.split(",").map(Number);
                usrCfg.value = value.length > 1 ? value : value[0];
            }
        @endif

        // Initialize the plugin.
        let slider = $('#{{ $id }}').bootstrapSlider(usrCfg);

        // Fix height conflict when orientation is vertical.
        let or = slider.bootstrapSlider('getAttribute', 'orientation');
        if (or == 'vertical') {
            $('#' + usrCfg.id).css('height', '210px');
            slider.bootstrapSlider('relayout');
        }
    })
</script>
@endpush

{{-- Add CSS workarounds for the plugin --}}
@push('css')
<style type="text/css">
    {{-- Setup plugin color --}}
    @isset($color)
        #{{ $config['id'] }} .slider-handle {
            background: {{ $color }};
        }
        #{{ $config['id'] }} .slider-selection {
            background: {{ $color }};
            opacity: 0.5;
        }
        #{{ $config['id'] }} .slider-tick.in-selection {
            background: {{ $color }};
            opacity: 0.9;
        }
    @endisset

    {{-- Set flex property when using addons slots --}}
    @if(isset($appendSlot) || isset($prependSlot))
        #{{ $config['id'] }} {
            flex: 1 1 0;
            align-self: center;
            @isset($appendSlot) margin-right: 5px; @endisset
            @isset($prependSlot) margin-left: 5px; @endisset
        }
    @endif
</style>
@endpush

{{-- Setup custom invalid style  --}}
@once
@push('css')
<style type="text/css">
    .partner-invalid-islgroup .slider-track,
    .partner-invalid-islgroup > .input-group-prepend > *,
    .partner-invalid-islgroup > .input-group-append > * {
        box-shadow: 0 .25rem 0.5rem rgba(239,68,68,.25);
    }

    .partner-invalid-islgroup .slider-vertical {
        margin-bottom: 1rem;
    }
</style>
@endpush
@endonce