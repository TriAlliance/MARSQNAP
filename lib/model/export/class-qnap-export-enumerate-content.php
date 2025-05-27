<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Export_Enumerate_Content {

	public static function execute( $params ) {

		$exclude_filters = array( qnap_get_uploads_dir() );

		// Get total content files count
		if ( isset( $params['total_content_files_count'] ) ) {
			$total_content_files_count = (int) $params['total_content_files_count'];
		} else {
			$total_content_files_count = 0;
		}

		// Get total content files size
		if ( isset( $params['total_content_files_size'] ) ) {
			$total_content_files_size = (int) $params['total_content_files_size'];
		} else {
			$total_content_files_size = 0;
		}

		// Set progress
		QNAP_Status::info( __( 'Retrieving a list of WordPress content files...', QNAP_PLUGIN_NAME ) );

		// Exclude cache
		if ( isset( $params['options']['no_cache'] ) ) {
			$exclude_filters[] = 'cache';
		}

		// Exclude themes
		if ( isset( $params['options']['no_themes'] ) ) {
			$exclude_filters[] = 'themes';
		} else {
			$inactive_themes = array();

			// Exclude inactive themes
			if ( isset( $params['options']['no_inactive_themes'] ) ) {
				foreach ( search_theme_directories() as $theme => $info ) {
					// Exclude current parent and child themes
					if ( ! in_array( $theme, array( get_template(), get_stylesheet() ) ) ) {
						$inactive_themes[] = 'themes' . DIRECTORY_SEPARATOR . $theme;
					}
				}
			}

			$exclude_filters = array_merge( $exclude_filters, $inactive_themes );
		}

		// Exclude must-use plugins
		if ( isset( $params['options']['no_muplugins'] ) ) {
			$exclude_filters[] = 'mu-plugins';
		}

		// Exclude plugins
		if ( isset( $params['options']['no_plugins'] ) ) {
			$exclude_filters[] = 'plugins';
		} else {
			$inactive_plugins = array();

			// Exclude inactive plugins
			if ( isset( $params['options']['no_inactive_plugins'] ) ) {
				foreach ( get_plugins() as $plugin => $info ) {
					if ( is_plugin_inactive( $plugin ) ) {
						$inactive_plugins[] = 'plugins' . DIRECTORY_SEPARATOR . ( ( dirname( $plugin ) === '.' ) ? basename( $plugin ) : dirname( $plugin ) );
					}
				}
			}

			$exclude_filters = array_merge( $exclude_filters, qnap_plugin_filters( $inactive_plugins ) );
		}

		// Exclude media
		if ( isset( $params['options']['no_media'] ) ) {
			$exclude_filters[] = 'blogs.dir';
		}

		$user_filters = array();

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

		// Create content list file
		$content_list = qnap_open( qnap_content_list_path( $params ), 'w' );

		// Enumerate over content directory
		if ( isset( $params['options']['no_themes'], $params['options']['no_muplugins'], $params['options']['no_plugins'] ) === false ) {

			// Iterate over content directory
			$iterator = new QNAP_Recursive_Directory_Iterator( WP_CONTENT_DIR );

			// Exclude content files
			$iterator = new QNAP_Recursive_Exclude_Filter( $iterator, apply_filters( 'qnap_exclude_content_from_export', qnap_content_filters( $exclude_filters ) ) );

			// Recursively iterate over content directory
			$iterator = new QNAP_Recursive_Iterator_Iterator( $iterator, \RecursiveIteratorIterator::LEAVES_ONLY, \RecursiveIteratorIterator::CATCH_GET_CHILD );

			// Write path line
			foreach ( $iterator as $item ) {
				if ( $item->isFile() ) {
					if ( qnap_write( $content_list, $iterator->getSubPathname() . PHP_EOL ) ) {
						$total_content_files_count++;

						// Add current file size
						$total_content_files_size += $iterator->getSize();
					}
				}
			}
		}

		// Set progress
		QNAP_Status::info( __( 'Done retrieving a list of WordPress content files.', QNAP_PLUGIN_NAME ) );

		// Set total content files count
		$params['total_content_files_count'] = $total_content_files_count;

		// Set total content files size
		$params['total_content_files_size'] = $total_content_files_size;

		// Close the content list file
		qnap_close( $content_list );

		return $params;
	}
}
