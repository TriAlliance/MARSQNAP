<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Export_Init {

	public static function execute( $params ) {

		// Qnap_Log::append(qnap_get_log_client($params), 'Start to backup WordPress: ' . print_r($params, true));
		Qnap_Log::append(qnap_get_log_client($params), '[Multi-Application Recovery Service] Started backing up WordPress.');

		$items = QNAP_Backups::get_files();
		foreach ($items as $item) {
			QNAP_Backups::delete_file($item['filename']);
		}

		$blog_id = null;

		// Get subsite Blog ID
		if ( isset( $params['options']['sites'] ) && ( $sites = $params['options']['sites'] ) ) {
			if ( count( $sites ) === 1 ) {
				$blog_id = array_shift( $sites );
			}
		}

		// Set archive
		if ( empty( $params['archive'] ) ) {
			$params['archive'] = qnap_archive_file( $blog_id );
		}

		// Set storage
		if ( empty( $params['storage'] ) ) {
			$params['storage'] = qnap_storage_folder();
		}

		return $params;
	}
}
