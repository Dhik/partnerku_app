@extends('adminlte::page')

@section('title', 'KOL Details - ' . $keyOpinionLeader->username)

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>KOL Details</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('kol.index') }}">KOL Management</a></li>
                <li class="breadcrumb-item active">{{ $keyOpinionLeader->username }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-user-circle"></i> 
                            {{ '@' . $keyOpinionLeader->username }}
                            @if($keyOpinionLeader->name)
                                <small class="text-muted">({{ $keyOpinionLeader->name }})</small>
                            @endif
                        </h3>
                        <div class="card-tools">
                            @can('updateKOL', App\Domain\Campaign\Models\KeyOpinionLeader::class)
                                <button class="btn btn-warning btn-sm" onclick="openEditModal({{ $keyOpinionLeader->id }})">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-info btn-sm refresh-data" data-username="{{ $keyOpinionLeader->username }}">
                                    <i class="fas fa-sync-alt"></i> Refresh Data
                                </button>
                            @endcan
                            @if($keyOpinionLeader->phone_number)
                                @php
                                    $phoneNumber = preg_replace('/[^0-9]/', '', $keyOpinionLeader->phone_number);
                                    if (substr($phoneNumber, 0, 1) === '0') {
                                        $phoneNumber = '62' . substr($phoneNumber, 1);
                                    }
                                    $waLink = 'https://wa.me/' . $phoneNumber;
                                @endphp
                                <a href="{{ $waLink }}" class="btn btn-success btn-sm" target="_blank">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </a>
                            @endif
                            @if($keyOpinionLeader->link)
                                <a href="{{ $keyOpinionLeader->link }}" class="btn btn-primary btn-sm" target="_blank">
                                    <i class="fas fa-external-link-alt"></i> View Profile
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row">
        <div class="col-lg-2 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($keyOpinionLeader->followers ?: 0) }}</h3>
                    <p>Followers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <!-- <div class="col-lg-2 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $keyOpinionLeader->engagement_rate ? number_format($keyOpinionLeader->engagement_rate, 2) . '%' : '-' }}</h3>
                    <p>Engagement Rate</p>
                </div>
                <div class="icon">
                    <i class="fas fa-heart"></i>
                </div>
            </div>
        </div> -->
        <div class="col-lg-2 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($keyOpinionLeader->cpm ?: 0) }}</h3>
                    <p>CPM</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box {{ $keyOpinionLeader->status_recommendation === 'Worth it' ? 'bg-success' : 'bg-danger' }}">
                <div class="inner">
                    <h3 style="font-size: 1.5rem;">{{ $keyOpinionLeader->status_recommendation ?: 'Unknown' }}</h3>
                    <p>Status</p>
                </div>
                <div class="icon">
                    <i class="fas {{ $keyOpinionLeader->status_recommendation === 'Worth it' ? 'fa-thumbs-up' : 'fa-thumbs-down' }}"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3 style="font-size: 1.5rem;">{{ $tiering }}</h3>
                    <p>Tier</p>
                </div>
                <div class="icon">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-dark">
                <div class="inner">
                    <h3>{{ number_format($keyOpinionLeader->price_per_slot ?: 0) }}</h3>
                    <p>Rate</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Information -->
    <div class="row">
        <!-- Basic Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Basic Information
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Username:</strong></td>
                            <td>{{ '@' . $keyOpinionLeader->username }}</td>
                        </tr>
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td>{{ $keyOpinionLeader->name ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Channel:</strong></td>
                            <td>
                                <span class="badge badge-primary">
                                    {{ ucfirst(str_replace('_', ' ', $keyOpinionLeader->channel)) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Niche:</strong></td>
                            <td>{{ $keyOpinionLeader->niche ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Content Type:</strong></td>
                            <td>{{ $keyOpinionLeader->content_type ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Phone Number:</strong></td>
                            <td>
                                @if($keyOpinionLeader->phone_number)
                                    {{ $keyOpinionLeader->phone_number }}
                                    <a href="{{ $waLink ?? '#' }}" class="btn btn-xs btn-success ml-2" target="_blank">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Address:</strong></td>
                            <td>{{ $keyOpinionLeader->address ?: '-' }}</td>
                        </tr>
                        @if($keyOpinionLeader->link)
                        <tr>
                            <td><strong>Profile Link:</strong></td>
                            <td>
                                <a href="{{ $keyOpinionLeader->link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-external-link-alt"></i> View Profile
                                </a>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Performance & Financial -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> Performance & Financial
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Rate per Content:</strong></td>
                            <td>Rp {{ number_format($keyOpinionLeader->rate ?: 0) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Average Views:</strong></td>
                            <td>{{ number_format($keyOpinionLeader->average_view ?: 0) }}</td>
                        </tr>
                        <tr>
                            <td><strong>CPM:</strong></td>
                            <td>
                                <span class="badge {{ $cpmData['status_recommendation'] === 'Worth it' ? 'badge-success' : 'badge-danger' }}">
                                    {{ number_format($cpmData['cpm']) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Price per Slot:</strong></td>
                            <td>Rp {{ number_format($keyOpinionLeader->price_per_slot ?: 0) }}</td>
                        </tr>
                        <tr>
                            <td><strong>GMV:</strong></td>
                            <td>Rp {{ number_format($keyOpinionLeader->gmv ?: 0) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Category:</strong></td>
                            <td>{{ $keyOpinionLeader->category ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tier:</strong></td>
                            <td>
                                <span class="badge badge-info">{{ $tiering }}</span>
                                @if($tiering !== 'Unknown')
                                    <small class="text-muted">
                                        ({{ number_format($keyOpinionLeader->followers) }} followers)
                                    </small>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Management Information -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users-cog"></i> Management Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>PIC Contact:</strong></td>
                                    <td>{{ $keyOpinionLeader->picContact->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>PIC Listing:</strong></td>
                                    <td>{{ $keyOpinionLeader->pic_listing ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>PIC Content:</strong></td>
                                    <td>{{ $keyOpinionLeader->pic_content ?: '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Created By:</strong></td>
                                    <td>{{ $keyOpinionLeader->createdBy->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created At:</strong></td>
                                    <td>{{ $keyOpinionLeader->created_at ? $keyOpinionLeader->created_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td>{{ $keyOpinionLeader->updated_at ? $keyOpinionLeader->updated_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Following:</strong></td>
                                    <td>{{ number_format($keyOpinionLeader->following ?: 0) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Likes:</strong></td>
                                    <td>{{ number_format($keyOpinionLeader->total_likes ?: 0) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Video Count:</strong></td>
                                    <td>{{ number_format($keyOpinionLeader->video_count ?: 0) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Targets vs Actual -->
    @if($tiering !== 'Unknown' && isset($er_top) && isset($er_bottom) && isset($cpm_target))
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bullseye"></i> Performance Targets vs Actual
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Engagement Rate Performance</h6>
                            <div class="progress mb-3" style="height: 25px;">
                                @php
                                    $erActualPercent = $er_actual > 0 ? min(($er_actual / ($er_top * 100)) * 100, 100) : 0;
                                    $erStatus = $er_actual >= ($er_bottom * 100) ? 'bg-success' : 'bg-danger';
                                @endphp
                                <div class="progress-bar {{ $erStatus }}" 
                                     style="width: {{ $erActualPercent }}%">
                                    {{ number_format($er_actual, 2) }}%
                                </div>
                            </div>
                            <small class="text-muted">
                                Target Range: {{ number_format($er_bottom * 100, 1) }}% - {{ number_format($er_top * 100, 1) }}%
                            </small>
                        </div>
                        <div class="col-md-6">
                            <h6>CPM Performance</h6>
                            <div class="progress mb-3" style="height: 25px;">
                                @php
                                    $cpmActualPercent = $cpm_target > 0 ? min((($cpm_target - $cpmData['cpm']) / $cpm_target) * 100, 100) : 0;
                                    $cpmActualPercent = max($cpmActualPercent, 0);
                                    $cpmStatus = $cpmData['cpm'] <= $cpm_target ? 'bg-success' : 'bg-danger';
                                @endphp
                                <div class="progress-bar {{ $cpmStatus }}" 
                                     style="width: {{ $cpmActualPercent }}%">
                                    {{ number_format($cpmData['cpm']) }}
                                </div>
                            </div>
                            <small class="text-muted">
                                Target: ≤ {{ number_format($cpm_target) }}
                            </small>
                        </div>
                    </div>
                    
                    <!-- Performance Summary -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert {{ $cpmData['status_recommendation'] === 'Worth it' ? 'alert-success' : 'alert-danger' }}">
                                <h5>
                                    <i class="fas {{ $cpmData['status_recommendation'] === 'Worth it' ? 'fa-check-circle' : 'fa-exclamation-triangle' }}"></i> 
                                    Overall Performance: {{ $cpmData['status_recommendation'] ?: 'Unknown' }}
                                </h5>
                                <p class="mb-0">
                                    @if($cpmData['status_recommendation'] === 'Worth it')
                                        This KOL meets the performance criteria with good CPM and engagement metrics.
                                    @else
                                        This KOL needs performance improvement. Consider renegotiating rates or improving content strategy.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Campaign History (if any) -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Campaign History
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm" id="campaign-history-table">
                            <thead>
                                <tr>
                                    <th>Campaign</th>
                                    <th>Content Type</th>
                                    <th>Upload Date</th>
                                    <th>Views</th>
                                    <th>Likes</th>
                                    <th>Comments</th>
                                    <th>Engagement Rate</th>
                                    <th>CPM</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Will be populated via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Edit Modal -->
    @include('admin.kol.modals.edit-modal')
@stop

@section('css')
    <style>
        .small-box h3 {
            font-size: 2rem;
        }
        .progress {
            height: 25px;
        }
        .progress-bar {
            line-height: 25px;
            font-weight: bold;
        }
        .table td {
            border-top: none;
        }
        .badge {
            font-size: 0.9em;
        }
        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
        .alert {
            border-radius: 0.375rem;
        }
        .btn-xs {
            padding: 0.125rem 0.25rem;
            font-size: 0.75rem;
            line-height: 1.5;
            border-radius: 0.125rem;
        }
    </style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Refresh data functionality
    $('.refresh-data').click(function() {
        const username = $(this).data('username');
        const btn = $(this);
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');
        
        $.get(`{{ url('admin/kol/refresh-single') }}/${username}`)
            .done(function(response) {
                toastr.success('Data refreshed successfully');
                // Reload the page to show updated data
                setTimeout(() => {
                    location.reload();
                }, 1000);
            })
            .fail(function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response?.message || 'Failed to refresh data');
            })
            .always(function() {
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Refresh Data');
            });
    });

    // Open edit modal function
    window.openEditModal = function(kolId) {
        @can('updateKOL', App\Domain\Campaign\Models\KeyOpinionLeader::class)
        $.get(`{{ url('admin/kol') }}/${kolId}/edit-data`)
            .done(function(data) {
                // Use the global function from edit modal
                if (typeof populateEditForm === 'function') {
                    populateEditForm(data);
                }
                $('#editKolModal').modal('show');
            })
            .fail(function() {
                toastr.error('Failed to load KOL data');
            });
        @else
        toastr.error('You do not have permission to edit KOLs');
        @endcan
    };

    // Load campaign history
    function loadCampaignHistory() {
        const username = '{{ $keyOpinionLeader->username }}';
        
        $.get(`{{ url('admin/kol/campaign-history') }}/${username}`)
            .done(function(response) {
                let historyHtml = '';
                
                if (response.success && response.data && response.data.length > 0) {
                    response.data.forEach(function(item) {
                        const views = parseInt(item.views) || 0;
                        const likes = parseInt(item.likes) || 0;
                        const comments = parseInt(item.comments) || 0;
                        const engagementRate = views > 0 ? 
                            (((likes + comments) / views) * 100).toFixed(2) : '0.00';
                        
                        historyHtml += `
                            <tr>
                                <td>${item.campaign_title || '-'}</td>
                                <td>${item.task_name || '-'}</td>
                                <td>${item.upload_date ? new Date(item.upload_date).toLocaleDateString('id-ID') : '-'}</td>
                                <td>${views.toLocaleString()}</td>
                                <td>${likes.toLocaleString()}</td>
                                <td>${comments.toLocaleString()}</td>
                                <td>${engagementRate}%</td>
                                <td>${item.cpm ? parseFloat(item.cpm).toLocaleString() : '0'}</td>
                            </tr>
                        `;
                    });
                } else {
                    historyHtml = '<tr><td colspan="8" class="text-center text-muted">No campaign history found</td></tr>';
                }
                
                $('#campaign-history-table tbody').html(historyHtml);
            })
            .fail(function() {
                $('#campaign-history-table tbody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load campaign history</td></tr>');
            });
    }

    // Load campaign history on page load
    loadCampaignHistory();

    // Tooltip initialization
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@stop