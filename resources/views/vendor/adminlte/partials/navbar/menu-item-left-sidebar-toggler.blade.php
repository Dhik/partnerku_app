<li class="nav-item">
    <a class="nav-link" data-widget="pushmenu" href="#"
        @if(config('partner.sidebar_collapse_remember'))
            data-enable-remember="true"
        @endif
        @if(!config('partner.sidebar_collapse_remember_no_transition'))
            data-no-transition-after-reload="false"
        @endif
        @if(config('partner.sidebar_collapse_auto_size'))
            data-auto-collapse-size="{{ config('partner.sidebar_collapse_auto_size') }}"
        @endif>
        <i class="fas fa-bars"></i>
        <span class="sr-only">Toggle navigation</span>
    </a>
</li>