{{-- Enhanced Plugin Management for Partner Theme --}}

@foreach(config('adminlte.plugins') as $pluginName => $plugin)

    {{-- Check whether the plugin is active --}}
    @php
        $plugSection = View::getSection('plugins.' . ($plugin['name'] ?? $pluginName));
        $isPlugActive = $plugin['active']
            ? ! isset($plugSection) || $plugSection
            : ! empty($plugSection);
    @endphp

    {{-- When the plugin is active, include its files --}}
    @if($isPlugActive)
        @foreach($plugin['files'] as $file)

            {{-- Setup the file location --}}
            @php
                if (! empty($file['asset'])) {
                    $file['location'] = asset($file['location']);
                }
                
                // Add cache busting for better performance
                if (config('app.env') === 'production' && empty($file['no_cache_bust'])) {
                    $separator = strpos($file['location'], '?') !== false ? '&' : '?';
                    $file['location'] .= $separator . 'v=' . config('app.version', '1.0.0');
                }
            @endphp

            {{-- Check the requested file type and include CSS --}}
            @if($file['type'] == $type && $type == 'css')
                <link rel="stylesheet" href="{{ $file['location'] }}"
                      @if(!empty($file['media'])) media="{{ $file['media'] }}" @endif
                      @if(!empty($file['crossorigin'])) crossorigin="{{ $file['crossorigin'] }}" @endif
                      @if(!empty($file['integrity'])) integrity="{{ $file['integrity'] }}" @endif>
                
                {{-- Partner theme CSS overrides for specific plugins --}}
                @if($pluginName === 'Select2')
                    <style>
                        /* Partner theme overrides for Select2 */
                        .select2-container .select2-selection--single {
                            border: 2px solid #e2e8f0 !important;
                            border-radius: 0.75rem !important;
                            height: auto !important;
                            padding: 0.25rem 0 !important;
                        }
                        
                        .select2-container .select2-selection--single .select2-selection__rendered {
                            padding: 0.5rem 1rem !important;
                            line-height: 1.5 !important;
                        }
                        
                        .select2-container--default .select2-selection--single:focus {
                            border-color: #1e3a8a !important;
                            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1) !important;
                        }
                        
                        .select2-dropdown {
                            border: 1px solid #e2e8f0 !important;
                            border-radius: 0.75rem !important;
                            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
                        }
                        
                        .select2-container--default .select2-results__option--highlighted[aria-selected] {
                            background-color: #1e3a8a !important;
                        }
                    </style>
                @endif

                @if($pluginName === 'Datatables')
                    <style>
                        /* Partner theme overrides for DataTables */
                        .dataTables_wrapper .dataTables_length select,
                        .dataTables_wrapper .dataTables_filter input {
                            border: 2px solid #e2e8f0 !important;
                            border-radius: 0.5rem !important;
                            padding: 0.5rem !important;
                        }
                        
                        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
                        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
                            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%) !important;
                            border-color: #1e3a8a !important;
                            color: white !important;
                            border-radius: 0.5rem !important;
                        }
                        
                        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                            background: #f8fafc !important;
                            border-color: #1e3a8a !important;
                            color: #1e3a8a !important;
                            border-radius: 0.5rem !important;
                        }
                        
                        table.dataTable thead th {
                            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%) !important;
                            color: white !important;
                            border-bottom: none !important;
                        }
                        
                        table.dataTable tbody tr:hover {
                            background-color: #f8fafc !important;
                        }
                    </style>
                @endif

                @if($pluginName === 'Sweetalert2')
                    <style>
                        /* Partner theme overrides for SweetAlert2 */
                        .swal2-popup {
                            border-radius: 1rem !important;
                            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1) !important;
                        }
                        
                        .swal2-title {
                            color: #1e3a8a !important;
                            font-weight: 600 !important;
                        }
                        
                        .swal2-confirm {
                            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%) !important;
                            border-radius: 0.75rem !important;
                            font-weight: 600 !important;
                            padding: 0.75rem 1.5rem !important;
                        }
                        
                        .swal2-cancel {
                            background: #6b7280 !important;
                            border-radius: 0.75rem !important;
                            font-weight: 600 !important;
                            padding: 0.75rem 1.5rem !important;
                        }
                        
                        .swal2-success-circular-line-right,
                        .swal2-success-fix {
                            background: #10b981 !important;
                        }
                        
                        .swal2-error-x {
                            color: #ef4444 !important;
                        }
                    </style>
                @endif

                @if($pluginName === 'Toastr')
                    <style>
                        /* Partner theme overrides for Toastr */
                        .toast {
                            border-radius: 0.75rem !important;
                            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
                        }
                        
                        .toast-success {
                            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
                        }
                        
                        .toast-error {
                            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
                        }
                        
                        .toast-warning {
                            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
                        }
                        
                        .toast-info {
                            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%) !important;
                        }
                    </style>
                @endif

                @if($pluginName === 'DateRangePicker')
                    <style>
                        /* Partner theme overrides for DateRangePicker */
                        .daterangepicker {
                            border: 1px solid #e2e8f0 !important;
                            border-radius: 1rem !important;
                            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
                        }
                        
                        .daterangepicker .ranges li.active {
                            background-color: #1e3a8a !important;
                            color: white !important;
                            border-radius: 0.5rem !important;
                        }
                        
                        .daterangepicker td.active, 
                        .daterangepicker td.active:hover {
                            background-color: #1e3a8a !important;
                            border-color: #1e3a8a !important;
                            color: white !important;
                        }
                        
                        .daterangepicker .ranges li:hover {
                            background-color: #f8fafc !important;
                            color: #1e3a8a !important;
                            border-radius: 0.5rem !important;
                        }
                        
                        .daterangepicker .drp-buttons .btn {
                            border-radius: 0.5rem !important;
                            font-weight: 500 !important;
                        }
                        
                        .daterangepicker .drp-buttons .btn.btn-primary {
                            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%) !important;
                            border: none !important;
                        }
                    </style>
                @endif

            {{-- Check the requested file type and include JS --}}
            @elseif($file['type'] == $type && $type == 'js')
                <script src="{{ $file['location'] }}" 
                        @if(!empty($file['defer'])) defer @endif
                        @if(!empty($file['async'])) async @endif
                        @if(!empty($file['crossorigin'])) crossorigin="{{ $file['crossorigin'] }}" @endif
                        @if(!empty($file['integrity'])) integrity="{{ $file['integrity'] }}" @endif></script>

                {{-- Partner theme JavaScript enhancements for specific plugins --}}
                @if($pluginName === 'Datatables' && $type === 'js')
                    <script>
                        // Enhanced DataTables with Partner theme
                        $(document).ready(function() {
                            // Override default DataTables settings
                            if ($.fn.DataTable) {
                                $.extend(true, $.fn.dataTable.defaults, {
                                    "language": {
                                        "search": "Search partners:",
                                        "lengthMenu": "Show _MENU_ partners per page",
                                        "info": "Showing _START_ to _END_ of _TOTAL_ partners",
                                        "infoEmpty": "No partners found",
                                        "infoFiltered": "(filtered from _MAX_ total partners)",
                                        "paginate": {
                                            "first": "First",
                                            "last": "Last",
                                            "next": "Next",
                                            "previous": "Previous"
                                        }
                                    },
                                    "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                                           '<"row"<"col-sm-12"tr>>' +
                                           '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                                    "pageLength": 25,
                                    "responsive": true,
                                    "order": [[ 0, "asc" ]],
                                    "columnDefs": [
                                        { "orderable": false, "targets": "no-sort" }
                                    ]
                                });
                            }
                        });
                    </script>
                @endif

                @if($pluginName === 'Select2' && $type === 'js')
                    <script>
                        // Enhanced Select2 with Partner theme
                        $(document).ready(function() {
                            if ($.fn.select2) {
                                // Override default Select2 settings
                                $.fn.select2.defaults.set('theme', 'bootstrap4');
                                $.fn.select2.defaults.set('width', '100%');
                                
                                // Auto-initialize Select2 on elements with select2 class
                                $('.select2').select2({
                                    placeholder: 'Select an option...',
                                    allowClear: true,
                                    theme: 'bootstrap4'
                                });
                            }
                        });
                    </script>
                @endif

                @if($pluginName === 'Chartjs' && $type === 'js')
                    <script>
                        // Enhanced Chart.js with Partner theme
                        $(document).ready(function() {
                            if (typeof Chart !== 'undefined') {
                                // Set default Chart.js options for Partner theme
                                Chart.defaults.color = '#64748b';
                                Chart.defaults.borderColor = '#e2e8f0';
                                Chart.defaults.backgroundColor = 'rgba(30, 58, 138, 0.1)';
                                
                                // Partner color palette
                                Chart.defaults.elements.arc.backgroundColor = [
                                    '#1e3a8a', '#10b981', '#f59e0b', '#ef4444', 
                                    '#3b82f6', '#8b5cf6', '#06b6d4', '#84cc16'
                                ];
                                
                                // Default font family
                                Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif";
                                Chart.defaults.font.size = 12;
                                
                                // Responsive defaults
                                Chart.defaults.responsive = true;
                                Chart.defaults.maintainAspectRatio = false;
                                
                                // Grid styling
                                Chart.defaults.scales.linear.grid.color = '#f1f5f9';
                                Chart.defaults.scales.category.grid.display = false;
                            }
                        });
                    </script>
                @endif

                @if($pluginName === 'InputMask' && $type === 'js')
                    <script>
                        // Enhanced InputMask with Partner theme
                        $(document).ready(function() {
                            if (typeof Inputmask !== 'undefined') {
                                // Partner-specific input masks
                                Inputmask.extendAliases({
                                    "partnerPhone": {
                                        mask: "(999) 999-9999",
                                        placeholder: "(___) ___-____",
                                        showMaskOnHover: false,
                                        showMaskOnFocus: true
                                    },
                                    "partnerCurrency": {
                                        alias: 'numeric',
                                        groupSeparator: ',',
                                        radixPoint: '.',
                                        digits: 2,
                                        digitsOptional: false,
                                        prefix: '$ ',
                                        placeholder: '0',
                                        autoGroup: true,
                                        autoUnmask: true
                                    },
                                    "partnerPercentage": {
                                        alias: 'numeric',
                                        max: 100,
                                        suffix: '%',
                                        digits: 2,
                                        digitsOptional: true,
                                        placeholder: '0'
                                    }
                                });
                                
                                // Auto-apply masks based on data attributes
                                $('[data-mask="phone"]').inputmask("partnerPhone");
                                $('[data-mask="currency"]').inputmask("partnerCurrency");
                                $('[data-mask="percentage"]').inputmask("partnerPercentage");
                            }
                        });
                    </script>
                @endif

                @if($pluginName === 'Pace' && $type === 'js')
                    <script>
                        // Enhanced Pace with Partner theme
                        if (typeof Pace !== 'undefined') {
                            Pace.options = {
                                ajax: {
                                    trackMethods: ['GET', 'POST', 'DELETE', 'PUT']
                                },
                                document: true,
                                eventLag: true,
                                elements: {
                                    selectors: ['body']
                                }
                            };
                            
                            // Partner theme colors for Pace
                            Pace.on('start', function() {
                                $('.pace .pace-progress').css('background', '#1e3a8a');
                            });
                        }
                    </script>
                @endif

                @if($pluginName === 'Summernote' && $type === 'js')
                    <script>
                        // Enhanced Summernote with Partner theme
                        $(document).ready(function() {
                            if ($.fn.summernote) {
                                $('.summernote').summernote({
                                    height: 200,
                                    toolbar: [
                                        ['style', ['style']],
                                        ['font', ['bold', 'underline', 'clear']],
                                        ['fontname', ['fontname']],
                                        ['color', ['color']],
                                        ['para', ['ul', 'ol', 'paragraph']],
                                        ['table', ['table']],
                                        ['insert', ['link', 'picture', 'video']],
                                        ['view', ['fullscreen', 'codeview', 'help']]
                                    ],
                                    styleTags: [
                                        'p', 'blockquote', 'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
                                    ],
                                    fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica Neue', 'Helvetica', 'Impact', 'Lucida Grande', 'Tahoma', 'Times New Roman', 'Verdana', 'Inter'],
                                    fontNamesIgnoreCheck: ['Inter']
                                });
                            }
                        });
                    </script>
                @endif

            @endif

        @endforeach

        {{-- Plugin-specific initialization scripts --}}
        @if($type === 'js' && $isPlugActive)
            @if($pluginName === 'Datatables')
                <script>
                    // Global DataTable enhancement
                    $(document).ready(function() {
                        // Add Partner theme to all DataTables
                        $('table.dataTable').addClass('table-hover');
                        
                        // Enhanced search functionality
                        $('.dataTables_filter input').attr('placeholder', 'Search partners...');
                        
                        // Add loading animation
                        $.fn.dataTable.ext.errMode = 'none';
                        
                        // Handle DataTable errors gracefully
                        $(document).on('error.dt', function(e, settings, techNote, message) {
                            console.error('DataTable error:', message);
                            if (typeof toastr !== 'undefined') {
                                toastr.error('Error loading table data. Please refresh the page.');
                            }
                        });
                    });
                </script>
            @endif

            @if($pluginName === 'Select2')
                <script>
                    // Global Select2 enhancement
                    $(document).ready(function() {
                        // Handle AJAX errors in Select2
                        $(document).on('select2:open', function() {
                            $('.select2-search__field').attr('placeholder', 'Type to search...');
                        });
                        
                        // Add loading states
                        $(document).on('select2:opening', function() {
                            $(this).data('select2').$container.addClass('select2-loading');
                        });
                        
                        $(document).on('select2:close', function() {
                            $(this).data('select2').$container.removeClass('select2-loading');
                        });
                    });
                </script>
            @endif
        @endif

    @endif

