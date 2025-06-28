{{-- Niche View (admin/niche/index.blade.php) --}}
@extends('adminlte::page')

@section('title', 'Niche Management')

@section('content_header')
    <h1>Niche Management</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Niche Data</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary" onclick="createNiche()">
                        <i class="fas fa-plus"></i> Add New Niche
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="nicheTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="nicheModal" tabindex="-1" role="dialog" aria-labelledby="nicheModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nicheModalLabel">Add New Niche</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="nicheForm">
                <div class="modal-body">
                    <input type="hidden" id="nicheId" name="niche_id">
                    <div class="form-group">
                        <label for="name">Niche Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter niche name">
                        <small class="form-text text-muted">Leave empty if you want to create a niche without a name.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Niche</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Show Modal -->
<div class="modal fade" id="showNicheModal" tabindex="-1" role="dialog" aria-labelledby="showNicheModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showNicheModalLabel">Niche Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>ID:</strong>
                        <p id="show_id"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Name:</strong>
                        <p id="show_name"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Created At:</strong>
                        <p id="show_created_at"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Updated At:</strong>
                        <p id="show_updated_at"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
@stop

@section('js')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#nicheTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("niche.data") }}',
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name', render: function(data) {
                return data || '<em class="text-muted">No name</em>';
            }},
            {data: 'created_at', name: 'created_at', render: function(data) {
                return new Date(data).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }},
            {data: 'actions', name: 'actions', orderable: false, searchable: false}
        ]
    });

    // Form submission
    $('#nicheForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let nicheId = $('#nicheId').val();
        let url = nicheId ? '{{ route("niche.update", ":id") }}'.replace(':id', nicheId) : '{{ route("niche.store") }}';
        let method = nicheId ? 'PUT' : 'POST';

        if (method === 'PUT') {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#nicheModal').modal('hide');
                    $('#nicheTable').DataTable().ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    resetForm();
                }
            },
            error: function(xhr) {
                handleFormErrors(xhr);
            }
        });
    });
});

function createNiche() {
    resetForm();
    $('#nicheModalLabel').text('Add New Niche');
    $('#nicheModal').modal('show');
}

function editNiche(id) {
    $.ajax({
        url: '{{ route("niche.edit", ":id") }}'.replace(':id', id),
        method: 'GET',
        success: function(response) {
            if (response.success) {
                let data = response.data;
                $('#nicheId').val(data.id);
                $('#name').val(data.name);
                $('#nicheModalLabel').text('Edit Niche');
                $('#nicheModal').modal('show');
            }
        }
    });
}

function showNiche(id) {
    $.ajax({
        url: '{{ route("niche.show", ":id") }}'.replace(':id', id),
        method: 'GET',
        success: function(response) {
            if (response.success) {
                let data = response.data;
                $('#show_id').text(data.id);
                $('#show_name').text(data.name || 'No name');
                $('#show_created_at').text(new Date(data.created_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }));
                $('#show_updated_at').text(new Date(data.updated_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }));
                $('#showNicheModal').modal('show');
            }
        }
    });
}

function deleteNiche(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("niche.destroy", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                data: {
                    '_token': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#nicheTable').DataTable().ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON.message || 'Something went wrong!'
                    });
                }
            });
        }
    });
}

function resetForm() {
    $('#nicheForm')[0].reset();
    $('#nicheId').val('');
}

function handleFormErrors(xhr) {
    let errors = xhr.responseJSON.errors;
    if (errors) {
        let errorMessage = '';
        Object.keys(errors).forEach(function(key) {
            errorMessage += errors[key][0] + '\n';
        });
        Swal.fire({
            icon: 'error',
            title: 'Validation Error!',
            text: errorMessage
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: xhr.responseJSON.message || 'Something went wrong!'
        });
    }
}
</script>
@stop