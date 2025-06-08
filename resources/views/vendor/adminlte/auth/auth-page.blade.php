@extends('adminlte::master')

@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

@section('adminlte_css')
    @stack('css')
    @yield('css')
    <link rel="stylesheet" href="{{ asset('css/partner-theme.css') }}">
    <style>
        /* Auth Page Specific Styles */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%) !important;
            min-height: 100vh !important;
        }

        .{{ $auth_type ?? 'login' }}-page {
            background: transparent !important;
            min-height: 100vh !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 1rem !important;
        }

        .{{ $auth_type ?? 'login' }}-box {
            width: 100% !important;
            max-width: 450px !important;
            margin: 0 !important;
        }

        /* Logo Section */
        .{{ $auth_type ?? 'login' }}-logo {
            text-align: center !important;
            margin-bottom: 2rem !important;
        }

        .{{ $auth_type ?? 'login' }}-logo a {
            color: white !important;
            font-size: 2rem !important;
            font-weight: 700 !important;
            text-decoration: none !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 1rem !important;
            transition: all 0.3s ease !important;
        }

        .{{ $auth_type ?? 'login' }}-logo a:hover {
            transform: translateY(-2px) !important;
            text-decoration: none !important;
            color: rgba(255, 255, 255, 0.9) !important;
        }

        .{{ $auth_type ?? 'login' }}-logo img {
            width: 60px !important;
            height: 60px !important;
            background: white !important;
            padding: 8px !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.3s ease !important;
        }

        .{{ $auth_type ?? 'login' }}-logo a:hover img {
            transform: scale(1.05) !important;
            box-shadow: 0 8px 12px -2px rgba(0, 0, 0, 0.2) !important;
        }

        /* Card Styling */
        .card {
            border: none !important;
            border-radius: 1rem !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            overflow: hidden !important;
            backdrop-filter: blur(10px) !important;
            background: rgba(255, 255, 255, 0.95) !important;
        }

        .card-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%) !important;
            color: white !important;
            border-bottom: none !important;
            padding: 2rem !important;
            text-align: center !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.1;
        }

        .card-title {
            color: white !important;
            font-weight: 600 !important;
            margin: 0 !important;
            font-size: 1.5rem !important;
            position: relative !important;
            z-index: 1 !important;
        }

        .card-body {
            padding: 2rem !important;
            background: white !important;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 1.5rem !important;
        }

        .form-label {
            color: #1e293b !important;
            font-weight: 600 !important;
            margin-bottom: 0.5rem !important;
            font-size: 0.875rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
        }

        .form-control {
            border: 2px solid #e2e8f0 !important;
            border-radius: 0.75rem !important;
            padding: 0.875rem 1rem !important;
            font-size: 1rem !important;
            transition: all 0.3s ease !important;
            height: auto !important;
            background: white !important;
        }

        .form-control:focus {
            border-color: #1e3a8a !important;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1) !important;
            background: white !important;
            outline: none !important;
        }

        .form-control.is-invalid {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
        }

        .input-group {
            margin-bottom: 0 !important;
        }

        .input-group-text {
            background: #f8fafc !important;
            border: 2px solid #e2e8f0 !important;
            color: #64748b !important;
            border-radius: 0.75rem !important;
            border-left: none !important;
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }

        .input-group .form-control {
            border-right: none !important;
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }

        .input-group .form-control:focus + .input-group-append .input-group-text {
            border-color: #1e3a8a !important;
        }

        /* Buttons */
        .btn {
            border-radius: 0.75rem !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
            letter-spacing: 0.025em !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%) !important;
            border: none !important;
            color: white !important;
            padding: 0.875rem 2rem !important;
            font-size: 1rem !important;
            width: 100% !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            color: white !important;
        }

        .btn-primary:active {
            transform: translateY(0) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        }

        /* Links */
        .auth-link {
            color: #1e3a8a !important;
            text-decoration: none !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
        }

        .auth-link:hover {
            color: #1e40af !important;
            text-decoration: underline !important;
        }

        /* Checkbox Styling */
        .custom-control-input:checked ~ .custom-control-label::before {
            background-color: #1e3a8a !important;
            border-color: #1e3a8a !important;
        }

        .custom-control-input:focus ~ .custom-control-label::before {
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1) !important;
        }

        /* iCheck Bootstrap */
        .icheck-primary input[type=checkbox]:checked + label::before {
            background-color: #1e3a8a !important;
            border-color: #1e3a8a !important;
        }

        .icheck-primary input[type=checkbox]:focus + label::before {
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1) !important;
        }

        /* Validation Feedback */
        .invalid-feedback {
            display: block !important;
            font-weight: 500 !important;
            font-size: 0.875rem !important;
            margin-top: 0.5rem !important;
            color: #ef4444 !important;
        }

        /* Alert Styling */
        .alert {
            border-radius: 0.75rem !important;
            border: none !important;
            padding: 1rem 1.25rem !important;
            font-weight: 500 !important;
            margin-bottom: 1.5rem !important;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%) !important;
            color: #065f46 !important;
            border-left: 4px solid #10b981 !important;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%) !important;
            color: #991b1b !important;
            border-left: 4px solid #ef4444 !important;
        }

        .alert-info {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(96, 165, 250, 0.1) 100%) !important;
            color: #1e3a8a !important;
            border-left: 4px solid #3b82f6 !important;
        }

        /* Footer Links */
        .auth-footer {
            text-align: center !important;
            margin-top: 1.5rem !important;
            padding-top: 1rem !important;
            border-top: 1px solid #e2e8f0 !important;
        }

        .auth-footer a {
            color: #1e3a8a !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            transition: all 0.3s ease !important;
        }

        .auth-footer a:hover {
            color: #1e40af !important;
            text-decoration: underline !important;
        }

        /* Welcome Text */
        .welcome-text {
            color: rgba(255, 255, 255, 0.9) !important;
            font-size: 1rem !important;
            margin: 0.5rem 0 0 0 !important;
            font-weight: 400 !important;
        }

        /* Loading Animation */
        .btn .fa-spinner {
            animation: spin 1s linear infinite !important;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Fade In Animation */
        .auth-card-animate {
            animation: fadeInUp 0.6s ease-out !important;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .{{ $auth_type ?? 'login' }}-page {
                padding: 0.5rem !important;
            }
            
            .{{ $auth_type ?? 'login' }}-box {
                max-width: 100% !important;
            }

            .card-header,
            .card-body {
                padding: 1.5rem !important;
            }

            .{{ $auth_type ?? 'login' }}-logo {
                margin-bottom: 1.5rem !important;
            }

            .{{ $auth_type ?? 'login' }}-logo a {
                font-size: 1.75rem !important;
            }

            .{{ $auth_type ?? 'login' }}-logo img {
                width: 50px !important;
                height: 50px !important;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .card {
                background: rgba(30, 41, 59, 0.95) !important;
            }
            
            .card-body {
                background: #1e293b !important;
                color: #e2e8f0 !important;
            }
            
            .form-control {
                background: #334155 !important;
                border-color: #475569 !important;
                color: #e2e8f0 !important;
            }
            
            .form-control:focus {
                background: #334155 !important;
                border-color: #3b82f6 !important;
            }
            
            .form-label {
                color: #e2e8f0 !important;
            }
            
            .input-group-text {
                background: #475569 !important;
                border-color: #475569 !important;
                color: #94a3b8 !important;
            }
        }
    </style>
@stop

@section('classes_body'){{ ($auth_type ?? 'login') . '-page' }}@stop

@section('body')
    <div class="{{ $auth_type ?? 'login' }}-box auth-card-animate">

        {{-- Logo --}}
        <div class="{{ $auth_type ?? 'login' }}-logo">
            <a href="{{ $dashboard_url }}">
                {{-- Logo Image --}}
                @if (config('adminlte.auth_logo.enabled', false))
                    <img src="{{ asset(config('adminlte.auth_logo.img.path')) }}"
                         alt="{{ config('adminlte.auth_logo.img.alt') }}"
                         @if (config('adminlte.auth_logo.img.class', null))
                            class="{{ config('adminlte.auth_logo.img.class') }}"
                         @endif
                         @if (config('adminlte.auth_logo.img.width', null))
                            width="{{ config('adminlte.auth_logo.img.width') }}"
                         @endif
                         @if (config('adminlte.auth_logo.img.height', null))
                            height="{{ config('adminlte.auth_logo.img.height') }}"
                         @endif>
                @else
                    <img src="{{ asset(config('adminlte.logo_img', 'img/partner-logo.png')) }}"
                         alt="{{ config('adminlte.logo_img_alt', 'Partner Logo') }}" 
                         height="50">
                @endif

                {{-- Logo Label --}}
                {!! config('adminlte.logo', '<b>Partner</b>') !!}
            </a>
            
            @hasSection('auth_header')
                @yield('auth_header')
            @endif
        </div>

        {{-- Card Box --}}
        <div class="card {{ config('adminlte.classes_auth_card', 'card-outline card-primary') }}">

            {{-- Card Header --}}
            @hasSection('auth_header')
                <div class="card-header {{ config('adminlte.classes_auth_header', '') }}">
                    <h3 class="card-title">
                        @yield('auth_header')
                    </h3>
                </div>
            @endif

            {{-- Card Body --}}
            <div class="card-body {{ $auth_type ?? 'login' }}-card-body {{ config('adminlte.classes_auth_body', '') }}">
                @yield('auth_body')
            </div>

            {{-- Card Footer --}}
            @hasSection('auth_footer')
                <div class="card-footer {{ config('adminlte.classes_auth_footer', '') }} auth-footer">
                    @yield('auth_footer')
                </div>
            @endif

        </div>

    </div>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
    
    <script>
        $(document).ready(function() {
            // Enhanced form interactions
            $('.form-control').on('focus', function() {
                $(this).closest('.form-group').addClass('focused');
            }).on('blur', function() {
                if (!$(this).val()) {
                    $(this).closest('.form-group').removeClass('focused');
                }
            });

            // Auto-remove validation errors on input
            $('.form-control').on('input', function() {
                if ($(this).hasClass('is-invalid')) {
                    $(this).removeClass('is-invalid');
                    $(this).siblings('.invalid-feedback').fadeOut();
                }
            });

            // Enhanced button click effect
            $('.btn').on('click', function() {
                $(this).addClass('clicked');
                setTimeout(() => {
                    $(this).removeClass('clicked');
                }, 200);
            });

            // Floating label effect
            $('.form-control').each(function() {
                if ($(this).val()) {
                    $(this).closest('.form-group').addClass('focused');
                }
            });

            // Add ripple effect to buttons
            $('.btn').on('click', function(e) {
                const button = $(this);
                const ripple = $('<span class="ripple"></span>');
                
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.css({
                    position: 'absolute',
                    width: size + 'px',
                    height: size + 'px',
                    left: x + 'px',
                    top: y + 'px',
                    background: 'rgba(255, 255, 255, 0.3)',
                    borderRadius: '50%',
                    transform: 'scale(0)',
                    animation: 'ripple 0.6s linear',
                    pointerEvents: 'none'
                });
                
                button.append(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });

            // Add CSS for ripple animation
            $('<style>')
                .prop('type', 'text/css')
                .html(`
                    @keyframes ripple {
                        to {
                            transform: scale(4);
                            opacity: 0;
                        }
                    }
                    .btn {
                        position: relative;
                        overflow: hidden;
                    }
                `)
                .appendTo('head');

            // Smooth transitions for all elements
            $('.card, .form-control, .btn').addClass('transition-all');
            
            // Add focus indicators for accessibility
            $('.form-control, .btn').on('focus', function() {
                $(this).addClass('focus-visible');
            }).on('blur', function() {
                $(this).removeClass('focus-visible');
            });
        });
    </script>
@stop