@endforeach

{{-- Partner Theme Global Plugin Enhancements --}}
@if($type === 'js')
    <script>
        // Global plugin enhancements for Partner theme
        $(document).ready(function() {
            
            // Enhanced Bootstrap Modal with Partner theme
            $('.modal').on('show.bs.modal', function() {
                $(this).find('.modal-content').addClass('animate-fade-in');
            });
            
            // Enhanced Bootstrap Tooltip with Partner theme
            $('[data-toggle="tooltip"]').tooltip({
                container: 'body',
                boundary: 'window',
                placement: 'auto'
            });
            
            // Enhanced Bootstrap Popover with Partner theme
            $('[data-toggle="popover"]').popover({
                container: 'body',
                html: true,
                placement: 'auto'
            });
            
            // Global loading state for AJAX forms
            $(document).ajaxStart(function() {
                $('.btn[type="submit"]').prop('disabled', true).addClass('loading');
            }).ajaxStop(function() {
                $('.btn[type="submit"]').prop('disabled', false).removeClass('loading');
            });
            
            // Enhanced file upload with progress
            if (typeof window.File !== 'undefined') {
                $('input[type="file"]').on('change', function() {
                    const files = this.files;
                    if (files.length > 0) {
                        for (let i = 0; i < files.length; i++) {
                            const file = files[i];
                            if (file.size > 10 * 1024 * 1024) { // 10MB limit
                                if (typeof toastr !== 'undefined') {
                                    toastr.warning('File size should be less than 10MB: ' + file.name);
                                }
                                this.value = '';
                                return false;
                            }
                        }
                    }
                });
            }
            
            // Global form validation enhancement
            $('form').on('submit', function(e) {
                const requiredFields = $(this).find('[required]');
                let isValid = true;
                
                requiredFields.each(function() {
                    const field = $(this);
                    if (!field.val() || field.val().trim() === '') {
                        field.addClass('is-invalid');
                        isValid = false;
                    } else {
                        field.removeClass('is-invalid');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Please fill in all required fields.');
                    }
                    return false;
                }
            });
            
            // Real-time validation
            $('[required]').on('blur input', function() {
                const field = $(this);
                if (field.val() && field.val().trim() !== '') {
                    field.removeClass('is-invalid').addClass('is-valid');
                } else {
                    field.removeClass('is-valid').addClass('is-invalid');
                }
            });
            
            // Enhanced table interactions
            $('.table-responsive table').on('click', 'tr', function() {
                $(this).addClass('table-active').siblings().removeClass('table-active');
            });
            
            // Auto-resize textareas
            $('textarea[data-auto-resize]').each(function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            }).on('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
            
            // Enhanced copy to clipboard functionality
            $(document).on('click', '[data-copy]', function() {
                const text = $(this).data('copy');
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(text).then(function() {
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Copied to clipboard!');
                        }
                    });
                }
            });
            
            // Performance monitoring for plugins
            if (window.performance && window.performance.mark) {
                window.performance.mark('plugins-loaded');
            }
        });
    </script>
