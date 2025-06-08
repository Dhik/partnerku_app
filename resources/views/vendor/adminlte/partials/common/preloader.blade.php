<div class="preloader flex-column justify-content-center align-items-center">
    {{-- Preloader logo --}}
    <img src="{{ asset(config('partner.preloader.img.path', 'img/partner-logo.png')) }}"
         class="{{ config('partner.preloader.img.effect', 'animation__shake') }}"
         alt="{{ config('partner.preloader.img.alt', 'Partner Preloader Image') }}"
         width="{{ config('partner.preloader.img.width', 60) }}"
         height="{{ config('partner.preloader.img.height', 60) }}">
</div>