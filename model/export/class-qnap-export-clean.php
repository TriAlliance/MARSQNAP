<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Export_Clean {

	public static function execute( $params ) {

		Qnap_Log::append(qnap_get_log_client($params), sprintf('[Multi-Application Recovery Service] WordPress backup file is ready. File size: %s', qnap_backup_size( $params )));

		// Delete storage files
		QNAP_Directory::delete( qnap_storage_path( $params ) );

		// Exit in console
		if ( defined( 'WP_CLI' ) ) {
			return $params;
		}

		exit;
	}
}
