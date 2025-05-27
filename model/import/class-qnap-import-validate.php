<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Import_Validate {

	public static function execute( $params ) {

		// Verify file if size > 2GB and PHP = 32-bit
		if ( ! qnap_is_filesize_supported( qnap_archive_path( $params ) ) ) {
			throw new QNAP_Import_Exception( __( 'Your PHP is 32-bit. In order to import your file, please change your PHP version to 64-bit and try again.', QNAP_PLUGIN_NAME ) );
		}

		// Set archive bytes offset
		if ( isset( $params['archive_bytes_offset'] ) ) {
			$archive_bytes_offset = (int) $params['archive_bytes_offset'];
		} else {
			$archive_bytes_offset = 0;
		}

		// Set file bytes offset
		if ( isset( $params['file_bytes_offset'] ) ) {
			$file_bytes_offset = (int) $params['file_bytes_offset'];
		} else {
			$file_bytes_offset = 0;
		}

		// Get total archive size
		if ( isset( $params['total_archive_size'] ) ) {
			$total_archive_size = (int) $params['total_archive_size'];
		} else {
			$total_archive_size = qnap_archive_bytes( $params );
		}

		// What percent of archive have we processed?
		$progress = (int) min( ( $archive_bytes_offset / $total_archive_size ) * 100, 100 );

		// Set progress
		QNAP_Status::info( sprintf( __( 'Unpacking archive...<br />%d%% complete', QNAP_PLUGIN_NAME ), $progress ) );

		// Open the archive file for reading
		$archive = new QNAP_Extractor( qnap_archive_path( $params ) );

		// Set the file pointer to the one that we have saved
		$archive->set_file_pointer( $archive_bytes_offset );

		// Validate the archive file consistency
		if ( ! $archive->is_valid() ) {
			throw new QNAP_Import_Exception( __( 'The archive file is corrupted. Follow <a href="https://help.qeek.com/knowledgebase/corrupted-archive/" target="_blank">this article</a> to resolve the problem.', QNAP_PLUGIN_NAME ) );
		}

		// Flag to hold if file data has been processed
		$completed = true;

		if ( $archive->has_not_reached_eof() ) {
			$file_bytes_written = 0;

			// Unpack package.json, multisite.json and database.sql files
			if ( ( $completed = $archive->extract_by_files_array( qnap_storage_path( $params ), array( QNAP_PACKAGE_NAME, QNAP_MULTISITE_NAME, QNAP_DATABASE_NAME ), array(), array(), $file_bytes_written, $file_bytes_offset ) ) ) {
				$file_bytes_offset = 0;
			}

			// Get archive bytes offset
			$archive_bytes_offset = $archive->get_file_pointer();
		}

		// End of the archive?
		if ( $archive->has_reached_eof() ) {

			// Check package.json file
			if ( false === is_file( qnap_package_path( $params ) ) ) {
				throw new QNAP_Import_Exception( __( 'Please make sure that your file was exported using <strong>QNAP WP Migration</strong> plugin.', QNAP_PLUGIN_NAME ) );
			}

			// Set progress
			QNAP_Status::info( __( 'Done unpacking archive.', QNAP_PLUGIN_NAME ) );

			// Unset archive bytes offset
			unset( $params['archive_bytes_offset'] );

			// Unset file bytes offset
			unset( $params['file_bytes_offset'] );

			// Unset total archive size
			unset( $params['total_archive_size'] );

			// Unset completed flag
			unset( $params['completed'] );

		} else {

			// What percent of archive have we processed?
			$progress = (int) min( ( $archive_bytes_offset / $total_archive_size ) * 100, 100 );

			// Set progress
			QNAP_Status::info( sprintf( __( 'Unpacking archive...<br />%d%% complete', QNAP_PLUGIN_NAME ), $progress ) );

			// Set archive bytes offset
			$params['archive_bytes_offset'] = $archive_bytes_offset;

			// Set file bytes offset
			$params['file_bytes_offset'] = $file_bytes_offset;

			// Set total archive size
			$params['total_archive_size'] = $total_archive_size;

			// Set completed flag
			$params['completed'] = $completed;
		}

		// Close the archive file
		$archive->close();

		return $params;
	}
}
