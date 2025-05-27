<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Export_Download {

	public static function execute( $params ) {

		// Set progress
		QNAP_Status::info( __( 'Renaming exported file...', QNAP_PLUGIN_NAME ) );

		// Open the archive file for writing
		$archive = new QNAP_Compressor( qnap_archive_path( $params ) );

		// Append EOF block
		$archive->close( true );

		// Rename archive file
		if ( rename( qnap_archive_path( $params ), qnap_backup_path( $params ) ) ) {

			$blog_id = null;

			// Get subsite Blog ID
			if ( isset( $params['options']['sites'] ) && ( $sites = $params['options']['sites'] ) ) {
				if ( count( $sites ) === 1 ) {
					$blog_id = array_shift( $sites );
				}
			}

			// Set archive details
			$file = qnap_archive_name( $params );
			$link = qnap_backup_url( $params );
			$size = qnap_backup_size( $params );
			$name = qnap_site_name( $blog_id );

			// Set progress
			QNAP_Status::download(
				sprintf(
					__(
						'<a href="%s" class="qnap-button-green qnap-emphasize qnap-button-download" title="%s" download="%s">' .
						'<span>Download %s</span>' .
						'<em>Size: %s</em>' .
						'</a>',
						QNAP_PLUGIN_NAME
					),
					$link,
					$name,
					$file,
					$name,
					$size
				)
			);
		}

		return $params;
	}
}
