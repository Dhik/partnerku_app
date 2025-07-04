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
    <!-- Debug Information -->
    @if (session('alert'))
        <div class="alert alert-{{ session('alert') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ session('message') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <h5><i class="icon fas fa-ban"></i> Validation Errors!</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            <h5><i class="icon fas fa-ban"></i> Error!</h5>
            {{ session('error') }}
        </div>
    @endif

    <!-- Debug Data -->
    @if (old())
        <div class="alert alert-info">
            <h6><i class="fas fa-info-circle"></i> Debug - Old Input Data:</h6>
            <pre>{{ print_r(old(), true) }}</pre>
        </div>
    @endif

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
                                @if(isset($niches))
                                    @foreach($niches as $niche)
                                        <option value="{{ $niche->name }}" {{ old('niche') == $niche->name ? 'selected' : '' }}>
                                            {{ ucfirst($niche->name) }}
                                        </option>
                                    @endforeach
                                @endif
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
                                @if(isset($contentTypes))
                                    @foreach($contentTypes as $contentType)
                                        <option value="{{ $contentType }}" {{ old('content_type') == $contentType ? 'selected' : '' }}>
                                            {{ ucfirst($contentType) }}
                                        </option>
                                    @endforeach
                                @endif
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
                            <label for="rate">Rate per Content/Slot</label>
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
                            <small class="form-text text-muted">This rate will be used for both content and slot pricing</small>
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
                            <small class="text-muted">Formula: (Rate ÷ 1) × 1000</small>
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
                                    <label for="video_link_{{ $i }}">Video Link {{ $i }}</label>
                                    <input type="url" 
                                           class="form-control video-link @error('video_10_links.'.$i-1) is-invalid @enderror" 
                                           id="video_link_{{ $i }}" 
                                           name="video_10_links[]" 
                                           value="{{ old('video_10_links.'.($i-1)) }}"
                                           placeholder="https://tiktok.com/@username/video/123...">
                                    @error('video_10_links.'.($i-1))
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
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

        <div class="row">
            <!-- Contact & Management -->
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
                                    <label for="pic_contact">PIC Contact <span class="text-danger">*</span></label>
                                    <select class="form-control @error('pic_contact') is-invalid @enderror" 
                                            id="pic_contact" 
                                            name="pic_contact"
                                            required>
                                        <option value="">Select PIC Contact</option>
                                        @if(isset($marketingUsers))
                                            @foreach($marketingUsers as $user)
                                                <option value="{{ $user->id }}" 
                                                        {{ (old('pic_contact', Auth::id()) == $user->id) ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('pic_contact')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pic_listing">PIC Listing</label>
                                    <select class="form-control @error('pic_listing') is-invalid @enderror" 
                                            id="pic_listing" 
                                            name="pic_listing">
                                        <option value="">Select PIC Listing</option>
                                        @if(isset($marketingUsers))
                                            @foreach($marketingUsers as $user)
                                                <option value="{{ $user->id }}" 
                                                        {{ old('pic_listing') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('pic_listing')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
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
                                <button type="submit" class="btn btn-primary" id="submit-btn">
                                    <i class="fas fa-save"></i> Create KOL
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
        .form-group label {
            font-weight: 600;
        }
        .card-header {
            border-bottom: 1px solid rgba(0,0,0,.125);
        }
        .video-link {
            font-size: 0.875rem;
        }
        .fetch-single-video {
            min-width: 40px;
        }
        #video-stats {
            margin-top: 15px;
        }
        pre {
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
        }

        /* Loading Overlay Styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        .loading-text {
            color: white;
            font-size: 18px;
            font-weight: 500;
            text-align: center;
            margin-bottom: 10px;
        }

        .loading-steps {
            color: #ccc;
            font-size: 14px;
            text-align: center;
            max-width: 300px;
        }

        .loading-step {
            padding: 2px 0;
            opacity: 0.6;
            transition: all 0.3s ease;
        }

        .loading-step.active {
            opacity: 1;
            color: #28a745;
        }

        .loading-step.completed {
            opacity: 0.8;
            color: #6c757d;
        }

        .loading-step i {
            margin-right: 8px;
            width: 16px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }

        .loading-step.active i {
            animation: pulse 1.5s infinite;
        }

        /* Form disabled state */
        .form-disabled {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn-loading {
            position: relative;
            pointer-events: none;
        }

        .btn-loading .btn-text {
            opacity: 0;
        }

        .btn-loading .btn-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
@stop

@section('js')
<script>
$(document).ready(function() {
    
    // CPM Calculation (with average_view = 1)
    function calculateCPM() {
        const rate = parseFloat($('#rate').val()) || 0;
        const avgView = 1; // Fixed value as per requirement
        
        if (rate > 0) {
            const cpm = (rate / avgView) * 1000;
            const status = cpm < 25000 ? 'Worth it' : 'Gagal';
            const statusClass = cpm < 25000 ? 'badge badge-success' : 'badge badge-danger';
            
            $('#cpm-value').text('Rp ' + cpm.toLocaleString('id-ID', { maximumFractionDigits: 2 }));
            $('#cpm-status-badge').html(`<span class="${statusClass}">${status}</span>`);
            $('#cpm-preview').show();
        } else {
            $('#cpm-preview').hide();
        }
    }

    // Auto-calculate on input change
    $('#rate').on('input', calculateCPM);
    $('#calculate-cpm-btn').click(calculateCPM);

    // Create loading overlay
    function createLoadingOverlay() {
        const overlay = $(`
            <div class="loading-overlay" id="loadingOverlay">
                <div class="loading-spinner"></div>
                <div class="loading-text">Creating KOL Profile...</div>
                <div class="loading-steps">
                    <div class="loading-step active" id="step1">
                        <i class="fas fa-database"></i>Saving KOL data...
                    </div>
                    <div class="loading-step" id="step2">
                        <i class="fas fa-users"></i>Refreshing follower statistics...
                    </div>
                    <div class="loading-step" id="step3">
                        <i class="fas fa-video"></i>Processing video links...
                    </div>
                    <div class="loading-step" id="step4">
                        <i class="fas fa-check-circle"></i>Finalizing setup...
                    </div>
                </div>
            </div>
        `);
        $('body').append(overlay);
    }

    // Show loading animation with steps
    function showLoading() {
        $('#loadingOverlay').css('display', 'flex');
        $('body').addClass('form-disabled');
        
        // Simulate loading steps
        setTimeout(() => {
            $('#step1').removeClass('active').addClass('completed');
            $('#step2').addClass('active');
        }, 1500);

        setTimeout(() => {
            $('#step2').removeClass('active').addClass('completed');
            $('#step3').addClass('active');
        }, 3000);

        setTimeout(() => {
            $('#step3').removeClass('active').addClass('completed');
            $('#step4').addClass('active');
        }, 5000);
    }

    // Hide loading animation
    function hideLoading() {
        $('#loadingOverlay').fadeOut(300, function() {
            $(this).remove();
        });
        $('body').removeClass('form-disabled');
    }

    // Track if form is being submitted legitimately
    let isFormSubmitting = false;

    // Form validation and submission
    $('#createKolForm').submit(function(e) {
        console.log('Form submitted');
        
        let isValid = true;
        let errorMessages = [];

        // Check required fields
        if (!$('#username').val().trim()) {
            isValid = false;
            errorMessages.push('Username is required');
        }

        if (!$('#channel').val()) {
            isValid = false;
            errorMessages.push('Channel is required');
        }

        if (!$('#pic_contact').val()) {
            isValid = false;
            errorMessages.push('PIC Contact is required');
        }

        if (!isValid) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n' + errorMessages.join('\n'));
            return false;
        }

        // Mark that we're legitimately submitting
        isFormSubmitting = true;

        // Update submit button first (before overlay to prevent interference)
        const submitBtn = $('#submit-btn');
        const originalHtml = submitBtn.html();
        submitBtn.prop('disabled', true).html(`
            <i class="fas fa-spinner fa-spin"></i> Creating KOL...
        `);

        // Show loading animation after a short delay
        setTimeout(() => {
            createLoadingOverlay();
            showLoading();
        }, 100);

        // Log form data for debugging
        const formData = new FormData(this);
        console.log('Form data being submitted:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        
        // Count video links
        const videoLinks = $('.video-link').filter(function() {
            return $(this).val().trim() !== '';
        });
        
        if (videoLinks.length > 0) {
            console.log(`Saving ${videoLinks.length} video link(s)...`);
            
            // Update loading text for video processing
            setTimeout(() => {
                if ($('.loading-text').length) {
                    $('.loading-text').text(`Processing ${videoLinks.length} video link(s)...`);
                }
            }, 3000);
        }

        // Safety timeout - but don't interfere with legitimate redirects
        setTimeout(() => {
            if ($('#loadingOverlay').is(':visible') && !isFormSubmitting) {
                hideLoading();
                submitBtn.prop('disabled', false).html(originalHtml);
                console.warn('Form submission timeout - hiding loading animation');
            }
        }, 45000); // 45 second timeout

        // Allow form to submit normally
        return true;
    });

    // Handle page unload - but only warn if not legitimately submitting
    $(window).on('beforeunload', function(e) {
        // Don't show warning if we're in the middle of a legitimate form submission
        if ($('#loadingOverlay').is(':visible') && !isFormSubmitting) {
            e.preventDefault();
            return 'KOL creation is in progress. Are you sure you want to leave?';
        }
        // If form is submitting, allow navigation without warning
        return undefined;
    });

    // Hide loading when page loads (in case of redirect back)
    $(window).on('load', function() {
        hideLoading();
        isFormSubmitting = false; // Reset the flag
    });

    // Also reset on page visibility change (when user comes back to tab)
    $(document).on('visibilitychange', function() {
        if (!document.hidden) {
            hideLoading();
            isFormSubmitting = false;
        }
    });

    // Auto-calculate CPM on page load if rate exists
    if ($('#rate').val()) {
        calculateCPM();
    }

    // Add some visual feedback for form interactions during normal state
    $('.form-control, .form-select').on('focus', function() {
        $(this).closest('.form-group').addClass('focused');
    }).on('blur', function() {
        $(this).closest('.form-group').removeClass('focused');
    });

    // Video link counter
    $('.video-link').on('input', function() {
        const filledLinks = $('.video-link').filter(function() {
            return $(this).val().trim() !== '';
        }).length;
        
        // Update counter if you want to show it somewhere
        console.log(`Video links filled: ${filledLinks}/10`);
    });
});
</script>
@stop