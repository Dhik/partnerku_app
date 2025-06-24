<!-- Create KOL Modal -->
<div class="modal fade" id="createKolModal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">
                    <i class="fas fa-user-plus"></i> Create New Key Opinion Leader
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createKolForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-user"></i> Basic Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="create-username">Username <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="create-username" name="username" required>
                                        <small class="form-text text-muted">Username without @ symbol</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="create-name">Full Name</label>
                                        <input type="text" class="form-control" id="create-name" name="name">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="create-phone">Phone Number</label>
                                        <input type="text" class="form-control" id="create-phone" name="phone_number" placeholder="e.g., 08123456789">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="create-channel">Channel <span class="text-danger">*</span></label>
                                        <select class="form-control" id="create-channel" name="channel" required>
                                            <option value="">Select Channel</option>
                                            @foreach($channels as $channel)
                                                <option value="{{ $channel }}">{{ ucfirst(str_replace('_', ' ', $channel)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="create-niche">Niche</label>
                                        <select class="form-control" id="create-niche" name="niche">
                                            <option value="">Select Niche</option>
                                            @foreach($niches as $niche)
                                                <option value="{{ $niche }}">{{ ucfirst($niche) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="create-content-type">Content Type</label>
                                        <select class="form-control" id="create-content-type" name="content_type">
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
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-chart-line"></i> Financial & Performance
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="create-rate">Rate per Content</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="number" class="form-control" id="create-rate" name="rate" min="0" step="1000">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="create-average-view">Average Views (Last 10 Videos)</label>
                                        <input type="number" class="form-control" id="create-average-view" name="average_view" min="0">
                                        <small class="form-text text-muted">Used for CPM calculation</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="create-price-per-slot">Price per Slot</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="number" class="form-control" id="create-price-per-slot" name="price_per_slot" min="0">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="create-gmv">GMV (Gross Merchandise Value)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="number" class="form-control" id="create-gmv" name="gmv" min="0">
                                        </div>
                                    </div>
                                    
                                    <!-- CPM Preview -->
                                    <div class="alert alert-info" id="cpm-preview" style="display: none;">
                                        <h6><i class="fas fa-calculator"></i> CPM Preview</h6>
                                        <p class="mb-1">Calculated CPM: <span id="cpm-value">0</span></p>
                                        <p class="mb-0">Status: <span id="cpm-status" class="badge">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <!-- Contact & Management -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-address-book"></i> Contact & Management
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="create-pic-contact">PIC Contact</label>
                                        <select class="form-control" id="create-pic-contact" name="pic_contact">
                                            <option value="">Select PIC</option>
                                            @foreach($marketingUsers as $user)
                                                <option value="{{ $user->id }}" {{ Auth::id() == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="create-pic-listing">PIC Listing</label>
                                        <input type="text" class="form-control" id="create-pic-listing" name="pic_listing">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="create-pic-content">PIC Content</label>
                                        <input type="text" class="form-control" id="create-pic-content" name="pic_content">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="create-address">Address</label>
                                        <textarea class="form-control" id="create-address" name="address" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-info-circle"></i> Additional Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="create-category">Category</label>
                                        <input type="text" class="form-control" id="create-category" name="category">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="create-tier">Tier</label>
                                        <select class="form-control" id="create-tier" name="tier">
                                            <option value="">Auto-calculated based on followers</option>
                                            <option value="Nano">Nano (1K - 10K)</option>
                                            <option value="Micro">Micro (10K - 50K)</option>
                                            <option value="Mid-Tier">Mid-Tier (50K - 250K)</option>
                                            <option value="Macro">Macro (250K - 1M)</option>
                                            <option value="Mega">Mega (1M+)</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="create-link">Profile Link</label>
                                        <input type="url" class="form-control" id="create-link" name="link" placeholder="https://...">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="create-status-recommendation">Status Recommendation</label>
                                        <select class="form-control" id="create-status-recommendation" name="status_recommendation">
                                            <option value="">Auto-calculated</option>
                                            <option value="Worth it">Worth it</option>
                                            <option value="Gagal">Gagal</option>
                                        </select>
                                        <small class="form-text text-muted">Will be auto-calculated based on CPM if left empty</small>
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
                    <button type="button" class="btn btn-info" id="calculateCpmBtn">
                        <i class="fas fa-calculator"></i> Calculate CPM
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create KOL
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// CPM Calculation for Create Modal
$('#calculateCpmBtn').click(function() {
    const rate = parseFloat($('#create-rate').val()) || 0;
    const avgView = parseFloat($('#create-average-view').val()) || 0;
    
    if (rate > 0 && avgView > 0) {
        const cpm = (rate / avgView) * 1000;
        const status = cmp < 25000 ? 'Worth it' : 'Gagal';
        const statusClass = cpm < 25000 ? 'badge-success' : 'badge-danger';
        
        $('#cpm-value').text(cpm.toLocaleString('id-ID', { maximumFractionDigits: 2 }));
        $('#cpm-status').removeClass('badge-success badge-danger').addClass(statusClass).text(status);
        $('#cpm-preview').show();
    } else {
        toastr.warning('Please enter both Rate and Average View to calculate CPM');
    }
});

// Auto-calculate on input change
$('#create-rate, #create-average-view').on('input', function() {
    const rate = parseFloat($('#create-rate').val()) || 0;
    const avgView = parseFloat($('#create-average-view').val()) || 0;
    
    if (rate > 0 && avgView > 0) {
        $('#calculateCpmBtn').trigger('click');
    }
});

// Submit Create Form
$('#createKolForm').submit(function(e) {
    e.preventDefault();
    
    const submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');
    
    $.ajax({
        url: '{{ route("kol.store") }}',
        method: 'POST',
        data: $(this).serialize(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done(function(response) {
        toastr.success('KOL created successfully!');
        $('#createKolModal').modal('hide');
        $('#createKolForm')[0].reset();
        $('#cpm-preview').hide();
        
        // Reload table and KPI
        if (typeof kolTable !== 'undefined') {
            kolTable.ajax.reload();
        }
        if (typeof loadKpiData === 'function') {
            loadKpiData();
        }
    })
    .fail(function(xhr) {
        const response = xhr.responseJSON;
        if (response && response.errors) {
            let errorMsg = '';
            Object.keys(response.errors).forEach(function(key) {
                errorMsg += response.errors[key].join('<br>') + '<br>';
            });
            toastr.error(errorMsg);
        } else {
            toastr.error(response?.message || 'Failed to create KOL');
        }
    })
    .always(function() {
        submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Create KOL');
    });
});

// Reset form when modal is closed
$('#createKolModal').on('hidden.bs.modal', function() {
    $('#createKolForm')[0].reset();
    $('#cpm-preview').hide();
});
</script>