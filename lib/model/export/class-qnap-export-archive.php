<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Export_Archive {

	public static function execute( $params ) {

		// Set progress
		QNAP_Status::info( __( 'Creating an empty archive...', QNAP_PLUGIN_NAME ) );

		// Create empty archive file
		$archive = new QNAP_Compressor( qnap_archive_path( $params ) );
		$archive->close();

		// Set progress
		QNAP_Status::info( __( 'Done creating an empty archive.', QNAP_PLUGIN_NAME ) );

		return $params;
	}
}
