@extends('adminlte::page')

@section('title', 'Key Opinion Leaders Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Key Opinion Leaders Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">KOL Management</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <!-- KPI Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="total-kol">0</h3>
                    <p>Total KOLs</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="worth-it-count">0</h3>
                    <p>Worth It KOLs</p>
                </div>
                <div class="icon">
                    <i class="fas fa-thumbs-up"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="avg-cpm">0</h3>
                    <p>Average CPM</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="worth-it-percentage">0%</h3>
                    <p>Worth It Percentage</p>
                </div>
                <div class="icon">
                    <i class="fas fa-percentage"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filters & Actions</h3>
                <div class="card-tools">
                    @can('createKOL', App\Domain\Campaign\Models\KeyOpinionLeader::class)
                        <a href="{{ route('kol.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add KOL
                        </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <form id="filter-form">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Approval Status</label>
                                <select name="approve_status" id="filter-approve-status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="approved">Approved</option>
                                    <option value="declined">Declined</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status Recommendation</label>
                                <select name="status_recommendation" id="filter-status-recommendation" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="Worth it">Worth it</option>
                                    <option value="Gagal">Gagal</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Niche</label>
                                <select name="niche" id="filter-niche" class="form-control">
                                    <option value="">All Niches</option>
                                    @foreach($niches as $niche)
                                        <option value="{{ $niche->name }}">{{ ucfirst($niche->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tier</label>
                                <select name="tier" id="filter-tier" class="form-control">
                                    <option value="">All Tiers</option>
                                    <option value="Nano">Nano (1K - 10K)</option>
                                    <option value="Micro">Micro (10K - 50K)</option>
                                    <option value="Mid-Tier">Mid-Tier (50K - 250K)</option>
                                    <option value="Macro">Macro (250K - 1M)</option>
                                    <option value="Mega">Mega (1M+)</option>
                                    <option value="Unknown">Unknown (&lt;1K)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary mr-2" id="apply-filters">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                                <button type="button" class="btn btn-secondary" id="reset-filters">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- KOL Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Key Opinion Leaders List</h3>
                </div>
                <div class="card-body">
                    <table id="kol-table" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Username</th>
            <th>Niche</th>
            <th>Followers</th>
            <th>Tier</th>
            <th>Price/Slot</th>
            <th>Avg View</th>
            <th>CPM</th>
            <th>Status</th>
            <th>Approval</th>
            <th>PIC Contact</th>
            <th>Refresh</th>
            <th>Approval Actions</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editKolModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit"></i> Edit Key Opinion Leader
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editKolForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit-kol-id" name="id">
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-user"></i> Basic Information
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="edit-username">Username <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">@</span>
                                                </div>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="edit-username" 
                                                       name="username" 
                                                       required
                                                       placeholder="Enter username without @">
                                            </div>
                                            <small class="form-text text-muted">Username without @ symbol</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="edit-name">Full Name</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="edit-name" 
                                                   name="name"
                                                   placeholder="Enter full name">
                                        </div>

                                        <div class="form-group">
                                            <label for="edit-phone">Phone Number</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="edit-phone" 
                                                   name="phone_number"
                                                   placeholder="e.g., 08123456789">
                                        </div>

                                        <div class="form-group">
                                            <label for="edit-channel">Channel <span class="text-danger">*</span></label>
                                            <select class="form-control" id="edit-channel" name="channel" required>
                                                <option value="">Select Channel</option>
                                                @foreach($channels as $channel)
                                                    <option value="{{ $channel }}">{{ ucfirst(str_replace('_', ' ', $channel)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="edit-niche">Niche</label>
                                            <select class="form-control" id="edit-niche" name="niche">
                                                <option value="">Select Niche</option>
                                                @foreach($niches as $niche)
                                                    <option value="{{ $niche->name }}">{{ ucfirst($niche->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="edit-content-type">Content Type</label>
                                            <select class="form-control" id="edit-content-type" name="content_type">
                                                <option value="">Select Content Type</option>
                                                @foreach($contentTypes as $contentType)
                                                    <option value="{{ $contentType }}">{{ ucfirst($contentType) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial & Performance -->
                            <div class="col-md-6">
                                <div class="card card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-chart-line"></i> Financial & Performance
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool btn-sm" id="edit-calculate-cpm-btn">
                                                <i class="fas fa-calculator"></i> Calculate CPM
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="edit-rate">Rate per Content/Slot</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="number" 
                                                       class="form-control" 
                                                       id="edit-rate" 
                                                       name="rate" 
                                                       min="0" 
                                                       step="1000"
                                                       placeholder="0">
                                            </div>
                                            <small class="form-text text-muted">This rate will be used for both content and slot pricing</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="edit-gmv">GMV (Gross Merchandise Value)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="number" 
                                                       class="form-control" 
                                                       id="edit-gmv" 
                                                       name="gmv" 
                                                       min="0"
                                                       placeholder="0">
                                            </div>
                                        </div>

                                        <!-- CPM Preview Card -->
                                        <div class="alert alert-info" id="edit-cpm-preview" style="display: none;">
                                            <h6><i class="fas fa-calculator"></i> CPM Calculation Preview</h6>
                                            <div class="row">
                                                <div class="col-6">
                                                    <p class="mb-1"><strong>Calculated CPM:</strong></p>
                                                    <h5 class="text-primary" id="edit-cpm-value">0</h5>
                                                </div>
                                                <div class="col-6">
                                                    <p class="mb-1"><strong>Recommendation:</strong></p>
                                                    <h5 id="edit-cpm-status-badge">-</h5>
                                                </div>
                                            </div>
                                            <small class="text-muted">Formula: (Rate ÷ Average View) × 1000</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Video Links Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-video"></i> Video Links (Up to 10 Links)
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @for($i = 1; $i <= 10; $i++)
                                            <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label for="edit-video-link-{{ $i }}">Video Link {{ $i }}</label>
                                                    <input type="url" 
                                                           class="form-control edit-video-link" 
                                                           id="edit-video-link-{{ $i }}" 
                                                           name="video_10_links[]" 
                                                           placeholder="https://tiktok.com/@username/video/123...">
                                                </div>
                                            </div>
                                            @endfor
                                        </div>
                                        
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Note:</strong> Enter up to 10 video links. Empty fields will be ignored.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact & Management -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-address-book"></i> Contact & Management
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="edit-pic-contact">PIC Contact <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="edit-pic-contact" name="pic_contact" required>
                                                        <option value="">Select PIC Contact</option>
                                                        @if(isset($marketingUsers))
                                                            @foreach($marketingUsers as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="edit-pic-listing">PIC Listing</label>
                                                    <select class="form-control" id="edit-pic-listing" name="pic_listing">
                                                        <option value="">Select PIC Listing</option>
                                                        @if(isset($marketingUsers))
                                                            @foreach($marketingUsers as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="edit-submit-btn">
                            <i class="fas fa-save"></i> Update KOL
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .small-box h3 {
            font-size: 2.2rem;
        }
        .badge {
            font-size: 0.8em;
        }
        
        /* SweetAlert2 custom styles */
        .swal2-popup {
            font-size: 0.875rem;
        }
        
        .swal2-html-container {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .swal2-html-container h6 {
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 0.25rem;
        }
        
        .swal2-html-container h6:first-child {
            margin-top: 0;
        }
        
        .swal2-html-container .list-unstyled li {
            padding: 0.125rem 0;
        }
        
        .swal2-html-container .badge {
            font-size: 0.75rem;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        /* Modal styles */
        .modal-xl {
            max-width: 1200px;
        }

        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }

        #edit-cpm-preview {
            border-left: 4px solid #007bff;
        }

        .form-group label {
            font-weight: 600;
        }

        .card-header {
            border-bottom: 1px solid rgba(0,0,0,.125);
        }

        .edit-video-link {
            font-size: 0.875rem;
        }

        .swal-wide {
            width: 600px !important;
        }
        .swal2-html-container .form-check {
            text-align: left;
        }
        .swal2-html-container .form-check-input {
            margin-top: 0.125rem;
        }
        .swal2-html-container .form-check-label {
            margin-left: 0.25rem;
        }
    </style>
@stop

@section('js')
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    const kolTable = $('#kol-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("kol.get") }}',
            data: function(d) {
                d.approve_status = $('#filter-approve-status').val();
                d.status_recommendation = $('#filter-status-recommendation').val();
                d.niche = $('#filter-niche').val();
                d.tier = $('#filter-tier').val();
            }
        },
        columns: [
            { data: 'username', name: 'username' },
            { data: 'niche', name: 'niche' },
            { 
                data: 'followers', 
                name: 'followers',
                render: function(data, type, row) {
                    if (type === 'display' && data != null) {
                        return new Intl.NumberFormat('id-ID').format(data);
                    }
                    return data;
                }
            },
            { data: 'tier_display', name: 'tier_display', orderable: false },
            { 
                data: 'price_per_slot', 
                name: 'price_per_slot',
                render: function(data, type, row) {
                    if (type === 'display' && data != null) {
                        return 'Rp. ' + new Intl.NumberFormat('id-ID').format(data);
                    }
                    return data;
                }
            },
            { 
                data: 'average_view', 
                name: 'average_view',
                render: function(data, type, row) {
                    if (type === 'display' && data != null) {
                        return new Intl.NumberFormat('id-ID').format(data);
                    }
                    return data;
                }
            },
            { data: 'cpm_display', name: 'cpm', orderable: false },
            { data: 'status_recommendation_display', name: 'status_recommendation', orderable: false },
            { data: 'approval_status', name: 'approve', orderable: false },
            { data: 'pic_contact_name', name: 'pic_contact_name' },
            { data: 'refresh_follower', name: 'refresh_follower', orderable: false, searchable: false },
            { data: 'approval_actions', name: 'approval_actions', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']],
        pageLength: 25,
        responsive: true,
        scrollX: true
    });

    // Add the approval function
    window.updateApprovalStatus = function(kolId, approve) {
        const action = approve ? 'approve' : 'decline';
        const actionText = approve ? 'Approve' : 'Decline';
        const actionColor = approve ? '#28a745' : '#dc3545';
        const iconClass = approve ? 'fa-check' : 'fa-times';
        
        Swal.fire({
            title: `${actionText} KOL?`,
            html: `
                <div class="text-center">
                    <i class="fas ${iconClass} fa-3x mb-3" style="color: ${actionColor}"></i>
                    <p>Are you sure you want to <strong>${action}</strong> this Key Opinion Leader?</p>
                    <small class="text-muted">This will update the approval status for this KOL.</small>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: actionColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Yes, ${actionText}!`,
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: `${actionText}ing KOL...`,
                    html: `
                        <div class="text-center">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p>Please wait while we update the approval status.</p>
                        </div>
                    `,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false
                });

                // Make the AJAX request
                $.ajax({
                    url: '{{ route("kol.updateApproval", ":id") }}'.replace(':id', kolId),
                    method: 'PUT',
                    data: {
                        approve: approve ? 1 : 0,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .done(function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonColor: '#28a745',
                            timer: 3000,
                            timerProgressBar: true
                        });
                        
                        // Reload the table to reflect changes
                        kolTable.ajax.reload(null, false);
                        loadKpiData();
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || `Failed to ${action} KOL`,
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                })
                .fail(function(xhr) {
                    const response = xhr.responseJSON;
                    let errorMessage = `Failed to ${action} KOL`;
                    
                    if (response) {
                        if (response.message) {
                            errorMessage = response.message;
                        } else if (response.errors) {
                            errorMessage = Object.values(response.errors).flat().join(', ');
                        }
                    }
                    
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                });
            }
        });
    };

    // Load KPI data
    function loadKpiData() {
        const filterData = {
            approve_status: $('#filter-approve-status').val(),
            status_recommendation: $('#filter-status-recommendation').val(),
            niche: $('#filter-niche').val(),
            tier: $('#filter-tier').val()
        };

        $.get('{{ route("kol.kpi") }}', filterData, function(data) {
            $('#total-kol').text(data.total_kol.toLocaleString());
            $('#worth-it-count').text(data.worth_it_count.toLocaleString());
            $('#avg-cpm').text(data.avg_cpm.toLocaleString());
            $('#worth-it-percentage').text(data.worth_it_percentage + '%');
        });
    }

    // Apply filters
    $('#apply-filters').click(function() {
        kolTable.ajax.reload();
        loadKpiData();
    });

    // Reset filters
    $('#reset-filters').click(function() {
        $('#filter-form')[0].reset();
        kolTable.ajax.reload();
        loadKpiData();
    });

    // CPM Calculation for Edit Modal
    function calculateEditCPM() {
        const rate = parseFloat($('#edit-rate').val()) || 0;
        // Get current average_view from the loaded data (will be set when modal opens)
        const avgView = parseFloat($('#editKolModal').data('current-avg-view')) || 1;
        
        if (rate > 0) {
            const cpm = (rate / avgView) * 1000;
            const status = cpm < 25000 ? 'Worth it' : 'Gagal';
            const statusClass = cpm < 25000 ? 'badge badge-success' : 'badge badge-danger';
            
            $('#edit-cpm-value').text('Rp ' + cpm.toLocaleString('id-ID', { maximumFractionDigits: 2 }));
            $('#edit-cpm-status-badge').html(`<span class="${statusClass}">${status}</span>`);
            $('#edit-cpm-preview').show();
        } else {
            $('#edit-cpm-preview').hide();
        }
    }

    // Auto-calculate CPM on input change for edit modal
    $('#edit-rate').on('input', calculateEditCPM);
    $('#edit-calculate-cpm-btn').click(calculateEditCPM);

    // Refresh video statistics (UPDATED FUNCTION)
    $(document).on('click', '.refresh-follower', function() {
        const username = $(this).data('id');
        const btn = $(this);
        
        // Show SweetAlert with options
        Swal.fire({
            title: 'Refresh KOL Data',
            html: `
                <div class="text-left">
                    <p>Choose what data to refresh for <strong>@${username}</strong>:</p>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="refresh-followers" checked>
                        <label class="form-check-label" for="refresh-followers">
                            <i class="fas fa-users text-primary"></i> Update Followers & Profile Stats
                        </label>
                        <small class="d-block text-muted">Fetches current follower count, following, likes, etc.</small>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="refresh-videos" checked>
                        <label class="form-check-label" for="refresh-videos">
                            <i class="fas fa-video text-success"></i> Update Video Statistics
                        </label>
                        <small class="d-block text-muted">Processes video links and calculates average views</small>
                    </div>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Start Refresh',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const refreshFollowers = document.getElementById('refresh-followers').checked;
                const refreshVideos = document.getElementById('refresh-videos').checked;
                
                if (!refreshFollowers && !refreshVideos) {
                    Swal.showValidationMessage('Please select at least one option to refresh');
                    return false;
                }
                
                return { refreshFollowers, refreshVideos };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const { refreshFollowers, refreshVideos } = result.value;
                
                // Show loading alert
                Swal.fire({
                    title: 'Refreshing KOL Data...',
                    html: `
                        <div class="text-center">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p>Processing data for <strong>@${username}</strong></p>
                            <div id="refresh-progress">
                                <p class="text-muted">Initializing...</p>
                            </div>
                        </div>
                    `,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });

                // Disable the button
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                
                // Execute refresh operations
                performRefreshOperations(username, refreshFollowers, refreshVideos, btn);
            }
        });
    });
    function showRefreshResults(username, results, btn) {
        const hasSuccess = (results.followers?.success || results.videos?.success);
        const hasErrors = results.errors.length > 0;
        
        let html = '<div class="text-left">';
        
        // KOL Info Header
        html += `
            <h6><i class="fas fa-user text-primary"></i> KOL: @${username}</h6>
            <hr>
        `;
        
        // Followers Results
        if (results.followers) {
            if (results.followers.success) {
                const data = results.followers.data;
                html += `
                    <h6><i class="fas fa-users text-success"></i> Followers & Profile Data Updated:</h6>
                    <ul class="list-unstyled mb-3">
                        <li><strong>Followers:</strong> ${(data.followers || 0).toLocaleString()}</li>
                        <li><strong>Following:</strong> ${(data.following || 0).toLocaleString()}</li>
                        <li><strong>Total Likes:</strong> ${(data.total_likes || 0).toLocaleString()}</li>
                        <li><strong>Video/Post Count:</strong> ${(data.video_count || 0).toLocaleString()}</li>
                        ${data.engagement_rate ? `<li><strong>Engagement Rate:</strong> ${data.engagement_rate}%</li>` : ''}
                    </ul>
                `;
            } else {
                html += `
                    <h6><i class="fas fa-users text-danger"></i> Followers Update Failed:</h6>
                    <p class="text-danger mb-3">${results.followers.error}</p>
                `;
            }
        }
        
        // Video Results
        if (results.videos) {
            if (results.videos.success) {
                const data = results.videos.data;
                const stats = data.statistics;
                const cpm = data.cpm_calculation || data.cpm_calculation; // Handle both possible typos
                
                html += `
                    <h6><i class="fas fa-video text-success"></i> Video Statistics Updated:</h6>
                    <ul class="list-unstyled mb-3">
                        <li><strong>Videos Processed:</strong> ${stats.successful_videos}/${stats.total_video_links}</li>
                        <li><strong>Previous Avg Views:</strong> ${(stats.old_average_views || 0).toLocaleString()}</li>
                        <li><strong>New Avg Views:</strong> ${stats.new_average_views.toLocaleString()}</li>
                        ${stats.failed_videos > 0 ? `<li class="text-warning"><strong>Failed Videos:</strong> ${stats.failed_videos}</li>` : ''}
                    </ul>
                    
                    ${cpm ? `
                        <h6><i class="fas fa-calculator text-info"></i> CPM Calculation:</h6>
                        <ul class="list-unstyled mb-3">
                            <li><strong>Price per Slot:</strong> Rp ${(cpm.price_per_slot || 0).toLocaleString()}</li>
                            <li><strong>New CPM:</strong> ${cpm.new_cpm ? 'Rp ' + cpm.new_cpm.toLocaleString() : 'N/A'}</li>
                            <li><strong>Status:</strong> 
                                <span class="badge badge-${cpm.new_status === 'Worth it' ? 'success' : 'danger'}">
                                    ${cpm.new_status}
                                </span>
                            </li>
                        </ul>
                    ` : ''}
                `;
            } else {
                html += `
                    <h6><i class="fas fa-video text-danger"></i> Video Statistics Update Failed:</h6>
                    <p class="text-danger mb-3">${results.videos.error}</p>
                `;
            }
        }
        
        // Error Summary
        if (hasErrors && results.errors.length > 0) {
            html += `
                <h6><i class="fas fa-exclamation-triangle text-warning"></i> Issues Encountered:</h6>
                <ul class="list-unstyled mb-3 text-warning">
            `;
            results.errors.forEach(error => {
                html += `<li><small>• ${error}</small></li>`;
            });
            html += '</ul>';
        }
        
        html += '</div>';
        
        // Show results
        Swal.fire({
            title: hasSuccess ? 'Refresh Completed!' : 'Refresh Failed',
            html: html,
            icon: hasSuccess ? (hasErrors ? 'warning' : 'success') : 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: hasSuccess ? '#28a745' : '#dc3545',
            customClass: {
                popup: 'swal-wide'
            }
        });
        
        // Reload table and KPI data if any operation was successful
        if (hasSuccess) {
            kolTable.ajax.reload(null, false);
            loadKpiData();
        }
        
        // Re-enable the button
        btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i>');
    }


    function performRefreshOperations(username, refreshFollowers, refreshVideos, btn) {
        const results = {
            followers: null,
            videos: null,
            errors: []
        };
        
        let completedOperations = 0;
        const totalOperations = (refreshFollowers ? 1 : 0) + (refreshVideos ? 1 : 0);
        
        function updateProgress(message) {
            $('#refresh-progress p').text(message);
        }
        
        function checkCompletion() {
            completedOperations++;
            if (completedOperations >= totalOperations) {
                showRefreshResults(username, results, btn);
            }
        }
        
        // Refresh followers data
        if (refreshFollowers) {
            updateProgress('Fetching follower data from social media APIs...');
            
            $.get('{{ route("kol.refreshSingle", ":username") }}'.replace(':username', username))
                .done(function(response) {
                    results.followers = {
                        success: true,
                        data: response
                    };
                    updateProgress('Follower data updated successfully. Processing video statistics...');
                })
                .fail(function(xhr) {
                    const response = xhr.responseJSON;
                    results.followers = {
                        success: false,
                        error: response?.error || 'Failed to refresh follower data'
                    };
                    results.errors.push('Follower refresh failed: ' + (response?.error || 'Unknown error'));
                    updateProgress('Follower refresh failed. Continuing with video statistics...');
                })
                .always(function() {
                    checkCompletion();
                });
        }
        
        // Refresh video statistics
        if (refreshVideos) {
            if (!refreshFollowers) {
                updateProgress('Fetching video statistics...');
            }
            
            $.get('{{ route("kol.fetch.video.stats") }}', {
                username: username,
                tenant_id: {{ Auth::user()->current_tenant_id }}
            })
            .done(function(response) {
                results.videos = {
                    success: response.success,
                    data: response
                };
                if (!response.success) {
                    results.errors.push('Video stats failed: ' + response.message);
                }
                updateProgress('Video statistics processed successfully.');
            })
            .fail(function(xhr) {
                const response = xhr.responseJSON;
                results.videos = {
                    success: false,
                    error: response?.message || 'Failed to refresh video statistics'
                };
                results.errors.push('Video stats failed: ' + (response?.message || 'Unknown error'));
                updateProgress('Video statistics refresh failed.');
            })
            .always(function() {
                checkCompletion();
            });
        }
    }

    // Open edit modal
    window.openEditModal = function(kolId) {
        @can('updateKOL', App\Domain\Campaign\Models\KeyOpinionLeader::class)
        $.get(`{{ url('admin/kol') }}/${kolId}/edit-data`)
            .done(function(data) {
                // Store current average_view for CPM calculation
                $('#editKolModal').data('current-avg-view', data.average_view || 1);
                
                // Fill basic information
                $('#edit-kol-id').val(data.id);
                $('#edit-username').val(data.username);
                $('#edit-name').val(data.name);
                $('#edit-phone').val(data.phone_number);
                $('#edit-channel').val(data.channel);
                $('#edit-niche').val(data.niche);
                $('#edit-content-type').val(data.content_type);
                
                // Fill financial information - use rate for both rate and price_per_slot display
                $('#edit-rate').val(data.rate);
                $('#edit-gmv').val(data.gmv);
                
                // Fill contact information
                $('#edit-pic-contact').val(data.pic_contact);
                $('#edit-pic-listing').val(data.pic_listing);
                $('#edit-pic-content').val(data.pic_content);
                
                // Fill video links
                const videoLinks = data.video_10_links ? JSON.parse(data.video_10_links) : [];
                for (let i = 1; i <= 10; i++) {
                    $(`#edit-video-link-${i}`).val(videoLinks[i-1] || '');
                }
                
                // Calculate and show current CPM
                if (data.rate) {
                    calculateEditCPM();
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

    // Submit edit form
    $('#editKolForm').submit(function(e) {
        e.preventDefault();
        
        const kolId = $('#edit-kol-id').val();
        const formData = new FormData(this);
        
        // Show loading state
        const submitBtn = $('#edit-submit-btn');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
        
        // Convert FormData to regular object for AJAX
        const data = {};
        for (let [key, value] of formData.entries()) {
            if (key === 'video_10_links[]') {
                if (!data.video_10_links) data.video_10_links = [];
                if (value.trim() !== '') {
                    data.video_10_links.push(value);
                }
            } else {
                data[key] = value;
            }
        }
        
        $.ajax({
            url: `{{ url('admin/kol') }}/${kolId}`,
            method: 'PUT',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                $('#editKolModal').modal('hide');
                kolTable.ajax.reload(null, false);
                loadKpiData();
            } else {
                toastr.error(response.message || 'Failed to update KOL');
            }
        })
        .fail(function(xhr) {
            const response = xhr.responseJSON;
            if (response && response.errors) {
                // Show validation errors
                let errorMessage = 'Validation errors:\n';
                for (let field in response.errors) {
                    errorMessage += `${field}: ${response.errors[field].join(', ')}\n`;
                }
                toastr.error(errorMessage);
            } else {
                toastr.error(response?.message || 'Failed to update KOL');
            }
        })
        .always(function() {
            // Re-enable the button
            submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Update KOL');
        });
    });

    // Delete KOL
    window.deleteKol = function(kolId) {
        @can('deleteKOL', App\Domain\Campaign\Models\KeyOpinionLeader::class)
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will permanently delete the KOL and all related campaign contents and statistics. This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we delete the KOL and related data.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `{{ route('kol.destroy', ':id') }}`.replace(':id', kolId),
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .done(function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        kolTable.ajax.reload();
                        loadKpiData();
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Failed to delete KOL',
                            icon: 'error'
                        });
                    }
                })
                .fail(function(xhr) {
                    const response = xhr.responseJSON;
                    Swal.fire({
                        title: 'Error!',
                        text: response?.message || 'Failed to delete KOL',
                        icon: 'error'
                    });
                });
            }
        });
        @else
        Swal.fire({
            title: 'Access Denied',
            text: 'You do not have permission to delete KOLs',
            icon: 'error'
        });
        @endcan
    };

    // Export functionality
    $('#export-btn').click(function() {
        const filterData = {
            channel: $('#filter-channel').val(),
            niche: $('#filter-niche').val(),
            content_type: $('#filter-content-type').val(),
            pic_contact: $('#filter-pic').val(),
            followersMin: $('#filter-followers-min').val(),
            followersMax: $('#filter-followers-max').val()
        };
        
        const queryString = $.param(filterData);
        window.location.href = `{{ route('kol.export') }}?${queryString}`;
    });

    // Initial load
    loadKpiData();
    
    // Auto-refresh KPI data every 30 seconds
    setInterval(loadKpiData, 30000);
});
</script>
@stop