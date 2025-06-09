<script>
$(document).on('click', '.btnUpdateContent', function(event) {
    event.preventDefault();
    
    let $btnRow = $(this).closest('tr');
    if ($btnRow.hasClass('child')) {
        $btnRow = $btnRow.prev();
    }
    
    let rowData = contentTable.row($btnRow).data();
    if (!rowData) {
        console.log("Row data is undefined.");
        return;
    }

    // Populate update form
    $('#contentId').val(rowData.id);
    $('#usernameUpdate').val(rowData.username);
    $('#taskNameUpdate').val(rowData.task);
    $('#rateCardUpdate').val(rowData.rate_card);
    $('#platformUpdate').val(rowData.channel).trigger('change');
    $('#linkUpdate').val(rowData.link);
    $('#productUpdate').val(rowData.product);
    $('#boostCodeUpdate').val(rowData.boost_code);
    $('#adsCodeUpdate').val(rowData.kode_ads);
    $('#viewsUpdate').val(rowData.view);
    $('#likesUpdate').val(rowData.like);
    $('#commentsUpdate').val(rowData.comment);

    $('#contentUpdateModal').modal('show');
});

$(document).on('click', '.btnDeleteContent', function(event) {
    event.preventDefault();
    
    let $btnRow = $(this).closest('tr');
    if ($btnRow.hasClass('child')) {
        $btnRow = $btnRow.prev();
    }
    
    let rowData = contentTable.row($btnRow).data();
    if (!rowData) {
        console.log("Row data is undefined.");
        return;
    }

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '{{ trans('labels.are_you_sure') }}',
            text: '{{ trans('labels.not_be_able_to_recover') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ trans('buttons.confirm_swal') }}',
            cancelButtonText: '{{ trans('buttons.cancel_swal') }}',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                deleteContentAction(rowData.id);
            }
        });
    } else {
        if (confirm('Are you sure you want to delete this content?')) {
            deleteContentAction(rowData.id);
        }
    }
});

function deleteContentAction(contentId) {
    $.ajax({
        url: "{{ route('campaignContent.destroy', ['campaignContent' => ':campaignContentId']) }}".replace(':campaignContentId', contentId),
        type: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (typeof contentTable !== 'undefined') {
                contentTable.ajax.reload();
            }
            
            if (typeof Swal !== 'undefined') {
                Swal.fire(
                    '{{ trans('labels.success') }}',
                    '{{ trans('messages.success_delete') }}',
                    'success'
                );
            } else {
                alert('Content deleted successfully!');
            }
        },
        error: function(xhr, status, error) {
            let message = xhr.status === 422 ? xhr.responseJSON.message : '{{ trans('messages.error_delete') }}';
            
            if (typeof Swal !== 'undefined') {
                Swal.fire(
                    '{{ trans('labels.failed') }}',
                    message,
                    'error'
                );
            } else {
                alert('Error: ' + message);
            }
        }
    });
}

// Submit update form
$('#contentUpdateForm').submit(function(e) {
    e.preventDefault();

    let formData = $(this).serialize();
    let saveButton = $('button[form="contentUpdateForm"]');

    saveButton.prop('disabled', true);

    $.ajax({
        type: 'PUT',
        url: "{{ route('campaignContent.update', ['campaignContent' => ':campaignContentId']) }}".replace(':campaignContentId', $('#contentId').val()),
        data: formData,
        success: function(response) {
            if (typeof contentTable !== 'undefined') {
                contentTable.ajax.reload();
            }
            $('#contentUpdateModal').modal('hide');
            $('#usernameUpdate').val(null).trigger('change');
            $('#platformUpdate').val(null).trigger('change');
            $('#contentUpdateForm')[0].reset();
            $('#errorContentUpdate').removeClass('d-none').empty();
            
            if (typeof toastr !== 'undefined') {
                toastr.success('{{ trans('messages.success_update', ['model' => trans('labels.content')]) }}');
            } else {
                alert('Content updated successfully!');
            }
        },
        error: function(xhr, status, error) {
            if (typeof errorAjaxValidation === 'function') {
                errorAjaxValidation(xhr, status, error, $('#errorContentUpdate'));
            } else {
                $('#errorContentUpdate').removeClass('d-none').html('<div class="alert alert-danger">Error updating content</div>');
            }
        },
        complete: function() {
            saveButton.prop('disabled', false);
        }
    });
});
</script>
