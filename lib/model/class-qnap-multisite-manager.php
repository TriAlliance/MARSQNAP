<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
    die( 'not here' );
}

/**
 * Multisite Manager class
 * Handles multisite backup and restore operations
 */
class QNAP_Multisite_Manager {
    
    /**
     * Get all sites in the network
     * 
     * @return array List of site information
     */
    public static function get_sites() {
        if (!is_multisite()) {
            return array(array(
                'blog_id' => 1,
                'domain' => parse_url(site_url(), PHP_URL_HOST),
                'path' => parse_url(site_url(), PHP_URL_PATH),
                'name' => get_bloginfo('name'),
            ));
        }
        
        $sites = array();
        
        // Get all sites in the network
        $blog_list = get_sites(array('number' => 1000));
        
        foreach ($blog_list as $blog) {
            $blog_details = get_blog_details($blog->blog_id);
            
            $sites[] = array(
                'blog_id' => $blog->blog_id,
                'domain' => $blog->domain,
                'path' => $blog->path,
                'name' => $blog_details->blogname,
            );
        }
        
        return $sites;
    }
    
    /**
     * Get site tables for a specific blog
     * 
     * @param int $blog_id Blog ID
     * @return array List of database tables for the site
     */
    public static function get_site_tables($blog_id = 1) {
        global $wpdb;
        
        if ($blog_id == 1) {
            $prefix = $wpdb->base_prefix;
        } else {
            $prefix = $wpdb->base_prefix . $blog_id . '_';
        }
        
        $tables = array();
        $results = $wpdb->get_results("SHOW TABLES LIKE '{$prefix}%'", ARRAY_N);
        
        foreach ($results as $table) {
            $tables[] = $table[0];
        }
        
        return $tables;
    }
    
    /**
     * Get all tables including global tables
     * 
     * @return array List of all database tables in the multisite network
     */
    public static function get_all_tables() {
        global $wpdb;
        
        $tables = array();
        $results = $wpdb->get_results("SHOW TABLES", ARRAY_N);
        
        foreach ($results as $table) {
            $tables[] = $table[0];
        }
        
        return $tables;
    }
    
    /**
     * Prepare multisite data for export
     * 
     * @param array $params Request parameters
     * @return array Updated parameters
     */
    public static function prepare_multisite_export($params) {
        // Create multisite.json file to store multisite information
        $multisite_data = array();
        
        // Add network information
        $multisite_data['Network'] = array(
            'Version' => get_site_option('db_version'),
            'SiteURL' => network_site_url(),
            'HomeURL' => network_home_url(),
        );
        
        // Get selected sites
        $selected_sites = isset($params['options']['sites']) ? $params['options']['sites'] : array();
        
        // If no sites selected, include all sites
        if (empty($selected_sites)) {
            $sites = self::get_sites();
            $selected_sites = array_column($sites, 'blog_id');
            $params['options']['sites'] = $selected_sites;
        }
        
        // Add site information
        $multisite_data['Sites'] = array();
        
        foreach ($selected_sites as $blog_id) {
            switch_to_blog($blog_id);
            
            $multisite_data['Sites'][] = array(
                'BlogID' => $blog_id,
                'SiteURL' => site_url(),
                'HomeURL' => home_url(),
                'Uploads' => get_option('upload_path'),
                'UploadsURL' => get_option('upload_url_path'),
                'Plugins' => get_option('active_plugins', array()),
                'Template' => get_option('template'),
                'Stylesheet' => get_option('stylesheet'),
                'WordPress' => array(
                    'Version' => get_bloginfo('version'),
                    'Charset' => get_bloginfo('charset'),
                    'Language' => get_bloginfo('language'),
                    'Uploads' => qnap_get_uploads_dir(),
                    'UploadsURL' => qnap_get_uploads_url(),
                ),
            );
            
            restore_current_blog();
        }
        
        // Save multisite data to file
        $handle = qnap_open(qnap_multisite_path($params), 'w');
        qnap_write($handle, json_encode($multisite_data));
        qnap_close($handle);
        
        return $params;
    }
    
    /**
     * Process multisite tables for export
     * 
     * @param array $params Request parameters
     * @return array Updated parameters
     */
    public static function process_multisite_tables($params) {
        if (!is_multisite()) {
            return $params;
        }
        
        // Get selected sites
        $selected_sites = isset($params['options']['sites']) ? $params['options']['sites'] : array();
        
        // If no sites selected, return without changes
        if (empty($selected_sites)) {
            return $params;
        }
        
        // Add table prefix filters for each site
        $mysql = QNAP_Database_Utility::create_client();
        
        global $wpdb;
        
        // Include global tables
        foreach ($wpdb->global_tables as $global_table) {
            $mysql->add_table_prefix_filter($wpdb->base_prefix . $global_table);
        }
        
        // Include site-specific tables
        foreach ($selected_sites as $blog_id) {
            if ($blog_id == 1) {
                $mysql->add_table_prefix_filter($wpdb->base_prefix);
            } else {
                $mysql->add_table_prefix_filter($wpdb->base_prefix . $blog_id . '_');
            }
        }
        
        return $params;
    }
    
    /**
     * Check if multisite extension is active
     * 
     * @return boolean
     */
    public static function is_multisite_supported() {
        // We're adding direct support rather than requiring an extension
        return true;
    }
}