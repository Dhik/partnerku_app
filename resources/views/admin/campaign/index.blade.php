@extends('adminlte::page')

@section('title', trans('labels.campaign'))

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/campaign-enhanced.css') }}">
    <style>
        /* Custom styling for Client2 role - white text */
        .client2-expense-card .inner h4,
        .client2-expense-card .inner p {
            color: white !important;
        }
    </style>
@stop

@section('content_header')
    <h1>{{ trans('labels.campaign') }}</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                {{-- Filter Controls --}}
                <div class="filter-controls">
                    <div class="row">
                        <div class="col-auto">
                            @hasanyrole(\App\Domain\User\Enums\RoleEnum::SuperAdmin . '|' . \App\Domain\User\Enums\RoleEnum::Client1 . '|' . \App\Domain\User\Enums\RoleEnum::TimInternal . '|' . \App\Domain\User\Enums\RoleEnum::TimAds)
                                <a href="{{ route('campaign.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> {{ trans('labels.add') }}
                                </a>
                                
                                <button id="bulkRefreshBtn" type="button" class="btn btn-success">
                                    <i class="fas fa-sync-alt"></i> {{ trans('labels.bulk_refresh') }}
                                </button>
                            @endhasanyrole
                        </div>
                        <div class="col-auto">
                            <input type="month" class="form-control" id="filterMonth" autocomplete="off">
                        </div>
                        <div class="col-auto">
                            <input type="text" class="form-control" id="filterDates" placeholder="Select Date Range" autocomplete="off">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-secondary" id="resetFilterBtn">{{ trans('buttons.reset_filter') }}</button>
                        </div>
                    </div>
                </div>

                {{-- KPI Cards --}}
                <div class="row mb-4">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info @hasrole(\App\Domain\User\Enums\RoleEnum::Client2) client2-expense-card @endhasrole">
                            <div class="inner">
                                <h4 id="kpi_total_expense" style="color: white !important;">Loading...</h4>
                                <p>Total Expense</p>
                            </div>
                            <div class="icon"><i class="fas fa-credit-card"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-purple">
                            <div class="inner">
                                <h4 id="kpi_total_content">Loading...</h4>
                                <p>Total Content</p>
                            </div>
                            <div class="icon"><i class="fas fa-video"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h4 id="kpi_cpm">Loading...</h4>
                                <p>CPM</p>
                            </div>
                            <div class="icon"><i class="fas fa-chart-bar"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h4 id="views">Loading...</h4>
                                <p>Total Views</p>
                            </div>
                            <div class="icon"><i class="fas fa-eye"></i></div>
                        </div>
                    </div>
                </div>

                {{-- DataTable --}}
                <div class="table-responsive">
                    <table id="campaignTable" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                                <th>{{ trans('labels.created_at') }}</th>
                                <th>{{ trans('labels.title') }}</th>
                                <th>{{ trans('labels.total_spend') }}</th>
                                <th>CPM</th>
                                <th>{{ trans('labels.views') }}</th>
                                <th>ER</th>
                                <th>{{ trans('labels.period') }}</th>
                                <th>{{ trans('labels.created_by') }}</th>
                                <th>{{ trans('labels.action') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pass user role to JavaScript --}}
<script>
    window.userRole = @json(auth()->user()->getRoleNames()->first());
    window.isClient2 = @json(auth()->user()->hasRole(\App\Domain\User\Enums\RoleEnum::Client2));
</script>
@stop

