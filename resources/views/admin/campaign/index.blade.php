@extends('adminlte::page')

@section('title', trans('labels.campaign'))

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/campaign-enhanced.css') }}">
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
                            @can(\App\Domain\User\Enums\PermissionEnum::CreateCampaign)
                                <a href="{{ route('campaign.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> {{ trans('labels.add') }}
                                </a>
                            @endcan
                            
                            @can(\App\Domain\User\Enums\PermissionEnum::UpdateCampaign)
                                <button id="bulkRefreshBtn" type="button" class="btn btn-success">
                                    <i class="fas fa-sync-alt"></i> {{ trans('labels.bulk_refresh') }}
                                </button>
                            @endcan
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
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h4 id="kpi_total_expense">Loading...</h4>
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

            // Load campaign summary
            function loadCampaignSummary(month = '') {
                $.ajax({
                    url: "{{ route('campaign.summary') }}",
                    method: 'GET',
                    data: { filterMonth: month },
                    success: function(response) {
                        $('#kpi_total_expense').text(response.total_expense);
                        $('#kpi_cpm').text(response.cpm);
                        $('#views').text(response.views);
                        $('#kpi_total_content').text(response.total_content);
                    },
                    error: function() {
                        console.error('Error fetching campaign summary');
                    }
                });
            }

            // Initial load
            loadCampaignSummary();
        });
    </script>
@stop