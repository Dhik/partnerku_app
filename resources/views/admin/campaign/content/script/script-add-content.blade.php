// script-add-content.blade.php
<script>
// Auto-fill form fields when KOL is selected (simplified)
$('#username').on('change', function() {
    const selectedUsername = $(this).val();
    
    if (selectedUsername) {
        console.log('Selected KOL username:', selectedUsername);
        // You can add any simple auto-fill logic here if needed
        // For example, clearing rate card to force manual entry:
        // $('#rateCard').val('');
    }
});

// Submit content form
$('#contentForm').submit(function(e) {
    e.preventDefault();

    let formData = $(this).serialize();
    let saveButton = $('button[form="contentForm"]');
    let spinner = saveButton.find('.spinner-border');

    // Show loading spinner
    spinner.removeClass('d-none');
    saveButton.prop('disabled', true);

    $.ajax({
        type: 'POST',
        url: "{{ route('campaignContent.store', ['campaignId' => ':campaignId']) }}".replace(':campaignId', campaignId),
        data: formData,
        success: function(response) {
            if (typeof contentTable !== 'undefined') {
                contentTable.ajax.reload();
            }
            $('#contentModal').modal('hide');
            $('#platform').val(null).trigger('change');
            $('#contentForm')[0].reset();
            $('#errorContent').removeClass('d-none').empty();
            
            // Success notification
            if (typeof toastr !== 'undefined') {
                toastr.success('{{ trans('messages.success_save', ['model' => trans('labels.content')]) }}');
            } else {
                alert('Content saved successfully!');
            }
        },
        error: function(xhr, status, error) {
            if (typeof errorAjaxValidation === 'function') {
                errorAjaxValidation(xhr, status, error, $('#errorContent'));
            } else {
                $('#errorContent').removeClass('d-none').html('<div class="alert alert-danger">Error saving content</div>');
            }
        },
        complete: function() {
            // Hide loading spinner and re-enable button
            spinner.addClass('d-none');
            saveButton.prop('disabled', false);
        }
    });
});

// Reset form when modal is closed
$('#contentModal').on('hidden.bs.modal', function() {
    $('#contentForm')[0].reset();
    $('#errorContent').addClass('d-none').empty();
    $('#username').val('').trigger('change');
    $('#platform').val('').trigger('change');
});

// Initialize Select2 for better dropdown experience (optional)
$(document).ready(function() {
    if (typeof $.fn.select2 !== 'undefined') {
        $('#username').select2({
            placeholder: 'Search and select an influencer...',
            allowClear: true,
            dropdownParent: $('#contentModal'),
            width: '100%'
        });
    }
});
</script>