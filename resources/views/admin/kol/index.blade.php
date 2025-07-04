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
                            <!-- <a href="{{ route('kol.createExcel') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel"></i> Import Excel
                            </a> -->
                        @endcan
                        <!-- @can('viewKOL', App\Domain\Campaign\Models\KeyOpinionLeader::class)
                            <button type="button" class="btn btn-info btn-sm" id="export-btn">
                                <i class="fas fa-download"></i> Export
                            </button>
                        @endcan -->
                    </div>
                </div>
                <div class="card-body">
                    <form id="filter-form">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Channel</label>
                                    <select name="channel" id="filter-channel" class="form-control">
                                        <option value="">All Channels</option>
                                        @foreach($channels as $channel)
                                            <option value="{{ $channel }}">{{ ucfirst(str_replace('_', ' ', $channel)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Niche</label>
                                    <select name="niche" id="filter-niche" class="form-control">
                                        <option value="">All Niches</option>
                                        @foreach($niches as $niche)
                                            <option value="{{ $niche }}">{{ ucfirst($niche) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Content Type</label>
                                    <select name="content_type" id="filter-content-type" class="form-control">
                                        <option value="">All Content Types</option>
                                        @foreach($contentTypes as $contentType)
                                            <option value="{{ $contentType }}">{{ ucfirst($contentType) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>PIC Contact</label>
                                    <select name="pic_contact" id="filter-pic" class="form-control">
                                        <option value="">All PICs</option>
                                        @foreach($marketingUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Status Recommendation</label>
                                    <select name="status_recommendation" id="filter-status-recommendation" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="Worth it">Worth it</option>
                                        <option value="Gagal">Gagal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-primary mr-2" id="apply-filters">
                                            <i class="fas fa-filter"></i> Apply
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="reset-filters">
                                            <i class="fas fa-undo"></i> Reset
                                        </button>
                                    </div>
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
                                <th>Channel</th>
                                <th>Niche</th>
                                <th>Price/Slot</th>
                                <th>Avg View</th>
                                <th>CPM</th>
                                <th>Status</th>
                                <th>PIC Contact</th>
                                <th>Refresh</th>
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
                                                    <option value="{{ $niche }}">{{ ucfirst($niche) }}</option>
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
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="edit-pic-contact">PIC Contact <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="edit-pic-contact" name="pic_contact" required>
                                                        <option value="">Select PIC</option>
                                                        @foreach($marketingUsers as $user)
                                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="edit-pic-listing">PIC Listing</label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="edit-pic-listing" 
                                                           name="pic_listing"
                                                           placeholder="Person in charge of listing">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="edit-pic-content">PIC Content</label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="edit-pic-content" 
                                                           name="pic_content"
                                                           placeholder="Person in charge of content">
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

        #edit-cmp-preview {
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
                d.channel = $('#filter-channel').val();
                d.niche = $('#filter-niche').val();
                d.content_type = $('#filter-content-type').val();
                d.pic_contact = $('#filter-pic').val();
                d.status_recommendation = $('#filter-status-recommendation').val();
            }
        },
        columns: [
            { data: 'username', name: 'username' },
            { data: 'channel', name: 'channel' },
            { data: 'niche', name: 'niche' },
            { data: 'price_per_slot', name: 'price_per_slot' },
            { data: 'average_view', name: 'average_view' },
            { data: 'cpm_display', name: 'cpm', orderable: false },
            { data: 'status_recommendation_display', name: 'status_recommendation', orderable: false },
            { data: 'pic_contact_name', name: 'pic_contact_name' },
            { data: 'refresh_follower', name: 'refresh_follower', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']],
        pageLength: 25,
        responsive: true
    });

    // Load KPI data
    function loadKpiData() {
        const filterData = {
            channel: $('#filter-channel').val(),
            niche: $('#filter-niche').val(),
            content_type: $('#filter-content-type').val(),
            pic_contact: $('#filter-pic').val(),
            status_recommendation: $('#filter-status-recommendation').val()
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
            const statusClass = cmp < 25000 ? 'badge badge-success' : 'badge badge-danger';
            
            $('#edit-cpm-value').text('Rp ' + cpm.toLocaleString('id-ID', { maximumFractionDigits: 2 }));
            $('#edit-cpm-status-badge').html(`<span class="${statusClass}">${status}</span>`);
            $('#edit-cpm-preview').show();
        } else {
            $('#edit-cmp-preview').hide();
        }
    }

    // Auto-calculate CPM on input change for edit modal
    $('#edit-rate').on('input', calculateEditCPM);
    $('#edit-calculate-cpm-btn').click(calculateEditCPM);

    // Refresh video statistics (UPDATED FUNCTION)
    $(document).on('click', '.refresh-follower', function() {
        const username = $(this).data('id');
        const btn = $(this);
        
        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Fetch Video Statistics?',
            text: `This will fetch TikTok video data for @${username} and update average views, CPM, and status.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, fetch data!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading alert
                Swal.fire({
                    title: 'Fetching Video Statistics...',
                    html: `
                        <div class="text-center">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p>Processing video links for <strong>@${username}</strong></p>
                            <p class="text-muted">This may take a few moments...</p>
                        </div>
                    `,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });

                // Disable the button
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                
                // Make API call to fetch video statistics
                $.get('{{ route("kol.fetch.video.stats") }}', {
                    username: username,
                    tenant_id: {{ Auth::user()->current_tenant_id }}
                })
                .done(function(response) {
                    if (response.success) {
                        // Show success result with details
                        const stats = response.statistics;
                        const cpm = response.cpm_calculation; // Fixed: was 'cmp', now 'cpm'
                        
                        Swal.fire({
                            title: 'Success!',
                            html: `
                                <div class="text-left">
                                    <h6><i class="fas fa-user text-primary"></i> KOL Information:</h6>
                                    <ul class="list-unstyled mb-3">
                                        <li><strong>Username:</strong> @${response.kol_info.username}</li>
                                        <li><strong>Channel:</strong> ${response.kol_info.channel}</li>
                                    </ul>
                                    
                                    <h6><i class="fas fa-chart-line text-success"></i> Statistics Updated:</h6>
                                    <ul class="list-unstyled mb-3">
                                        <li><strong>Videos Processed:</strong> ${stats.successful_videos}/${stats.total_video_links}</li>
                                        <li><strong>Previous Avg Views:</strong> ${(stats.old_average_views || 0).toLocaleString()}</li>
                                        <li><strong>New Avg Views:</strong> ${stats.new_average_views.toLocaleString()}</li>
                                        ${stats.failed_videos > 0 ? `<li class="text-warning"><strong>Failed Videos:</strong> ${stats.failed_videos}</li>` : ''}
                                    </ul>
                                    
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
                                    
                                    ${response.view_counts && response.view_counts.length > 0 ? `
                                        <h6><i class="fas fa-eye text-warning"></i> Individual Video Views:</h6>
                                        <div class="text-muted small">
                                            ${response.view_counts.map(view => view.toLocaleString()).join(', ')}
                                        </div>
                                    ` : ''}
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonText: 'Great!',
                            confirmButtonColor: '#28a745'
                        });
                        
                        // Reload table and KPI data
                        kolTable.ajax.reload(null, false);
                        loadKpiData();
                        
                    } else {
                        // Show error message
                        Swal.fire({
                            title: 'Failed to Fetch Data',
                            html: `
                                <div class="text-left">
                                    <p><strong>Error:</strong> ${response.message}</p>
                                    ${response.kol_info ? `
                                        <hr>
                                        <h6>KOL Information:</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>ID:</strong> ${response.kol_info.id}</li>
                                            <li><strong>Username:</strong> @${response.kol_info.username}</li>
                                            <li><strong>Current Avg Views:</strong> ${(response.kol_info.current_average_view || 0).toLocaleString()}</li>
                                            <li><strong>Price per Slot:</strong> Rp ${(response.kol_info.price_per_slot || 0).toLocaleString()}</li>
                                        </ul>
                                    ` : ''}
                                </div>
                            `,
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                })
                .fail(function(xhr) {
                    const response = xhr.responseJSON;
                    
                    Swal.fire({
                        title: 'Request Failed',
                        html: `
                            <div class="text-left">
                                <p><strong>Status:</strong> ${xhr.status}</p>
                                <p><strong>Error:</strong> ${response?.message || 'Unknown error occurred'}</p>
                                <p class="text-muted">Please try again or contact support if the problem persists.</p>
                            </div>
                        `,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                })
                .always(function() {
                    // Re-enable the button
                    btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i>');
                });
            }
        });
    });

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