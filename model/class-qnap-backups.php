<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Backups {

	/**
	 * Get all backup files
	 *
	 * @return array
	 */
	public static function get_files() {
		$backups = array();

		try {

			// Iterate over directory
			$iterator = new QNAP_Recursive_Directory_Iterator( QNAP_BACKUPS_PATH );

			// Filter by extensions
			$iterator = new QNAP_Recursive_Extension_Filter( $iterator, array( 'qwp' ) );

			// Recursively iterate over directory
			$iterator = new QNAP_Recursive_Iterator_Iterator( $iterator, \RecursiveIteratorIterator::LEAVES_ONLY, \RecursiveIteratorIterator::CATCH_GET_CHILD );

			// Get backup files
			foreach ( $iterator as $item ) {
				try {
					if ( qnap_is_filesize_supported( $item->getPathname() ) ) {
						$backups[] = array(
							'path'     => $iterator->getSubPath(),
							'filename' => $iterator->getSubPathname(),
							'mtime'    => $iterator->getMTime(),
							'size'     => $iterator->getSize(),
						);
					} else {
						$backups[] = array(
							'path'     => $iterator->getSubPath(),
							'filename' => $iterator->getSubPathname(),
							'mtime'    => $iterator->getMTime(),
							'size'     => null,
						);
					}
				} catch ( Exception $e ) {
					$backups[] = array(
						'path'     => $iterator->getSubPath(),
						'filename' => $iterator->getSubPathname(),
						'mtime'    => null,
						'size'     => null,
					);
				}
			}

			// Sort backups modified date
			usort( $backups, 'qnap\QNAP_Backups::compare' );

		} catch ( Exception $e ) {
		}

		return $backups;
	}

	/**
	 * Delete backup file
	 *
	 * @param  string  $file File name
	 * @return boolean
	 */
	public static function delete_file( $file ) {
		if ( validate_file( $file ) === 0 ) {
			return @unlink( qnap_backup_path( array( 'archive' => $file ) ) );
		}
	}

	/**
	 * Get all backup labels
	 *
	 * @return array
	 */
	public static function get_labels() {
		return get_option( QNAP_BACKUPS_LABELS, array() );
	}

	/**
	 * Set backup label
	 *
	 * @param  string  $file  File name
	 * @param  string  $label File label
	 * @return boolean
	 */
	public static function set_label( $file, $label ) {
		if ( ( $labels = get_option( QNAP_BACKUPS_LABELS, array() ) ) !== false ) {
			$labels[ $file ] = $label;
		}

		return update_option( QNAP_BACKUPS_LABELS, $labels );
	}

	/**
	 * Delete backup label
	 *
	 * @param  string  $file File name
	 * @return boolean
	 */
	public static function delete_label( $file ) {
		if ( ( $labels = get_option( QNAP_BACKUPS_LABELS, array() ) ) !== false ) {
			unset( $labels[ $file ] );
		}

		return update_option( QNAP_BACKUPS_LABELS, $labels );
	}

	/**
	 * Compare backup files by modified time
	 *
	 * @param  array $a File item A
	 * @param  array $b File item B
	 * @return integer
	 */
	public static function compare( $a, $b ) {
		if ( $a['mtime'] === $b['mtime'] ) {
			return 0;
		}

		return ( $a['mtime'] > $b['mtime'] ) ? - 1 : 1;
	}
}
