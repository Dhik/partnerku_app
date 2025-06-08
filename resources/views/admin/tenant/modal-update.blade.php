<!-- Modal -->
<div class="modal fade" id="tenantUpdateModal" tabindex="-1" role="dialog" aria-labelledby="tenantUpdateModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="socialMediaModalLabel">{{ trans('labels.update') }} {{ trans('labels.tenant') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="tenantUpdateForm" enctype="multipart/form-data">
                    @csrf
                    <!-- Name field -->
                    <div class="form-group">
                        <label for="nameUpdate">{{ trans('labels.name') }}</label>
                        <input type="text" class="form-control" id="nameUpdate" name="name" required>
                    </div>

                    <!-- Logo field -->
                    <div class="form-group">
                        <label for="logoUpdate">{{ trans('labels.logo') }}</label>
                        <input type="file" class="form-control-file" id="logoUpdate" name="logo" accept="image/*">
                        <small class="form-text text-muted">{{ trans('messages.logo_help_update') }}</small>
                    </div>

                    <!-- Current logo display -->
                    <div class="form-group" id="currentLogo">
                        <label>{{ trans('labels.current_logo') }}</label>
                        <div>
                            <img id="currentLogoImg" src="" alt="Current Logo" style="max-width: 200px; max-height: 100px;" class="img-thumbnail">
                        </div>
                    </div>

                    <!-- Logo preview -->
                    <div class="form-group" id="logoPreviewUpdate" style="display: none;">
                        <label>{{ trans('labels.logo_preview') }}</label>
                        <div>
                            <img id="logoPreviewUpdateImg" src="" alt="Logo Preview" style="max-width: 200px; max-height: 100px;" class="img-thumbnail">
                        </div>
                    </div>

                    <input type="hidden" name="tenantId" id="tenantId"/>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ trans('buttons.update') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>