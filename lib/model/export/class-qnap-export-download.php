<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
    die( 'not here' );
}

class QNAP_Export_Download {

    public static function execute( $params ) {

        // Set progress
        QNAP_Status::info( __( 'Finalizing exported file...', QNAP_PLUGIN_NAME ) );

        // Open the archive file for writing
        $archive = new QNAP_Compressor( qnap_archive_path( $params ) );

        // Append EOF block
        $archive->close( true );

        // Set total archive size for verification
        $params['total_archive_size'] = filesize(qnap_archive_path($params));

        // Rename archive file
        if ( rename( qnap_archive_path( $params ), qnap_backup_path( $params ) ) ) {

            // Verify the backup for integrity
            $verification_result = QNAP_Verification::verify_backup(
                qnap_backup_path($params), 
                $params['total_archive_size']
            );
            
            // Store verification result in the params
            $params['verification_result'] = $verification_result;
            
            // Log verification result
            if ($verification_result['success']) {
                if (isset($verification_result['warning'])) {
                    Qnap_Log::append(
                        qnap_get_log_client($params), 
                        sprintf(
                            '[Multi-Application Recovery Service] Backup verification completed with warnings: %s', 
                            $verification_result['message']
                        )
                    );
                } else {
                    Qnap_Log::append(
                        qnap_get_log_client($params), 
                        '[Multi-Application Recovery Service] Backup verification completed successfully'
                    );
                }
            } else {
                Qnap_Log::append(
                    qnap_get_log_client($params), 
                    sprintf(
                        '[Multi-Application Recovery Service] Backup verification failed: %s', 
                        $verification_result['message']
                    )
                );
            }

            $blog_id = null;

            // Get subsite Blog ID
            if ( isset( $params['options']['sites'] ) && ( $sites = $params['options']['sites'] ) ) {
                if ( count( $sites ) === 1 ) {
                    $blog_id = array_shift( $sites );
                }
            }

            // Set archive details
            $file = qnap_archive_name( $params );
            $link = qnap_backup_url( $params );
            $size = qnap_backup_size( $params );
            $name = qnap_site_name( $blog_id );

            // Build download message with verification status
            $download_message = sprintf(
                __(
                    '<a href="%s" class="qnap-button-green qnap-emphasize qnap-button-download" title="%s" download="%s">' .
                    '<span>Download %s</span>' .
                    '<em>Size: %s</em>' .
                    '</a>',
                    QNAP_PLUGIN_NAME
                ),
                $link,
                $name,
                $file,
                $name,
                $size
            );
            
            // Add verification result to the message
            if ($verification_result['success']) {
                if (isset($verification_result['warning'])) {
                    $download_message .= '<div class="qnap-verification-warning">' .
                        '<p><i class="qnap-icon-notification"></i> ' . 
                        __('Warning: The backup was created but the file size is different than expected. It may still be usable.', QNAP_PLUGIN_NAME) . 
                        '</p></div>';
                } else {
                    $download_message .= '<div class="qnap-verification-success">' .
                        '<p><i class="qnap-icon-checkmark"></i> ' . 
                        __('Backup verified successfully!', QNAP_PLUGIN_NAME) . 
                        '</p></div>';
                }
            } else {
                $download_message .= '<div class="qnap-verification-error">' .
                    '<p><i class="qnap-icon-notification"></i> ' . 
                    __('Warning: The backup verification failed. This backup may not be restorable.', QNAP_PLUGIN_NAME) . 
                    '</p></div>';
            }
            
            // Add restore test option
            $download_message .= '<p><button type="button" id="qnap-test-restore" class="qnap-button-blue" ' .
                'data-backup="' . esc_attr($file) . '" ' .
                'data-path="' . esc_attr(qnap_backup_path($params)) . '">' .
                '<i class="qnap-icon-checkmark"></i> ' . 
                __('Test Restore Capability', QNAP_PLUGIN_NAME) .
                '</button></p>';

            // Set progress
            QNAP_Status::download($download_message);
            
            // Add CSS for verification messages
            echo '<style>
                .qnap-verification-success {
                    background-color: #f0f7ed;
                    border-left: 4px solid #7ad03a;
                    padding: 10px;
                    margin: 15px 0;
                }
                .qnap-verification-warning {
                    background-color: #fff8e5;
                    border-left: 4px solid #ffba00;
                    padding: 10px;
                    margin: 15px 0;
                }
                .qnap-verification-error {
                    background-color: #fef1f1;
                    border-left: 4px solid #dc3232;
                    padding: 10px;
                    margin: 15px 0;
                }
            </style>';
            
            // Add JavaScript for test restore
            echo '<script>
                jQuery(document).ready(function($) {
                    $("#qnap-test-restore").on("click", function() {
                        var button = $(this);
                        var backup = button.data("backup");
                        var path = button.data("path");
                        
                        button.prop("disabled", true);
                        button.html("<i class=\'qnap-icon-loader\'></i> ' . __('Testing Restore...', QNAP_PLUGIN_NAME) . '");
                        
                        $.ajax({
                            url: ajaxurl,
                            type: "POST",
                            data: {
                                action: "qnap_test_restore",
                                backup: backup,
                                path: path,
                                secret_key: qnap_export.secret_key
                            },
                            success: function(response) {
                                if (response.success) {
                                    $("<div class=\'qnap-verification-success\'><p><i class=\'qnap-icon-checkmark\'></i> " + 
                                        response.data.message + "</p></div>").insertAfter(button);
                                } else {
                                    $("<div class=\'qnap-verification-error\'><p><i class=\'qnap-icon-notification\'></i> " + 
                                        response.data.message + "</p></div>").insertAfter(button);
                                }
                                button.prop("disabled", false);
                                button.html("<i class=\'qnap-icon-checkmark\'></i> ' . __('Test Restore Capability', QNAP_PLUGIN_NAME) . '");
                            },
                            error: function() {
                                $("<div class=\'qnap-verification-error\'><p><i class=\'qnap-icon-notification\'></i> " + 
                                    "' . __('Error testing restore capability', QNAP_PLUGIN_NAME) . '" + "</p></div>").insertAfter(button);
                                button.prop("disabled", false);
                                button.html("<i class=\'qnap-icon-checkmark\'></i> ' . __('Test Restore Capability', QNAP_PLUGIN_NAME) . '");
                            }
                        });
                    });
                });
            </script>';
        }

        return $params;
    }
}