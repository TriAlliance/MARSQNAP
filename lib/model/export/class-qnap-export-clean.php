<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
    die( 'not here' );
}

class QNAP_Export_Clean {

    public static function execute( $params ) {

        Qnap_Log::append(qnap_get_log_client($params), sprintf('[Multi-Application Recovery Service] WordPress backup file is ready. File size: %s', qnap_backup_size( $params )));

        // Log the exact file path for debugging purposes
        $backup_file_path = qnap_backup_path($params);
        
        // Keep track of whether we need to delete the storage directory
        $delete_storage = true;
        
        // Check if this is being called from a manual export (browser)
        if (isset($params['qnap_manual_export'])) {
            // For manual exports, we want to keep the backup file for download
            $delete_storage = false;
            
            // Verify the backup file exists and is the correct size
            if (file_exists($backup_file_path)) {
                $actual_size = filesize($backup_file_path);
                $expected_size = isset($params['total_archive_size']) ? $params['total_archive_size'] : 0;
                
                if ($actual_size > 0 && ($expected_size == 0 || abs($actual_size - $expected_size) < 1024)) {
                    // File size looks good
                    Qnap_Log::append(qnap_get_log_client($params), 
                        sprintf('[Multi-Application Recovery Service] Backup verification successful. File: %s, Size: %s', 
                        basename($backup_file_path), 
                        qnap_size_format($actual_size))
                    );
                } else {
                    // Log the size discrepancy
                    Qnap_Log::append(qnap_get_log_client($params), 
                        sprintf('[Multi-Application Recovery Service] Warning: Backup file size mismatch. Expected: %s, Actual: %s', 
                        qnap_size_format($expected_size), 
                        qnap_size_format($actual_size))
                    );
                }
            } else {
                // Log that the file wasn't found
                Qnap_Log::append(qnap_get_log_client($params), 
                    sprintf('[Multi-Application Recovery Service] Error: Backup file not found at %s', $backup_file_path)
                );
            }
        } else {
            // For automated/scheduled exports, clean up all files
            Qnap_Log::append(qnap_get_log_client($params), 
                '[Multi-Application Recovery Service] Cleaning up temporary files after scheduled backup'
            );
        }

        // Delete storage files
        if ($delete_storage) {
            QNAP_Directory::delete(qnap_storage_path($params));
        }

        // Exit in console
        if (defined('WP_CLI')) {
            return $params;
        }

        exit;
    }
}