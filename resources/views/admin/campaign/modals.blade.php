{{-- All Campaign Modals in One File --}}

{{-- Create Content Modal --}}
<div class="modal fade" id="contentModal" tabindex="-1" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('labels.add') }} {{ trans('labels.content') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="contentForm" action="{{ route('campaignContent.store', ['campaignId' => $campaign->id ?? ':campaignId']) }}">
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label for="username">{{ trans('labels.influencer') }}<span class="required">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>

                    <div class="form-group">
                        <label for="taskName">{{ trans('labels.task') }}<span class="required">*</span></label>
                        <input type="text" class="form-control" id="taskName" name="task_name" required>
                    </div>

                    <div class="form-group">
                        <label for="rateCard">{{ trans('labels.rate_card') }}<span class="required">*</span></label>
                        <input type="text" class="form-control" id="rateCard" name="rate_card" required>
                    </div>

                    <div class="form-group">
                        <label for="platform">{{ trans('labels.platform') }}<span class="required">*</span></label>
                        <select class="form-control" id="platform" name="channel" required>
                            <option value="">Select Platform</option>
                            @foreach($platforms ?? [] as $platform)
                                <option value="{{ $platform['value'] }}">{{ $platform['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="link">{{ trans('labels.link') }}</label>
                        <input type="text" class="form-control" id="link" name="link">
                    </div>

                    <div class="form-group">
                        <label for="product">{{ trans('labels.product') }}<span class="required">*</span></label>
                        <input type="text" class="form-control" id="product" name="product" required>
                    </div>

                    <div class="form-group">
                        <label for="boostCode">{{ trans('labels.boost_code') }}</label>
                        <input type="text" class="form-control" id="boostCode" name="boost_code">
                    </div>

                    <div class="form-group">
                        <label for="adsCode">{{ trans('labels.kode_ads') }}</label>
                        <input type="text" class="form-control" id="adsCode" name="kode_ads">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ trans('buttons.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Update Content Modal --}}
<div class="modal fade" id="contentUpdateModal" tabindex="-1" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('labels.update') }} {{ trans('labels.content') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="contentUpdateForm">
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="contentId">
                    
                    <div class="form-group">
                        <label for="usernameUpdate">{{ trans('labels.influencer') }}</label>
                        <input type="text" class="form-control" id="usernameUpdate" readonly>
                    </div>

                    <div class="form-group">
                        <label for="taskNameUpdate">{{ trans('labels.task') }}<span class="required">*</span></label>
                        <input type="text" class="form-control" id="taskNameUpdate" name="task_name" required>
                    </div>

                    <div class="form-group">
                        <label for="rateCardUpdate">{{ trans('labels.rate_card') }}<span class="required">*</span></label>
                        <input type="text" class="form-control" id="rateCardUpdate" name="rate_card" required>
                    </div>

                    <div class="form-group">
                        <label for="platformUpdate">{{ trans('labels.platform') }}<span class="required">*</span></label>
                        <select class="form-control" id="platformUpdate" name="channel" required>
                            @foreach($platforms ?? [] as $platform)
                                <option value="{{ $platform['value'] }}">{{ $platform['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="linkUpdate">{{ trans('labels.link') }}</label>
                        <input type="text" class="form-control" id="linkUpdate" name="link">
                    </div>

                    <div class="form-group">
                        <label for="productUpdate">{{ trans('labels.product') }}<span class="required">*</span></label>
                        <input type="text" class="form-control" id="productUpdate" name="product" required>
                    </div>

                    <div class="form-group">
                        <label for="viewsUpdate">{{ trans('labels.views') }}</label>
                        <input type="number" class="form-control" id="viewsUpdate" name="views">
                    </div>

                    <div class="form-group">
                        <label for="likesUpdate">{{ trans('labels.like') }}</label>
                        <input type="number" class="form-control" id="likesUpdate" name="likes">
                    </div>

                    <div class="form-group">
                        <label for="commentsUpdate">{{ trans('labels.comment') }}</label>
                        <input type="number" class="form-control" id="commentsUpdate" name="comments">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ trans('buttons.update') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Detail Content Modal --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('labels.content') }} Detail</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    {{-- Content Embed --}}
                    <div class="col-md-4">
                        <div id="contentEmbed"></div>
                    </div>
                    
                    {{-- Statistics Info --}}
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-12 col-sm-4">
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-center text-muted">{{ trans('labels.like') }}</span>
                                        <span class="info-box-number text-center text-muted mb-0" id="likeModal">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-center text-muted">{{ trans('labels.comment') }}</span>
                                        <span class="info-box-number text-center text-muted mb-0" id="commentModal">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-center text-muted">{{ trans('labels.views') }}</span>
                                        <span class="info-box-number text-center text-muted mb-0" id="viewModal">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-center text-muted">{{ trans('labels.rate_card') }}</span>
                                        <span class="info-box-number text-center text-muted mb-0" id="rateCardModal">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-center text-muted">{{ trans('labels.upload_date') }}</span>
                                        <span class="info-box-number text-center text-muted mb-0" id="uploadDateModal">-</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-center text-muted">Kode Ads</span>
                                        <span class="info-box-number text-center text-muted mb-0" id="kodeAdsModal">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Statistics Chart --}}
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Statistics Over Time</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="statisticDetailChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Manual Statistic Modal --}}
<div class="modal fade" id="statisticModal" tabindex="-1" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Manual Statistics</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="statisticForm">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="statisticContentId">
                    
                    <div class="form-group">
                        <label for="view">{{ trans('labels.view') }}</label>
                        <input type="number" class="form-control" id="view" name="view" min="0">
                    </div>

                    <div class="form-group">
                        <label for="like">{{ trans('labels.like') }}</label>
                        <input type="number" class="form-control" id="like" name="like" min="0">
                    </div>

                    <div class="form-group">
                        <label for="comment">{{ trans('labels.comment') }}</label>
                        <input type="number" class="form-control" id="comment" name="comment" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ trans('buttons.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Bulk Refresh Modal --}}
<div class="modal fade" id="bulkRefreshModal" tabindex="-1" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Refresh Statistics</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <p class="text-muted">Content to be refreshed:</p>
                </div>
                <div class="table-responsive" style="max-height: 300px;">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Influencer</th>
                                <th>Platform</th>
                                <th>Product</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="bulkRefreshContentList">
                            <!-- Content will be loaded here -->
                        </tbody>
                    </table>
                </div>
                <div class="progress mt-3">
                    <div id="bulkRefreshProgressBar" class="progress-bar" role="progressbar" 
                         style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="confirmBulkRefresh">Start Refresh</button>
            </div>
        </div>
    </div>
</div>