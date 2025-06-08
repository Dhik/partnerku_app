{{-- IFrame Content Wrapper --}}
<div class="content-wrapper iframe-mode {{ config('partner.classes_content_wrapper', '') }}" data-widget="iframe"
     data-auto-show-new-tab="{{ config('partner.iframe.options.auto_show_new_tab', true) }}"
     data-loading-screen="{{ config('partner.iframe.options.loading_screen', true) }}"
     data-use-navbar-items="{{ config('partner.iframe.options.use_navbar_items', true) }}">

    {{-- IFrame Navbar --}}
    <div class="nav navbar navbar-expand navbar-white navbar-light border-bottom p-0">
        {{-- Close Buttons --}}
        @if(config('partner.iframe.buttons.close_all', true) || config('partner.iframe.buttons.close_all_other', true))
            <div class="nav-item dropdown">
                <a class="nav-link bg-danger dropdown-toggle" data-toggle="dropdown" href="#"
                   role="button" aria-haspopup="true" aria-expanded="false">
                    Close
                </a>
                <div class="dropdown-menu mt-0">
                    @if(config('partner.iframe.buttons.close', false))
                        <a class="dropdown-item" href="#" data-widget="iframe-close">
                            Close Active
                        </a>
                    @endif
                    @if(config('partner.iframe.buttons.close_all', true))
                        <a class="dropdown-item" href="#" data-widget="iframe-close" data-type="all">
                            Close All
                        </a>
                    @endif
                    @if(config('partner.iframe.buttons.close_all_other', true))
                        <a class="dropdown-item" href="#" data-widget="iframe-close" data-type="all-other">
                            Close All Other
                        </a>
                    @endif
                </div>
            </div>
        @elseif(config('partner.iframe.buttons.close', false))
            <a class="nav-link bg-danger" href="#" data-widget="iframe-close">
                 Close
            </a>
        @endif

        {{-- Scroll Left Button --}}
        @if(config('partner.iframe.buttons.scroll_left', true))
            <a class="nav-link bg-light" href="#" data-widget="iframe-scrollleft">
                <i class="fas fa-angle-double-left"></i>
            </a>
        @endif

        {{-- Tab List --}}
        <ul class="navbar-nav overflow-hidden" role="tablist">
            {{-- Default Tab --}}
            @if(! empty(config('partner.iframe.default_tab.url')))
                <li class="nav-item active" role="presentation">
                    <a href="#" class="btn-iframe-close" data-widget="iframe-close" data-type="only-this">
                        <i class="fas fa-times"></i>
                    </a>
                    <a id="tab-default" class="nav-link active" data-toggle="row" href="#panel-default"
                       role="tab" aria-controls="panel-default" aria-selected="true">
                        {{ config('partner.iframe.default_tab.title') ?: 'Home' }}
                    </a>
                </li>
            @endif
        </ul>

        {{-- Scroll Right Button --}}
        @if(config('partner.iframe.buttons.scroll_right', true))
            <a class="nav-link bg-light" href="#" data-widget="iframe-scrollright">
                <i class="fas fa-angle-double-right"></i>
            </a>
        @endif

        {{-- Fullscreen Button --}}
        @if(config('partner.iframe.buttons.fullscreen', true))
            <a class="nav-link bg-light" href="#" data-widget="iframe-fullscreen">
                <i class="fas fa-expand"></i>
            </a>
        @endif
    </div>

    {{-- IFrame Tab Content --}}
    <div class="tab-content">
        {{-- Loading Overlay --}}
        <div class="tab-loading">
        <div>
            <h2 class="display-4 text-center">
                <i class="fa fa-sync fa-spin text-secondary"></i>
                <br/>
                Loading...
            </h2>
        </div>
        </div>

        {{-- Default Tab Content --}}
        @if(! empty(config('partner.iframe.default_tab.url')))
            <div id="panel-default" class="tab-pane fade" role="tabpanel" aria-labelledby="tab-default">
                <iframe src="{{ config('partner.iframe.default_tab.url') }}"></iframe>
            </div>
        @endif

        {{-- Empty Tab --}}
        <div class="tab-empty">
            <h2 class="display-4 text-center">
                No content available
            </h2>
        </div>
    </div>
</div>