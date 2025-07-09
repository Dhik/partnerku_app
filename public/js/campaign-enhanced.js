// Campaign Enhanced - All JavaScript in One File

// ===== GLOBAL UTILITIES =====
window.CampaignUtils = {
    // Format numbers
    formatNumber: function(num) {
        return new Intl.NumberFormat('id-ID').format(num || 0);
    },

    // Show notifications
    showToast: function(message, type = 'success') {
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({ title: type === 'success' ? 'Success!' : 'Error!', text: message, icon: type, timer: 3000 });
        } else {
            alert(message);
        }
    },

    // Confirm dialog
    confirmDelete: function(title = 'Are you sure?', text = 'This action cannot be undone.') {
        if (typeof Swal !== 'undefined') {
            return Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            });
        } else {
            return new Promise((resolve) => {
                resolve({ isConfirmed: confirm(text) });
            });
        }
    },

    // Set button loading state
    setButtonLoading: function(button, loading = true) {
        const btn = $(button);
        if (loading) {
            btn.prop('disabled', true);
            if (btn.find('.spinner-border').length === 0) {
                btn.prepend('<span class="spinner-border spinner-border-sm mr-2"></span>');
            }
        } else {
            btn.prop('disabled', false);
            btn.find('.spinner-border').remove();
        }
    },

    // Handle AJAX errors
    handleError: function(xhr, defaultMessage = 'An error occurred') {
        let message = defaultMessage;
        if (xhr.responseJSON) {
            if (xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            } else if (xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                message = errors.join('<br>');
            }
        }
        return message;
    }
};

// ===== MODAL MANAGER (NO GLITCHING) =====
window.ModalManager = {
    // Show modal safely
    show: function(modalId, data = {}) {
        const modal = $(modalId);
        if (!modal.length) return;

        // Hide any open modals first
        $('.modal.show').modal('hide');
        
        setTimeout(() => {
            // Populate form if data provided
            if (Object.keys(data).length > 0) {
                this.populateForm(modal, data);
            }
            
            modal.modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        }, 150);
    },

    // Hide modal safely
    hide: function(modalId) {
        $(modalId).modal('hide');
    },

    // Populate form with data
    populateForm: function(modal, data) {
        const form = modal.find('form');
        Object.keys(data).forEach(key => {
            const input = form.find(`[name="${key}"], #${key}`);
            if (input.length) {
                if (input.is('select')) {
                    input.val(data[key]).trigger('change');
                } else {
                    input.val(data[key]);
                }
            }
        });
    },

    // Clear form errors
    clearErrors: function(modalId) {
        const modal = $(modalId);
        modal.find('.is-invalid').removeClass('is-invalid');
        modal.find('.invalid-feedback').addClass('d-none');
        modal.find('.alert').remove();
    },

    // Show form errors
    showErrors: function(modalId, errors) {
        const modal = $(modalId);
        const form = modal.find('form');
        
        Object.keys(errors).forEach(field => {
            const input = form.find(`[name="${field}"]`);
            const feedback = input.siblings('.invalid-feedback');
            
            input.addClass('is-invalid');
            if (feedback.length) {
                feedback.removeClass('d-none').html(errors[field][0]);
            } else {
                input.after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
            }
        });
    }
};

// ===== TABLE MANAGER =====
window.TableManager = {
    // Get row data safely
    getRowData: function(table, element) {
        let $row = $(element).closest('tr');
        if ($row.hasClass('child')) {
            $row = $row.prev();
        }
        return table && $row.length ? table.row($row).data() : null;
    },

    // Reload table safely
    reload: function(table) {
        if (table && typeof table.ajax !== 'undefined') {
            table.ajax.reload(null, false);
        }
    }
};

