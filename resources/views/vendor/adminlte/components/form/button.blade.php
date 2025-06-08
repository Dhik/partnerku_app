<button type="{{ $type }}" {{ $attributes->merge(['class' => "btn btn-{$theme}"]) }}>
    @isset($icon) <i class="{{ $icon }} mr-2"></i> @endisset
    @isset($label) {{ $label }} @endisset
</button>