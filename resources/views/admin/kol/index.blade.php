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
                                    <label>Min Followers</label>
                                    <input type="number" name="followersMin" id="filter-followers-min" class="form-control" placeholder="Min followers">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Max Followers</label>
                                    <input type="number" name="followersMax" id="filter-followers-max" class="form-control" placeholder="Max followers">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary" id="apply-filters">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                                <button type="button" class="btn btn-secondary" id="reset-filters">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
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
                                <th>Name</th>
                                <th>Channel</th>
                                <th>Niche</th>
                                <th>Followers</th>
                                <th>Rate</th>
                                <th>CPM</th>
                                <th>Status</th>
                                <th>Tier</th>
                                <th>PIC Contact</th>
                                <th>Engagement Rate</th>
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit KOL</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editKolForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit-kol-id" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Username *</label>
                                    <input type="text" class="form-control" id="edit-username" name="username" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" class="form-control" id="edit-name" name="name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Channel *</label>
                                    <select class="form-control" id="edit-channel" name="channel" required>
                                        @foreach($channels as $channel)
                                            <option value="{{ $channel }}">{{ ucfirst(str_replace('_', ' ', $channel)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Niche</label>
                                    <select class="form-control" id="edit-niche" name="niche">
                                        <option value="">Select Niche</option>
                                        @foreach($niches as $niche)
                                            <option value="{{ $niche }}">{{ ucfirst($niche) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Content Type</label>
                                    <select class="form-control" id="edit-content-type" name="content_type">
                                        <option value="">Select Content Type</option>
                                        @foreach($contentTypes as $contentType)
                                            <option value="{{ $contentType }}">{{ ucfirst($contentType) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="text" class="form-control" id="edit-phone" name="phone_number">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Rate</label>
                                    <input type="number" class="form-control" id="edit-rate" name="rate" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Average View</label>
                                    <input type="number" class="form-control" id="edit-average-view" name="average_view" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>PIC Contact</label>
                                    <select class="form-control" id="edit-pic-contact" name="pic_contact">
                                        @foreach($marketingUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Category</label>
                                    <input type="text" class="form-control" id="edit-category" name="category">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Price Per Slot</label>
                                    <input type="number" class="form-control" id="edit-price-per-slot" name="price_per_slot" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>GMV</label>
                                    <input type="number" class="form-control" id="edit-gmv" name="gmv" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea class="form-control" id="edit-address" name="address" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update KOL</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
    <style>
        .small-box h3 {
            font-size: 2.2rem;
        }
        .badge {
            font-size: 0.8em;
        }
    </style>
@stop

@section('js')
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
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
                d.followersMin = $('#filter-followers-min').val();
                d.followersMax = $('#filter-followers-max').val();
            }
        },
        columns: [
            { data: 'username', name: 'username' },
            { data: 'name', name: 'name' },
            { data: 'channel', name: 'channel' },
            { data: 'niche', name: 'niche' },
            { data: 'followers', name: 'followers' },
            { data: 'rate', name: 'rate' },
            { data: 'cpm_display', name: 'cpm', orderable: false },
            { data: 'status_recommendation_display', name: 'status_recommendation', orderable: false },
            { data: 'tier_display', name: 'tier', orderable: false },
            { data: 'pic_contact_name', name: 'pic_contact_name' },
            { data: 'engagement_rate_display', name: 'engagement_rate' },
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
            followersMin: $('#filter-followers-min').val(),
            followersMax: $('#filter-followers-max').val()
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

    // Refresh follower data
    $(document).on('click', '.refresh-follower', function() {
        const username = $(this).data('id');
        const btn = $(this);
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.get(`{{ url('admin/kol/refresh-single') }}/${username}`)
            .done(function(response) {
                toastr.success('Follower data refreshed successfully');
                kolTable.ajax.reload(null, false);
            })
            .fail(function(xhr) {
                toastr.error('Failed to refresh follower data');
            })
            .always(function() {
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i>');
            });
    });

    // Open edit modal
    window.openEditModal = function(kolId) {
        @can('updateKOL', App\Domain\Campaign\Models\KeyOpinionLeader::class)
        $.get(`{{ url('admin/kol') }}/${kolId}/edit-data`)
            .done(function(data) {
                $('#edit-kol-id').val(data.id);
                $('#edit-username').val(data.username);
                $('#edit-name').val(data.name);
                $('#edit-channel').val(data.channel);
                $('#edit-niche').val(data.niche);
                $('#edit-content-type').val(data.content_type);
                $('#edit-phone').val(data.phone_number);
                $('#edit-rate').val(data.rate);
                $('#edit-average-view').val(data.average_view);
                $('#edit-pic-contact').val(data.pic_contact);
                $('#edit-category').val(data.category);
                $('#edit-price-per-slot').val(data.price_per_slot);
                $('#edit-gmv').val(data.gmv);
                $('#edit-address').val(data.address);
                
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
        const formData = $(this).serialize();
        
        $.ajax({
            url: `{{ url('admin/kol') }}/${kolId}`,
            method: 'PUT',
            data: formData,
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
            toastr.error(response?.message || 'Failed to update KOL');
        });
    });

    // Delete KOL
    window.deleteKol = function(kolId) {
        @can('deleteKOL', App\Domain\Campaign\Models\KeyOpinionLeader::class)
        if (confirm('Are you sure you want to delete this KOL?')) {
            $.ajax({
                url: `{{ url('admin/kol') }}/${kolId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            .done(function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    kolTable.ajax.reload();
                    loadKpiData();
                } else {
                    toastr.error(response.message || 'Failed to delete KOL');
                }
            })
            .fail(function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response?.message || 'Failed to delete KOL');
            });
        }
        @else
        toastr.error('You do not have permission to delete KOLs');
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