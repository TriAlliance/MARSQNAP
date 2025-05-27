<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Import_Clean {

	public static function execute( $params ) {
		global $wpdb;

		// Get database client
		if ( empty( $wpdb->use_mysqli ) ) {
			$mysql = new QNAP_Database_Mysql( $wpdb );
		} else {
			$mysql = new QNAP_Database_Mysqli( $wpdb );
		}

		// Flush mainsite tables
		$mysql->add_table_prefix_filter( qnap_table_prefix( 'mainsite' ) );
		$mysql->flush();

		// Delete storage files
		QNAP_Directory::delete( qnap_storage_path( $params ) );

		// Exit in console
		if ( defined( 'WP_CLI' ) ) {
			return $params;
		}

		exit;
	}
}
