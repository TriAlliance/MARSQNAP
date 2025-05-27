<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Directory {

	/**
	 * Create directory (recursively)
	 *
	 * @param  string  $path Path to the directory
	 * @return boolean
	 */
	public static function create( $path ) {
		if ( @is_dir( $path ) ) {
			return true;
		}

		return @mkdir( $path, 0777, true );
	}

	/**
	 * Delete directory (recursively)
	 *
	 * @param  string  $path Path to the directory
	 * @return boolean
	 */
	public static function delete( $path ) {
		if ( @is_dir( $path ) ) {
			try {
				// Iterate over directory
				$iterator = new QNAP_Recursive_Directory_Iterator( $path );

				// Recursively iterate over directory
				$iterator = new QNAP_Recursive_Iterator_Iterator( $iterator, \RecursiveIteratorIterator::CHILD_FIRST, \RecursiveIteratorIterator::CATCH_GET_CHILD );

				// Remove files and directories
				foreach ( $iterator as $item ) {
					if ( $item->isDir() ) {
						@rmdir( $item->getPathname() );
					} else {
						@unlink( $item->getPathname() );
					}
				}
			} catch ( Exception $e ) {
			}

			return @rmdir( $path );
		}

		return false;
	}
}
