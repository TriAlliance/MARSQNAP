<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Export_Enumerate_Media {

	public static function execute( $params ) {

		$exclude_filters = $user_filters = array();

		// Get total media files count
		if ( isset( $params['total_media_files_count'] ) ) {
			$total_media_files_count = (int) $params['total_media_files_count'];
		} else {
			$total_media_files_count = 0;
		}

		// Get total media files size
		if ( isset( $params['total_media_files_size'] ) ) {
			$total_media_files_size = (int) $params['total_media_files_size'];
		} else {
			$total_media_files_size = 0;
		}

		// Set progress
		QNAP_Status::info( __( 'Retrieving a list of WordPress media files...', QNAP_PLUGIN_NAME ) );

		// Exclude selected files
		if ( isset( $params['options']['exclude_files'], $params['excluded_files'] ) ) {
			$excluded_files = explode( ',', $params['excluded_files'] );
			if ( $excluded_files ) {
				foreach ( $excluded_files as $excluded_path ) {
					$user_filters[] = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . untrailingslashit( $excluded_path );
				}
			}

			$exclude_filters = array_merge( $exclude_filters, $user_filters );
		}

		// Create media list file
		$media_list = qnap_open( qnap_media_list_path( $params ), 'w' );

		// Enumerate over media directory
		if ( isset( $params['options']['no_media'] ) === false ) {
			if ( is_dir( qnap_get_uploads_dir() ) ) {

				// Iterate over media directory
				$iterator = new QNAP_Recursive_Directory_Iterator( qnap_get_uploads_dir() );

				// Exclude media files
				$iterator = new QNAP_Recursive_Exclude_Filter( $iterator, apply_filters( 'qnap_exclude_media_from_export', $exclude_filters ) );

				// Recursively iterate over content directory
				$iterator = new QNAP_Recursive_Iterator_Iterator( $iterator, \RecursiveIteratorIterator::LEAVES_ONLY, \RecursiveIteratorIterator::CATCH_GET_CHILD );

				// Write path line
				foreach ( $iterator as $item ) {
					if ( $item->isFile() ) {
						if ( qnap_write( $media_list, $iterator->getSubPathname() . PHP_EOL ) ) {
							$total_media_files_count++;

							// Add current file size
							$total_media_files_size += $iterator->getSize();
						}
					}
				}
			}
		}

		// Set progress
		QNAP_Status::info( __( 'Done retrieving a list of WordPress media files.', QNAP_PLUGIN_NAME ) );

		// Set total media files count
		$params['total_media_files_count'] = $total_media_files_count;

		// Set total media files size
		$params['total_media_files_size'] = $total_media_files_size;

		// Close the media list file
		qnap_close( $media_list );

		return $params;
	}
}
