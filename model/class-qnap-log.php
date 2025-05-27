<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class Q_Log {

	public static function error( $params ) {
		$data = array();

		// Add date
		$data[] = date( 'M d Y H:i:s' );

		// Add params
		$data[] = json_encode( $params );

		// Add empty line
		$data[] = PHP_EOL;

		// Write log data
		if ( $handle = qnap_open( qnap_error_path(), 'a' ) ) {
			qnap_write( $handle, implode( PHP_EOL, $data ) );
			qnap_close( $handle );
		}
	}
}
