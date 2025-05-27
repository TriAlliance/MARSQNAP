<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Import_Plugins {

	public static function execute( $params, QNAP_Database $mysql = null ) {
		global $wpdb;

		// Set progress
		QNAP_Status::info( __( 'Preparing plugins...', QNAP_PLUGIN_NAME ) );

		// Get database client
		if ( is_null( $mysql ) ) {
			$mysql = QNAP_Database_Utility::create_client();
		}

		$tables = $mysql->get_tables();

		// Get base prefix
		$base_prefix = qnap_table_prefix();

		// Get mainsite prefix
		$mainsite_prefix = qnap_table_prefix( 'mainsite' );

		// Check WP sitemeta table exists
		if ( in_array( "{$mainsite_prefix}sitemeta", $tables ) ) {

			// Get fs_accounts option value (Freemius)
			$result = $mysql->query( "SELECT meta_value FROM `{$mainsite_prefix}sitemeta` WHERE meta_key = 'fs_accounts'" );
			if ( $row = $mysql->fetch_assoc( $result ) ) {
				$fs_accounts = get_option( 'fs_accounts', array() );
				$meta_value  = maybe_unserialize( $row['meta_value'] );

				// Update fs_accounts option value (Freemius)
				if ( ( $fs_accounts = array_merge( $fs_accounts, $meta_value ) ) ) {
					if ( isset( $fs_accounts['users'], $fs_accounts['sites'] ) ) {
						update_option( 'fs_accounts', $fs_accounts );
					} else {
						delete_option( 'fs_accounts' );
						delete_option( 'fs_dbg_accounts' );
						delete_option( 'fs_active_plugins' );
						delete_option( 'fs_api_cache' );
						delete_option( 'fs_dbg_api_cache' );
						delete_option( 'fs_debug_mode' );
					}
				}
			}
		}

		// Set progress
		QNAP_Status::info( __( 'Done preparing plugins.', QNAP_PLUGIN_NAME ) );

		return $params;
	}
}
