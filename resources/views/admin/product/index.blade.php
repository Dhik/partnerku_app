{{-- Product View (admin/product/index.blade.php) --}}
@extends('adminlte::page')

@section('title', 'Product Management')

@section('content_header')
    <h1>Product Management</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Data</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary" onclick="createProduct()">
                        <i class="fas fa-plus"></i> Add New Product
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="productTable" class="table table-bordered table-striped">
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
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Add New Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="productForm">
                <div class="modal-body">
                    <input type="hidden" id="productId" name="product_id">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter product name">
                        <small class="form-text text-muted">Leave empty if you want to create a product without a name.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Show Modal -->
<div class="modal fade" id="showProductModal" tabindex="-1" role="dialog" aria-labelledby="showProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showProductModalLabel">Product Details</h5>
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
    $('#productTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("product.data") }}',
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
    $('#productForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let productId = $('#productId').val();
        let url = productId ? '{{ route("product.update", ":id") }}'.replace(':id', productId) : '{{ route("product.store") }}';
        let method = productId ? 'PUT' : 'POST';

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
                    $('#productModal').modal('hide');
                    $('#productTable').DataTable().ajax.reload();
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

function createProduct() {
    resetForm();
    $('#productModalLabel').text('Add New Product');
    $('#productModal').modal('show');
}

function editProduct(id) {
    $.ajax({
        url: '{{ route("product.edit", ":id") }}'.replace(':id', id),
        method: 'GET',
        success: function(response) {
            if (response.success) {
                let data = response.data;
                $('#productId').val(data.id);
                $('#name').val(data.name);
                $('#productModalLabel').text('Edit Product');
                $('#productModal').modal('show');
            }
        }
    });
}

function showProduct(id) {
    $.ajax({
        url: '{{ route("product.show", ":id") }}'.replace(':id', id),
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
                $('#showProductModal').modal('show');
            }
        }
    });
}

function deleteProduct(id) {
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
                url: '{{ route("product.destroy", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                data: {
                    '_token': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#productTable').DataTable().ajax.reload();
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
    $('#productForm')[0].reset();
    $('#productId').val('');
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