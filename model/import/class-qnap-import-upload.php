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

		self::validate();

		$uploadfile = wp_handle_upload( $_FILES['upload-file'], array('test_form' => false, 'action' => 'qnap_import', 'test_type' => false) );
		$error   = $uploadfile['error'];
		$upload  = $uploadfile['file'];
		$archive = qnap_archive_path( $params );

		switch ( $error ) {
			case UPLOAD_ERR_OK:
				try {
					qnap_copy( $upload, $archive );
					qnap_unlink( $upload );

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
}
