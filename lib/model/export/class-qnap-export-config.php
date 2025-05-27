<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Export_Config {

	public static function execute( $params ) {
		global $table_prefix, $wp_version, $wpdb;

		// Set progress
		QNAP_Status::info( __( 'Preparing configuration file...', QNAP_PLUGIN_NAME ) );

		// Get options
		$options = wp_load_alloptions();

		// Get database client
		$mysql = QNAP_Database_Utility::create_client();

		$config = array();

		// Set site URL
		$config['SiteURL'] = site_url();

		// Set home URL
		$config['HomeURL'] = home_url();

		// Set internal site URL
		if ( isset( $options['siteurl'] ) ) {
			$config['InternalSiteURL'] = $options['siteurl'];
		}

		// Set internal home URL
		if ( isset( $options['home'] ) ) {
			$config['InternalHomeURL'] = $options['home'];
		}

		// Set replace old and new values
		if ( isset( $params['options']['replace'] ) && ( $replace = $params['options']['replace'] ) ) {
			for ( $i = 0; $i < count( $replace['old_value'] ); $i++ ) {
				if ( ! empty( $replace['old_value'][ $i ] ) && ! empty( $replace['new_value'][ $i ] ) ) {
					$config['Replace']['OldValues'][] = $replace['old_value'][ $i ];
					$config['Replace']['NewValues'][] = $replace['new_value'][ $i ];
				}
			}
		}

		// Set no spam comments
		if ( isset( $params['options']['no_spam_comments'] ) ) {
			$config['NoSpamComments'] = true;
		}

		// Set no post revisions
		if ( isset( $params['options']['no_post_revisions'] ) ) {
			$config['NoPostRevisions'] = true;
		}

		// Set no media
		if ( isset( $params['options']['no_media'] ) ) {
			$config['NoMedia'] = true;
		}

		// Set no themes
		if ( isset( $params['options']['no_themes'] ) ) {
			$config['NoThemes'] = true;
		}

		// Set no inactive themes
		if ( isset( $params['options']['no_inactive_themes'] ) ) {
			$config['NoInactiveThemes'] = true;
		}

		// Set no must-use plugins
		if ( isset( $params['options']['no_muplugins'] ) ) {
			$config['NoMustUsePlugins'] = true;
		}

		// Set no plugins
		if ( isset( $params['options']['no_plugins'] ) ) {
			$config['NoPlugins'] = true;
		}

		// Set no inactive plugins
		if ( isset( $params['options']['no_inactive_plugins'] ) ) {
			$config['NoInactivePlugins'] = true;
		}

		// Set no cache
		if ( isset( $params['options']['no_cache'] ) ) {
			$config['NoCache'] = true;
		}

		// Set no database
		if ( isset( $params['options']['no_database'] ) ) {
			$config['NoDatabase'] = true;
		}

		// Set no email replace
		if ( isset( $params['options']['no_email_replace'] ) ) {
			$config['NoEmailReplace'] = true;
		}

		// Set plugin version
		$config['Plugin'] = array( 'Version' => QNAP_VERSION );

		// Set WordPress version and content
		$config['WordPress'] = array( 'Version' => $wp_version, 'Content' => WP_CONTENT_DIR, 'Plugins' => WP_PLUGIN_DIR, 'Themes' => get_theme_root(), 'Uploads' => qnap_get_uploads_dir(), 'UploadsURL' => qnap_get_uploads_url() );

		// Set database version
		$config['Database'] = array( 'Version' => $mysql->version(), 'Charset' => DB_CHARSET, 'Collate' => DB_COLLATE, 'Prefix' => $table_prefix );

		// Set PHP version
		$config['PHP'] = array( 'Version' => PHP_VERSION, 'System' => PHP_OS, 'Integer' => PHP_INT_SIZE );

		// Set active plugins
		$config['Plugins'] = array_values( array_diff( qnap_active_plugins(), qnap_active_qeek_plugins() ) );

		// Set active template
		$config['Template'] = qnap_active_template();

		// Set active stylesheet
		$config['Stylesheet'] = qnap_active_stylesheet();

		// Set upload path
		$config['Uploads'] = get_option( 'upload_path' );

		// Set upload URL path
		$config['UploadsURL'] = get_option( 'upload_url_path' );

		// Set server info
		$config['Server'] = array( '.htaccess' => base64_encode( qnap_get_htaccess() ), 'web.config' => base64_encode( qnap_get_webconfig() ) );

		// Save package.json file
		$handle = qnap_open( qnap_package_path( $params ), 'w' );
		qnap_write( $handle, json_encode( $config ) );
		qnap_close( $handle );

		// Set progress
		QNAP_Status::info( __( 'Done preparing configuration file.', QNAP_PLUGIN_NAME ) );

		return $params;
	}
}
