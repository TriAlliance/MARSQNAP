<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Export_Database_File {

	public static function execute( $params ) {

		// Set exclude database
		if ( isset( $params['options']['no_database'] ) ) {
			return $params;
		}

		$database_bytes_written = 0;

		// Set archive bytes offset
		if ( isset( $params['archive_bytes_offset'] ) ) {
			$archive_bytes_offset = (int) $params['archive_bytes_offset'];
		} else {
			$archive_bytes_offset = qnap_archive_bytes( $params );
		}

		// Set database bytes offset
		if ( isset( $params['database_bytes_offset'] ) ) {
			$database_bytes_offset = (int) $params['database_bytes_offset'];
		} else {
			$database_bytes_offset = 0;
		}

		// Get total database size
		if ( isset( $params['total_database_size'] ) ) {
			$total_database_size = (int) $params['total_database_size'];
		} else {
			$total_database_size = qnap_database_bytes( $params );
		}

		// What percent of database have we processed?
		$progress = (int) min( ( $database_bytes_offset / $total_database_size ) * 100, 100 );

		// Set progress
		QNAP_Status::info( sprintf( __( 'Archiving database...<br />%d%% complete', QNAP_PLUGIN_NAME ), $progress ) );

		// Open the archive file for writing
		$archive = new QNAP_Compressor( qnap_archive_path( $params ) );

		// Set the file pointer to the one that we have saved
		$archive->set_file_pointer( $archive_bytes_offset );

		// Add database.sql to archive
		if ( $archive->add_file( qnap_database_path( $params ), QNAP_DATABASE_NAME, $database_bytes_written, $database_bytes_offset ) ) {

			// Set progress
			QNAP_Status::info( __( 'Done archiving database.', QNAP_PLUGIN_NAME ) );

			// Unset archive bytes offset
			unset( $params['archive_bytes_offset'] );

			// Unset database bytes offset
			unset( $params['database_bytes_offset'] );

			// Unset total database size
			unset( $params['total_database_size'] );

			// Unset completed flag
			unset( $params['completed'] );

		} else {

			// Get archive bytes offset
			$archive_bytes_offset = $archive->get_file_pointer();

			// What percent of database have we processed?
			$progress = (int) min( ( $database_bytes_offset / $total_database_size ) * 100, 100 );

			// Set progress
			QNAP_Status::info( sprintf( __( 'Archiving database...<br />%d%% complete', QNAP_PLUGIN_NAME ), $progress ) );

			// Set archive bytes offset
			$params['archive_bytes_offset'] = $archive_bytes_offset;

			// Set database bytes offset
			$params['database_bytes_offset'] = $database_bytes_offset;

			// Set total database size
			$params['total_database_size'] = $total_database_size;

			// Set completed flag
			$params['completed'] = false;
		}

		// Truncate the archive file
		$archive->truncate();

		// Close the archive file
		$archive->close();

		return $params;
	}
}
