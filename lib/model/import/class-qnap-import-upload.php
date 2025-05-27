<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
    die( 'not here' );
}

class QNAP_Import_Upload {

    private static function validate() {
        if ( ! array_key_exists( 'upload-file', $_FILES ) || ! is_array( $_FILES['upload-file'] ) ) {
            throw new QNAP_Import_Retry_Exception( __( 'Missing upload file.', QNAP_PLUGIN_NAME ), 400 );
        }

        if ( ! array_key_exists( 'error', $_FILES['upload-file'] ) ) {
            throw new QNAP_Import_Retry_Exception( __( 'Missing error key in upload file.', QNAP_PLUGIN_NAME ), 400 );
        }

        if ( ! array_key_exists( 'tmp_name', $_FILES['upload-file'] ) ) {
            throw new QNAP_Import_Retry_Exception( __( 'Missing tmp_name in upload file.', QNAP_PLUGIN_NAME ), 400 );
        }
    }

    public static function execute( $params ) {
        Qnap_Log::append(qnap_get_log_client($params), '[Multi-Application Recovery Service] Started restoring WordPress from file "' . basename( qnap_archive_path( $params ) . '"' ) );
        
        // Initialize progress tracking
        $params = QNAP_Progress_Bar::init($params, 'import');

        self::validate();

        $uploadfile = wp_handle_upload( $_FILES['upload-file'], array('test_form' => false, 'action' => 'qnap_import', 'test_type' => false) );
        $error   = $uploadfile['error'];
        $upload  = $uploadfile['file'];
        $archive = qnap_archive_path( $params );

        switch ( $error ) {
            case UPLOAD_ERR_OK:
                try {
                    // Set status for starting upload
                    $message = sprintf(__( 'Uploading file %s...', QNAP_PLUGIN_NAME ), basename($upload));
                    QNAP_Progress_Bar::update($params, 0, 100, $message);
                    
                    // Get file sizes
                    $size = filesize($upload);
                    
                    // Copy file with progress updates
                    self::copy_with_progress($upload, $archive, $params);
                    qnap_unlink( $upload );
                    
                    // Complete progress
                    $message = sprintf(__( 'File upload completed: %s', QNAP_PLUGIN_NAME ), basename($archive));
                    QNAP_Progress_Bar::update($params, 100, 100, $message);

                } catch ( Exception $e ) {
                    throw new QNAP_Import_Retry_Exception( sprintf( __( 'Unable to upload the file because %s', QNAP_PLUGIN_NAME ), $e->getMessage() ), 400 );
                }

                break;

            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
            case UPLOAD_ERR_PARTIAL:
            case UPLOAD_ERR_NO_FILE:
                // File is too large
                throw new QNAP_Import_Retry_Exception( __( 'The file is too large for this server.', QNAP_PLUGIN_NAME ), 413 );

            case UPLOAD_ERR_NO_TMP_DIR:
                throw new QNAP_Import_Retry_Exception( __( 'Missing a temporary folder.', QNAP_PLUGIN_NAME ), 400 );

            case UPLOAD_ERR_CANT_WRITE:
                throw new QNAP_Import_Retry_Exception( __( 'Failed to write file to disk.', QNAP_PLUGIN_NAME ), 400 );

            case UPLOAD_ERR_EXTENSION:
                throw new QNAP_Import_Retry_Exception( __( 'A PHP extension stopped the file upload.', QNAP_PLUGIN_NAME ), 400 );

            default:
                throw new QNAP_Import_Retry_Exception( sprintf( __( 'Unrecognized error %s during upload.', QNAP_PLUGIN_NAME ), $error ), 400 );
        }

        exit;
    }
    
    /**
     * Copy a file with progress updates
     *
     * @param string $source Source file
     * @param string $destination Destination file
     * @param array $params Parameters for tracking progress
     */
    private static function copy_with_progress($source, $destination, &$params) {
        $source_size = filesize($source);
        $chunk_size = 1024 * 1024; // 1MB chunks
        $copied = 0;
        
        $src = fopen($source, 'rb');
        $dst = fopen($destination, 'wb');
        
        while (!feof($src)) {
            $buffer = fread($src, $chunk_size);
            fwrite($dst, $buffer);
            
            $copied += strlen($buffer);
            $progress = min(round(($copied / $source_size) * 100), 100);
            
            // Update progress every 5MB
            if ($copied % (5 * $chunk_size) < $chunk_size) {
                $message = sprintf(__( 'Uploading file: %s (%s of %s)', QNAP_PLUGIN_NAME ), 
                    basename($source),
                    qnap_size_format($copied),
                    qnap_size_format($source_size)
                );
                QNAP_Progress_Bar::update($params, $copied, $source_size, $message);
            }
        }
        
        fclose($src);
        fclose($dst);
        
        return true;
    }
}