// ===== MAIN CAMPAIGN FUNCTIONALITY =====
$(document).ready(function() {
    
    // Global AJAX setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // ===== MODAL EVENT HANDLERS =====
    
    // Modal cleanup on hide
    $(document).on('hidden.bs.modal', '.modal', function() {
        const modal = $(this);
        const form = modal.find('form');
        if (form.length) {
            form[0].reset();
            ModalManager.clearErrors('#' + modal.attr('id'));
            form.find('button[type="submit"]').prop('disabled', false);
            form.find('.spinner-border').remove();
        }
    });

    // ===== CONTENT MODAL HANDLERS =====
    
    // Add Content
    $(document).on('click', '[data-toggle="modal"][data-target="#contentModal"]', function() {
        ModalManager.clearErrors('#contentModal');
        ModalManager.show('#contentModal');
    });

    // Update Content
    // SIMPLE FIX: Just replace this part in your campaign-enhanced.js

    // Update Content - SIMPLE VERSION
    $(document).on('click', '.btnUpdateContent', function(e) {
        e.preventDefault();
        let $row = $(this).closest('tr');
        if ($row.hasClass('child')) $row = $row.prev();
        
        const rowData = window.contentTable ? TableManager.getRowData(window.contentTable, this) : null;
        if (!rowData) return;

        // Set the values directly to the form fields
        $('#contentId').val(rowData.id);
        $('#usernameUpdate').val(rowData.username);
        $('#taskNameUpdate').val(rowData.task);
        $('#platformUpdate').val(rowData.channel); // This is the key fix
        $('#linkUpdate').val(rowData.link);
        $('#productUpdate').val(rowData.product);
        $('#viewsUpdate').val(rowData.view);
        $('#likesUpdate').val(rowData.like);
        $('#commentsUpdate').val(rowData.comment);
        
        // Show the modal
        $('#contentUpdateModal').modal('show');
    });

    // Show Detail Modal
    $(document).on('click', '.btnDetail', function(e) {
        e.preventDefault();
        const rowData = window.contentTable ? TableManager.getRowData(window.contentTable, this) : null;
        if (!rowData) return;

        // Update modal content
        $('#likeModal').text(CampaignUtils.formatNumber(rowData.like));
        $('#viewModal').text(CampaignUtils.formatNumber(rowData.view));
        $('#commentModal').text(CampaignUtils.formatNumber(rowData.comment));
        $('#rateCardModal').text(rowData.rate_card_formatted || '0');
        $('#kodeAdsModal').text(rowData.kode_ads || '-');
        $('#uploadDateModal').text(rowData.upload_date || 'Not posted yet');

        // Load content embed
        loadContentEmbed(rowData.link, rowData.channel);
        ModalManager.show('#detailModal');
    });

    // ===== FORM SUBMISSIONS =====
    
    // Content Form Submit
    $('#contentForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        
        ModalManager.clearErrors('#contentModal');
        CampaignUtils.setButtonLoading(submitBtn, true);
        
        // Determine URL
        let storeUrl = form.attr('action');
        if (!storeUrl && typeof window.campaignContentStoreUrl !== 'undefined') {
            storeUrl = window.campaignContentStoreUrl;
        }
        
        if (!storeUrl) {
            CampaignUtils.showToast('Store URL is not configured', 'error');
            CampaignUtils.setButtonLoading(submitBtn, false);
            return;
        }
        
        $.ajax({
            type: 'POST',
            url: storeUrl,
            data: new FormData(form[0]),
            processData: false,
            contentType: false,
            success: function(response) {
                if (window.contentTable) TableManager.reload(window.contentTable);
                ModalManager.hide('#contentModal');
                CampaignUtils.showToast('Content saved successfully!');
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    ModalManager.showErrors('#contentModal', xhr.responseJSON.errors);
                } else {
                    const message = CampaignUtils.handleError(xhr, 'Error saving content');
                    CampaignUtils.showToast(message, 'error');
                }
            },
            complete: function() {
                CampaignUtils.setButtonLoading(submitBtn, false);
            }
        });
    });

    // Update Form Submit
    // REPLACE your existing contentUpdateForm submit handler with this:

// Replace your form submission with this fixed version:

// Method 2: Fix the JavaScript to send data properly

