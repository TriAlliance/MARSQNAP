<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Import_Permalinks {

	public static function execute( $params ) {
		global $wp_rewrite;

		// Set progress
		QNAP_Status::info( __( 'Getting WordPress permalinks settings...', QNAP_PLUGIN_NAME ) );

		// Get using permalinks
		$params['using_permalinks'] = (int) $wp_rewrite->using_permalinks();

		// Set progress
		QNAP_Status::info( __( 'Done getting WordPress permalinks settings.', QNAP_PLUGIN_NAME ) );

		return $params;
	}
}
