<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
    die( 'not here' );
}

class QNAP_Export_Multisite {

    /**
     * Execute multisite export process
     *
     * @param  array $params Request parameters
     * @return array
     */
    public static function execute($params) {
        // Check if WordPress is in multisite mode
        if (!is_multisite()) {
            return $params;
        }

        // Set progress
        QNAP_Status::info(__('Preparing multisite configuration...', QNAP_PLUGIN_NAME));

        // Prepare multisite data for export
        $params = QNAP_Multisite_Manager::prepare_multisite_export($params);

        // Set progress
        QNAP_Status::info(__('Done preparing multisite configuration.', QNAP_PLUGIN_NAME));

        return $params;
    }
}