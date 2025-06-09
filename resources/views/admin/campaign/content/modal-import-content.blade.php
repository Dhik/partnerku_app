{{-- modal-import-content.blade.php --}}
<div class="modal fade" id="contentImportModal" tabindex="-1" role="dialog" aria-labelledby="contentImportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contentImportModalLabel">{{ trans('labels.import') }} Konten</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <a href="{{ route('campaignContent.template') }}">
                        {{ trans('labels.download_template') }} <i class="fas fa-download"></i>
                    </a>
                </div>
                <form id="contentImportForm" action="{{ route('campaignContent.import', ['campaign' => $campaign->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="fileContentImport">{{ trans('labels.file') }}</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="fileContentImport" name="fileContentImport" required>
                            <label class="custom-file-label" for="fileContentImport" id="labelUploadImport">{{ trans('placeholder.select_file') }}</label>
                        </div>
                    </div>

                    <div class="form-group d-none" id="errorImportCampaign"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="contentImportForm" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ trans('labels.import') }}
                </button>
            </div>
        </div>
    </div>
</div>