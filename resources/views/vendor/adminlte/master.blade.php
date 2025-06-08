<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- Base Meta Tags --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('img/partner-logo.png') }}" type="image/x-icon">

    {{-- Custom Meta Tags --}}
    @yield('meta_tags')

    {{-- Title --}}
    <title>
        @yield('title_prefix', config('adminlte.title_prefix', 'Partner | '))
        @yield('title', config('adminlte.title', 'Dashboard'))
        @yield('title_postfix', config('adminlte.title_postfix', ''))
    </title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Custom stylesheets (pre AdminLTE) --}}
    @yield('adminlte_css_pre')

    {{-- Base Stylesheets --}}
    @if(!config('adminlte.enabled_laravel_mix'))
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

        {{-- Partner Theme Stylesheets --}}
        <link rel="stylesheet" href="{{ asset('css/partner-theme.css') }}">
        <link rel="stylesheet" href="{{ asset('css/partner-mobile.css') }}">

        {{-- Additional Plugin Stylesheets --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
        <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core/main.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid/main.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid/main.css" rel="stylesheet" />
        
        @if(config('adminlte.google_fonts.allowed', true))
            <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        @endif
    @else
        <link rel="stylesheet" href="{{ mix(config('adminlte.laravel_mix_css_path', 'css/app.css')) }}">
    @endif

    {{-- Extra Configured Plugins Stylesheets --}}
    @include('adminlte::plugins', ['type' => 'css'])

    {{-- Livewire Styles --}}
    @if(config('adminlte.livewire'))
        @if(intval(app()->version()) >= 7)
            @livewireStyles
        @else
            <livewire:styles />
        @endif
    @endif

    {{-- Custom Stylesheets (post AdminLTE) --}}
    @yield('adminlte_css')

    {{-- Progressive Web App Meta --}}
    <meta name="theme-color" content="#1e3a8a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Partner">
    <link rel="apple-touch-icon" href="{{ asset('img/partner-logo-192.png') }}">

    {{-- Favicon Support --}}
    @if(config('adminlte.use_ico_only'))
        <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
    @elseif(config('adminlte.use_full_favicon'))
        <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicons/apple-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicons/apple-icon-60x60.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicons/apple-icon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicons/apple-icon-76x76.png') }}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicons/apple-icon-114x114.png') }}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicons/apple-icon-120x120.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicons/apple-icon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicons/apple-icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicons/apple-icon-180x180.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicons/favicon-16x16.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicons/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicons/favicon-96x96.png') }}">
        <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('favicons/android-icon-192x192.png') }}">
        <meta name="msapplication-TileColor" content="#1e3a8a">
        <meta name="msapplication-TileImage" content="{{ asset('favicon/ms-icon-144x144.png') }}">
    @endif

    {{-- Critical CSS for faster loading --}}
    <style>
        /* Critical CSS for initial paint */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Preloader styles */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .preloader.fade-out {
            opacity: 0;
            visibility: hidden;
        }

        .preloader-logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
            margin-bottom: 1rem;
        }

        .preloader-text {
            color: white;
            font-weight: 600;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .preloader-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem;
        }

        .preloader-spinner {
            margin-top: 2rem;
            width: 40px;
            height: 40px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Partner theme colors */
        :root {
            --primary-navy: #1e3a8a;
            --secondary-navy: #1e40af;
            --light-navy: #3b82f6;
            --accent-blue: #60a5fa;
            --soft-gray: #f8fafc;
            --medium-gray: #e2e8f0;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        /* Treeview Fix - Critical CSS for dropdown functionality */
        .nav-sidebar .nav-treeview {
            display: none;
        }
        
        .nav-sidebar .nav-item.menu-open > .nav-treeview {
            display: block !important;
        }
        
        .nav-sidebar .nav-item.has-treeview > .nav-link .right {
            transition: transform 0.3s ease;
        }
        
        .nav-sidebar .nav-item.menu-open > .nav-link .right {
            transform: rotate(-90deg);
        }

        /* Base layout improvements */
        .wrapper {
            background-color: var(--soft-gray);
        }

        .main-header.navbar {
            background: linear-gradient(135deg, var(--primary-navy) 0%, var(--secondary-navy) 100%) !important;
            border-bottom: none !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1) !important;
        }

        .main-sidebar {
            background: white !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1) !important;
        }

        .content-wrapper {
            background-color: var(--soft-gray) !important;
        }

        /* Loading animation for better UX */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid var(--primary-navy);
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }
    </style>
