<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_File_Htaccess {

	/**
	 * Create .htaccess file (Qeek)
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
					'<IfModule mod_mime.c>',
					'AddType application/octet-stream .qwp',
					'</IfModule>',
					'<IfModule mod_dir.c>',
					'DirectoryIndex index.php',
					'</IfModule>',
					'<IfModule mod_autoindex.c>',
					'Options -Indexes',
					'</IfModule>',
				)
			)
		);
	}

	/**
	 * Create .htaccess file (LiteSpeed)
	 *
	 * @param  string  $path Path to file
	 * @return boolean
	 */
	public static function litespeed( $path ) {
		return QNAP_File::create_with_markers(
			$path,
			'LiteSpeed',
			array(
				'<IfModule Litespeed>',
				'SetEnv noabort 1',
				'</IfModule>',
			)
		);
	}
}
