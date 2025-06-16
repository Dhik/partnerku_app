<div class="modal fade" id="statisticModal" tabindex="-1" role="dialog" aria-labelledby="statisticModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statisticModalLabel">{{ trans('labels.add') }} {{ trans('labels.data') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="statisticForm">
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label for="date">{{ trans('labels.date') }}</label>
                        <input type="text" class="form-control" id="date" name="date" readonly value="{{ \Carbon\Carbon::now()->format('d M Y') }}">
                    </div>

                    <div class="form-group">
                        <label for="view">{{ trans('labels.view') }}</label>
                        <input type="text" class="form-control money" id="view" name="view" placeholder="{{ trans('placeholder.input', ['field' => trans('labels.view')]) }}">
                    </div>

                    <div class="form-group">
                        <label for="like">{{ trans('labels.like') }}</label>
                        <input type="text" class="form-control money" id="like" name="like" placeholder="{{ trans('placeholder.input', ['field' => trans('labels.like')]) }}">
                    </div>

                    <div class="form-group">
                        <label for="comment">{{ trans('labels.comment') }}</label>
                        <input type="text" class="form-control money" id="comment" name="comment" placeholder="{{ trans('placeholder.input', ['field' => trans('labels.comment')]) }}">
                    </div>

                    <input type="hidden" id="statisticContentId">

                    <div class="form-group d-none" id="errorStatistic"></div>
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