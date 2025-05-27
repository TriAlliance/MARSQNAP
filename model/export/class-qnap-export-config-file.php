<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Export_Config_File {

	public static function execute( $params ) {

		$package_bytes_written = 0;

		// Set archive bytes offset
		if ( isset( $params['archive_bytes_offset'] ) ) {
			$archive_bytes_offset = (int) $params['archive_bytes_offset'];
		} else {
			$archive_bytes_offset = qnap_archive_bytes( $params );
		}

		// Set package bytes offset
		if ( isset( $params['package_bytes_offset'] ) ) {
			$package_bytes_offset = (int) $params['package_bytes_offset'];
		} else {
			$package_bytes_offset = 0;
		}

		// Get total package size
		if ( isset( $params['total_package_size'] ) ) {
			$total_package_size = (int) $params['total_package_size'];
		} else {
			$total_package_size = qnap_package_bytes( $params );
		}

		// What percent of package have we processed?
		$progress = (int) min( ( $package_bytes_offset / $total_package_size ) * 100, 100 );

		// Set progress
		QNAP_Status::info( sprintf( __( 'Archiving configuration file...<br />%d%% complete', QNAP_PLUGIN_NAME ), $progress ) );

		// Open the archive file for writing
		$archive = new QNAP_Compressor( qnap_archive_path( $params ) );

		// Set the file pointer to the one that we have saved
		$archive->set_file_pointer( $archive_bytes_offset );

		// Add package.json to archive
		if ( $archive->add_file( qnap_package_path( $params ), QNAP_PACKAGE_NAME, $package_bytes_written, $package_bytes_offset ) ) {

			// Set progress
			QNAP_Status::info( __( 'Done archiving configuration file.', QNAP_PLUGIN_NAME ) );

			// Unset archive bytes offset
			unset( $params['archive_bytes_offset'] );

			// Unset package bytes offset
			unset( $params['package_bytes_offset'] );

			// Unset total package size
			unset( $params['total_package_size'] );

			// Unset completed flag
			unset( $params['completed'] );

		} else {

			// Get archive bytes offset
			$archive_bytes_offset = $archive->get_file_pointer();

			// What percent of package have we processed?
			$progress = (int) min( ( $package_bytes_offset / $total_package_size ) * 100, 100 );

			// Set progress
			QNAP_Status::info( sprintf( __( 'Archiving configuration file...<br />%d%% complete', QNAP_PLUGIN_NAME ), $progress ) );

			// Set archive bytes offset
			$params['archive_bytes_offset'] = $archive_bytes_offset;

			// Set package bytes offset
			$params['package_bytes_offset'] = $package_bytes_offset;

			// Set total package size
			$params['total_package_size'] = $total_package_size;

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
