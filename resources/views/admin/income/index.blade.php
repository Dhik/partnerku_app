{{-- Income View (admin/income/index.blade.php) --}}
@extends('adminlte::page')

@section('title', 'Income Management')

@section('content_header')
    <h1>Income Management</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Income Data</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary" onclick="createIncome()">
                        <i class="fas fa-plus"></i> Add New Income
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="incomeTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client Name</th>
                            <th>Revenue Contract</th>
                            <th>Service</th>
                            <th>Team In Charge</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="incomeModal" tabindex="-1" role="dialog" aria-labelledby="incomeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="incomeModalLabel">Add New Income</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="incomeForm">
                <div class="modal-body">
                    <input type="hidden" id="incomeId" name="income_id">
                    <div class="form-group">
                        <label for="nama_client">Client Name</label>
                        <input type="text" class="form-control" id="nama_client" name="nama_client" required>
                    </div>
                    <div class="form-group">
                        <label for="revenue_contract">Revenue Contract</label>
                        <input type="number" step="0.01" class="form-control" id="revenue_contract" name="revenue_contract" required>
                    </div>
                    <div class="form-group">
                        <label for="service">Service</label>
                        <input type="text" class="form-control" id="service" name="service" required>
                    </div>
                    <div class="form-group">
                        <label for="team_in_charge">Team In Charge</label>
                        <textarea class="form-control" id="team_in_charge" name="team_in_charge" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Income</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Show Modal -->
<div class="modal fade" id="showIncomeModal" tabindex="-1" role="dialog" aria-labelledby="showIncomeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showIncomeModalLabel">Income Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Client Name:</strong>
                        <p id="show_nama_client"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Revenue Contract:</strong>
                        <p id="show_revenue_contract"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Service:</strong>
                        <p id="show_service"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Team In Charge:</strong>
                        <p id="show_team_in_charge"></p>
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
    $('#incomeTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("income.data") }}',
        columns: [
            {data: 'id', name: 'id'},
            {data: 'nama_client', name: 'nama_client'},
            {data: 'revenue_contract', name: 'revenue_contract'},
            {data: 'service', name: 'service'},
            {data: 'team_in_charge', name: 'team_in_charge'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false}
        ]
    });

    // Form submission
    $('#incomeForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let incomeId = $('#incomeId').val();
        let url = incomeId ? '{{ route("income.update", ":id") }}'.replace(':id', incomeId) : '{{ route("income.store") }}';
        let method = incomeId ? 'PUT' : 'POST';

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
                    $('#incomeModal').modal('hide');
                    $('#incomeTable').DataTable().ajax.reload();
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

function createIncome() {
    resetForm();
    $('#incomeModalLabel').text('Add New Income');
    $('#incomeModal').modal('show');
}

function editIncome(id) {
    $.ajax({
        url: '{{ route("income.edit", ":id") }}'.replace(':id', id),
        method: 'GET',
        success: function(response) {
            if (response.success) {
                let data = response.data;
                $('#incomeId').val(data.id);
                $('#nama_client').val(data.nama_client);
                $('#revenue_contract').val(data.revenue_contract);
                $('#service').val(data.service);
                $('#team_in_charge').val(data.team_in_charge);
                $('#incomeModalLabel').text('Edit Income');
                $('#incomeModal').modal('show');
            }
        }
    });
}

function showIncome(id) {
    $.ajax({
        url: '{{ route("income.show", ":id") }}'.replace(':id', id),
        method: 'GET',
        success: function(response) {
            if (response.success) {
                let data = response.data;
                $('#show_nama_client').text(data.nama_client);
                $('#show_revenue_contract').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.revenue_contract));
                $('#show_service').text(data.service);
                $('#show_team_in_charge').text(data.team_in_charge);
                $('#showIncomeModal').modal('show');
            }
        }
    });
}

function deleteIncome(id) {
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
                url: '{{ route("income.destroy", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                data: {
                    '_token': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#incomeTable').DataTable().ajax.reload();
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
    $('#incomeForm')[0].reset();
    $('#incomeId').val('');
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