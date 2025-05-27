<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Handler {

	/**
	 * Error handler
	 *
	 * @param  integer $errno   Error level
	 * @param  string  $errstr  Error message
	 * @param  string  $errfile Error file
	 * @param  integer $errline Error line
	 * @return void
	 */
	public static function error( $errno, $errstr, $errfile, $errline ) {
		Q_Log::error(
			array(
				'Number'  => $errno,
				'Message' => $errstr,
				'File'    => $errfile,
				'Line'    => $errline,
			)
		);
	}

	/**
	 * Shutdown handler
	 *
	 * @return void
	 */
	public static function shutdown() {
		if ( ( $error = error_get_last() ) ) {
			Q_Log::error( $error );
		}
	}
}
