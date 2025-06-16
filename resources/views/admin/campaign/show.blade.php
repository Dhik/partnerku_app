@extends('adminlte::page')

@section('title', trans('labels.campaign'))

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/campaign-enhanced.css') }}">
@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="mb-0">{{ $campaign->title }} : {{ $campaign->start_date }} - {{ $campaign->end_date }}</h1>
    <div>
        @can('updateCampaign', $campaign)
            <a href="{{ route('campaign.edit', $campaign->id) }}" class="btn btn-outline-success">
                {{ trans('buttons.edit') }}
            </a>
        @endcan
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    {{-- Filter Controls --}}
                    <div class="filter-controls">
                        <div class="row">
                            <div class="col-auto">
                                <input type="text" class="form-control" id="filterDates" placeholder="Select Date Range" autocomplete="off">
                            </div>
                            <div class="col-auto">
                                <select class="form-control" id="filterPlatform">
                                    <option value="">All Platforms</option>
                                    @foreach($platforms as $platform)
                                        <option value="{{ $platform['value'] }}">{{ $platform['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="filterFyp">
                                    <label class="form-check-label" for="filterFyp">FYP</label>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="filterPayment">
                                    <label class="form-check-label" for="filterPayment">Payment</label>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="filterDelivery">
                                    <label class="form-check-label" for="filterDelivery">Delivery</label>
                                </div>
                            </div>
                            <div class="col-auto">
                                <button id="resetFilterBtn" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>

                    {{-- KPI Cards --}}
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="small-box bg-white">
                                <div class="inner">
                                    <h4 id="totalExpense">0</h4>
                                    <p>Total Pengeluaran</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-comment-dollar text-gray"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-white">
                                <div class="inner">
                                    <h4 id="totalCPM">0</h4>
                                    <p>Cost Per Mile</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chart-bar text-gray"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-white">
                                <div class="inner">
                                    <h4 id="totalInfluencer">0</h4>
                                    <p>Total Influencer</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-bullhorn text-gray"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-white">
                                <div class="inner">
                                    <h4 id="totalContent">0</h4>
                                    <p>Total Konten</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-folder-open text-gray"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-white">
                                <div class="inner">
                                    <h4 id="totalAchievement">0</h4>
                                    <p>Pencapaian</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-trophy text-gray"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-white">
                                <div class="inner">
                                    <h4 id="totalViews">0</h4>
                                    <p>Video Views</p>
                                </div>
                                <div class="icon">
                                    <i class="far fa-eye text-gray"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-white">
                                <div class="inner">
                                    <h4 id="totalLikes">0</h4>
                                    <p>Likes</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-thumbs-up text-gray"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-white">
                                <div class="inner">
                                    <h4 id="totalComment">0</h4>
                                    <p>Comment</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-comment-dots text-gray"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-white">
                                <div class="inner">
                                    <h4 id="engagementRate">0</h4>
                                    <p>Engagement Rate</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chart-line text-gray"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Statistics Chart --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Statistics Chart</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="statisticChart" class="w-100" height="80"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="row mb-3">
                        <div class="col-auto">
                            @can('UpdateCampaign', $campaign)
                                <button class="btn btn-primary" data-toggle="modal" data-target="#contentModal">
                                    <i class="fas fa-plus"></i> Add Content
                                </button>
                            @endcan
                        </div>
                        <div class="col-auto">
                            <a class="btn btn-outline-primary" href="{{ route('campaignContent.export', $campaign->id) }}">
                                <i class="fas fa-file-download"></i> Export
                            </a>
                        </div>
                    </div>

                    {{-- Content Table --}}
                    <div class="table-responsive">
                        <table id="contentTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>{{ trans('labels.influencer') }}</th>
                                    <th>{{ trans('labels.platform') }}</th>
                                    <th>{{ trans('labels.product') }}</th>
                                    <th>{{ trans('labels.task') }}</th>
                                    <th>{{ trans('labels.like') }}</th>
                                    <th>{{ trans('labels.comment') }}</th>
                                    <th>{{ trans('labels.view') }}</th>
                                    <th>CPM</th>
                                    <th>ER</th>
                                    <th>Followers</th>
                                    <th>Tiering</th>
                                    <th>Status</th>
                                    <th>{{ trans('labels.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data populated via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="card-footer">
                    {{ trans('labels.created_by') }} {{ $campaign->createdBy->name ?? '' }}
                    @can('deleteCampaign', $campaign)
                        <a href="#" class="delete-campaign float-right text-danger">
                            {{ trans('buttons.delete') }}
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Include All Modals --}}
@include('admin.campaign.modals')
@endsection

@section('adminlte_js')
    <script src="{{ asset('js/campaign-enhanced.js') }}"></script>
    
    {{-- Include Chart.js for statistics chart --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Set global URLs for JavaScript
        window.campaignContentStoreUrl = "{{ route('campaignContent.store', ['campaignId' => $campaign->id]) }}";
        window.campaignContentUpdateUrl = "{{ route('campaignContent.update', ['campaignContent' => ':campaignContentId']) }}";
        window.campaignContentDestroyUrl = "{{ route('campaignContent.destroy', ['campaignContent' => ':campaignContentId']) }}";
        
        // Chart instance
        let campaignContentChart;
        
        $(document).ready(function() {
            const campaignId = '{{ $campaign->id }}';
            
            // Initialize content table
            window.contentTable = $('#contentTable').DataTable({
                responsive: true,
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('campaignContent.getJson', ['campaignId' => $campaign->id]) }}",
                    dataSrc: 'data',
                    data: function(d) {
                        d.filterPlatform = $('#filterPlatform').val();
                        d.filterFyp = $('#filterFyp').prop('checked');
                        d.filterPayment = $('#filterPayment').prop('checked');
                        d.filterDelivery = $('#filterDelivery').prop('checked');
                        d.filterDates = $('#filterDates').val();
                    }
                },
                columns: [
                    {data: 'username'},
                    {data: 'channel', orderable: false},
                    {data: 'product', orderable: false},
                    {data: 'task', orderable: false},
                    {data: 'like', className: "text-right"},
                    {data: 'comment', className: "text-right"},
                    {data: 'view', className: "text-right"},
                    {data: 'cpm', className: "text-right"},
                    {data: 'engagement_rate', className: "text-right"},
                    {data: 'kol_followers', className: "text-right"},
                    {data: 'tiering', className: "text-center", orderable: false},
                    {data: 'additional_info', orderable: false, searchable: false},
                    {data: 'actions', orderable: false, searchable: false}
                ],
                order: [[6, 'desc']],
                drawCallback: function() {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            // Filter change handlers
            $('#filterPlatform, #filterFyp, #filterPayment, #filterDelivery, #filterDates').on('change', function() {
                window.contentTable.ajax.reload();
                updateCard();
                initChart();
            });

            // Reset filters
            $('#resetFilterBtn').on('click', function() {
                $('#filterPlatform').val('');
                $('#filterFyp, #filterPayment, #filterDelivery').prop('checked', false);
                $('#filterDates').val('');
                window.contentTable.ajax.reload();
                updateCard();
                initChart();
            });

            // Date range picker
            $('#filterDates').daterangepicker({
                autoUpdateInput: false,
                locale: { format: 'DD/MM/YYYY', cancelLabel: 'Clear' }
            });

            $('#filterDates').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                window.contentTable.ajax.reload();
                updateCard();
                initChart();
            });

            $('#filterDates').on('cancel.daterangepicker', function() {
                $(this).val('');
                window.contentTable.ajax.reload();
                updateCard();
                initChart();
            });

            // Delete campaign handler
            $('.delete-campaign').click(function(e) {
                e.preventDefault();
                
                CampaignUtils.confirmDelete('Delete Campaign', 'This campaign and all its content will be deleted.')
                    .then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route('campaign.destroy', $campaign->id) }}',
                                type: 'DELETE',
                                success: function() {
                                    window.location.href = "{{ route('campaign.index') }}";
                                },
                                error: function(xhr) {
                                    const message = CampaignUtils.handleError(xhr, 'Error deleting campaign');
                                    CampaignUtils.showToast(message, 'error');
                                }
                            });
                        }
                    });
            });

            // Update KPI cards
            function updateCard() {
                $.ajax({
                    url: "{{ route('statistic.card', ['campaignId' => $campaign->id]) }}" + '?filterDates=' + $('#filterDates').val(),
                    method: 'GET',
                    success: function(response) {
                        $('#totalExpense').text(response.total_expense);
                        $('#totalCPM').text(response.cpm);
                        $('#totalInfluencer').text(response.total_influencer);
                        $('#totalContent').text(response.total_content);
                        $('#totalAchievement').text(response.achievement);
                        $('#totalViews').text(response.view);
                        $('#totalLikes').text(response.like);
                        $('#totalComment').text(response.comment);
                        $('#engagementRate').text(response.engagement_rate);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching card data', error);
                    }
                });
            }

            // Initialize chart
            function initChart() {
                $.ajax({
                    url: "{{ route('statistic.chart', ['campaignId' => $campaign->id]) }}" + '?filterDates=' + $('#filterDates').val(),
                    type: 'GET',
                    success: function (response) {
                        renderChart(response);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching chart data', xhr.responseText);
                    }
                });
            }

            // Render chart
            function renderChart(chartData) {
                // Clear existing chart if it exists
                if (campaignContentChart) {
                    campaignContentChart.destroy();
                }

                const ctx = document.getElementById('statisticChart').getContext('2d');
                campaignContentChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.map(data => data.date),
                        datasets: [{
                            label: 'Views',
                            data: chartData.map(data => data.total_view),
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            fill: false,
                            tension: 0.1
                        }, {
                            label: 'Likes',
                            data: chartData.map(data => data.total_like),
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            fill: false,
                            tension: 0.1
                        }, {
                            label: 'Comments',
                            data: chartData.map(data => data.total_comment),
                            backgroundColor: 'rgba(255, 206, 86, 0.2)',
                            borderColor: 'rgba(255, 206, 86, 1)',
                            fill: false,
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Campaign Statistics Over Time'
                            },
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Date'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Count'
                                },
                                beginAtZero: true
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
            }

            // Initial load
            updateCard();
            initChart();
        });
    </script>
@stop