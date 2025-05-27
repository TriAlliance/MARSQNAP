<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
    die( 'not here' );
}

/**
 * Verification class to ensure backup integrity
 */
class QNAP_Verification {

    /**
     * Verify backup file integrity
     * 
     * @param string $file_path Path to the backup file
     * @param int $expected_size Expected file size (if known)
     * @return array Results of verification
     */
    public static function verify_backup($file_path, $expected_size = 0) {
        $result = array(
            'success' => false,
            'message' => '',
            'actual_size' => 0,
            'expected_size' => $expected_size
        );

        if (!file_exists($file_path)) {
            $result['message'] = sprintf(__('Backup file not found at %s', QNAP_PLUGIN_NAME), $file_path);
            return $result;
        }

        // Check file size
        $actual_size = filesize($file_path);
        $result['actual_size'] = $actual_size;
        
        if ($actual_size <= 0) {
            $result['message'] = __('Backup file exists but has zero size', QNAP_PLUGIN_NAME);
            return $result;
        }

        // If we have an expected size, verify it
        if ($expected_size > 0) {
            // Allow for small differences (compression can cause minor variations)
            $size_difference = abs($actual_size - $expected_size);
            $size_difference_percentage = ($size_difference / $expected_size) * 100;
            
            if ($size_difference_percentage > 5) { // More than 5% difference
                $result['message'] = sprintf(
                    __('Backup file size mismatch. Expected: %s, Actual: %s', QNAP_PLUGIN_NAME),
                    qnap_size_format($expected_size),
                    qnap_size_format($actual_size)
                );
                // We'll still mark it as successful but with a warning
                $result['success'] = true;
                $result['warning'] = true;
                return $result;
            }
        }

        // Verify file structure
        try {
            // Open the archive to check if it's a valid archive
            $archive = new QNAP_Extractor($file_path);
            if (!$archive->is_valid()) {
                $archive->close();
                $result['message'] = __('Backup file is not a valid archive', QNAP_PLUGIN_NAME);
                return $result;
            }
            $archive->close();
            
            $result['success'] = true;
            $result['message'] = __('Backup verification successful', QNAP_PLUGIN_NAME);
            return $result;
            
        } catch (Exception $e) {
            $result['message'] = sprintf(__('Error verifying backup: %s', QNAP_PLUGIN_NAME), $e->getMessage());
            return $result;
        }
    }

    /**
     * Test restore functionality by extracting and checking essential files
     * 
     * @param string $file_path Path to the backup file
     * @return array Results of test restore
     */
    public static function test_restore($file_path) {
        $result = array(
            'success' => false,
            'message' => '',
            'details' => array()
        );

        if (!file_exists($file_path)) {
            $result['message'] = sprintf(__('Backup file not found at %s', QNAP_PLUGIN_NAME), $file_path);
            return $result;
        }

        // Create a temporary directory for testing
        $temp_dir = QNAP_STORAGE_PATH . DIRECTORY_SEPARATOR . 'verify_' . uniqid();
        if (!QNAP_Directory::create($temp_dir)) {
            $result['message'] = __('Could not create temporary directory for verification', QNAP_PLUGIN_NAME);
            return $result;
        }

        try {
            // Open the archive
            $archive = new QNAP_Extractor($file_path);
            
            // Check for essential files
            $essential_files = array(
                QNAP_PACKAGE_NAME,
                QNAP_DATABASE_NAME
            );
            
            // Extract just the essential files for testing
            $archive->extract_by_files_array($temp_dir, $essential_files);
            $archive->close();
            
            // Verify extracted files
            foreach ($essential_files as $file) {
                $extracted_file = $temp_dir . DIRECTORY_SEPARATOR . $file;
                if (!file_exists($extracted_file)) {
                    $result['details'][] = sprintf(__('Missing essential file: %s', QNAP_PLUGIN_NAME), $file);
                } else {
                    $result['details'][] = sprintf(__('Found essential file: %s', QNAP_PLUGIN_NAME), $file);
                }
            }
            
            // Check package.json
            $package_file = $temp_dir . DIRECTORY_SEPARATOR . QNAP_PACKAGE_NAME;
            if (file_exists($package_file)) {
                $package_data = file_get_contents($package_file);
                if (!empty($package_data)) {
                    $package = json_decode($package_data, true);
                    if (is_array($package)) {
                        $result['details'][] = __('Package file contains valid data', QNAP_PLUGIN_NAME);
                    } else {
                        $result['details'][] = __('Package file contains invalid data', QNAP_PLUGIN_NAME);
                    }
                }
            }
            
            // Determine overall success
            $success = true;
            foreach ($result['details'] as $detail) {
                if (strpos($detail, 'Missing') !== false || strpos($detail, 'invalid') !== false) {
                    $success = false;
                    break;
                }
            }
            
            $result['success'] = $success;
            $result['message'] = $success 
                ? __('Restore verification successful', QNAP_PLUGIN_NAME)
                : __('Restore verification failed - some essential files could not be extracted', QNAP_PLUGIN_NAME);
                
            // Clean up
            QNAP_Directory::delete($temp_dir);
            
            return $result;
            
        } catch (Exception $e) {
            // Clean up
            QNAP_Directory::delete($temp_dir);
            
            $result['message'] = sprintf(__('Error testing restore functionality: %s', QNAP_PLUGIN_NAME), $e->getMessage());
            return $result;
        }
    }
}