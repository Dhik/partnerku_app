@extends('adminlte::page')

@section('title', 'Create New KOL')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Create New Key Opinion Leader</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('kol.index') }}">KOL Management</a></li>
                <li class="breadcrumb-item active">Create New KOL</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <form id="createKolForm" action="{{ route('kol.store') }}" method="POST">
        @csrf
        
        <!-- Progress Steps -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-plus"></i> KOL Registration Form
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" id="calculate-cpm-btn">
                                <i class="fas fa-calculator"></i> Calculate CPM
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                            <label for="username">Username <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">@</span>
                                </div>
                                <input type="text" 
                                       class="form-control @error('username') is-invalid @enderror" 
                                       id="username" 
                                       name="username" 
                                       value="{{ old('username') }}" 
                                       required
                                       placeholder="Enter username without @">
                            </div>
                            @error('username')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Username without @ symbol</small>
                        </div>

                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   placeholder="Enter full name">
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input type="text" 
                                   class="form-control @error('phone_number') is-invalid @enderror" 
                                   id="phone_number" 
                                   name="phone_number" 
                                   value="{{ old('phone_number') }}"
                                   placeholder="e.g., 08123456789">
                            @error('phone_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="channel">Channel <span class="text-danger">*</span></label>
                            <select class="form-control @error('channel') is-invalid @enderror" 
                                    id="channel" 
                                    name="channel" 
                                    required>
                                <option value="">Select Channel</option>
                                @foreach($channels as $channel)
                                    <option value="{{ $channel }}" {{ old('channel') == $channel ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $channel)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('channel')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="niche">Niche</label>
                            <select class="form-control @error('niche') is-invalid @enderror" 
                                    id="niche" 
                                    name="niche">
                                <option value="">Select Niche</option>
                                @foreach($niches as $niche)
                                    <option value="{{ $niche }}" {{ old('niche') == $niche ? 'selected' : '' }}>
                                        {{ ucfirst($niche) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('niche')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="content_type">Content Type</label>
                            <select class="form-control @error('content_type') is-invalid @enderror" 
                                    id="content_type" 
                                    name="content_type">
                                <option value="">Select Content Type</option>
                                @foreach($contentTypes as $contentType)
                                    <option value="{{ $contentType }}" {{ old('content_type') == $contentType ? 'selected' : '' }}>
                                        {{ ucfirst($contentType) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('content_type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
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
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="rate">Rate per Content</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" 
                                       class="form-control @error('rate') is-invalid @enderror" 
                                       id="rate" 
                                       name="rate" 
                                       value="{{ old('rate') }}"
                                       min="0" 
                                       step="1000"
                                       placeholder="0">
                            </div>
                            @error('rate')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="average_view">Average Views (Last 10 Videos)</label>
                            <input type="number" 
                                   class="form-control @error('average_view') is-invalid @enderror" 
                                   id="average_view" 
                                   name="average_view" 
                                   value="{{ old('average_view') }}"
                                   min="0"
                                   placeholder="0">
                            @error('average_view')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Used for CPM calculation</small>
                        </div>

                        <div class="form-group">
                            <label for="price_per_slot">Price per Slot</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" 
                                       class="form-control @error('price_per_slot') is-invalid @enderror" 
                                       id="price_per_slot" 
                                       name="price_per_slot" 
                                       value="{{ old('price_per_slot') }}"
                                       min="0"
                                       placeholder="0">
                            </div>
                            @error('price_per_slot')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="gmv">GMV (Gross Merchandise Value)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" 
                                       class="form-control @error('gmv') is-invalid @enderror" 
                                       id="gmv" 
                                       name="gmv" 
                                       value="{{ old('gmv') }}"
                                       min="0"
                                       placeholder="0">
                            </div>
                            @error('gmv')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- CPM Preview Card -->
                        <div class="alert alert-info" id="cpm-preview" style="display: none;">
                            <h6><i class="fas fa-calculator"></i> CPM Calculation Preview</h6>
                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-1"><strong>Calculated CPM:</strong></p>
                                    <h4 class="text-primary" id="cpm-value">0</h4>
                                </div>
                                <div class="col-6">
                                    <p class="mb-1"><strong>Recommendation:</strong></p>
                                    <h4 id="cpm-status-badge">-</h4>
                                </div>
                            </div>
                            <small class="text-muted">Formula: (Rate ÷ Average Views) × 1000</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Contact & Management -->
            <div class="col-md-6">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-address-book"></i> Contact & Management
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="pic_contact">PIC Contact</label>
                            <select class="form-control @error('pic_contact') is-invalid @enderror" 
                                    id="pic_contact" 
                                    name="pic_contact">
                                <option value="">Select PIC</option>
                                @foreach($marketingUsers as $user)
                                    <option value="{{ $user->id }}" 
                                            {{ (old('pic_contact', Auth::id()) == $user->id) ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pic_contact')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="pic_listing">PIC Listing</label>
                            <input type="text" 
                                   class="form-control @error('pic_listing') is-invalid @enderror" 
                                   id="pic_listing" 
                                   name="pic_listing" 
                                   value="{{ old('pic_listing') }}"
                                   placeholder="Person in charge of listing">
                            @error('pic_listing')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="pic_content">PIC Content</label>
                            <input type="text" 
                                   class="form-control @error('pic_content') is-invalid @enderror" 
                                   id="pic_content" 
                                   name="pic_content" 
                                   value="{{ old('pic_content') }}"
                                   placeholder="Person in charge of content">
                            @error('pic_content')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" 
                                      name="address" 
                                      rows="3"
                                      placeholder="Enter complete address">{{ old('address') }}</textarea>
                            @error('address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Additional Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <input type="text" 
                                   class="form-control @error('category') is-invalid @enderror" 
                                   id="category" 
                                   name="category" 
                                   value="{{ old('category') }}"
                                   placeholder="e.g., Beauty, Fashion, Tech">
                            @error('category')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="tier">Tier</label>
                            <select class="form-control @error('tier') is-invalid @enderror" 
                                    id="tier" 
                                    name="tier">
                                <option value="">Auto-calculated based on followers</option>
                                <option value="Nano" {{ old('tier') == 'Nano' ? 'selected' : '' }}>Nano (1K - 10K)</option>
                                <option value="Micro" {{ old('tier') == 'Micro' ? 'selected' : '' }}>Micro (10K - 50K)</option>
                                <option value="Mid-Tier" {{ old('tier') == 'Mid-Tier' ? 'selected' : '' }}>Mid-Tier (50K - 250K)</option>
                                <option value="Macro" {{ old('tier') == 'Macro' ? 'selected' : '' }}>Macro (250K - 1M)</option>
                                <option value="Mega" {{ old('tier') == 'Mega' ? 'selected' : '' }}>Mega (1M+)</option>
                            </select>
                            @error('tier')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="link">Profile Link</label>
                            <input type="url" 
                                   class="form-control @error('link') is-invalid @enderror" 
                                   id="link" 
                                   name="link" 
                                   value="{{ old('link') }}"
                                   placeholder="https://...">
                            @error('link')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="status_recommendation">Status Recommendation</label>
                            <select class="form-control @error('status_recommendation') is-invalid @enderror" 
                                    id="status_recommendation" 
                                    name="status_recommendation">
                                <option value="">Auto-calculated based on CPM</option>
                                <option value="Worth it" {{ old('status_recommendation') == 'Worth it' ? 'selected' : '' }}>Worth it</option>
                                <option value="Gagal" {{ old('status_recommendation') == 'Gagal' ? 'selected' : '' }}>Gagal</option>
                            </select>
                            @error('status_recommendation')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Will be auto-calculated if left empty</small>
                        </div>

                        <!-- Quick Actions -->
                        <div class="form-group">
                            <label>Quick Actions</label>
                            <div class="btn-group btn-group-sm d-block">
                                <button type="button" class="btn btn-outline-primary" id="fetch-profile-btn">
                                    <i class="fas fa-download"></i> Fetch Profile Data
                                </button>
                                <button type="button" class="btn btn-outline-success" id="validate-username-btn">
                                    <i class="fas fa-check"></i> Validate Username
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-6">
                                <a href="{{ route('kol.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                            <div class="col-sm-6 text-right">
                                <button type="button" class="btn btn-info" id="preview-btn">
                                    <i class="fas fa-eye"></i> Preview
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create KOL
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">KOL Preview</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="preview-content">
                    <!-- Preview content will be populated here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="$('#createKolForm').submit()">
                        <i class="fas fa-save"></i> Confirm & Create
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
        .alert {
            border-radius: 0.375rem;
        }
        #cpm-preview {
            border-left: 4px solid #007bff;
        }
        .btn-group-sm .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .form-group label {
            font-weight: 600;
        }
        .card-header {
            border-bottom: 1px solid rgba(0,0,0,.125);
        }
    </style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // CPM Calculation
    function calculateCPM() {
        const rate = parseFloat($('#rate').val()) || 0;
        const avgView = parseFloat($('#average_view').val()) || 0;
        
        if (rate > 0 && avgView > 0) {
            const cpm = (rate / avgView) * 1000;
            const status = cpm < 25000 ? 'Worth it' : 'Gagal';
            const statusClass = cmp < 25000 ? 'badge badge-success' : 'badge badge-danger';
            
            $('#cpm-value').text(cpm.toLocaleString('id-ID', { maximumFractionDigits: 2 }));
            $('#cpm-status-badge').html(`<span class="${statusClass}">${status}</span>`);
            $('#cmp-preview').show();
            
            // Auto-select status if not manually set
            if (!$('#status_recommendation').val()) {
                $('#status_recommendation').val(status);
            }
        } else {
            $('#cmp-preview').hide();
        }
    }

    // Auto-calculate on input change
    $('#rate, #average_view').on('input', calculateCPM);
    $('#calculate-cmp-btn').click(calculateCPM);

    // Validate username
    $('#validate-username-btn').click(function() {
        const username = $('#username').val();
        if (!username) {
            toastr.warning('Please enter username first');
            return;
        }
        
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Validating...');
        
        // Simulate validation (replace with actual API call)
        setTimeout(() => {
            toastr.success('Username is available!');
            btn.prop('disabled', false).html('<i class="fas fa-check"></i> Validate Username');
        }, 1000);
    });

    // Fetch profile data
    $('#fetch-profile-btn').click(function() {
        const username = $('#username').val();
        const channel = $('#channel').val();
        
        if (!username || !channel) {
            toastr.warning('Please enter username and select channel first');
            return;
        }
        
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Fetching...');
        
        $.get(`{{ url('admin/kol/refresh-single') }}/${username}`)
            .done(function(response) {
                toastr.success('Profile data fetched successfully');
                // You can populate fields with fetched data here
                if (response.followers) {
                    toastr.info(`Followers: ${response.followers.toLocaleString()}`);
                }
            })
            .fail(function() {
                toastr.error('Failed to fetch profile data');
            })
            .always(function() {
                btn.prop('disabled', false).html('<i class="fas fa-download"></i> Fetch Profile Data');
            });
    });

    // Preview functionality
    $('#preview-btn').click(function() {
        const formData = $('#createKolForm').serializeArray();
        let previewHtml = '<div class="row">';
        
        // Basic Info
        previewHtml += '<div class="col-md-6"><h6>Basic Information</h6><table class="table table-sm">';
        previewHtml += `<tr><td><strong>Username:</strong></td><td>@${$('#username').val() || '-'}</td></tr>`;
        previewHtml += `<tr><td><strong>Name:</strong></td><td>${$('#name').val() || '-'}</td></tr>`;
        previewHtml += `<tr><td><strong>Channel:</strong></td><td>${$('#channel option:selected').text() || '-'}</td></tr>`;
        previewHtml += `<tr><td><strong>Niche:</strong></td><td>${$('#niche option:selected').text() || '-'}</td></tr>`;
        previewHtml += '</table></div>';
        
        // Financial Info
        previewHtml += '<div class="col-md-6"><h6>Financial Information</h6><table class="table table-sm">';
        previewHtml += `<tr><td><strong>Rate:</strong></td><td>Rp ${$('#rate').val() ? parseInt($('#rate').val()).toLocaleString() : '0'}</td></tr>`;
        previewHtml += `<tr><td><strong>Average Views:</strong></td><td>${$('#average_view').val() ? parseInt($('#average_view').val()).toLocaleString() : '0'}</td></tr>`;
        previewHtml += `<tr><td><strong>CPM:</strong></td><td>${$('#cmp-value').text() || 'Not calculated'}</td></tr>`;
        previewHtml += `<tr><td><strong>Status:</strong></td><td>${$('#status_recommendation').val() || 'Auto-calculated'}</td></tr>`;
        previewHtml += '</table></div>';
        
        previewHtml += '</div>';
        
        $('#preview-content').html(previewHtml);
        $('#previewModal').modal('show');
    });

    // Form submission with loading state
    $('#createKolForm').submit(function() {
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');
    });

    // Auto-calculate CPM on page load if values exist
    if ($('#rate').val() && $('#average_view').val()) {
        calculateCPM();
    }
});
</script>
@stop