$('#contentUpdateForm').on('submit', function(e) {
    e.preventDefault();
    const form = $(this);
    const contentId = $('#contentId').val();
    const submitBtn = form.find('button[type="submit"]');
    
    if (!contentId) {
        CampaignUtils.showToast('Content ID is missing', 'error');
        return;
    }
    
    // Try Method A: Regular form serialization with POST
    const formData = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        _method: 'PUT',
        task_name: $('#taskNameUpdate').val() || '',
        channel: $('#platformUpdate').val() || '',
        link: $('#linkUpdate').val() || '',
        product: $('#productUpdate').val() || '',
        views: $('#viewsUpdate').val() || '0',
        likes: $('#likesUpdate').val() || '0',
        comments: $('#commentsUpdate').val() || '0'
    };
    
    // DEBUG: Log what we're sending
    console.log('=== SENDING DATA ===');
    console.log('Form data object:', formData);
    console.log('Content ID:', contentId);
    
    // Validate required fields
    if (!formData.channel) {
        CampaignUtils.showToast('Platform/Channel is required', 'error');
        return;
    }
    
    let updateUrl = window.campaignContentUpdateUrl.replace(':campaignContentId', contentId);
    console.log('Update URL:', updateUrl);
    
    ModalManager.clearErrors('#contentUpdateModal');
    CampaignUtils.setButtonLoading(submitBtn, true);
    
    $.ajax({
        type: 'POST', // Use POST with _method spoofing
        url: updateUrl,
        data: formData, // Send as regular form data, not FormData
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            console.log('Success response:', response);
            if (window.contentTable) TableManager.reload(window.contentTable);
            ModalManager.hide('#contentUpdateModal');
            CampaignUtils.showToast('Content updated successfully!');
        },
        error: function(xhr) {
            console.error('Error response:', xhr.responseJSON);
            console.error('Status:', xhr.status);
            console.error('Status text:', xhr.statusText);
            
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                ModalManager.showErrors('#contentUpdateModal', xhr.responseJSON.errors);
            } else {
                const message = CampaignUtils.handleError(xhr, 'Error updating content');
                CampaignUtils.showToast(message, 'error');
            }
        },
        complete: function() {
            CampaignUtils.setButtonLoading(submitBtn, false);
        }
    });
});

    // ===== ACTION BUTTONS =====
    
    // Delete Content
    $(document).on('click', '.btnDeleteContent', function(e) {
        e.preventDefault();
        const rowData = window.contentTable ? TableManager.getRowData(window.contentTable, this) : null;
        if (!rowData) return;

        // Check if URL is defined
        if (typeof window.campaignContentDestroyUrl === 'undefined') {
            CampaignUtils.showToast('Delete URL is not configured', 'error');
            console.error('window.campaignContentDestroyUrl is not defined');
            return;
        }

        CampaignUtils.confirmDelete('Delete Content', 'Are you sure you want to delete this content?')
            .then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: window.campaignContentDestroyUrl.replace(':campaignContentId', rowData.id),
                        type: 'DELETE',
                        success: function() {
                            if (window.contentTable) TableManager.reload(window.contentTable);
                            CampaignUtils.showToast('Content deleted successfully!');
                        },
                        error: function(xhr) {
                            const message = CampaignUtils.handleError(xhr, 'Delete failed');
                            CampaignUtils.showToast(message, 'error');
                        }
                    });
                }
            });
    });

    // Status Toggles (FYP, Payment, Delivery)
    $(document).on('click', '.btnFyp, .btnDeliver, .btnPay', function(e) {
        e.preventDefault();
        const button = $(this);
        const rowData = window.contentTable ? TableManager.getRowData(window.contentTable, this) : null;
        if (!rowData) return;

        // Check if URL is defined
        if (typeof window.campaignContentUpdateUrl === 'undefined') {
            CampaignUtils.showToast('Update URL is not configured', 'error');
            console.error('window.campaignContentUpdateUrl is not defined');
            return;
        }

        let actionType = '';
        if (button.hasClass('btnFyp')) actionType = 'fyp';
        else if (button.hasClass('btnDeliver')) actionType = 'deliver';
        else if (button.hasClass('btnPay')) actionType = 'payment';

        const url = window.campaignContentUpdateUrl.replace(':campaignContentId', rowData.id) + '/' + actionType;
        
        $.ajax({
            url: url,
            method: 'GET',
            success: function() {
                if (window.contentTable) TableManager.reload(window.contentTable);
                CampaignUtils.showToast('Status updated successfully');
            },
            error: function(xhr) {
                const message = CampaignUtils.handleError(xhr, 'Status update failed');
                CampaignUtils.showToast(message, 'error');
            }
        });
    });

    // Copy to Clipboard
    $(document).on('click', '.btnCopy, .btnKode', function(e) {
        e.preventDefault();
        const button = $(this);
        const rowData = window.contentTable ? TableManager.getRowData(window.contentTable, this) : null;
        if (!rowData) return;

        const textToCopy = button.hasClass('btnCopy') ? rowData.link : rowData.kode_ads;
        if (textToCopy) {
            copyToClipboard(textToCopy);
            CampaignUtils.showToast('Copied to clipboard!');
        }
    });

    // ===== CAMPAIGN LIST HANDLERS =====
    
    // Delete Campaign
    $(document).on('click', '.deleteButton', function() {
        const button = $(this);
        const campaignId = button.data('id');
        
        // Check if URL is defined
        if (typeof window.campaignDestroyUrl === 'undefined') {
            CampaignUtils.showToast('Delete URL is not configured', 'error');
            console.error('window.campaignDestroyUrl is not defined');
            return;
        }
        
        CampaignUtils.confirmDelete('Delete Campaign', 'This campaign and all its content will be deleted.')
            .then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: window.campaignDestroyUrl.replace(':id', campaignId),
                        type: 'DELETE',
                        success: function() {
                            if (window.campaignTable) TableManager.reload(window.campaignTable);
                            CampaignUtils.showToast('Campaign deleted successfully!');
                        },
                        error: function(xhr) {
                            const message = CampaignUtils.handleError(xhr, 'Delete failed');
                            CampaignUtils.showToast(message, 'error');
                        }
                    });
                }
            });
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    console.log('Campaign Enhanced JS loaded successfully');
});

