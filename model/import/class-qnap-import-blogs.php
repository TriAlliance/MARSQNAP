<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Import_Blogs {

	public static function execute( $params ) {

		// Set progress
		QNAP_Status::info( __( 'Preparing blogs...', QNAP_PLUGIN_NAME ) );

		$blogs = array();

		// Check multisite.json file
		if ( true === is_file( qnap_multisite_path( $params ) ) ) {

			// Read multisite.json file
			$handle = qnap_open( qnap_multisite_path( $params ), 'r' );

			// Parse multisite.json file
			$multisite = qnap_read( $handle, filesize( qnap_multisite_path( $params ) ) );
			$multisite = json_decode( $multisite, true );

			// Close handle
			qnap_close( $handle );

			// Validate
			if ( empty( $multisite['Network'] ) ) {
				if ( isset( $multisite['Sites'] ) && ( $sites = $multisite['Sites'] ) ) {
					if ( count( $sites ) === 1 && ( $subsite = current( $sites ) ) ) {

						// Set internal Site URL (backward compatibility)
						if ( empty( $subsite['InternalSiteURL'] ) ) {
							$subsite['InternalSiteURL'] = null;
						}

						// Set internal Home URL (backward compatibility)
						if ( empty( $subsite['InternalHomeURL'] ) ) {
							$subsite['InternalHomeURL'] = null;
						}

						// Set active plugins (backward compatibility)
						if ( empty( $subsite['Plugins'] ) ) {
							$subsite['Plugins'] = array();
						}

						// Set active template (backward compatibility)
						if ( empty( $subsite['Template'] ) ) {
							$subsite['Template'] = null;
						}

						// Set active stylesheet (backward compatibility)
						if ( empty( $subsite['Stylesheet'] ) ) {
							$subsite['Stylesheet'] = null;
						}

						// Set uploads path (backward compatibility)
						if ( empty( $subsite['Uploads'] ) ) {
							$subsite['Uploads'] = null;
						}

						// Set uploads URL path (backward compatibility)
						if ( empty( $subsite['UploadsURL'] ) ) {
							$subsite['UploadsURL'] = null;
						}

						// Set uploads path (backward compatibility)
						if ( empty( $subsite['WordPress']['Uploads'] ) ) {
							$subsite['WordPress']['Uploads'] = null;
						}

						// Set uploads URL path (backward compatibility)
						if ( empty( $subsite['WordPress']['UploadsURL'] ) ) {
							$subsite['WordPress']['UploadsURL'] = null;
						}

						// Set blog items
						$blogs[] = array(
							'Old' => array(
								'BlogID'          => $subsite['BlogID'],
								'SiteURL'         => $subsite['SiteURL'],
								'HomeURL'         => $subsite['HomeURL'],
								'InternalSiteURL' => $subsite['InternalSiteURL'],
								'InternalHomeURL' => $subsite['InternalHomeURL'],
								'Plugins'         => $subsite['Plugins'],
								'Template'        => $subsite['Template'],
								'Stylesheet'      => $subsite['Stylesheet'],
								'Uploads'         => $subsite['Uploads'],
								'UploadsURL'      => $subsite['UploadsURL'],
								'WordPress'       => $subsite['WordPress'],
							),
							'New' => array(
								'BlogID'          => null,
								'SiteURL'         => site_url(),
								'HomeURL'         => home_url(),
								'InternalSiteURL' => site_url(),
								'InternalHomeURL' => home_url(),
								'Plugins'         => $subsite['Plugins'],
								'Template'        => $subsite['Template'],
								'Stylesheet'      => $subsite['Stylesheet'],
								'Uploads'         => get_option( 'upload_path' ),
								'UploadsURL'      => get_option( 'upload_url_path' ),
								'WordPress'       => array(
									'UploadsURL' => qnap_get_uploads_url(),
								),
							),
						);
					} else {
						throw new QNAP_Import_Exception( __( 'The archive should contain <strong>Single WordPress</strong> site! Please revisit your export settings.', QNAP_PLUGIN_NAME ) );
					}
				} else {
					throw new QNAP_Import_Exception( __( 'At least <strong>one WordPress</strong> site should be presented in the archive.', QNAP_PLUGIN_NAME ) );
				}
			} else {
				throw new QNAP_Import_Exception( __( 'Unable to import <strong>WordPress Network</strong> into WordPress <strong>Single</strong> site.', QNAP_PLUGIN_NAME ) );
			}
		}

		// Write blogs.json file
		$handle = qnap_open( qnap_blogs_path( $params ), 'w' );
		qnap_write( $handle, json_encode( $blogs ) );
		qnap_close( $handle );

		// Set progress
		QNAP_Status::info( __( 'Done preparing blogs.', QNAP_PLUGIN_NAME ) );

		return $params;
	}
}
