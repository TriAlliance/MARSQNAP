<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_File_Webconfig {

	/**
	 * Create web.config file
	 *
	 * @param  string  $path Path to file
	 * @return boolean
	 */
	public static function create( $path ) {
		return QNAP_File::create(
			$path,
			implode(
				PHP_EOL,
				array(
					'<configuration>',
					'<system.webServer>',
					'<staticContent>',
					'<mimeMap fileExtension=".qwp" mimeType="application/octet-stream" />',
					'</staticContent>',
					'<defaultDocument>',
					'<files>',
					'<add value="index.php" />',
					'</files>',
					'</defaultDocument>',
					'<directoryBrowse enabled="false" />',
					'</system.webServer>',
					'</configuration>',
				)
			)
		);
	}
}