// ===== UTILITY FUNCTIONS =====

// Copy text to clipboard
function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        // Modern clipboard API
        navigator.clipboard.writeText(text).then(() => {
            console.log('Text copied to clipboard');
        }).catch(err => {
            console.error('Failed to copy text: ', err);
            fallbackCopyToClipboard(text);
        });
    } else {
        // Fallback method
        fallbackCopyToClipboard(text);
    }
}

// Fallback copy method
function fallbackCopyToClipboard(text) {
    const tempInput = document.createElement('input');
    tempInput.style.position = 'absolute';
    tempInput.style.left = '-9999px';
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);
}

// Load content embed for detail modal
function loadContentEmbed(link, channel) {
    const embedContainer = $('#contentEmbed');
    
    if (!link) {
        embedContainer.html('<p class="text-muted">No content link provided</p>');
        return;
    }
    
    switch (channel) {
        case 'twitter_post':
            const twitterLink = link.replace('https://x.com/', 'https://twitter.com/');
            embedContainer.html(`<blockquote class="twitter-tweet"><a href="${twitterLink}"></a></blockquote>`);
            if (typeof twttr !== 'undefined') {
                twttr.widgets.load(embedContainer[0]);
            }
            break;
            
        case 'tiktok_video':
            embedContainer.html('<div class="text-center"><div class="spinner-border"></div></div>');
            $.ajax({
                url: `https://www.tiktok.com/oembed?url=${encodeURIComponent(link)}`,
                success: function(response) {
                    embedContainer.html(response.html);
                },
                error: function() {
                    embedContainer.html(`<a href="${link}" target="_blank" class="btn btn-primary">View TikTok Video</a>`);
                }
            });
            break;
            
        case 'instagram_feed':
            const cleanLink = link.split('?')[0];
            const embedLink = cleanLink.endsWith('/') ? cleanLink + 'embed' : cleanLink + '/embed';
            embedContainer.html(`<iframe width="315" height="560" src="${embedLink}" frameborder="0"></iframe>`);
            break;
            
        case 'youtube_video':
            const videoId = link.split('/').pop();
            embedContainer.html(`<iframe width="315" height="560" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe>`);
            break;
            
        case 'shopee_video':
            embedContainer.html(`<iframe src="${link}" width="315" height="560" frameborder="0" allowfullscreen></iframe>`);
            break;
            
        default:
            embedContainer.html(`<a href="${link}" target="_blank" class="btn btn-primary">View Content</a>`);
            break;
    }
}