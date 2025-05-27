<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Notification {

	public static function ok( $subject, $message ) {
		// Enable notifications
		if ( ! apply_filters( 'qnap_notification_ok_toggle', false ) ) {
			return;
		}

		// Set email
		if ( ! ( $email = apply_filters( 'qnap_notification_ok_email', get_option( 'admin_email', false ) ) ) ) {
			return;
		}

		// Set subject
		if ( ! ( $subject = apply_filters( 'qnap_notification_ok_subject', $subject ) ) ) {
			return;
		}

		// Set message
		if ( ! ( $message = apply_filters( 'qnap_notification_ok_message', $message ) ) ) {
			return;
		}

		// Send email
		if ( qnap_is_scheduled_backup() ) {
			wp_mail( $email, $subject, $message, array( 'Content-Type: text/html; charset=UTF-8' ) );
		}
	}

	public static function error( $subject, $message ) {
		// Enable notifications
		if ( ! apply_filters( 'qnap_notification_error_toggle', false ) ) {
			return;
		}

		// Set email
		if ( ! ( $email = apply_filters( 'qnap_notification_error_email', get_option( 'admin_email', false ) ) ) ) {
			return;
		}

		// Set subject
		if ( ! ( $subject = apply_filters( 'qnap_notification_error_subject', $subject ) ) ) {
			return;
		}

		// Set message
		if ( ! ( $message = apply_filters( 'qnap_notification_error_message', $message ) ) ) {
			return;
		}

		// Send email
		if ( qnap_is_scheduled_backup() ) {
			wp_mail( $email, $subject, $message, array( 'Content-Type: text/html; charset=UTF-8' ) );
		}
	}
}