@endif

@if($type === 'css')
    <style>
        /* Global plugin enhancements for Partner theme */
        
        /* Enhanced animations */
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Loading states */
        .loading {
            position: relative;
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
            border: 2px solid #1e3a8a;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
            z-index: 10;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Enhanced form validation */
        .is-invalid {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
        }
        
        .is-valid {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
        }
        
        /* Enhanced table interactions */
        .table-active {
            background-color: rgba(30, 58, 138, 0.1) !important;
        }
        
        /* Enhanced tooltips */
        .tooltip .tooltip-inner {
            background: #1e3a8a;
            border-radius: 0.5rem;
            font-weight: 500;
        }
        
        .tooltip .arrow::before {
            border-top-color: #1e3a8a;
            border-bottom-color: #1e3a8a;
        }
        
        /* Enhanced popovers */
        .popover {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .popover-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            border-radius: 0.75rem 0.75rem 0 0;
            font-weight: 600;
        }
        
        /* Enhanced modals */
        .modal-content {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            border-radius: 1rem 1rem 0 0;
            border-bottom: none;
        }
        
        /* File upload enhancements */
        .custom-file-label::after {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            border-radius: 0 0.75rem 0.75rem 0;
        }
        
        /* Enhanced progress bars */
        .progress {
            height: 0.75rem;
            border-radius: 0.75rem;
            background-color: #e2e8f0;
        }
        
        .progress-bar {
            background: linear-gradient(90deg, #1e3a8a 0%, #3b82f6 100%);
            border-radius: 0.75rem;
        }
        
        /* Responsive enhancements */
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .modal-dialog {
                margin: 0.5rem;
            }
            
            .popover {
                max-width: calc(100vw - 2rem);
            }
        }
    </style>
@endif