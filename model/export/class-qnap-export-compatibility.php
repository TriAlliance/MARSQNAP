<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Export_Compatibility {

	public static function execute( $params ) {

		// Set progress
		QNAP_Status::info( __( 'Checking extensions compatibility...', QNAP_PLUGIN_NAME ) );

		// Get messages
		$messages = QNAP_Compatibility::get( $params );

		// Set messages
		if ( empty( $messages ) ) {
			return $params;
		}

		// Error message
		throw new QNAP_Compatibility_Exception( implode( $messages ) );
	}
}
