<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Compatibility {

	public static function get( $params ) {
		$extensions = QNAP_Extensions::get();

		foreach ( $extensions as $extension_name => $extension_data ) {
			if ( ! isset( $params[ $extension_data['short'] ] ) ) {
				unset( $extensions[ $extension_name ] );
			}
		}

		// Get updater URL
		$updater_url = add_query_arg( array( 'qnap_check_for_updates' => 1, 'qnap_nonce' => wp_create_nonce( 'qnap_check_for_updates' ) ), network_admin_url( 'plugins.php' ) );

		// If no extension is used, update everything that is available
		if ( empty( $extensions ) ) {
			$extensions = QNAP_Extensions::get();
		}

		$messages = array();
		foreach ( $extensions as $extension_name => $extension_data ) {
			if ( ! QNAP_Compatibility::check( $extension_data ) ) {
				if ( defined( 'WP_CLI' ) ) {
					$messages[] = sprintf( __( '%s is not the latest version. You must update the plugin before you can use it. ', QNAP_PLUGIN_NAME ), $extension_data['title'] );
				} else {
					$messages[] = sprintf( __( '<strong>%s</strong> is not the latest version. You must <a href="%s">update the plugin</a> before you can use it. <br />', QNAP_PLUGIN_NAME ), $extension_data['title'], $updater_url );
				}
			}
		}

		return $messages;
	}

	public static function check( $extension ) {
		if ( $extension['version'] !== 'develop' ) {
			if ( version_compare( $extension['version'], $extension['requires'], '<' ) ) {
				return false;
			}
		}

		return true;
	}
}
