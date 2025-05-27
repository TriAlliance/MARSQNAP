<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

// Include plugin bootstrap file
require_once dirname( __FILE__ ) .
	DIRECTORY_SEPARATOR .
	'qnap.php';

/**
 * Trigger Uninstall process only if WP_UNINSTALL_PLUGIN is defined
 */
if ( defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	global $wpdb, $wp_filesystem;

	// Delete any options or other data stored in the database here
	delete_option( QNAP_STATUS );
	delete_option( QNAP_SECRET_KEY );
	delete_option( QNAP_AUTH_USER );
	delete_option( QNAP_AUTH_PASSWORD );
}
