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
                                    <option value="{{ $niche->name }}" {{ old('niche') == $niche->name ? 'selected' : '' }}>
                                        {{ ucfirst($niche->name) }}
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
            <!-- Video Links for Average View Calculation -->
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-video"></i> Video Links (For Average View Calculation)
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool btn-sm" id="fetch-all-videos-btn">
                                <i class="fas fa-sync-alt"></i> Fetch All Video Data
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @for($i = 1; $i <= 10; $i++)
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="video_link_{{ $i }}">Video Link {{ $i }}</label>
                                    <div class="input-group">
                                        <input type="url" 
                                               class="form-control video-link" 
                                               id="video_link_{{ $i }}" 
                                               name="video_links[]" 
                                               placeholder="https://tiktok.com/@username/video/123..."
                                               data-index="{{ $i }}">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary fetch-single-video" data-index="{{ $i }}">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text">
                                        <span class="video-status-{{ $i }} text-muted">Not fetched</span>
                                        <span class="video-views-{{ $i }} text-info" style="display: none;"></span>
                                    </small>
                                </div>
                            </div>
                            @endfor
                        </div>
                        
                        <!-- Video Stats Summary -->
                        <div class="alert alert-secondary" id="video-stats" style="display: none;">
                            <h6><i class="fas fa-chart-bar"></i> Video Statistics Summary</h6>
                            <div class="row">
                                <div class="col-3">
                                    <strong>Total Videos:</strong> <span id="total-videos">0</span>
                                </div>
                                <div class="col-3">
                                    <strong>Total Views:</strong> <span id="total-views">0</span>
                                </div>
                                <div class="col-3">
                                    <strong>Average Views:</strong> <span id="calculated-average">0</span>
                                </div>
                                <div class="col-3">
                                    <strong>Status:</strong> <span id="fetch-status" class="badge badge-info">Ready</span>
                                </div>
                            </div>
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
                            </div>
                            
                            <div class="col-md-6">
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
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
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
                            </div>
                            
                            <div class="col-md-6">
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
    </style>
@stop

@section('js')
<script>
$(document).ready(function() {
    let videoData = {};
    
    // CPM Calculation
    function calculateCPM() {
        const rate = parseFloat($('#rate').val()) || 0;
        const avgView = parseFloat($('#average_view').val()) || 0;
        
        if (rate > 0 && avgView > 0) {
            const cpm = (rate / avgView) * 1000;
            const status = cpm < 25000 ? 'Worth it' : 'Gagal';
            const statusClass = cpm < 25000 ? 'badge badge-success' : 'badge badge-danger';
            
            $('#cpm-value').text(cpm.toLocaleString('id-ID', { maximumFractionDigits: 2 }));
            $('#cpm-status-badge').html(`<span class="${statusClass}">${status}</span>`);
            $('#cpm-preview').show();
        } else {
            $('#cpm-preview').hide();
        }
    }

    // Calculate average views from video data
    function calculateAverageViews() {
        const validVideos = Object.values(videoData).filter(data => data && data.views > 0);
        
        if (validVideos.length > 0) {
            const totalViews = validVideos.reduce((sum, data) => sum + data.views, 0);
            const averageViews = Math.round(totalViews / validVideos.length);
            
            $('#average_view').val(averageViews);
            $('#total-videos').text(validVideos.length);
            $('#total-views').text(totalViews.toLocaleString());
            $('#calculated-average').text(averageViews.toLocaleString());
            $('#video-stats').show();
            
            calculateCPM();
        } else {
            $('#average_view').val(0);
            $('#video-stats').hide();
        }
    }

    // Fetch single video data
    function fetchSingleVideo(index) {
        const videoLink = $(`#video_link_${index}`).val();
        if (!videoLink) {
            toastr.warning('Please enter video link first');
            return;
        }

        const btn = $(`.fetch-single-video[data-index="${index}"]`);
        const statusSpan = $(`.video-status-${index}`);
        const viewsSpan = $(`.video-views-${index}`);
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        statusSpan.text('Fetching...').removeClass('text-muted text-success text-danger').addClass('text-info');

        // Extract video ID from TikTok URL
        const videoId = extractVideoId(videoLink);
        if (!videoId) {
            statusSpan.text('Invalid URL').removeClass('text-info').addClass('text-danger');
            btn.prop('disabled', false).html('<i class="fas fa-download"></i>');
            return;
        }

        // API call to fetch video data
        $.get(`{{ url('admin/kol/fetch-video-data') }}/${videoId}`)
            .done(function(response) {
                if (response.success && response.data) {
                    videoData[index] = {
                        views: response.data.views,
                        likes: response.data.likes,
                        comments: response.data.comments
                    };
                    
                    statusSpan.text('Success').removeClass('text-info').addClass('text-success');
                    viewsSpan.text(`Views: ${response.data.views.toLocaleString()}`).show();
                    
                    calculateAverageViews();
                } else {
                    statusSpan.text('Failed to fetch').removeClass('text-info').addClass('text-danger');
                }
            })
            .fail(function() {
                statusSpan.text('Error occurred').removeClass('text-info').addClass('text-danger');
            })
            .always(function() {
                btn.prop('disabled', false).html('<i class="fas fa-download"></i>');
            });
    }

    // Extract video ID from TikTok URL
    function extractVideoId(url) {
        const patterns = [
            /tiktok\.com\/@[^\/]+\/video\/(\d+)/,
            /vm\.tiktok\.com\/([A-Za-z0-9]+)/,
            /tiktok\.com\/t\/([A-Za-z0-9]+)/
        ];
        
        for (let pattern of patterns) {
            const match = url.match(pattern);
            if (match) return match[1];
        }
        return null;
    }

    // Auto-calculate on input change
    $('#rate, #average_view').on('input', calculateCPM);
    $('#calculate-cpm-btn').click(calculateCPM);

    // Fetch single video data
    $('.fetch-single-video').click(function() {
        const index = $(this).data('index');
        fetchSingleVideo(index);
    });

    // Fetch all video data
    $('#fetch-all-videos-btn').click(function() {
        const btn = $(this);
        const videoLinks = $('.video-link').filter(function() {
            return $(this).val().trim() !== '';
        });

        if (videoLinks.length === 0) {
            toastr.warning('Please enter at least one video link');
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Fetching All...');
        $('#fetch-status').removeClass('badge-info badge-success badge-danger').addClass('badge-warning').text('Fetching...');

        let completed = 0;
        const total = videoLinks.length;

        videoLinks.each(function() {
            const index = $(this).data('index');
            const videoLink = $(this).val();
            
            if (videoLink) {
                setTimeout(() => {
                    fetchSingleVideo(index);
                    completed++;
                    
                    if (completed === total) {
                        btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Fetch All Video Data');
                        $('#fetch-status').removeClass('badge-warning').addClass('badge-success').text('Completed');
                    }
                }, index * 500); // Stagger requests to avoid rate limiting
            }
        });
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