@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@php( $dashboard_url = View::getSection('dashboard_url') ?? config('partner.dashboard_url', 'home') )

@if (config('partner.use_route_url', false))
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

<a href="{{ $dashboard_url }}"
    @if($layoutHelper->isLayoutTopnavEnabled())
        class="navbar-brand {{ config('partner.classes_brand') }}"
    @else
        class="brand-link {{ config('partner.classes_brand') }}"
    @endif>

    {{-- Brand logo --}}
    <img src="{{ asset(config('partner.logo_img', 'img/partner-logo.png')) }}"
         alt="{{ config('partner.logo_img_alt', 'Partner') }}"
         class="{{ config('partner.logo_img_class', 'brand-image img-circle elevation-3') }}"
         style="opacity:.8">

    {{-- Brand text --}}
    <span class="brand-text font-weight-light {{ config('partner.classes_brand_text') }}">
        {!! config('partner.logo', '<b>Partner</b>') !!}
    </span>
</a>