{{-- Navbar darkmode widget --}}
<li class="nav-item partner-darkmode-widget">
    <a class="nav-link" href="#" role="button">
        <i class="{{ $makeIconClass() }}"></i>
    </a>
</li>

{{-- Add Javascript listener for the click event --}}
@once
@push('js')
<script>
    $(() => {
        const body = document.querySelector('body');
        const widget = document.querySelector('li.partner-darkmode-widget');
        const widgetIcon = widget.querySelector('i');

        // Get the set of classes to be toggled on the widget icon.
        const iconClasses = [
            ...@json($makeIconEnabledClass()),
            ...@json($makeIconDisabledClass())
        ];

        // Add 'click' event listener for the darkmode widget.
        widget.addEventListener('click', () => {
            // Toggle dark-mode class on the main body tag.
            body.classList.toggle('dark-mode');

            // Support to IFrame mode: toggle dark-mode class on the body of any present iframe.
            let iframes = document.querySelectorAll('div.iframe-mode iframe');

            iframes.forEach((f) => {
                b = f.contentDocument.querySelector('body');
                b.classList.toggle('dark-mode');
            });

            // Toggle the classes on the widget icon.
            iconClasses.forEach((c) => widgetIcon.classList.toggle(c));

            // Notify the server about dark mode toggle
            const fetchCfg = {
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                method: 'POST',
            };

            fetch("{{ route('partner.darkmode.toggle') }}", fetchCfg)
            .catch((error) => {
                console.log('Failed to notify server that dark mode was toggled', error);
            });
        });
    })
</script>
@endpush
@endonce