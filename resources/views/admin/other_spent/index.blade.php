{{-- Other Spent View (admin/other_spent/index.blade.php) --}}
@extends('adminlte::page')

@section('title', 'Other Spent Management')

@section('content_header')
    <h1>Other Spent Management</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Other Spent Data</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary" onclick="createOtherSpent()">
                        <i class="fas fa-plus"></i> Add New Other Spent
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="otherSpentTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Detail</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Evidence Link</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="otherSpentModal" tabindex="-1" role="dialog" aria-labelledby="otherSpentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="otherSpentModalLabel">Add New Other Spent</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="otherSpentForm">
                <div class="modal-body">
                    <input type="hidden" id="otherSpentId" name="other_spent_id">
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="detail">Detail</label>
                        <textarea class="form-control" id="detail" name="detail" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select class="form-control" id="type" name="type">
                            <option value="">Select Type</option>
                            <option value="Sales Marketing">Sales Marketing</option>
                            <option value="Utilities">Utilities</option>
                            <option value="Admin and General">Admin and General</option>
                            <option value="Learning and Development">Learning and Development</option>
                            <option value="THR">THR</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="evidence_link">Evidence Link</label>
                        <input type="url" class="form-control" id="evidence_link" name="evidence_link" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Other Spent</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Show Modal -->
<div class="modal fade" id="showOtherSpentModal" tabindex="-1" role="dialog" aria-labelledby="showOtherSpentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showOtherSpentModalLabel">Other Spent Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Date:</strong>
                        <p id="show_date"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Amount:</strong>
                        <p id="show_amount"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Type:</strong>
                        <p id="show_type"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Evidence Link:</strong>
                        <p><a id="show_evidence_link" href="#" target="_blank">View Evidence</a></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <strong>Detail:</strong>
                        <p id="show_detail"></p>
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
    $('#otherSpentTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("otherSpent.data") }}',
        columns: [
            {data: 'id', name: 'id'},
            {data: 'date', name: 'date'},
            {data: 'detail', name: 'detail'},
            {data: 'amount', name: 'amount'},
            {data: 'type', name: 'type'},
            {data: 'evidence_link', name: 'evidence_link'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false}
        ]
    });

    // Form submission
    $('#otherSpentForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let otherSpentId = $('#otherSpentId').val();
        let url = otherSpentId ? '{{ route("otherSpent.update", ":id") }}'.replace(':id', otherSpentId) : '{{ route("otherSpent.store") }}';
        let method = otherSpentId ? 'PUT' : 'POST';

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
                    $('#otherSpentModal').modal('hide');
                    $('#otherSpentTable').DataTable().ajax.reload();
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

function createOtherSpent() {
    resetForm();
    $('#otherSpentModalLabel').text('Add New Other Spent');
    $('#otherSpentModal').modal('show');
}

function editOtherSpent(id) {
    $.ajax({
        url: '{{ route("otherSpent.edit", ":id") }}'.replace(':id', id),
        method: 'GET',
        success: function(response) {
            if (response.success) {
                let data = response.data;
                $('#otherSpentId').val(data.id);
                $('#date').val(data.date);
                $('#detail').val(data.detail);
                $('#amount').val(data.amount);
                $('#type').val(data.type);
                $('#evidence_link').val(data.evidence_link);
                $('#otherSpentModalLabel').text('Edit Other Spent');
                $('#otherSpentModal').modal('show');
            }
        }
    });
}

function showOtherSpent(id) {
    $.ajax({
        url: '{{ route("otherSpent.show", ":id") }}'.replace(':id', id),
        method: 'GET',
        success: function(response) {
            if (response.success) {
                let data = response.data;
                $('#show_date').text(new Date(data.date).toLocaleDateString('id-ID'));
                $('#show_detail').text(data.detail);
                $('#show_amount').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.amount));
                $('#show_type').text(data.type || '-');
                $('#show_evidence_link').attr('href', data.evidence_link).text(data.evidence_link);
                $('#showOtherSpentModal').modal('show');
            }
        }
    });
}

function deleteOtherSpent(id) {
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
                url: '{{ route("otherSpent.destroy", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                data: {
                    '_token': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#otherSpentTable').DataTable().ajax.reload();
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
    $('#otherSpentForm')[0].reset();
    $('#otherSpentId').val('');
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