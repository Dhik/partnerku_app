{{-- Payroll View (admin/payroll/index.blade.php) --}}
@extends('adminlte::page')

@section('title', 'Payroll Management')

@section('content_header')
    <h1>Payroll Management</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Payroll Data</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary" onclick="createPayroll()">
                        <i class="fas fa-plus"></i> Add New Payroll
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="payrollTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Month</th>
                            <th>Salary</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="payrollModal" tabindex="-1" role="dialog" aria-labelledby="payrollModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payrollModalLabel">Add New Payroll</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="payrollForm">
                <div class="modal-body">
                    <input type="hidden" id="payrollId" name="payroll_id">
                    <div class="form-group">
                        <label for="name">Employee Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="posisi">Position</label>
                        <input type="text" class="form-control" id="posisi" name="posisi" required>
                    </div>
                    <div class="form-group">
                        <label for="bulan">Month</label>
                        <input type="text" class="form-control" id="bulan" name="bulan" placeholder="e.g., January 2025" required>
                    </div>
                    <div class="form-group">
                        <label for="salary">Salary</label>
                        <input type="number" step="0.01" class="form-control" id="salary" name="salary" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Payroll</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Show Modal -->
<div class="modal fade" id="showPayrollModal" tabindex="-1" role="dialog" aria-labelledby="showPayrollModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showPayrollModalLabel">Payroll Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Employee Name:</strong>
                        <p id="show_name"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Position:</strong>
                        <p id="show_posisi"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Month:</strong>
                        <p id="show_bulan"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Salary:</strong>
                        <p id="show_salary"></p>
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
    $('#payrollTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("payroll.data") }}',
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'posisi', name: 'posisi'},
            {data: 'bulan', name: 'bulan'},
            {data: 'salary', name: 'salary'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false}
        ]
    });

    // Form submission
    $('#payrollForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let payrollId = $('#payrollId').val();
        let url = payrollId ? '{{ route("payroll.update", ":id") }}'.replace(':id', payrollId) : '{{ route("payroll.store") }}';
        let method = payrollId ? 'PUT' : 'POST';

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
                    $('#payrollModal').modal('hide');
                    $('#payrollTable').DataTable().ajax.reload();
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

function createPayroll() {
    resetForm();
    $('#payrollModalLabel').text('Add New Payroll');
    $('#payrollModal').modal('show');
}

function editPayroll(id) {
    $.ajax({
        url: '{{ route("payroll.edit", ":id") }}'.replace(':id', id),
        method: 'GET',
        success: function(response) {
            if (response.success) {
                let data = response.data;
                $('#payrollId').val(data.id);
                $('#name').val(data.name);
                $('#posisi').val(data.posisi);
                $('#bulan').val(data.bulan);
                $('#salary').val(data.salary);
                $('#payrollModalLabel').text('Edit Payroll');
                $('#payrollModal').modal('show');
            }
        }
    });
}

function showPayroll(id) {
    $.ajax({
        url: '{{ route("payroll.show", ":id") }}'.replace(':id', id),
        method: 'GET',
        success: function(response) {
            if (response.success) {
                let data = response.data;
                $('#show_name').text(data.name);
                $('#show_posisi').text(data.posisi);
                $('#show_bulan').text(data.bulan);
                $('#show_salary').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.salary));
                $('#showPayrollModal').modal('show');
            }
        }
    });
}

function deletePayroll(id) {
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
                url: '{{ route("payroll.destroy", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                data: {
                    '_token': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#payrollTable').DataTable().ajax.reload();
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
    $('#payrollForm')[0].reset();
    $('#payrollId').val('');
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