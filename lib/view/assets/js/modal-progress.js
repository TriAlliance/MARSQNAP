jQuery(document).ready(function($) {
    // Handle showing/hiding of progress bar in both normal view and modal
    $(document).on('qnap-export-status', function(e, params) {
        if (params.type === 'progress') {
            // Update inline progress bar if it exists
            if ($('#qnap-backup-progress-container').length) {
                $('#qnap-backup-progress-bar').width(params.percent + '%');
                $('#qnap-backup-progress-percentage').text(params.percent + '%');
                
                // Extract ETA if available
                if (params.message) {
                    var etaMatch = params.message.match(/ETA: ([0-9]+ (?:min|sec)[^-]*)/);
                    if (etaMatch && etaMatch[1]) {
                        $('#qnap-backup-progress-eta').text(etaMatch[1]);
                    }
                    
                    // Update status text
                    $('#qnap-backup-progress-status').html(params.message);
                }
                
                // Show progress container if hidden
                $('#qnap-backup-progress-container').show();
            }
        } else if (params.type === 'done' || params.type === 'download' || params.type === 'error') {
            // Hide progress bar when done
            $('#qnap-backup-progress-container').hide();
        }
    });

    // Add test restore functionality to backup files
    $(document).on('click', '.qnap-test-restore', function(e) {
        e.preventDefault();
        var button = $(this);
        var backup = button.data('backup');
        var path = button.data('path');
        
        button.prop('disabled', true);
        button.html("<i class='qnap-icon-loader'></i> " + qnapBackupLocale.testing_restore);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'qnap_test_restore',
                backup: backup,
                path: path,
                secret_key: qnap_export.secret_key
            },
            success: function(response) {
                if (response.success) {
                    $("<div class='qnap-verification-success'><p><i class='qnap-icon-checkmark'></i> " + 
                        response.data.message + "</p></div>").insertAfter(button);
                } else {
                    $("<div class='qnap-verification-error'><p><i class='qnap-icon-notification'></i> " + 
                        response.data.message + "</p></div>").insertAfter(button);
                }
                button.prop('disabled', false);
                button.html("<i class='qnap-icon-checkmark'></i> " + qnapBackupLocale.test_restore);
            },
            error: function() {
                $("<div class='qnap-verification-error'><p><i class='qnap-icon-notification'></i> " + 
                    qnapBackupLocale.error_testing + "</p></div>").insertAfter(button);
                button.prop('disabled', false);
                button.html("<i class='qnap-icon-checkmark'></i> " + qnapBackupLocale.test_restore);
            }
        });
    });
});