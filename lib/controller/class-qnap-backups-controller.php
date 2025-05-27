<?php
namespace qnap;
if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Backups_Controller {

	public static function index() {
		QNAP_Template::render(
			'backups/qnap',
			array(
				'backups'  => QNAP_Backups::get_files(),
				'logs'     => array_reverse(Qnap_Log::getAll()),
				'labels'   => QNAP_Backups::get_labels(),
				'username' => get_option( QNAP_AUTH_USER ),
				'password' => get_option( QNAP_AUTH_PASSWORD ),
				'secret_key' => get_option( QNAP_SECRET_KEY ),
			)
		);
	}

	public static function delete( $params = array() ) {
		qnap_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = qnap_sanitized_params();
		}

		// Set secret key
		$secret_key = null;
		if ( isset( $params['secret_key'] ) ) {
			$secret_key = trim( $params['secret_key'] );
		}

		// Set archive
		$archive = null;
		if ( isset( $params['archive'] ) ) {
			$archive = trim( $params['archive'] );
		}

		try {
			// Ensure that unauthorized people cannot access delete action
			qnap_verify_secret_key( $secret_key );
		} catch ( QNAP_Not_Valid_Secret_Key_Exception $e ) {
			exit;
		}

		try {
			QNAP_Backups::delete_file( $archive );
			QNAP_Backups::delete_label( $archive );
		} catch ( QNAP_Backups_Exception $e ) {
			echo json_encode( array( 'error' => array( 'message' => $e->getMessage() ) ) );
			exit;
		}

		echo json_encode( array( 'error' => array( 'message' => 'unknown error' ) ) );
		exit;
	}

	public static function backup_list( $params = array() ) {
		qnap_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = qnap_sanitized_params();
		}

		// Set secret key
		$secret_key = null;
		if ( isset( $params['secret_key'] ) ) {
			$secret_key = trim( $params['secret_key'] );
		}

		try {
			// Ensure that unauthorized people cannot access backups list action
			qnap_verify_secret_key( $secret_key );
		} catch ( QNAP_Not_Valid_Secret_Key_Exception $e ) {
			exit;
		}

		QNAP_Template::render(
			'backups/backups-list',
			array(
				'backups' => QNAP_Backups::get_files(),
				'labels'  => QNAP_Backups::get_labels(),
			)
		);
		exit;
	}

	public static function delete_log( $params = array() ) {
		qnap_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = qnap_sanitized_params();
		}

		// Set secret key
		$secret_key = null;
		if ( isset( $params['secret_key'] ) ) {
			$secret_key = trim( $params['secret_key'] );
		}

		// Set archive
		$archive = null;
		if ( isset( $params['archive'] ) ) {
			$archive = trim( $params['archive'] );
		}
		try {
			// Ensure that unauthorized people cannot access delete action
			qnap_verify_secret_key( $secret_key );
		} catch ( QNAP_Not_Valid_Secret_Key_Exception $e ) {
			exit;
		}

		Qnap_Log::flush();
		echo json_encode( array( 'error' => array( 'message' => 'unknown error' ) ) );
		exit;
	}
}
