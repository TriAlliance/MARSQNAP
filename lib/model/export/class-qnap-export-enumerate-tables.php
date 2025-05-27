<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Export_Enumerate_Tables {

	public static function execute( $params, QNAP_Database $mysql = null ) {
		global $wpdb;

		// Set exclude database
		if ( isset( $params['options']['no_database'] ) ) {
			return $params;
		}

		// Get total tables count
		if ( isset( $params['total_tables_count'] ) ) {
			$total_tables_count = (int) $params['total_tables_count'];
		} else {
			$total_tables_count = 0;
		}

		// Set progress
		QNAP_Status::info( __( 'Retrieving a list of WordPress database tables...', QNAP_PLUGIN_NAME ) );

		// Get database client
		if ( is_null( $mysql ) ) {
			$mysql = QNAP_Database_Utility::create_client();
		}

		// Include table prefixes
		if ( qnap_table_prefix() ) {
			$mysql->add_table_prefix_filter( qnap_table_prefix() );
		} else {
			foreach ( $mysql->get_tables() as $table_name ) {
				$mysql->add_table_prefix_filter( $table_name );
			}
		}

		// Include table prefixes (Webba Booking)
		foreach ( array( 'wbk_services', 'wbk_days_on_off', 'wbk_locked_time_slots', 'wbk_appointments', 'wbk_cancelled_appointments', 'wbk_email_templates', 'wbk_service_categories', 'wbk_gg_calendars', 'wbk_coupons' ) as $table_name ) {
			$mysql->add_table_prefix_filter( $table_name );
		}

		// Create tables list file
		$tables_list = qnap_open( qnap_tables_list_path( $params ), 'w' );

		// Write table line
		foreach ( $mysql->get_tables() as $table_name ) {
			if ( qnap_write( $tables_list, $table_name . PHP_EOL ) ) {
				$total_tables_count++;
			}
		}

		// Set progress
		QNAP_Status::info( __( 'Done retrieving a list of WordPress database tables.', QNAP_PLUGIN_NAME ) );

		// Set total tables count
		$params['total_tables_count'] = $total_tables_count;

		// Close the tables list file
		qnap_close( $tables_list );

		return $params;
	}
}