@section('adminlte_js')
    <script src="{{ asset('js/campaign-enhanced.js') }}"></script>
    <script>
        // Set global URLs for JavaScript
        window.campaignDestroyUrl = "{{ route('campaign.destroy', ':id') }}";
        
        $(document).ready(function() {
            // Initialize DataTable
            window.campaignTable = $('#campaignTable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('campaign.get') }}",
                    data: function (d) {
                        if ($('#filterDates').val()) d.filterDates = $('#filterDates').val();
                        if ($('#filterMonth').val()) d.filterMonth = $('#filterMonth').val();
                    }
                },
                columns: [
                    {data: 'created_at', name: 'created_at', visible: false},
                    {
                        data: 'title',
                        name: 'title',
                        render: function(data, type, row) {
                            return '<a href="/admin/campaign/' + row.id + '/show">' + data + '</a>';
                        }
                    },
                    {data: 'total_expense', name: 'total_expense', className: "text-right", searchable: false},
                    {data: 'cpm', name: 'cpm', className: "text-right", searchable: false},
                    {data: 'view', name: 'view', className: "text-right", searchable: false},
                    {
                        data: 'engagement_rate',
                        name: 'engagement_rate',
                        className: "text-center",
                        render: function(data) { return data + '%'; },
                        searchable: false
                    },
                    {data: 'period', name: 'period', className: "text-center", orderable: false, searchable: false},
                    {data: 'created_by_name', name: 'created_by_name', visible: false},
                    {data: 'actions', className: "text-center", orderable: false, searchable: false}
                ],
                order: [[0, 'desc']],
                drawCallback: function() {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            // Filter handlers
            $('#resetFilterBtn').click(function() {
                $('#filterMonth, #filterDates').val('');
                window.campaignTable.ajax.reload();
                loadCampaignSummary();
            });

            $('#filterMonth, #filterDates').change(function() {
                window.campaignTable.ajax.reload();
                loadCampaignSummary($('#filterMonth').val());
            });

            // Bulk refresh
            $('#bulkRefreshBtn').click(function() {
                const button = $(this);
                CampaignUtils.setButtonLoading(button, true);
                
                $.ajax({
                    url: "{{ route('campaign.bulkRefresh') }}",
                    method: 'GET',
                    success: function(response) {
                        window.campaignTable.ajax.reload();
                        loadCampaignSummary();
                        CampaignUtils.showToast('Bulk refresh completed successfully');
                    },
                    error: function() {
                        CampaignUtils.showToast('Bulk refresh failed', 'error');
                    },
                    complete: function() {
                        CampaignUtils.setButtonLoading(button, false);
                    }
                });
            });

            // Date range picker
            $('#filterDates').daterangepicker({
                autoUpdateInput: false,
                locale: { format: 'DD/MM/YYYY', cancelLabel: 'Clear' }
            });

            $('#filterDates').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                window.campaignTable.ajax.reload();
            });

            $('#filterDates').on('cancel.daterangepicker', function() {
                $(this).val('');
                window.campaignTable.ajax.reload();
            });

            // Load campaign summary with Client2 logic
            function loadCampaignSummary(month = '') {
                $.ajax({
                    url: "{{ route('campaign.summary') }}",
                    method: 'GET',
                    data: { filterMonth: month },
                    success: function(response) {
                        // Process total_expense for Client2 role
                        let displayExpense = response.total_expense;
                        if (window.isClient2) {
                            // Parse the expense value (remove currency formatting if needed)
                            let numericValue = parseFloat(response.total_expense.replace(/[^\d.-]/g, ''));
                            if (!isNaN(numericValue)) {
                                // Calculate 130% of the actual value
                                let adjustedValue = numericValue * 1.35;
                                
                                // Format back to currency if original was formatted
                                if (response.total_expense.includes('Rp') || response.total_expense.includes('$')) {
                                    // Maintain original currency format
                                    if (response.total_expense.includes('Rp')) {
                                        displayExpense = 'Rp ' + new Intl.NumberFormat('id-ID').format(adjustedValue);
                                    } else {
                                        displayExpense = '$' + new Intl.NumberFormat('en-US').format(adjustedValue);
                                    }
                                } else {
                                    displayExpense = new Intl.NumberFormat().format(adjustedValue);
                                }
                            }
                        }
                        
                        $('#kpi_total_expense').text(displayExpense);
                        $('#kpi_cpm').text(response.cpm);
                        $('#views').text(response.views);
                        $('#kpi_total_content').text(response.total_content);
                    },
                    error: function() {
                        console.error('Error fetching campaign summary');
                        // Show loading error message
                        $('#kpi_total_expense').text('Error loading data');
                        $('#kpi_cpm').text('Error loading data');
                        $('#views').text('Error loading data');
                        $('#kpi_total_content').text('Error loading data');
                    }
                });
            }

            // Initial load
            loadCampaignSummary();
        });
    </script>
@stop