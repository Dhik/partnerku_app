@extends('adminlte::page')

@section('title', trans('labels.user'))

@section('content_header')
    <h1>{{ trans('labels.user') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-end">
                        <div class="col-auto">
                            <a href="{{ route('users.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ trans('labels.add') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="userTable" class="table table-bordered table-striped dataTable responsive" aria-describedby="userTable-info" width="100%">
                        <thead>
                            <tr>
                                <th>{{ trans('labels.name') }}</th>
                                <th>{{ trans('labels.email') }}</th>
                                <th>{{ trans('labels.phone_number') }}</th>
                                <th>{{ trans('labels.position') }}</th>
                                <th>{{ trans('labels.roles') }}</th>
                                <th>{{ trans('labels.tenant') }}</th>
                                <th width="10%">{{ trans('labels.action') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(function () {
            const userTableSelector = $('#userTable');

            // datatable
            userTableSelector.DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('users.get') }}",
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'phone_number', name: 'phone_number'},
                    {data: 'position', name: 'position'},
                    {data: 'roles', name: 'roles'},
                    {data: 'tenants', name: 'tenants'},
                    {data: 'actions', sortable: false, orderable: false}
                ]
            });
            $(document).on('click', '.delete-user', function() {
        const userId = $(this).data('id');
        const deleteUrl = '{{ url("admin/users") }}/' + userId;
        
        // Show confirmation dialog
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
                // Perform delete request
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire(
                            'Deleted!',
                            response.message,
                            'success'
                        ).then(() => {
                            // Auto refresh the page after success
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            'An error occurred while deleting the user.',
                            'error'
                        );
                    }
                });
            }
        });
    });
        });
    </script>
@stop
