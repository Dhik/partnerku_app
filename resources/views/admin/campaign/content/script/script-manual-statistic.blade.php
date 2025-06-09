<script>
$(document).on('click', '.btnStatistic', function(event) {
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

    // Populate statistic form with current data
    $('#statisticContentId').val(rowData.id);
    $('#view').val(rowData.view || '');
    $('#like').val(rowData.like || '');
    $('#comment').val(rowData.comment || '');
    
    // Set current date
    $('#date').val('{{ \Carbon\Carbon::now()->format('d M Y') }}');
    
    // Clear any previous errors
    $('#errorStatistic').addClass('d-none').empty();
    
    // Show the modal
    $('#statisticModal').modal('show');
});

// Submit statistic form
$('#statisticForm').submit(function(e) {
    e.preventDefault();

    let formData = $(this).serialize();
    let saveButton = $('button[form="statisticForm"]');
    let spinner = saveButton.find('.spinner-border');

    // Show loading state
    if (spinner.length === 0) {
        spinner = $('<span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>');
        saveButton.prepend(spinner);
    }
    
    spinner.removeClass('d-none');
    saveButton.prop('disabled', true);

    $.ajax({
        type: 'POST',
        url: "{{ route('statistic.store', ['campaignContent' => ':campaignContent']) }}".replace(':campaignContent', $('#statisticContentId').val()),
        data: formData,
        success: function(response) {
            // Reload the content table to show updated statistics
            if (typeof contentTable !== 'undefined') {
                contentTable.ajax.reload();
            }
            
            // Hide the modal
            $('#statisticModal').modal('hide');
            
            // Reset the form
            $('#statisticForm')[0].reset();
            
            // Clear errors
            $('#errorStatistic').addClass('d-none').empty();
            
            // Show success message
            if (typeof toastr !== 'undefined') {
                toastr.success('{{ trans('messages.success_save', ['model' => trans('labels.data')]) }}');
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Statistics saved successfully!',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                alert('Statistics saved successfully!');
            }
        },
        error: function(xhr, status, error) {
            // Handle validation errors
            if (typeof errorAjaxValidation === 'function') {
                errorAjaxValidation(xhr, status, error, $('#errorStatistic'));
            } else {
                // Fallback error handling
                let errorMessage = 'Error saving statistics';
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                $('#errorStatistic').removeClass('d-none').html('<div class="alert alert-danger">' + errorMessage + '</div>');
            }
            
            // Show error notification
            if (typeof toastr !== 'undefined') {
                toastr.error('Failed to save statistics. Please try again.');
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to save statistics. Please try again.',
                });
            }
        },
        complete: function() {
            // Hide loading state and re-enable button
            spinner.addClass('d-none');
            saveButton.prop('disabled', false);
        }
    });
});

// Handle modal events
$('#statisticModal').on('show.bs.modal', function() {
    // Clear any previous errors when modal opens
    $('#errorStatistic').addClass('d-none').empty();
});

$('#statisticModal').on('hidden.bs.modal', function() {
    // Reset form when modal is closed
    $('#statisticForm')[0].reset();
    $('#errorStatistic').addClass('d-none').empty();
    
    // Re-enable submit button and hide spinner
    let saveButton = $('button[form="statisticForm"]');
    saveButton.prop('disabled', false);
    saveButton.find('.spinner-border').addClass('d-none');
});

// Format number inputs (if using money class for formatting)
$(document).on('input', '.money', function() {
    let value = $(this).val().replace(/[^\d]/g, '');
    if (value) {
        // Format as number with thousand separators
        let formatted = parseInt(value).toLocaleString('id-ID');
        $(this).val(formatted);
    }
});

// Remove formatting before form submission
$('#statisticForm').on('submit', function() {
    $(this).find('.money').each(function() {
        let rawValue = $(this).val().replace(/[^\d]/g, '');
        $(this).val(rawValue);
    });
});
</script>