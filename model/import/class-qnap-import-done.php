<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Import_Done {

	public static function execute( $params ) {
		global $wp_rewrite;

		// Check multisite.json file
		if ( is_file( qnap_multisite_path( $params ) ) ) {

			// Read multisite.json file
			$handle = qnap_open( qnap_multisite_path( $params ), 'r' );

			// Parse multisite.json file
			$multisite = qnap_read( $handle, filesize( qnap_multisite_path( $params ) ) );
			$multisite = json_decode( $multisite, true );

			// Close handle
			qnap_close( $handle );

			// Activate WordPress plugins
			if ( isset( $multisite['Plugins'] ) && ( $plugins = $multisite['Plugins'] ) ) {
				qnap_activate_plugins( $plugins );
			}

			// Deactivate WordPress SSL plugins
			if ( ! is_ssl() ) {
				qnap_deactivate_plugins(
					array(
						qnap_discover_plugin_basename( 'really-simple-ssl/rlrsssl-really-simple-ssl.php' ),
						qnap_discover_plugin_basename( 'wordpress-https/wordpress-https.php' ),
						qnap_discover_plugin_basename( 'wp-force-ssl/wp-force-ssl.php' ),
						qnap_discover_plugin_basename( 'force-https-littlebizzy/force-https.php' ),
					)
				);
			}

			// Deactivate WordPress plugins
			qnap_deactivate_plugins(
				array(
					qnap_discover_plugin_basename( 'invisible-recaptcha/invisible-recaptcha.php' ),
					qnap_discover_plugin_basename( 'wps-hide-login/wps-hide-login.php' ),
					qnap_discover_plugin_basename( 'hide-my-wp/index.php' ),
					qnap_discover_plugin_basename( 'hide-my-wordpress/index.php' ),
					qnap_discover_plugin_basename( 'mycustomwidget/my_custom_widget.php' ),
					qnap_discover_plugin_basename( 'lockdown-wp-admin/lockdown-wp-admin.php' ),
					qnap_discover_plugin_basename( 'rename-wp-login/rename-wp-login.php' ),
					qnap_discover_plugin_basename( 'wp-simple-firewall/icwp-wpsf.php' ),
					qnap_discover_plugin_basename( 'join-my-multisite/joinmymultisite.php' ),
					qnap_discover_plugin_basename( 'multisite-clone-duplicator/multisite-clone-duplicator.php' ),
					qnap_discover_plugin_basename( 'wordpress-mu-domain-mapping/domain_mapping.php' ),
					qnap_discover_plugin_basename( 'wordpress-starter/siteground-wizard.php' ),
					qnap_discover_plugin_basename( 'pro-sites/pro-sites.php' ),
					qnap_discover_plugin_basename( 'wpide/WPide.php' ),
					qnap_discover_plugin_basename( 'page-optimize/page-optimize.php' ),
				)
			);

			// Deactivate Swift Optimizer rules
			qnap_deactivate_swift_optimizer_rules(
				array(
					qnap_discover_plugin_basename( 'qnap-wp-migration/qnap-wp-migration.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-azure-storage-extension/qnap-wp-migration-azure-storage-extension.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-b2-extension/qnap-wp-migration-b2-extension.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-backup/qnap-wp-migration-backup.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-box-extension/qnap-wp-migration-box-extension.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-digitalocean-extension/qnap-wp-migration-digitalocean-extension.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-direct-extension/qnap-wp-migration-direct-extension.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-dropbox-extension/qnap-wp-migration-dropbox-extension.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-file-extension/qnap-wp-migration-file-extension.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-ftp-extension/qnap-wp-migration-ftp-extension.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-gcloud-storage-extension/qnap-wp-migration-gcloud-storage-extension.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-gdrive-extension/qnap-wp-migration-gdrive-extension.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-glacier-extension/qnap-wp-migration-glacier-extension.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-mega-extension/qnap-wp-migration-mega-extension.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-multisite-extension/qnap-wp-migration-multisite-extension.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-onedrive-extension/qnap-wp-migration-onedrive-extension.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-pcloud-extension/qnap-wp-migration-pcloud-extension.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-pro/qnap-wp-migration-pro.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-s3-client-extension/qnap-wp-migration-s3-client-extension.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-s3-extension/qnap-wp-migration-s3-extension.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-unlimited-extension/qnap-wp-migration-unlimited-extension.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-url-extension/qnap-wp-migration-url-extension.php' ),
					qnap_discover_plugin_basename( 'qnap-wp-migration-webdav-extension/qnap-wp-migration-webdav-extension.php' ),
				)
			);

			// Deactivate Revolution Slider
			qnap_deactivate_revolution_slider( qnap_discover_plugin_basename( 'revslider/revslider.php' ) );

			// Deactivate Jetpack modules
			qnap_deactivate_jetpack_modules( array( 'photon', 'sso' ) );

			// Flush Elementor cache
			qnap_elementor_cache_flush();

			// Initial DB version
			qnap_initial_db_version();

		} else {

			// Check package.json file
			if ( is_file( qnap_package_path( $params ) ) ) {

				// Read package.json file
				$handle = qnap_open( qnap_package_path( $params ), 'r' );

				// Parse package.json file
				$package = qnap_read( $handle, filesize( qnap_package_path( $params ) ) );
				$package = json_decode( $package, true );

				// Close handle
				qnap_close( $handle );

				// Activate WordPress plugins
				if ( isset( $package['Plugins'] ) && ( $plugins = $package['Plugins'] ) ) {
					qnap_activate_plugins( $plugins );
				}

				// Activate WordPress template
				if ( isset( $package['Template'] ) && ( $template = $package['Template'] ) ) {
					qnap_activate_template( $template );
				}

				// Activate WordPress stylesheet
				if ( isset( $package['Stylesheet'] ) && ( $stylesheet = $package['Stylesheet'] ) ) {
					qnap_activate_stylesheet( $stylesheet );
				}

				// Deactivate WordPress SSL plugins
				if ( ! is_ssl() ) {
					qnap_deactivate_plugins(
						array(
							qnap_discover_plugin_basename( 'really-simple-ssl/rlrsssl-really-simple-ssl.php' ),
							qnap_discover_plugin_basename( 'wordpress-https/wordpress-https.php' ),
							qnap_discover_plugin_basename( 'wp-force-ssl/wp-force-ssl.php' ),
							qnap_discover_plugin_basename( 'force-https-littlebizzy/force-https.php' ),
						)
					);
				}

				// Deactivate WordPress plugins
				qnap_deactivate_plugins(
					array(
						qnap_discover_plugin_basename( 'invisible-recaptcha/invisible-recaptcha.php' ),
						qnap_discover_plugin_basename( 'wps-hide-login/wps-hide-login.php' ),
						qnap_discover_plugin_basename( 'hide-my-wp/index.php' ),
						qnap_discover_plugin_basename( 'hide-my-wordpress/index.php' ),
						qnap_discover_plugin_basename( 'mycustomwidget/my_custom_widget.php' ),
						qnap_discover_plugin_basename( 'lockdown-wp-admin/lockdown-wp-admin.php' ),
						qnap_discover_plugin_basename( 'rename-wp-login/rename-wp-login.php' ),
						qnap_discover_plugin_basename( 'wp-simple-firewall/icwp-wpsf.php' ),
						qnap_discover_plugin_basename( 'join-my-multisite/joinmymultisite.php' ),
						qnap_discover_plugin_basename( 'multisite-clone-duplicator/multisite-clone-duplicator.php' ),
						qnap_discover_plugin_basename( 'wordpress-mu-domain-mapping/domain_mapping.php' ),
						qnap_discover_plugin_basename( 'wordpress-starter/siteground-wizard.php' ),
						qnap_discover_plugin_basename( 'pro-sites/pro-sites.php' ),
						qnap_discover_plugin_basename( 'wpide/WPide.php' ),
						qnap_discover_plugin_basename( 'page-optimize/page-optimize.php' ),
					)
				);

				// Deactivate Swift Optimizer rules
				qnap_deactivate_swift_optimizer_rules(
					array(
						qnap_discover_plugin_basename( 'qnap-wp-migration/qnap-wp-migration.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-azure-storage-extension/qnap-wp-migration-azure-storage-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-b2-extension/qnap-wp-migration-b2-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-backup/qnap-wp-migration-backup.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-box-extension/qnap-wp-migration-box-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-digitalocean-extension/qnap-wp-migration-digitalocean-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-direct-extension/qnap-wp-migration-direct-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-dropbox-extension/qnap-wp-migration-dropbox-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-file-extension/qnap-wp-migration-file-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-ftp-extension/qnap-wp-migration-ftp-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-gcloud-storage-extension/qnap-wp-migration-gcloud-storage-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-gdrive-extension/qnap-wp-migration-gdrive-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-glacier-extension/qnap-wp-migration-glacier-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-mega-extension/qnap-wp-migration-mega-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-multisite-extension/qnap-wp-migration-multisite-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-onedrive-extension/qnap-wp-migration-onedrive-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-pcloud-extension/qnap-wp-migration-pcloud-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-pro/qnap-wp-migration-pro.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-s3-client-extension/qnap-wp-migration-s3-client-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-s3-extension/qnap-wp-migration-s3-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-unlimited-extension/qnap-wp-migration-unlimited-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-url-extension/qnap-wp-migration-url-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-webdav-extension/qnap-wp-migration-webdav-extension.php' ),
					)
				);

				// Deactivate Revolution Slider
				qnap_deactivate_revolution_slider( qnap_discover_plugin_basename( 'revslider/revslider.php' ) );

				// Deactivate Jetpack modules
				qnap_deactivate_jetpack_modules( array( 'photon', 'sso' ) );

				// Flush Elementor cache
				qnap_elementor_cache_flush();

				// Initial DB version
				qnap_initial_db_version();
			}
		}

		// Check blogs.json file
		if ( is_file( qnap_blogs_path( $params ) ) ) {

			// Read blogs.json file
			$handle = qnap_open( qnap_blogs_path( $params ), 'r' );

			// Parse blogs.json file
			$blogs = qnap_read( $handle, filesize( qnap_blogs_path( $params ) ) );
			$blogs = json_decode( $blogs, true );

			// Close handle
			qnap_close( $handle );

			// Loop over blogs
			foreach ( $blogs as $blog ) {

				// Activate WordPress plugins
				if ( isset( $blog['New']['Plugins'] ) && ( $plugins = $blog['New']['Plugins'] ) ) {
					qnap_activate_plugins( $plugins );
				}

				// Activate WordPress template
				if ( isset( $blog['New']['Template'] ) && ( $template = $blog['New']['Template'] ) ) {
					qnap_activate_template( $template );
				}

				// Activate WordPress stylesheet
				if ( isset( $blog['New']['Stylesheet'] ) && ( $stylesheet = $blog['New']['Stylesheet'] ) ) {
					qnap_activate_stylesheet( $stylesheet );
				}

				// Deactivate WordPress SSL plugins
				if ( ! is_ssl() ) {
					qnap_deactivate_plugins(
						array(
							qnap_discover_plugin_basename( 'really-simple-ssl/rlrsssl-really-simple-ssl.php' ),
							qnap_discover_plugin_basename( 'wordpress-https/wordpress-https.php' ),
							qnap_discover_plugin_basename( 'wp-force-ssl/wp-force-ssl.php' ),
							qnap_discover_plugin_basename( 'force-https-littlebizzy/force-https.php' ),
						)
					);
				}

				// Deactivate WordPress plugins
				qnap_deactivate_plugins(
					array(
						qnap_discover_plugin_basename( 'invisible-recaptcha/invisible-recaptcha.php' ),
						qnap_discover_plugin_basename( 'wps-hide-login/wps-hide-login.php' ),
						qnap_discover_plugin_basename( 'hide-my-wp/index.php' ),
						qnap_discover_plugin_basename( 'hide-my-wordpress/index.php' ),
						qnap_discover_plugin_basename( 'mycustomwidget/my_custom_widget.php' ),
						qnap_discover_plugin_basename( 'lockdown-wp-admin/lockdown-wp-admin.php' ),
						qnap_discover_plugin_basename( 'rename-wp-login/rename-wp-login.php' ),
						qnap_discover_plugin_basename( 'wp-simple-firewall/icwp-wpsf.php' ),
						qnap_discover_plugin_basename( 'join-my-multisite/joinmymultisite.php' ),
						qnap_discover_plugin_basename( 'multisite-clone-duplicator/multisite-clone-duplicator.php' ),
						qnap_discover_plugin_basename( 'wordpress-mu-domain-mapping/domain_mapping.php' ),
						qnap_discover_plugin_basename( 'wordpress-starter/siteground-wizard.php' ),
						qnap_discover_plugin_basename( 'pro-sites/pro-sites.php' ),
						qnap_discover_plugin_basename( 'wpide/WPide.php' ),
						qnap_discover_plugin_basename( 'page-optimize/page-optimize.php' ),
					)
				);

				// Deactivate Swift Optimizer rules
				qnap_deactivate_swift_optimizer_rules(
					array(
						qnap_discover_plugin_basename( 'qnap-wp-migration/qnap-wp-migration.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-azure-storage-extension/qnap-wp-migration-azure-storage-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-b2-extension/qnap-wp-migration-b2-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-backup/qnap-wp-migration-backup.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-box-extension/qnap-wp-migration-box-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-digitalocean-extension/qnap-wp-migration-digitalocean-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-direct-extension/qnap-wp-migration-direct-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-dropbox-extension/qnap-wp-migration-dropbox-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-file-extension/qnap-wp-migration-file-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-ftp-extension/qnap-wp-migration-ftp-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-gcloud-storage-extension/qnap-wp-migration-gcloud-storage-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-gdrive-extension/qnap-wp-migration-gdrive-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-glacier-extension/qnap-wp-migration-glacier-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-mega-extension/qnap-wp-migration-mega-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-multisite-extension/qnap-wp-migration-multisite-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-onedrive-extension/qnap-wp-migration-onedrive-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-pcloud-extension/qnap-wp-migration-pcloud-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-pro/qnap-wp-migration-pro.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-s3-client-extension/qnap-wp-migration-s3-client-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-s3-extension/qnap-wp-migration-s3-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-unlimited-extension/qnap-wp-migration-unlimited-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-url-extension/qnap-wp-migration-url-extension.php' ),
						qnap_discover_plugin_basename( 'qnap-wp-migration-webdav-extension/qnap-wp-migration-webdav-extension.php' ),
					)
				);

				// Deactivate Revolution Slider
				qnap_deactivate_revolution_slider( qnap_discover_plugin_basename( 'revslider/revslider.php' ) );

				// Deactivate Jetpack modules
				qnap_deactivate_jetpack_modules( array( 'photon', 'sso' ) );

				// Flush Elementor cache
				qnap_elementor_cache_flush();

				// Initial DB version
				qnap_initial_db_version();
			}
		}

		// Clear auth cookie (WP Cerber)
		if ( qnap_validate_plugin_basename( 'wp-cerber/wp-cerber.php' ) ) {
			wp_clear_auth_cookie();
		}

		$should_reset_permalinks = false;

		// Switch to default permalink structure
		if ( ( $should_reset_permalinks = qnap_should_reset_permalinks( $params ) ) ) {
			$wp_rewrite->set_permalink_structure( '' );
		}

		// Set progress
		QNAP_Status::done( __( 'Your site has been imported successfully!', QNAP_PLUGIN_NAME ), '' );

		Qnap_Log::append(qnap_get_log_client($params), '[Multi-Application Recovery Service] Finished restoring WordPress.');

		return $params;
	}
}
