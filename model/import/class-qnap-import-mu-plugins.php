<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Import_Mu_Plugins {

	public static function execute( $params ) {

		// Set progress
		QNAP_Status::info( __( 'Activating mu-plugins...', QNAP_PLUGIN_NAME ) );

		$exclude_files = array(
			QNAP_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . QNAP_ENDURANCE_PAGE_CACHE_NAME,
			QNAP_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . QNAP_ENDURANCE_PHP_EDGE_NAME,
			QNAP_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . QNAP_ENDURANCE_BROWSER_CACHE_NAME,
			QNAP_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . QNAP_GD_SYSTEM_PLUGIN_NAME,
			QNAP_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . QNAP_WP_STACK_CACHE_NAME,
			QNAP_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . QNAP_WP_COMSH_LOADER_NAME,
			QNAP_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . QNAP_WP_COMSH_HELPER_NAME,
			QNAP_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . QNAP_WP_ENGINE_SYSTEM_PLUGIN_NAME,
			QNAP_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . QNAP_WPE_SIGN_ON_PLUGIN_NAME,
			QNAP_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . QNAP_WP_ENGINE_SECURITY_AUDITOR_NAME,
			QNAP_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . QNAP_WP_CERBER_SECURITY_NAME,
		);

		// Open the archive file for reading
		$archive = new QNAP_Extractor( qnap_archive_path( $params ) );

		// Unpack mu-plugins files
		$archive->extract_by_files_array( WP_CONTENT_DIR, array( QNAP_MUPLUGINS_NAME ), $exclude_files );

		// Close the archive file
		$archive->close();

		// Set progress
		QNAP_Status::info( __( 'Done activating mu-plugins.', QNAP_PLUGIN_NAME ) );

		return $params;
	}
}
