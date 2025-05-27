<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

define( 'QNAP_LOG_PATH', QNAP_PATH . DIRECTORY_SEPARATOR . 'storage' .  DIRECTORY_SEPARATOR . 'log.json');

class Qnap_Log {
	private static $_queue;

	private static function getQueue()     {
        if (!self::$_queue) {
            self::$_queue = new Qnap_Queue(100);
        }

		// init queue
		if ( @file_exists( QNAP_LOG_PATH ) ) {
			$arr = json_decode(file_get_contents(QNAP_LOG_PATH));
			if ( json_last_error() == JSON_ERROR_NONE) {
				self::$_queue->load($arr);
			}
		}

        return self::$_queue;
    }

	private static function save()     {
        if (!self::$_queue) {
            return;
        }

		$str = json_encode(self::$_queue->save());
		if ( json_last_error() == JSON_ERROR_NONE) {
			file_put_contents(QNAP_LOG_PATH, $str);
		}
    }

	public static function append( $client, $content ) {
		self::getQueue()->enqueue(array(date("Y-m-d H:i:s"), $client, $content));
		self::save();
	}

	public static function getAll() {
		return self::getQueue()->getData();
	}

	public static function flush() {
		if ( ! @file_exists( QNAP_LOG_PATH ) ) {
			return false;
		}
		self::$_queue = new Qnap_Queue(100);
		return @unlink( QNAP_LOG_PATH );
	}

	// /**
	//  * Create a file with content
	//  *
	//  * @param  string $path    Path to the file
	//  * @param  string $content Content of the file
	//  * @return boolean
	//  */
	// public static function create( $path, $content ) {
	// 	if ( ! @file_exists( $path ) ) {
	// 		if ( ! @is_writable( dirname( $path ) ) ) {
	// 			return false;
	// 		}

	// 		if ( ! @touch( $path ) ) {
	// 			return false;
	// 		}
	// 	} elseif ( ! @is_writable( $path ) ) {
	// 		return false;
	// 	}

	// 	// No changes were added
	// 	if ( function_exists( 'md5_file' ) ) {
	// 		if ( @md5_file( $path ) === md5( $content ) ) {
	// 			return true;
	// 		}
	// 	}

	// 	$is_written = false;
	// 	if ( ( $handle = @fopen( $path, 'w' ) ) !== false ) {
	// 		if ( @fwrite( $handle, $content ) !== false ) {
	// 			$is_written = true;
	// 		}

	// 		@fclose( $handle );
	// 	}

	// 	return $is_written;
	// }

	// /**
	//  * Create a file with marker and content
	//  *
	//  * @param  string $path    Path to the file
	//  * @param  string $marker  Name of the marker
	//  * @param  string $content Content of the file
	//  * @return boolean
	//  */
	// public static function create_with_markers( $path, $marker, $content ) {
	// 	return @insert_with_markers( $path, $marker, $content );
	// }

	// /**
	//  * Delete a file by path
	//  *
	//  * @param  string  $path Path to the file
	//  * @return boolean
	//  */
	// public static function delete( $path ) {
	// 	if ( ! @file_exists( $path ) ) {
	// 		return false;
	// 	}

	// 	return @unlink( $path );
	// }
}
