<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Status {

	public static function error( $title, $message ) {
		self::log( array( 'type' => 'error', 'title' => $title, 'message' => $message ) );
	}

	public static function info( $message ) {
		self::log( array( 'type' => 'info', 'message' => $message ) );
	}

	public static function download( $message ) {
		self::log( array( 'type' => 'download', 'message' => $message ) );
	}

	public static function disk_space_confirm( $message ) {
		self::log( array( 'type' => 'disk_space_confirm', 'message' => $message ) );
	}

	public static function confirm( $message ) {
		self::log( array( 'type' => 'confirm', 'message' => $message ) );
	}

	public static function done( $title, $message ) {
		self::log( array( 'type' => 'done', 'title' => $title, 'message' => $message ) );
	}

	public static function blogs( $title, $message ) {
		self::log( array( 'type' => 'blogs', 'title' => $title, 'message' => $message ) );
	}

	public static function progress( $percent ) {
		self::log( array( 'type' => 'progress', 'percent' => $percent ) );
	}

	public static function log( $data ) {
		if ( ! qnap_is_scheduled_backup() ) {
			update_option( QNAP_STATUS, $data );
		}
	}
}
