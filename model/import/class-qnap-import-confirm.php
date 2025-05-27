<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Import_Confirm {

	public static function execute( $params ) {
		$messages = array();

		// Read package.json file
		$handle = qnap_open( qnap_package_path( $params ), 'r' );

		// Parse package.json file
		$package = qnap_read( $handle, filesize( qnap_package_path( $params ) ) );
		$package = json_decode( $package, true );

		// Close handle
		qnap_close( $handle );

		// Confirm message
		if ( defined( 'WP_CLI' ) ) {
			$messages[] = __(
				'The import process will overwrite your website including the database, media, plugins, and themes. ' .
				'Are you sure to proceed?',
				QNAP_PLUGIN_NAME
			);
		} else {
			$messages[] = __(
				'The import process will overwrite your website including the database, media, plugins, and themes. ' .
				'Please ensure that you have a backup of your data before proceeding to the next step.',
				QNAP_PLUGIN_NAME
			);
		}

		// Check compatibility of PHP versions
		if ( isset( $package['PHP']['Version'] ) ) {
			if ( version_compare( $package['PHP']['Version'], '7.0.0', '<' ) && version_compare( PHP_VERSION, '7.0.0', '>=' ) ) {
				if ( defined( 'WP_CLI' ) ) {
					$messages[] = __(
						'Your backup is from a PHP 5 but the site that you are importing to is PHP 7. ' .
						'This could cause the import to fail. Technical details: https://help.qeek.com/knowledgebase/migrate-wordpress-from-php-5-to-php-7/',
						QNAP_PLUGIN_NAME
					);
				} else {
					$messages[] = __(
						'<i class="qnap-import-info">Your backup is from a PHP 5 but the site that you are importing to is PHP 7. ' .
						'This could cause the import to fail.</i>',
						QNAP_PLUGIN_NAME
					);
				}
			}
		}

		if ( defined( 'WP_CLI' ) ) {
			$assoc_args = array();
			if ( isset( $params['cli_args'] ) ) {
				$assoc_args = $params['cli_args'];
			}

			WP_CLI::confirm( implode( $messages ), $assoc_args );

			return $params;
		}

		// Set progress
		QNAP_Status::confirm( implode( $messages ) );
		exit;
	}
}
