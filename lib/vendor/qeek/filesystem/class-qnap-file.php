<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_File {

	/**
	 * Create a file with content
	 *
	 * @param  string $path    Path to the file
	 * @param  string $content Content of the file
	 * @return boolean
	 */
	public static function create( $path, $content ) {
		if ( ! @file_exists( $path ) ) {
			if ( ! @is_writable( dirname( $path ) ) ) {
				return false;
			}

			if ( ! @touch( $path ) ) {
				return false;
			}
		} elseif ( ! @is_writable( $path ) ) {
			return false;
		}

		// No changes were added
		if ( function_exists( 'md5_file' ) ) {
			if ( @md5_file( $path ) === md5( $content ) ) {
				return true;
			}
		}

		$is_written = false;
		if ( ( $handle = @fopen( $path, 'w' ) ) !== false ) {
			if ( @fwrite( $handle, $content ) !== false ) {
				$is_written = true;
			}

			@fclose( $handle );
		}

		return $is_written;
	}

	/**
	 * Create a file with marker and content
	 *
	 * @param  string $path    Path to the file
	 * @param  string $marker  Name of the marker
	 * @param  string $content Content of the file
	 * @return boolean
	 */
	public static function create_with_markers( $path, $marker, $content ) {
		return @insert_with_markers( $path, $marker, $content );
	}

	/**
	 * Delete a file by path
	 *
	 * @param  string  $path Path to the file
	 * @return boolean
	 */
	public static function delete( $path ) {
		if ( ! @file_exists( $path ) ) {
			return false;
		}

		return @unlink( $path );
	}
}
