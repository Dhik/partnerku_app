<!-- Modal -->
<div class="modal fade" id="tenantModal" tabindex="-1" role="dialog" aria-labelledby="tenantModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tenantModalLabel">{{ trans('labels.add') }} {{ trans('labels.tenant') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="tenantForm" enctype="multipart/form-data">
                    @csrf
                    <!-- Name field -->
                    <div class="form-group">
                        <label for="name">{{ trans('labels.name') }}</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <!-- Logo field -->
                    <div class="form-group">
                        <label for="logo">{{ trans('labels.logo') }}</label>
                        <input type="file" class="form-control-file" id="logo" name="logo" accept="image/*">
                        <small class="form-text text-muted">{{ trans('messages.logo_help') }}</small>
                    </div>

                    <!-- Logo preview -->
                    <div class="form-group" id="logoPreview" style="display: none;">
                        <label>{{ trans('labels.logo_preview') }}</label>
                        <div>
                            <img id="logoPreviewImg" src="" alt="Logo Preview" style="max-width: 200px; max-height: 100px;" class="img-thumbnail">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ trans('buttons.save') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>