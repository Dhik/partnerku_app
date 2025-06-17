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
                            <button id="refreshAllBtn" class="btn btn-success">
                                <i class="fas fa-sync-alt"></i> Refresh All Statistics
                            </button>
                        </div>
                        <div class="col-auto">
                            <a class="btn btn-outline-primary" href="{{ route('campaignContent.export', $campaign->id) }}">
                                <i class="fas fa-file-download"></i> Export
                            </a>
                        </div>
                    </div>

                    {{-- KPI Cards --}}
                    @include('admin.campaign.content.statisticCard')

                    {{-- Statistics Chart --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Campaign Statistics Chart</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="statisticChart" style="height: 400px;"></canvas>
                                </div>
                            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        const campaignId = '{{ $campaign->id }}';
        let campaignContentChart;
        
        $(document).ready(function() {
            
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

            // ===== REFRESH STATISTICS =====
            
            // Single content refresh
            $(document).on('click', '.btnRefresh', function(e) {
                e.preventDefault();
                const button = $(this);
                const rowData = TableManager.getRowData(window.contentTable, this);
                if (!rowData) return;

                const originalText = button.html();
                button.html('<i class="fas fa-spinner fa-spin"></i> Refreshing...').prop('disabled', true);

                $.ajax({
                    url: `{{ route('statistic.refresh', ['campaignContent' => ':id']) }}`.replace(':id', rowData.id),
                    method: 'GET',
                    success: function(response) {
                        window.contentTable.ajax.reload();
                        updateCard();
                        initChart();
                        CampaignUtils.showToast('Statistics refreshed successfully!');
                    },
                    error: function(xhr) {
                        const message = CampaignUtils.handleError(xhr, 'Refresh failed');
                        CampaignUtils.showToast(message, 'error');
                    },
                    complete: function() {
                        button.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Bulk refresh
            $('#refreshAllBtn').click(function() {
                const button = $(this);
                CampaignUtils.setButtonLoading(button, true);
                
                $.ajax({
                    url: `{{ route('statistic.bulkRefresh', ['campaign' => $campaign->id]) }}`,
                    method: 'GET',
                    success: function(response) {
                        window.contentTable.ajax.reload();
                        updateCard();
                        initChart();
                        
                        let message = response.message || 'Bulk refresh completed successfully!';
                        if (response.stats) {
                            message += ` (${response.stats.success_count} succeeded, ${response.stats.failed_count} failed)`;
                        }
                        CampaignUtils.showToast(message);
                    },
                    error: function(xhr) {
                        const message = CampaignUtils.handleError(xhr, 'Bulk refresh failed');
                        CampaignUtils.showToast(message, 'error');
                    },
                    complete: function() {
                        CampaignUtils.setButtonLoading(button, false);
                    }
                });
            });

            // ===== MANUAL STATISTICS =====
            
            $(document).on('click', '.btnStatistic', function(e) {
                e.preventDefault();
                const rowData = TableManager.getRowData(window.contentTable, this);
                if (!rowData) return;

                $('#statisticContentId').val(rowData.id);
                $('#view').val(rowData.view || '');
                $('#like').val(rowData.like || '');
                $('#comment').val(rowData.comment || '');
                
                ModalManager.show('#statisticModal');
            });

            $('#statisticForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const contentId = $('#statisticContentId').val();
                const submitBtn = form.find('button[type="submit"]');
                
                if (!contentId) return;
                
                CampaignUtils.setButtonLoading(submitBtn, true);
                
                $.ajax({
                    type: 'POST',
                    url: `{{ route('statistic.store', ['campaignContent' => ':id']) }}`.replace(':id', contentId),
                    data: form.serialize(),
                    success: function(response) {
                        window.contentTable.ajax.reload();
                        updateCard();
                        initChart();
                        ModalManager.hide('#statisticModal');
                        CampaignUtils.showToast('Statistics saved successfully!');
                    },
                    error: function(xhr) {
                        const message = CampaignUtils.handleError(xhr, 'Error saving statistics');
                        CampaignUtils.showToast(message, 'error');
                    },
                    complete: function() {
                        CampaignUtils.setButtonLoading(submitBtn, false);
                    }
                });
            });

            // ===== DETAIL MODAL =====
            
            $(document).on('click', '.btnDetail', function(e) {
                e.preventDefault();
                const rowData = TableManager.getRowData(window.contentTable, this);
                if (!rowData) return;

                // Update basic info
                $('#likeModal').text(CampaignUtils.formatNumber(rowData.like));
                $('#viewModal').text(CampaignUtils.formatNumber(rowData.view));
                $('#commentModal').text(CampaignUtils.formatNumber(rowData.comment));
                $('#rateCardModal').text(rowData.rate_card_formatted || '0');
                $('#kodeAdsModal').text(rowData.kode_ads || '-');
                $('#uploadDateModal').text(rowData.upload_date || 'Not posted yet');

                // Load content embed
                loadContentEmbed(rowData.link, rowData.channel);
                
                // Load detailed statistics chart
                loadDetailChart(rowData.id);
                
                ModalManager.show('#detailModal');
            });

            // ===== CHARTS =====
            
            function updateCard() {
                const filterDates = $('#filterDates').val();
                const url = `{{ route('statistic.card', ['campaignId' => $campaign->id]) }}${filterDates ? '?filterDates=' + encodeURIComponent(filterDates) : ''}`;
                
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        $('#totalExpense').text(response.total_expense || '0');
                        $('#totalCPM').text(response.cpm || '0');
                        $('#totalInfluencer').text(response.total_influencer || '0');
                        $('#totalContent').text(response.total_content || '0');
                        $('#totalAchievement').text(response.achievement || '0');
                        $('#totalViews').text(response.view || '0');
                        $('#totalLikes').text(response.like || '0');
                        $('#totalComment').text(response.comment || '0');
                        $('#engagementRate').text(response.engagement_rate || '0%');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching card data:', error);
                    }
                });
            }

            function initChart() {
                const filterDates = $('#filterDates').val();
                const url = `{{ route('statistic.chart', ['campaignId' => $campaign->id]) }}${filterDates ? '?filterDates=' + encodeURIComponent(filterDates) : ''}`;
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        renderChart(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching chart data:', xhr.responseText);
                    }
                });
            }

            function renderChart(chartData) {
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
                            data: chartData.map(data => data.total_view || 0),
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            fill: false,
                            tension: 0.1
                        }, {
                            label: 'Likes',
                            data: chartData.map(data => data.total_like || 0),
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            fill: false,
                            tension: 0.1
                        }, {
                            label: 'Comments',
                            data: chartData.map(data => data.total_comment || 0),
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
                            }
                        },
                        scales: {
                            x: { title: { display: true, text: 'Date' }},
                            y: { title: { display: true, text: 'Count' }, beginAtZero: true }
                        }
                    }
                });
            }

            function loadDetailChart(contentId) {
                $.ajax({
                    url: `{{ route('statistic.chartDetail', ['campaignContentId' => ':id']) }}`.replace(':id', contentId),
                    type: 'GET',
                    success: function(response) {
                        renderDetailChart(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching detail chart:', xhr.responseText);
                    }
                });
            }

            function renderDetailChart(chartData) {
                if (window.statisticDetailChart) {
                    window.statisticDetailChart.destroy();
                }

                const ctx = document.getElementById('statisticDetailChart').getContext('2d');
                window.statisticDetailChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.map(data => data.date),
                        datasets: [{
                            label: 'Views',
                            data: chartData.map(data => data.view || 0),
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            fill: false,
                            tension: 0.1
                        }, {
                            label: 'Likes',
                            data: chartData.map(data => data.like || 0),
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            fill: false,
                            tension: 0.1
                        }, {
                            label: 'Comments',
                            data: chartData.map(data => data.comment || 0),
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
                                text: 'Content Statistics Over Time'
                            }
                        },
                        scales: {
                            x: { title: { display: true, text: 'Date' }},
                            y: { title: { display: true, text: 'Count' }, beginAtZero: true }
                        }
                    }
                });
            }

            // ===== FILTER HANDLERS =====
            
            $('#filterPlatform, #filterFyp, #filterPayment, #filterDelivery, #filterDates').on('change', function() {
                window.contentTable.ajax.reload();
                updateCard();
                initChart();
            });

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
                                data: { _token: '{{ csrf_token() }}' },
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

            // ===== INITIALIZATION =====
            updateCard();
            initChart();
        });

        // ===== UTILITY FUNCTIONS =====

        function loadContentEmbed(link, channel) {
            const embedContainer = $('#contentEmbed');
            
            if (!link) {
                embedContainer.html('<p class="text-muted">No content link provided</p>');
                return;
            }
            
            switch (channel) {
                case 'twitter_post':
                    const twitterLink = link.replace('https://x.com/', 'https://twitter.com/');
                    embedContainer.html(`<blockquote class="twitter-tweet"><a href="${twitterLink}"></a></blockquote>`);
                    if (typeof twttr !== 'undefined') {
                        twttr.widgets.load(embedContainer[0]);
                    }
                    break;
                    
                case 'tiktok_video':
                    embedContainer.html('<div class="text-center"><div class="spinner-border"></div></div>');
                    $.ajax({
                        url: `https://www.tiktok.com/oembed?url=${encodeURIComponent(link)}`,
                        success: function(response) {
                            embedContainer.html(response.html);
                        },
                        error: function() {
                            embedContainer.html(`<a href="${link}" target="_blank" class="btn btn-primary">View TikTok Video</a>`);
                        }
                    });
                    break;
                    
                case 'instagram_feed':
                    const cleanLink = link.split('?')[0];
                    const embedLink = cleanLink.endsWith('/') ? cleanLink + 'embed' : cleanLink + '/embed';
                    embedContainer.html(`<iframe width="315" height="560" src="${embedLink}" frameborder="0"></iframe>`);
                    break;
                    
                case 'youtube_video':
                    const videoId = link.split('/').pop();
                    embedContainer.html(`<iframe width="315" height="560" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe>`);
                    break;
                    
                case 'shopee_video':
                    embedContainer.html(`<iframe src="${link}" width="315" height="560" frameborder="0" allowfullscreen></iframe>`);
                    break;
                    
                default:
                    embedContainer.html(`<a href="${link}" target="_blank" class="btn btn-primary">View Content</a>`);
                    break;
            }
        }
    </script>
@stop