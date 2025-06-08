@extends('partner::components.form.input-group-component')

{{-- Set errors bag internally --}}
@php($setErrorsBag($errors ?? null))

{{-- Set input group item section --}}
@section('input_group_item')
    {{-- Date Range Input --}}
    <input id="{{ $id }}" name="{{ $name }}"
        {{ $attributes->merge(['class' => $makeItemClass()]) }}>
@overwrite

{{-- Add plugin initialization and configuration code --}}
@push('js')
<script>
    $(() => {
        let usrCfg = _Partner_DateRange.parseCfg( @json($config) );

        // Add support to display a placeholder
        @if($attributes->has('placeholder'))
            usrCfg.autoUpdateInput = false;

            $('#{{ $id }}').on('apply.daterangepicker', function(ev, picker) {
                let startDate = picker.startDate.format(picker.locale.format);
                let endDate = picker.endDate.format(picker.locale.format);

                let value = picker.singleDatePicker
                    ? startDate
                    : startDate + picker.locale.separator + endDate;

                $(this).val(value);
            });

            $('#{{ $id }}').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        @endif

        // Check if the default set of ranges should be enabled
        @isset($enableDefaultRanges)
            usrCfg.ranges = usrCfg.ranges || _Partner_DateRange.defaultRanges;
            let range = usrCfg.ranges[ @json($enableDefaultRanges) ];

            if (Array.isArray(range) && range.length > 1) {
                usrCfg.startDate = range[0];
                usrCfg.endDate = range[1];
            }
        @endisset

        // Add support to auto select the previous submitted value
        @if($errors->any() && $enableOldSupport)
            let oldRange = @json($getOldValue($errorKey, ""));
            let separator = " - ";

            if (usrCfg.locale && usrCfg.locale.separator) {
                separator = usrCfg.locale.separator;
            }

            // Update the related input.
            if (! usrCfg.autoUpdateInput) {
                $('#{{ $id }}').val(oldRange);
            }

            // Update the internal plugin data.
            if (oldRange) {
                oldRange = oldRange.split(separator);
                usrCfg.startDate = oldRange.length > 0 ? oldRange[0] : null;
                usrCfg.endDate = oldRange.length > 1 ? oldRange[1] : null;
            }
        @endif

        // Setup the underlying date range plugin.
        $('#{{ $id }}').daterangepicker(usrCfg);
    })
</script>
@endpush

{{-- Register Javascript utility class for this component --}}
@once
@push('js')
<script>
    class _Partner_DateRange {
        /**
         * A default set of ranges options.
         */
        static defaultRanges = {
            'Today': [
                moment().startOf('day'),
                moment().endOf('day')
            ],
            'Yesterday': [
                moment().subtract(1, 'days').startOf('day'),
                moment().subtract(1, 'days').endOf('day')
            ],
            'Last 7 Days': [
                moment().subtract(6, 'days'),
                moment()
            ],
            'Last 30 Days': [
                moment().subtract(29, 'days'),
                moment()
            ],
            'This Month': [
                moment().startOf('month'),
                moment().endOf('month')
            ],
            'Last Month': [
                moment().subtract(1, 'month').startOf('month'),
                moment().subtract(1, 'month').endOf('month')
            ]
        }

        /**
         * Parse the php plugin configuration and eval the javascript code.
         */
        static parseCfg(cfg) {
            for (const prop in cfg) {
                let v = cfg[prop];

                if (typeof v === 'string' && v.startsWith('js:')) {
                    cfg[prop] = eval(v.slice(3));
                } else if (typeof v === 'object') {
                    cfg[prop] = _Partner_DateRange.parseCfg(v);
                }
            }

            return cfg;
        }
    }
</script>
@endpush
@endonce