</head>

<body class="@yield('classes_body')" @yield('body_data')>

    {{-- Preloader --}}
    @if(config('adminlte.preloader.enabled', true))
        <div class="preloader" id="preloader">
            <div class="preloader-logo">
                <i class="fas fa-handshake text-navy" style="font-size: 2rem;"></i>
            </div>
            <div class="preloader-text">Partner</div>
            <div class="preloader-subtitle">Loading your dashboard...</div>
            <div class="preloader-spinner"></div>
        </div>
    @endif

    {{-- Body Content --}}
    @yield('body')

    {{-- External Scripts --}}
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>

    {{-- Base Scripts --}}
    @if(!config('adminlte.enabled_laravel_mix'))
        <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
        <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    @else
        <script src="{{ mix(config('adminlte.laravel_mix_js_path', 'js/app.js')) }}"></script>
    @endif

    {{-- Extra Configured Plugins Scripts --}}
    @include('adminlte::plugins', ['type' => 'js'])

    {{-- Livewire Script --}}
    @if(config('adminlte.livewire'))
        @if(intval(app()->version()) >= 7)
            @livewireScripts
        @else
            <livewire:scripts />
        @endif
    @endif

    {{-- Partner Theme JavaScript with Treeview Fix --}}
    <script>
        // CRITICAL: AdminLTE Treeview Functionality Fix
        $(document).ready(function() {
            // Initialize AdminLTE Treeview
            $('[data-widget="treeview"]').Treeview();
            
            // Manual treeview toggle for Partner theme
            $('.nav-sidebar .has-treeview > .nav-link').on('click', function(e) {
                e.preventDefault();
                
                var $this = $(this);
                var $parent = $this.parent();
                var $treeview = $this.next('.nav-treeview');
                
                // Check if accordion mode is enabled
                var accordion = $this.closest('[data-widget="treeview"]').data('accordion');
                
                if (accordion !== false) {
                    // Close other open menus
                    $this.closest('.nav-sidebar').find('.nav-item.menu-open').not($parent).each(function() {
                        $(this).removeClass('menu-open');
                        $(this).find('.nav-treeview').slideUp(300);
                    });
                }
                
                // Toggle current menu
                if ($parent.hasClass('menu-open')) {
                    $parent.removeClass('menu-open');
                    $treeview.slideUp(300);
                } else {
                    $parent.addClass('menu-open');
                    $treeview.slideDown(300);
                }
                
                return false;
            });
            
            // Set initial state for menu items with active children
            $('.nav-sidebar .nav-treeview .nav-link.active').each(function() {
                $(this).closest('.has-treeview').addClass('menu-open');
                $(this).closest('.nav-treeview').show();
            });
        });

        // Hide preloader when page is loaded
        window.addEventListener('load', function() {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                setTimeout(() => {
                    preloader.classList.add('fade-out');
                    setTimeout(() => {
                        preloader.style.display = 'none';
                    }, 300);
                }, 500);
            }
        });

        // Show notifications from session
        @if(session('success'))
            $(document).ready(function() {
                toastr.success('{{ session('success') }}');
            });
        @endif

        @if(session('error'))
            $(document).ready(function() {
                toastr.error('{{ session('error') }}');
            });
        @endif

        @if(session('warning'))
            $(document).ready(function() {
                toastr.warning('{{ session('warning') }}');
            });
        @endif

        @if(session('info'))
            $(document).ready(function() {
                toastr.info('{{ session('info') }}');
            });
        @endif

        // Configure toastr for better UX
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
        }

        // Enhanced input masking
        $(document).ready(function() {
            // Input mask configurations
            if (typeof Inputmask !== 'undefined') {
                Inputmask.extendAliases({
                    "partnerNumeric": {
                        alias: 'numeric',
                        groupSeparator: '.',
                        radixPoint: ',',
                        autoGroup: true,
                        digits: 2,
                        digitsOptional: true,
                        placeholder: '0',
                        autoUnmask: true,
                        removeMaskOnSubmit: true,
                        onUnMask: function (maskedValue, unmaskedValue, opts) {
                            if (unmaskedValue === "" && opts.nullable === true) {
                                return unmaskedValue;
                            }
                            let processValue = maskedValue.replace(opts.prefix, "");
                            processValue = processValue.replace(opts.suffix, "");
                            processValue = processValue.replace(new RegExp(escapeRegExp(opts.groupSeparator), "g"), "");
                            if (opts.radixPoint !== "" && processValue.indexOf(opts.radixPoint) !== -1)
                                processValue = processValue.replace(new RegExp(escapeRegExp(opts.radixPoint), "g"), ".");
                            return processValue;
                        },
                    },
                    "partnerNumericNoDecimals": {
                        alias: 'numeric',
                        groupSeparator: '.',
                        autoGroup: true,
                        placeholder: '0',
                        autoUnmask: true,
                        removeMaskOnSubmit: true,
                        digits: 0,
                    }
                });

                $('.money').inputmask("partnerNumericNoDecimals");
                $('.moneyDecimal').inputmask("partnerNumeric");

                function escapeRegExp(string) {
                    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                }
            }

            // Enhanced date pickers
            if (typeof moment !== 'undefined' && $.fn.daterangepicker) {
                $('.singleDate').daterangepicker({
                    autoApply: true,
                    singleDatePicker: true,
                    showDropdowns: true,
                    locale: {
                        format: 'DD/MM/YYYY'
                    }
                });

                const startDate = moment().startOf('month');
                const endDate = moment().endOf('month');

                $('.rangeDate').daterangepicker({
                    startDate: startDate,
                    endDate: endDate,
                    autoApply: true,
                    showDropdowns: true,
                    locale: {
                        format: 'DD/MM/YYYY'
                    },
                    maxSpan: {
                        days: 31
                    }
                });

                $('.rangeDateNoLimit').daterangepicker({
                    startDate: startDate,
                    endDate: endDate,
                    autoApply: true,
                    showDropdowns: true,
                    locale: {
                        format: 'DD/MM/YYYY'
                    }
                });
            }

            // Month/Year picker
            if ($.fn.datepicker) {
                $('.monthYear').datepicker({
                    format: "mm/yyyy",
                    viewMode: "months",
                    minViewMode: "months",
                }).on('changeDate', function (e) {
                    let $this = $(this);
                    setTimeout(function() {
                        $this.change();
                    }, 0);
                    $(this).datepicker('hide');
                });
            }
        });

        // Enhanced error handling functions
        function errorAjaxValidation(xhr, status, error, selector) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function(field, message) {
                    selector.removeClass('d-none');
                    selector.html('<span class="text-danger">' + message[0] + '</span>');
                });
            } else {
                console.error('Error:', error);
                if (typeof toastr !== 'undefined') {
                    toastr.error('An unexpected error occurred. Please try again.');
                }
            }
        }

        function errorImportAjaxValidation(xhr, status, error, selector) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                let output = '';
                $.each(errors, function(index, errorArray) {
                    selector.removeClass('d-none');
                    $.each(errorArray, function(innerIndex, errorMessage) {
                        output += '<span class="text-danger">' + errorMessage + '</span><br>';
                    });
                });
                selector.html(output);
            } else {
                console.error('Error:', error);
            }
        }

        // Enhanced delete function with better UX
        function deleteAjax(route, id, table) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to recover this data!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1e3a8a',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'DELETE',
                        url: route.replace(':id', id),
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success: function (response, textStatus, jqXHR) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'The record has been deleted successfully.',
                                icon: 'success',
                                confirmButtonColor: '#10b981',
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                }
                            }).then((result) => {
                                if (table && table.ajax) {
                                    table.ajax.reload();
                                } else {
                                    location.reload();
                                }
                            });
                        },
                        error: function (xhr, status, error) {
                            let message = 'Failed to delete the record.';
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                title: 'Error!',
                                text: message,
                                icon: 'error',
                                confirmButtonColor: '#ef4444',
                                customClass: {
                                    confirmButton: 'btn btn-danger'
                                }
                            });
                        }
                    });
                }
            });
        }

        // Predefined colors for charts
        const predefinedColors = [
            'rgba(30, 58, 138, 0.8)',   // Primary navy
            'rgba(16, 185, 129, 0.8)',  // Success green
            'rgba(245, 158, 11, 0.8)',  // Warning yellow
            'rgba(239, 68, 68, 0.8)',   // Danger red
            'rgba(59, 130, 246, 0.8)',  // Light blue
            'rgba(139, 69, 19, 0.8)',   // Brown
            'rgba(75, 0, 130, 0.8)',    // Indigo
            'rgba(255, 20, 147, 0.8)',  // Deep pink
            'rgba(32, 178, 170, 0.8)',  // Light sea green
            'rgba(255, 69, 0, 0.8)',    // Orange red
        ];

        function generatePredefinedColors(numColors) {
            const colors = [];
            for (let i = 0; i < numColors; i++) {
                colors.push(predefinedColors[i % predefinedColors.length]);
            }
            return colors;
        }

        // Enhanced form handling
        $(document).ready(function() {
            // Add loading state to all forms
            $('form').on('submit', function() {
                const submitBtn = $(this).find('button[type="submit"]');
                if (!submitBtn.prop('disabled')) {
                    submitBtn.prop('disabled', true).addClass('loading');
                    const originalText = submitBtn.html();
                    submitBtn.data('original-text', originalText);
                    submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Please wait...');
                    
                    // Re-enable after 10 seconds as fallback
                    setTimeout(() => {
                        submitBtn.prop('disabled', false).removeClass('loading');
                        submitBtn.html(originalText);
                    }, 10000);
                }
            });

            // Enhanced validation feedback
            $('.form-control').on('input change', function() {
                if ($(this).hasClass('is-invalid')) {
                    $(this).removeClass('is-invalid');
                    $(this).siblings('.invalid-feedback').fadeOut();
                }
            });

            // Auto-hide alerts
            $('.alert').delay(5000).fadeOut(500);
        });

        // Service Worker registration for PWA
        if ('serviceWorker' in navigator && window.location.protocol === 'https:') {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('ServiceWorker registration successful');
                })
                .catch(function(error) {
                    console.log('ServiceWorker registration failed: ', error);
                });
        }

        // Enhanced keyboard shortcuts
        $(document).on('keydown', function(e) {
            // Ctrl/Cmd + K for search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = $('.sidebar-search-input, #sidebar-search-input');
                if (searchInput.length) {
                    searchInput.focus();
                }
            }
        });

        // Network status monitoring
        window.addEventListener('online', function() {
            if (typeof toastr !== 'undefined') {
                toastr.success('Connection restored');
            }
        });

        window.addEventListener('offline', function() {
            if (typeof toastr !== 'undefined') {
                toastr.warning('You are now offline. Some features may be limited.');
            }
        });

        // Enhanced mobile touch interactions
        if ('ontouchstart' in window) {
            $(document).on('touchstart', '.btn, .nav-link, .dropdown-item', function() {
                $(this).addClass('touch-active');
            }).on('touchend touchcancel', '.btn, .nav-link, .dropdown-item', function() {
                $(this).removeClass('touch-active');
            });

            // Add touch-active styles
            $('<style>').prop('type', 'text/css').html(`
                .touch-active {
                    transform: scale(0.98) !important;
                    opacity: 0.8 !important;
                }
            `).appendTo('head');
        }
    </script>

    {{-- Custom Scripts --}}
    @yield('adminlte_js')

    {{-- Ionicons for enhanced UI --}}
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>