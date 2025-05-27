<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
    die( 'not here' );
}

class QNAP_Import_Multisite {

    /**
     * Execute multisite import process
     *
     * @param  array $params Request parameters
     * @return array
     */
    public static function execute($params) {
        // Check if multisite.json file exists
        if (!is_file(qnap_multisite_path($params))) {
            return $params;
        }

        // Set progress
        QNAP_Status::info(__('Preparing multisite configuration...', QNAP_PLUGIN_NAME));

        try {
            // Read multisite.json file
            $handle = qnap_open(qnap_multisite_path($params), 'r');
            $multisite_data = qnap_read($handle, filesize(qnap_multisite_path($params)));
            $multisite = json_decode($multisite_data, true);
            qnap_close($handle);

            // Check if WordPress is in multisite mode
            if (!is_multisite()) {
                if (isset($multisite['Network'])) {
                    throw new QNAP_Import_Exception(
                        __('The backup contains a multisite WordPress installation. Your current WordPress is not configured for multisite. Please configure multisite before importing.', QNAP_PLUGIN_NAME)
                    );
                }
            } else {
                // Check if we're importing to the same WordPress network structure
                if (isset($multisite['Network']) && count($multisite['Sites']) > 1) {
                    // Validate network structure compatibility
                    self::validate_network_compatibility($multisite);
                }
            }

            // Set progress
            QNAP_Status::info(__('Done preparing multisite configuration.', QNAP_PLUGIN_NAME));
        } catch (Exception $e) {
            throw new QNAP_Import_Exception($e->getMessage());
        }

        return $params;
    }

    /**
     * Validate network compatibility
     *
     * @param array $multisite Multisite configuration
     * @throws QNAP_Import_Exception
     */
    private static function validate_network_compatibility($multisite) {
        // Check network structure
        $current_sites = QNAP_Multisite_Manager::get_sites();
        $backup_sites = isset($multisite['Sites']) ? $multisite['Sites'] : array();

        // Check if we have enough sites
        if (count($current_sites) < count($backup_sites)) {
            // We don't have enough sites, show a warning
            QNAP_Status::info(
                sprintf(
                    __('Warning: The backup contains %d sites, but your current network has only %d sites. Some sites may not be properly restored.', QNAP_PLUGIN_NAME),
                    count($backup_sites),
                    count($current_sites)
                )
            );
        }

        // Validate domain mapping if applicable
        foreach ($backup_sites as $backup_site) {
            $domain_found = false;
            foreach ($current_sites as $current_site) {
                if ($backup_site['BlogID'] == $current_site['blog_id']) {
                    $domain_found = true;
                    break;
                }
            }

            if (!$domain_found && $backup_site['BlogID'] > 1) {
                QNAP_Status::info(
                    sprintf(
                        __('Warning: The backup contains a site (ID: %d) that does not exist in your current network.', QNAP_PLUGIN_NAME),
                        $backup_site['BlogID']
                    )
                );
            }
        }
    }
}