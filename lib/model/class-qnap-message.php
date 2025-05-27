<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Message {

	public static function flash( $type, $message ) {
		if ( ( $messages = get_option( QNAP_MESSAGES, array() ) ) !== false ) {
			return update_option( QNAP_MESSAGES, array_merge( $messages, array( $type => $message ) ) );
		}

		return false;
	}

	public static function has( $type ) {
		if ( ( $messages = get_option( QNAP_MESSAGES, array() ) ) ) {
			if ( isset( $messages[ $type ] ) ) {
				return true;
			}
		}

		return false;
	}

	public static function get( $type ) {
		$message = null;
		if ( ( $messages = get_option( QNAP_MESSAGES, array() ) ) ) {
			if ( isset( $messages[ $type ] ) && ( $message = $messages[ $type ] ) ) {
				unset( $messages[ $type ] );
			}

			// Set messages
			update_option( QNAP_MESSAGES, $messages );
		}

		return $message;
	}
}
