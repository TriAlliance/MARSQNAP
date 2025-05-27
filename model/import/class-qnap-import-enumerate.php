<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Import_Enumerate {

	public static function execute( $params ) {

		// Set progress
		QNAP_Status::info( __( 'Retrieving a list of all WordPress files...', QNAP_PLUGIN_NAME ) );

		// Open the archive file for reading
		$archive = new QNAP_Extractor( qnap_archive_path( $params ) );

		// Get total files count
		$params['total_files_count'] = $archive->get_total_files_count();

		// Get total files size
		$params['total_files_size'] = $archive->get_total_files_size();

		// Close the archive file
		$archive->close();

		// Set progress
		QNAP_Status::info( __( 'Done retrieving a list of all WordPress files.', QNAP_PLUGIN_NAME ) );

		return $params;
	}
}
