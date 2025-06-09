{{-- modal-create-content.blade.php --}}
<div class="modal fade" id="contentModal" tabindex="-1" role="dialog" aria-labelledby="contentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contentModalLabel">{{ trans('labels.add') }} {{ trans('labels.content') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="contentForm">
                    @csrf
                    <div class="form-group">
                        <label for="username">{{ trans('labels.influencer') }}<span class="required">*</span></label>
                        <input type="text" class="form-control" name="username" placeholder="{{ trans('placeholder.input', ['field' => trans('labels.influencer')]) }}">
                    </div>

                    <div class="form-group">
                        <label for="taskName">{{ trans('labels.task') }}<span class="required">*</span></label>
                        <input type="text" class="form-control" id="taskName" name="task_name" placeholder="{{ trans('placeholder.input', ['field' => trans('labels.task')]) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="rateCard">{{ trans('labels.rate_card') }}<span class="required">*</span></label>
                        <input type="text" class="form-control money" id="rateCard" name="rate_card" placeholder="{{ trans('placeholder.input', ['field' => trans('labels.rate_card')]) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="platform">{{ trans('labels.platform') }}<span class="required">*</span></label>
                        <select class="form-control" id="platform" name="channel" required>
                            @foreach($platforms as $platform)
                                <option value="{{ $platform['value'] }}">
                                    {{ $platform['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="link">{{ trans('labels.link') }}</label>
                        <input type="text" class="form-control" id="link" name="link" placeholder="{{ trans('placeholder.input', ['field' => trans('labels.link')]) }}">
                    </div>

                    <div class="form-group">
                        <label for="product">{{ trans('labels.product') }}<span class="required">*</span></label>
                        <input type="text" class="form-control" id="product" name="product" placeholder="{{ trans('placeholder.input', ['field' => trans('labels.product')]) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="boostCode">{{ trans('labels.boost_code') }}</label>
                        <input type="text" class="form-control" id="boostCode" name="boost_code" placeholder="{{ trans('placeholder.input', ['field' => trans('labels.boost_code')]) }}">
                    </div>

                    <div class="form-group">
                        <label for="adsCode">{{ trans('labels.kode_ads') }}</label>
                        <input type="text" class="form-control" id="adsCode" name="kode_ads" placeholder="{{ trans('placeholder.input', ['field' => trans('labels.kode_ads')]) }}">
                    </div>

                    <div class="form-group d-none" id="errorContent"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="contentForm" class="btn btn-primary">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <i class="fas fa-save"></i> {{ trans('buttons.save') }}
                </button>
            </div>
        </div>
    </div>
</div>