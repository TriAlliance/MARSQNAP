<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Status_Controller {

	public static function status( $params = array() ) {
		qnap_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = qnap_sanitized_params();
		}

		// Set secret key
		$secret_key = null;
		if ( isset( $params['secret_key'] ) ) {
			$secret_key = trim( $params['secret_key'] );
		}

		try {
			// Ensure that unauthorized people cannot access status action
			qnap_verify_secret_key( $secret_key );
		} catch ( QNAP_Not_Valid_Secret_Key_Exception $e ) {
			http_response_code(403);
			exit;
		}

		exit;
	}
}
