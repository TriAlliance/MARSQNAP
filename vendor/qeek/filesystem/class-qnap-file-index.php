<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_File_Index {

	/**
	 * Create index file
	 *
	 * @param  string  $path Path to file
	 * @return boolean
	 */
	public static function create( $path ) {
		return QNAP_File::create( $path, 'not here' );
	}
}
