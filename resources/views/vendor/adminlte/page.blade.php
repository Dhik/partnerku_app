@extends('adminlte::master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('body_data', $layoutHelper->makeBodyData())

{{-- Tenant/Brand Switcher for Multi-tenant Apps --}}
@if(Auth::user())
    @section('content_top_nav_left')
        @if(method_exists(Auth::user(), 'currentTenant') && Auth::user()->currentTenant)
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-building mr-2"></i>
                    {{ Auth::user()->currentTenant->name ?? trans('messages.no_brand_assigned') }}
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    @if(Auth::user()->hasRole('superadmin') && isset($brandList))
                        @foreach($brandList as $brand)
                            <a class="dropdown-item @if($brand->id === Auth::user()->current_tenant_id) active @endif" 
                               href="{{ route('tenant.change', $brand->id) }}">
                                <i class="fas fa-check mr-2 @if($brand->id !== Auth::user()->current_tenant_id) invisible @endif"></i>
                                {{ $brand->name }}
                            </a>
                        @endforeach
                    @else
                        @if(method_exists(Auth::user(), 'tenants'))
                            @foreach(Auth::user()->tenants as $tenant)
                                <a class="dropdown-item @if($tenant->id === Auth::user()->current_tenant_id) active @endif" 
                                   href="{{ route('tenant.change', $tenant->id) }}">
                                    <i class="fas fa-check mr-2 @if($tenant->id !== Auth::user()->current_tenant_id) invisible @endif"></i>
                                    {{ $tenant->name }}
                                </a>
                            @endforeach
                        @endif
                    @endif
                </div>
            </li>
        @endif
    @stop
@endif

@section('body')
    <div class="wrapper">

        {{-- Enhanced Preloader with Partner Branding --}}
        @if(config('adminlte.preloader.enabled', false))
            <div class="preloader flex-column justify-content-center align-items-center" id="preloader">
                <div class="preloader-content text-center">
                    <div class="preloader-logo mb-3">
                        <img src="{{ asset(config('adminlte.preloader.img.path', 'img/partner-logo.png')) }}"
                             class="{{ config('adminlte.preloader.img.effect', 'animation__pulse') }}"
                             alt="{{ config('adminlte.preloader.img.alt', 'Partner Preloader Image') }}"
                             width="{{ config('adminlte.preloader.img.width', 60) }}"
                             height="{{ config('adminlte.preloader.img.height', 60) }}">
                    </div>
                    <h4 class="text-white font-weight-bold">Partner</h4>
                    <p class="text-white-50">Loading your dashboard...</p>
                    <div class="preloader-spinner mt-3">
                        <div class="spinner-border text-light" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Top Navbar --}}
        @if($layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.navbar.navbar-layout-topnav')
        @else
            @include('adminlte::partials.navbar.navbar')
        @endif

        {{-- Left Main Sidebar --}}
        @if(!$layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.sidebar.left-sidebar')
        @endif

        {{-- Content Wrapper --}}
        @empty($iFrameEnabled)
            @include('adminlte::partials.cwrapper.cwrapper-default')
        @else
            @include('adminlte::partials.cwrapper.cwrapper-iframe')
        @endempty

        {{-- Footer --}}
        @hasSection('footer')
            <footer class="main-footer">
                <div class="row">
                    <div class="col-sm-6">
                        <strong>Copyright &copy; {{ date('Y') }} <a href="#" class="text-navy">Partner</a>.</strong>
                        All rights reserved.
                    </div>
                    <div class="col-sm-6 text-right">
                        <div class="d-none d-sm-inline-block">
                            <b>Version</b> 2.0.0 | 
                            <b>Build</b> {{ config('app.build_number', date('Ymd')) }}
                        </div>
                    </div>
                </div>
                @yield('footer')
            </footer>
        @endif

        {{-- Right Control Sidebar --}}
        @if(config('adminlte.right_sidebar'))
            @include('adminlte::partials.sidebar.right-sidebar')
        @endif

    </div>

    {{-- Global Alert Container --}}
    <div id="global-alerts" class="position-fixed" style="top: 80px; right: 20px; z-index: 1050; max-width: 350px;">
        {{-- Dynamic alerts will be inserted here --}}
    </div>

    {{-- Partner Theme Enhancements --}}
    <style>
        /* Enhanced preloader styling */
        .preloader {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%) !important;
        }
        
        .preloader-logo img {
            background: white;
            padding: 10px;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }

        /* Enhanced main layout */
        .wrapper {
            background-color: #f8fafc;
        }

        .main-header.navbar {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%) !important;
            border-bottom: none !important;
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1) !important;
        }

        .main-sidebar {
            background: white !important;
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.1) !important;
        }

        .content-wrapper {
            background-color: #f8fafc !important;
            padding: 1rem !important;
        }

        /* Enhanced navbar dropdown */
        .navbar .dropdown-menu {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
        }

        .navbar .dropdown-item {
            border-radius: 0.5rem;
            margin: 0.125rem 0;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .navbar .dropdown-item:hover {
            background-color: #f8fafc;
            color: #1e3a8a;
        }

        .navbar .dropdown-item.active {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
        }

        /* Enhanced footer */
        .main-footer {
            background: white;
            border-top: 1px solid #e2e8f0;
            padding: 1rem 1.5rem;
            margin-left: 0;
        }

        /* Global alerts styling */
        #global-alerts .alert {
            margin-bottom: 0.5rem;
            border-radius: 0.75rem;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Enhanced responsive design */
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 0.5rem !important;
            }
            
            .main-footer {
                padding: 1rem;
                text-align: center;
            }
            
            .main-footer .row {
                flex-direction: column;
            }
            
            .main-footer .text-right {
                text-align: center !important;
                margin-top: 0.5rem;
            }
        }

        /* Loading overlay for AJAX operations */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(30, 58, 138, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-overlay .spinner {
            width: 3rem;
            height: 3rem;
            border: 0.25rem solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        /* Enhanced sidebar for Partner theme */
        .nav-sidebar .nav-item .nav-link.active {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%) !important;
            color: white !important;
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1);
        }

        .nav-sidebar .nav-item .nav-link:hover {
            background-color: #f8fafc !important;
            color: #1e3a8a !important;
        }

        /* Partner brand colors for various elements */
        .text-navy { color: #1e3a8a !important; }
        .bg-navy { background-color: #1e3a8a !important; }
        .border-navy { border-color: #1e3a8a !important; }
        .btn-navy {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            border: none;
            color: white;
        }
        .btn-navy:hover {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            color: white;
        }
    </style>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')

    <script>
        $(document).ready(function() {
            // Enhanced preloader with Partner branding
            const preloader = $('#preloader');
            if (preloader.length) {
                $(window).on('load', function() {
                    setTimeout(function() {
                        preloader.fadeOut(300, function() {
                            $(this).remove();
                        });
                    }, 500);
                });
            }

            // Global alert system
            window.showAlert = function(message, type = 'info', timeout = 5000) {
                const alertId = 'alert-' + Date.now();
                const alertHtml = `
                    <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} mr-2"></i>
                        ${message}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `;
                
                $('#global-alerts').append(alertHtml);
                
                if (timeout > 0) {
                    setTimeout(function() {
                        $('#' + alertId).fadeOut(300, function() {
                            $(this).remove();
                        });
                    }, timeout);
                }
            };

            // Enhanced AJAX setup with Partner theme
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    // Show loading overlay for long operations
                    if (!$('.loading-overlay').length) {
                        $('body').append(`
                            <div class="loading-overlay" style="display: none;">
                                <div class="text-center text-white">
                                    <div class="spinner mb-3"></div>
                                    <h5>Processing...</h5>
                                    <p>Please wait while we process your request.</p>
                                </div>
                            </div>
                        `);
                    }
                },
                complete: function() {
                    $('.loading-overlay').fadeOut(300);
                },
                error: function(xhr, status, error) {
                    $('.loading-overlay').fadeOut(300);
                    
                    let message = 'An unexpected error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    
                    showAlert(message, 'danger');
                }
            });

            // Enhanced sidebar interactions
            // $('.nav-sidebar .nav-item .nav-link').on('click', function(e) {
            //     const $this = $(this);
            //     const $parent = $this.parent();
                
            //     // Handle treeview menu
            //     if ($this.next('ul').length > 0) {
            //         e.preventDefault();
            //         $parent.toggleClass('menu-open');
            //         $this.next('ul').slideToggle(300);
            //     }
            // });

            // Enhanced form handling
            $('form').on('submit', function() {
                const $form = $(this);
                const $submitBtn = $form.find('button[type="submit"]');
                
                if (!$submitBtn.prop('disabled')) {
                    $submitBtn.prop('disabled', true);
                    const originalText = $submitBtn.html();
                    $submitBtn.data('original-text', originalText);
                    $submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');
                    
                    // Re-enable after timeout as fallback
                    setTimeout(function() {
                        $submitBtn.prop('disabled', false);
                        $submitBtn.html(originalText);
                    }, 10000);
                }
            });

            // Auto-hide session alerts
            $('.alert').each(function() {
                const $alert = $(this);
                setTimeout(function() {
                    $alert.fadeOut(300);
                }, 5000);
            });

            // Enhanced keyboard shortcuts
            $(document).on('keydown', function(e) {
                // Ctrl/Cmd + / for help
                if ((e.ctrlKey || e.metaKey) && e.key === '/') {
                    e.preventDefault();
                    showAlert('Keyboard shortcuts: Ctrl+K (Search), Ctrl+/ (Help)', 'info', 3000);
                }
                
                // Escape to close modals and dropdowns
                if (e.key === 'Escape') {
                    $('.modal').modal('hide');
                    $('.dropdown-menu').removeClass('show');
                }
            });

            // Enhanced dropdown behavior
            $('.dropdown-toggle').on('click', function(e) {
                e.preventDefault();
                const $dropdown = $(this).next('.dropdown-menu');
                $('.dropdown-menu').not($dropdown).removeClass('show');
                $dropdown.toggleClass('show');
            });

            // Close dropdowns when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dropdown').length) {
                    $('.dropdown-menu').removeClass('show');
                }
            });

            // Enhanced mobile menu behavior
            if ($(window).width() <= 768) {
                $('.nav-sidebar .nav-item .nav-link').on('click', function() {
                    if ($(this).next('ul').length === 0) {
                        // Close sidebar on mobile after clicking a link
                        $('body').removeClass('sidebar-open');
                    }
                });
            }

            // Real-time notifications (if implemented)
            if (typeof window.Echo !== 'undefined') {
                // Example notification handling
                window.Echo.private('App.Models.User.' + {{ Auth::id() ?? 0 }})
                    .notification((notification) => {
                        showAlert(notification.message, notification.type || 'info');
                    });
            }

            // Performance monitoring
            if (window.performance && window.performance.timing) {
                const loadTime = window.performance.timing.loadEventEnd - window.performance.timing.navigationStart;
                if (loadTime > 3000) {
                    console.warn('Page load time is high:', loadTime + 'ms');
                }
            }

            // Enhanced error boundary for JavaScript errors
            window.addEventListener('error', function(e) {
                console.error('JavaScript Error:', e.error);
                if (window.location.hostname !== 'localhost') {
                    // Only show user-friendly message in production
                    showAlert('Something went wrong. Please refresh the page.', 'warning');
                }
            });

            // Service worker updates
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.addEventListener('controllerchange', function() {
                    showAlert('App updated! Please refresh to get the latest version.', 'info', 0);
                });
            }
        });
    </script>
@stop
