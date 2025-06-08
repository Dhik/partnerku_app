@php( $logout_url = View::getSection('logout_url') ?? config('partner.logout_url', 'logout') )

@if (config('partner.use_route_url', false))
    @php( $logout_url = $logout_url ? route($logout_url) : '' )
@else
    @php( $logout_url = $logout_url ? url($logout_url) : '' )
@endif

<li class="nav-item">
    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fa fa-fw fa-power-off text-red"></i>
        Log Out
    </a>
    <form id="logout-form" action="{{ $logout_url }}" method="POST" style="display: none;">
        @if(config('partner.logout_method'))
            {{ method_field(config('partner.logout_method')) }}
        @endif
        {{ csrf_field() }}
    </form>
</li>