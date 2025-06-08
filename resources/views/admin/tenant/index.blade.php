@extends('adminlte::page')

@section('title', trans('labels.tenant'))

@section('content_header')
    <h1>{{ trans('labels.tenant') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-end">
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tenantModal">
                                <i class="fas fa-plus"></i> {{ trans('labels.add') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="tenantTable" class="table table-bordered table-striped dataTable responsive" aria-describedby="tenant-info" width="100%">
                        <thead>
                        <tr>
                            <th width="15%">{{ trans('labels.logo') }}</th>
                            <th>{{ trans('labels.name') }}</th>
                            <th width="10%">{{ trans('labels.action') }}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('admin.tenant.modal')
    @include('admin.tenant.modal-update')
@stop

@section('js')
    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(function () {
            const tenantTableSelector = $('#tenantTable');

            // datatable
            let tenantTable = tenantTableSelector.DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('tenant.get') }}",
                columns: [
                    {data: 'logo', name: 'logo', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'actions', sortable: false, orderable: false}
                ]
            });

            // Logo preview for add form
            $('#logo').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#logoPreviewImg').attr('src', e.target.result);
                        $('#logoPreview').show();
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#logoPreview').hide();
                }
            });

            // Logo preview for update form
            $('#logoUpdate').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#logoPreviewUpdateImg').attr('src', e.target.result);
                        $('#logoPreviewUpdate').show();
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#logoPreviewUpdate').hide();
                }
            });

            // submit form with SweetAlert2
            $('#tenantForm').submit(function(e) {
                e.preventDefault();

                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we save the tenant.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                let formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: "{{ route('tenant.store') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        tenantTable.ajax.reload();
                        $('#tenantForm').trigger("reset");
                        $('#logoPreview').hide();
                        $('#tenantModal').modal('hide');
                        
                        // Success alert
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: '{{ trans('messages.success_save', ['model' => trans('labels.tenant')]) }}',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        
                        let errorMessage = 'An error occurred while saving the tenant.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        // Error alert
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            html: errorMessage,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // Handle row click event to open modal and fill form
            tenantTableSelector.on('draw.dt', function() {
                const tableBodySelector =  $('#tenantTable tbody');

                tableBodySelector.on('click', '.updateButton', function() {
                    let rowData = tenantTable.row($(this).closest('tr')).data();

                    $('#tenantId').val(rowData.id);
                    $('#nameUpdate').val(rowData.name);
                    
                    // Show current logo if exists
                    if (rowData.logo_url) {
                        $('#currentLogoImg').attr('src', rowData.logo_url);
                        $('#currentLogo').show();
                    } else {
                        $('#currentLogo').hide();
                    }
                    
                    // Hide preview
                    $('#logoPreviewUpdate').hide();
                    
                    $('#tenantUpdateModal').modal('show');
                });

                tableBodySelector.on('click', '.deleteButton', function() {
                    let rowData = tenantTable.row($(this).closest('tr')).data();
                    let route = '{{ route('tenant.destroy', ':id') }}';
                    
                    // SweetAlert2 confirmation for delete
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You want to delete tenant '" + rowData.name + "'? This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            Swal.fire({
                                title: 'Deleting...',
                                text: 'Please wait while we delete the tenant.',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            deleteAjax(route, rowData.id, tenantTable);
                        }
                    });
                });
            });

            // submit update form with SweetAlert2
            $('#tenantUpdateForm').submit(function(e) {
                e.preventDefault();

                // Show loading
                Swal.fire({
                    title: 'Updating...',
                    text: 'Please wait while we update the tenant.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                let formData = new FormData(this);
                let tenantId = $('#tenantId').val();

                let updateUrl = '{{ route('tenant.update', ':tenantId') }}';
                updateUrl = updateUrl.replace(':tenantId', tenantId);

                // Add method override for PUT request
                formData.append('_method', 'PUT');

                $.ajax({
                    type: 'POST',
                    url: updateUrl,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        tenantTable.ajax.reload();
                        $('#logoPreviewUpdate').hide();
                        $('#tenantUpdateModal').modal('hide');
                        
                        // Success alert
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: '{{ trans('messages.success_update', ['model' => trans('labels.tenant')]) }}',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        
                        let errorMessage = 'An error occurred while updating the tenant.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        // Error alert
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            html: errorMessage,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // Reset form when modal is closed
            $('#tenantModal').on('hidden.bs.modal', function () {
                $('#logoPreview').hide();
            });

            $('#tenantUpdateModal').on('hidden.bs.modal', function () {
                $('#logoPreviewUpdate').hide();
            });
        });

        // Enhanced deleteAjax function with SweetAlert2
        function deleteAjax(route, id, table) {
            let deleteUrl = route.replace(':id', id);
            
            $.ajax({
                type: 'DELETE',
                url: deleteUrl,
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    table.ajax.reload();
                    
                    // Success alert
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'The tenant has been deleted successfully.',
                        timer: 3000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    
                    let errorMessage = 'An error occurred while deleting the tenant.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    // Error alert
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage,
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    </script>
@stop