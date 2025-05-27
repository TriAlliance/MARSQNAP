<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

/**
 * Get storage absolute path
 *
 * @param  array  $params Request parameters
 * @return string
 */
function qnap_storage_path( $params ) {
	if ( empty( $params['storage'] ) ) {
		throw new QNAP_Storage_Exception( __( 'Unable to locate storage path.', QNAP_PLUGIN_NAME ) );
	}

	// Get storage path
	$storage = QNAP_STORAGE_PATH . DIRECTORY_SEPARATOR . basename( $params['storage'] );
	if ( ! is_dir( $storage ) ) {
		mkdir( $storage );
	}

	return $storage;
}

/**
 * Get backup absolute path
 *
 * @param  array  $params Request parameters
 * @return string
 */
function qnap_backup_path( $params ) {
	if ( empty( $params['archive'] ) ) {
		throw new QNAP_Archive_Exception( __( 'Unable to locate archive path.', QNAP_PLUGIN_NAME ) );
	}

	// Validate archive path
	if ( validate_file( $params['archive'] ) !== 0 ) {
		throw new QNAP_Archive_Exception( __( 'Invalid archive path.', QNAP_PLUGIN_NAME ) );
	}

	return QNAP_BACKUPS_PATH . DIRECTORY_SEPARATOR . $params['archive'];
}

/**
 * Get archive absolute path
 *
 * @param  array  $params Request parameters
 * @return string
 */
function qnap_archive_path( $params ) {
	if ( empty( $params['archive'] ) ) {
		throw new QNAP_Archive_Exception( __( 'Unable to locate archive path.', QNAP_PLUGIN_NAME ) );
	}

	// Validate archive path
	if ( validate_file( $params['archive'] ) !== 0 ) {
		throw new QNAP_Archive_Exception( __( 'Invalid archive path.', QNAP_PLUGIN_NAME ) );
	}

	// Get archive path
	if ( empty( $params['qnap_manual_restore'] ) ) {
		return qnap_storage_path( $params ) . DIRECTORY_SEPARATOR . $params['archive'];
	}

	return qnap_backup_path( $params );
}

/**
 * Get content.list absolute path
 *
 * @param  array  $params Request parameters
 * @return string
 */
function qnap_content_list_path( $params ) {
	return qnap_storage_path( $params ) . DIRECTORY_SEPARATOR . QNAP_CONTENT_LIST_NAME;
}

/**
 * Get media.list absolute path
 *
 * @param  array  $params Request parameters
 * @return string
 */
function qnap_media_list_path( $params ) {
	return qnap_storage_path( $params ) . DIRECTORY_SEPARATOR . QNAP_MEDIA_LIST_NAME;
}

/**
 * Get tables.list absolute path
 *
 * @param  array  $params Request parameters
 * @return string
 */
function qnap_tables_list_path( $params ) {
	return qnap_storage_path( $params ) . DIRECTORY_SEPARATOR . QNAP_TABLES_LIST_NAME;
}

/**
 * Get package.json absolute path
 *
 * @param  array  $params Request parameters
 * @return string
 */
function qnap_package_path( $params ) {
	return qnap_storage_path( $params ) . DIRECTORY_SEPARATOR . QNAP_PACKAGE_NAME;
}

/**
 * Get multisite.json absolute path
 *
 * @param  array  $params Request parameters
 * @return string
 */
function qnap_multisite_path( $params ) {
	return qnap_storage_path( $params ) . DIRECTORY_SEPARATOR . QNAP_MULTISITE_NAME;
}

/**
 * Get blogs.json absolute path
 *
 * @param  array  $params Request parameters
 * @return string
 */
function qnap_blogs_path( $params ) {
	return qnap_storage_path( $params ) . DIRECTORY_SEPARATOR . QNAP_BLOGS_NAME;
}

/**
 * Get database.sql absolute path
 *
 * @param  array  $params Request parameters
 * @return string
 */
function qnap_database_path( $params ) {
	return qnap_storage_path( $params ) . DIRECTORY_SEPARATOR . QNAP_DATABASE_NAME;
}

/**
 * Get error log absolute path
 *
 * @return string
 */
function qnap_error_path() {
	return QNAP_STORAGE_PATH . DIRECTORY_SEPARATOR . QNAP_ERROR_NAME;
}

/**
 * Get archive name
 *
 * @param  array  $params Request parameters
 * @return string
 */
function qnap_archive_name( $params ) {
	return basename( 'qnap-' . $params['archive'] );
}

/**
 * Get backup URL address
 *
 * @param  array  $params Request parameters
 * @return string
 */
function qnap_backup_url( $params ) {
	return QNAP_BACKUPS_URL . '/' . qnap_replace_directory_separator_with_forward_slash( $params['archive'] );
}

/**
 * Get archive size in bytes
 *
 * @param  array   $params Request parameters
 * @return integer
 */
function qnap_archive_bytes( $params ) {
	return filesize( qnap_archive_path( $params ) );
}

/**
 * Get database size in bytes
 *
 * @param  array   $params Request parameters
 * @return integer
 */
function qnap_database_bytes( $params ) {
	return filesize( qnap_database_path( $params ) );
}

/**
 * Get package size in bytes
 *
 * @param  array   $params Request parameters
 * @return integer
 */
function qnap_package_bytes( $params ) {
	return filesize( qnap_package_path( $params ) );
}

/**
 * Get backup size as text
 *
 * @param  array  $params Request parameters
 * @return string
 */
function qnap_backup_size( $params ) {
	return qnap_size_format( filesize( qnap_backup_path( $params ) ) );
}

/**
 * Parse file size
 *
 * @param  string $size    File size
 * @param  string $default Default size
 * @return string
 */
function qnap_parse_size( $size, $default = null ) {
	$suffixes = array(
		''  => 1,
		'k' => 1000,
		'm' => 1000000,
		'g' => 1000000000,
	);

	// Parse size format
	if ( preg_match( '/([0-9]+)\s*(k|m|g)?(b?(ytes?)?)/i', $size, $matches ) ) {
		return $matches[1] * $suffixes[ strtolower( $matches[2] ) ];
	}

	return $default;
}

/**
 * Format file size into human-readable string
 *
 * Fixes the WP size_format bug: size_format( '0' ) => false
 *
 * @param  int|string   $bytes            Number of bytes. Note max integer size for integers.
 * @param  int          $decimals         Optional. Precision of number of decimal places. Default 0.
 * @return string|false False on failure. Number string on success.
 */
function qnap_size_format( $bytes, $decimals = 0 ) {
	if ( strval( $bytes ) === '0' ) {
		return size_format( 0, $decimals );
	}

	return size_format( $bytes, $decimals );
}

/**
 * Get current site name
 *
 * @param  integer $blog_id Blog ID
 * @return string
 */
function qnap_site_name( $blog_id = null ) {
	return parse_url( get_site_url( $blog_id ), PHP_URL_HOST );
}

/**
 * Get archive file name
 *
 * @param  integer $blog_id Blog ID
 * @return string
 */
function qnap_archive_file( $blog_id = null ) {
	$name = array();

	// Add domain
	$name[] = parse_url( get_site_url( $blog_id ), PHP_URL_HOST );

	// Add path
	if ( ( $path = explode( '/', parse_url( get_site_url( $blog_id ), PHP_URL_PATH ) ) ) ) {
		foreach ( $path as $directory ) {
			if ( $directory ) {
				$name[] = $directory;
			}
		}
	}

	// Add year, month and day
	$name[] = date( 'Ymd' );

	// Add hours, minutes and seconds
	$name[] = date( 'His' );

	// Add unique identifier
	$name[] = qnap_generate_random_string( 6, false );

	return sprintf( '%s.qwp', strtolower( implode( '-', $name ) ) );
}

/**
 * Generate random string
 *
 * @param  integer $length              String length
 * @param  boolean $mixed_chars         Whether to include mixed characters
 * @param  boolean $special_chars       Whether to include special characters
 * @param  boolean $extra_special_chars Whether to include extra special characters
 * @return string
 */
function qnap_generate_random_string( $length = 12, $mixed_chars = true, $special_chars = false, $extra_special_chars = false ) {
	$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
	if ( $mixed_chars ) {
		$chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	}

	if ( $special_chars ) {
		$chars .= '!@#$%^&*()';
	}

	if ( $extra_special_chars ) {
		$chars .= '-_ []{}<>~`+=,.;:/?|';
	}

	$str = '';
	for ( $i = 0; $i < $length; $i++ ) {
		$str .= substr( $chars, wp_rand( 0, strlen( $chars ) - 1 ), 1 );
	}

	return $str;
}

/**
 * Get storage folder name
 *
 * @return string
 */
function qnap_storage_folder() {
	return uniqid();
}

/**
 * Check whether blog ID is main site
 *
 * @param  integer $blog_id Blog ID
 * @return boolean
 */
function qnap_is_mainsite( $blog_id = null ) {
	return $blog_id === null || $blog_id === 0 || $blog_id === 1;
}

/**
 * Get files absolute path by blog ID
 *
 * @param  integer $blog_id Blog ID
 * @return string
 */
function qnap_blog_files_abspath( $blog_id = null ) {
	if ( qnap_is_mainsite( $blog_id ) ) {
		return qnap_get_uploads_dir();
	}

	return WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'blogs.dir' . DIRECTORY_SEPARATOR . $blog_id . DIRECTORY_SEPARATOR . 'files';
}

/**
 * Get blogs.dir absolute path by blog ID
 *
 * @param  integer $blog_id Blog ID
 * @return string
 */
function qnap_blog_blogsdir_abspath( $blog_id = null ) {
	if ( qnap_is_mainsite( $blog_id ) ) {
		return qnap_get_uploads_dir();
	}

	return WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'blogs.dir' . DIRECTORY_SEPARATOR . $blog_id;
}

/**
 * Get sites absolute path by blog ID
 *
 * @param  integer $blog_id Blog ID
 * @return string
 */
function qnap_blog_sites_abspath( $blog_id = null ) {
	if ( qnap_is_mainsite( $blog_id ) ) {
		return qnap_get_uploads_dir();
	}

	return qnap_get_uploads_dir() . DIRECTORY_SEPARATOR . 'sites' . DIRECTORY_SEPARATOR . $blog_id;
}

/**
 * Get files relative path by blog ID
 *
 * @param  integer $blog_id Blog ID
 * @return string
 */
function qnap_blog_files_relpath( $blog_id = null ) {
	if ( qnap_is_mainsite( $blog_id ) ) {
		return 'uploads';
	}

	return 'blogs.dir' . DIRECTORY_SEPARATOR . $blog_id . DIRECTORY_SEPARATOR . 'files';
}

/**
 * Get blogs.dir relative path by blog ID
 *
 * @param  integer $blog_id Blog ID
 * @return string
 */
function qnap_blog_blogsdir_relpath( $blog_id = null ) {
	if ( qnap_is_mainsite( $blog_id ) ) {
		return 'uploads';
	}

	return 'blogs.dir' . DIRECTORY_SEPARATOR . $blog_id;
}

/**
 * Get sites relative path by blog ID
 *
 * @param  integer $blog_id Blog ID
 * @return string
 */
function qnap_blog_sites_relpath( $blog_id = null ) {
	if ( qnap_is_mainsite( $blog_id ) ) {
		return 'uploads';
	}

	return 'uploads' . DIRECTORY_SEPARATOR . 'sites' . DIRECTORY_SEPARATOR . $blog_id;
}

/**
 * Get files URL by blog ID
 *
 * @param  integer $blog_id Blog ID
 * @return string
 */
function qnap_blog_files_url( $blog_id = null ) {
	$rel_content_url = basename( content_url() );
	if ( qnap_is_mainsite( $blog_id ) ) {
		return $rel_content_url . '/uploads/';
	}
	return $rel_content_url . '/blogs.dir/' . $blog_id . '/files';
}

/**
 * Get sites URL by blog ID
 *
 * @param  integer $blog_id Blog ID
 * @return string
 */
function qnap_blog_sites_url( $blog_id = null ) {
	$rel_content_url = basename( content_url() );
	if ( qnap_is_mainsite( $blog_id ) ) {
		return $rel_content_url . '/uploads/';
	}
	return $rel_content_url . '/uploads/sites/' . $blog_id . '/';
}

/**
 * Get uploads URL by blog ID
 *
 * @param  integer $blog_id Blog ID
 * @return string
 */
function qnap_blog_uploads_url( $blog_id = null ) {
	if ( qnap_is_mainsite( $blog_id ) ) {
		return sprintf( '%s/', qnap_get_uploads_path() );
	}

	return sprintf( '%s/sites/%d/', qnap_get_uploads_path(), $blog_id );
}

/**
 * Get Qeek table prefix by blog ID
 *
 * @param  integer $blog_id Blog ID
 * @return string
 */
function qnap_qeek_prefix( $blog_id = null ) {
	if ( qnap_is_mainsite( $blog_id ) ) {
		return QNAP_TABLE_PREFIX;
	}

	return QNAP_TABLE_PREFIX . $blog_id . '_';
}

/**
 * Get WordPress table prefix by blog ID
 *
 * @param  integer $blog_id Blog ID
 * @return string
 */
function qnap_table_prefix( $blog_id = null ) {
	global $wpdb;

	// Set base table prefix
	if ( qnap_is_mainsite( $blog_id ) ) {
		return $wpdb->base_prefix;
	}

	return $wpdb->base_prefix . $blog_id . '_';
}

/**
 * Get default content filters
 *
 * @param  array $filters List of files and directories
 * @return array
 */
function qnap_content_filters( $filters = array() ) {
	return array_merge(
		$filters,
		array(
			QNAP_BACKUPS_NAME,
			QNAP_PACKAGE_NAME,
			QNAP_MULTISITE_NAME,
			QNAP_DATABASE_NAME,
		)
	);
}

/**
 * Get default plugin filters
 *
 * @param  array $filters List of plugins
 * @return array
 */
function qnap_plugin_filters( $filters = array() ) {
	// WP Migration Plugin
	if ( defined( 'QNAP_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAP_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration';
	}

	// Microsoft Azure Extension
	if ( defined( 'QNAPZE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPZE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-azure-storage-extension';
	}

	// Backblaze B2 Extension
	if ( defined( 'QNAPAE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPAE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-b2-extension';
	}

	// Backup Plugin
	if ( defined( 'QNAPVE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPVE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-backup';
	}

	// Box Extension
	if ( defined( 'QNAPBE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPBE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-box-extension';
	}

	// DigitalOcean Spaces Extension
	if ( defined( 'QNAPIE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPIE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-digitalocean-extension';
	}

	// Direct Extension
	if ( defined( 'QNAPXE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPXE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-direct-extension';
	}

	// Dropbox Extension
	if ( defined( 'QNAPDE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPDE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-dropbox-extension';
	}

	// File Extension
	if ( defined( 'QNAPTE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPTE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-file-extension';
	}

	// FTP Extension
	if ( defined( 'QNAPFE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPFE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-ftp-extension';
	}

	// Google Cloud Storage Extension
	if ( defined( 'QNAPCE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPCE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-gcloud-storage-extension';
	}

	// Google Drive Extension
	if ( defined( 'QNAPGE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPGE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-gdrive-extension';
	}

	// Amazon Glacier Extension
	if ( defined( 'QNAPRE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPRE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-glacier-extension';
	}

	// Mega Extension
	if ( defined( 'QNAPEE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPEE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-mega-extension';
	}

	// Multisite Extension
	if ( defined( 'QNAPME_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPME_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-multisite-extension';
	}

	// OneDrive Extension
	if ( defined( 'QNAPOE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPOE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-onedrive-extension';
	}

	// pCloud Extension
	if ( defined( 'QNAPPE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPPE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-pcloud-extension';
	}

	// Pro Plugin
	if ( defined( 'QNAPKE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPKE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-pro';
	}

	// S3 Client Extension
	if ( defined( 'QNAPNE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPNE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-s3-client-extension';
	}

	// Amazon S3 Extension
	if ( defined( 'QNAPSE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPSE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-s3-extension';
	}

	// Unlimited Extension
	if ( defined( 'QNAPUE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPUE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-unlimited-extension';
	}

	// URL Extension
	if ( defined( 'QNAPLE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPLE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-url-extension';
	}

	// WebDAV Extension
	if ( defined( 'QNAPWE_PLUGIN_BASENAME' ) ) {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( QNAPWE_PLUGIN_BASENAME );
	} else {
		$filters[] = 'plugins' . DIRECTORY_SEPARATOR . 'qnap-wp-migration-webdav-extension';
	}

	return $filters;
}

/**
 * Get active Qeek plugins
 *
 * @return array
 */
function qnap_active_qeek_plugins( $plugins = array() ) {
	// WP Migration Plugin
	if ( defined( 'QNAP_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAP_PLUGIN_BASENAME;
	}

	// Microsoft Azure Extension
	if ( defined( 'QNAPZE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPZE_PLUGIN_BASENAME;
	}

	// Backblaze B2 Extension
	if ( defined( 'QNAPAE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPAE_PLUGIN_BASENAME;
	}

	// Backup Plugin
	if ( defined( 'QNAPVE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPVE_PLUGIN_BASENAME;
	}

	// Box Extension
	if ( defined( 'QNAPBE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPBE_PLUGIN_BASENAME;
	}

	// DigitalOcean Spaces Extension
	if ( defined( 'QNAPIE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPIE_PLUGIN_BASENAME;
	}

	// Direct Extension
	if ( defined( 'QNAPXE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPXE_PLUGIN_BASENAME;
	}

	// Dropbox Extension
	if ( defined( 'QNAPDE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPDE_PLUGIN_BASENAME;
	}

	// File Extension
	if ( defined( 'QNAPTE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPTE_PLUGIN_BASENAME;
	}

	// FTP Extension
	if ( defined( 'QNAPFE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPFE_PLUGIN_BASENAME;
	}

	// Google Cloud Storage Extension
	if ( defined( 'QNAPCE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPCE_PLUGIN_BASENAME;
	}

	// Google Drive Extension
	if ( defined( 'QNAPGE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPGE_PLUGIN_BASENAME;
	}

	// Amazon Glacier Extension
	if ( defined( 'QNAPRE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPRE_PLUGIN_BASENAME;
	}

	// Mega Extension
	if ( defined( 'QNAPEE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPEE_PLUGIN_BASENAME;
	}

	// Multisite Extension
	if ( defined( 'QNAPME_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPME_PLUGIN_BASENAME;
	}

	// OneDrive Extension
	if ( defined( 'QNAPOE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPOE_PLUGIN_BASENAME;
	}

	// pCloud Extension
	if ( defined( 'QNAPPE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPPE_PLUGIN_BASENAME;
	}

	// Pro Plugin
	if ( defined( 'QNAPKE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPKE_PLUGIN_BASENAME;
	}

	// S3 Client Extension
	if ( defined( 'QNAPNE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPNE_PLUGIN_BASENAME;
	}

	// Amazon S3 Extension
	if ( defined( 'QNAPSE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPSE_PLUGIN_BASENAME;
	}

	// Unlimited Extension
	if ( defined( 'QNAPUE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPUE_PLUGIN_BASENAME;
	}

	// URL Extension
	if ( defined( 'QNAPLE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPLE_PLUGIN_BASENAME;
	}

	// WebDAV Extension
	if ( defined( 'QNAPWE_PLUGIN_BASENAME' ) ) {
		$plugins[] = QNAPWE_PLUGIN_BASENAME;
	}

	return $plugins;
}

/**
 * Get active plugins
 *
 * @return array
 */
function qnap_active_plugins() {
	return array_values( get_option( QNAP_ACTIVE_PLUGINS, array() ) );
}

/**
 * Set active plugins (inspired by WordPress activate_plugins() function)
 *
 * @param  array   $plugins List of plugins
 * @return boolean
 */
function qnap_activate_plugins( $plugins ) {
	$current = get_option( QNAP_ACTIVE_PLUGINS, array() );

	// Add plugins
	foreach ( $plugins as $plugin ) {
		if ( ! in_array( $plugin, $current ) && ! is_wp_error( validate_plugin( $plugin ) ) ) {
			$current[] = $plugin;
		}
	}

	return update_option( QNAP_ACTIVE_PLUGINS, $current );
}

/**
 * Get active template
 *
 * @return string
 */
function qnap_active_template() {
	return get_option( QNAP_ACTIVE_TEMPLATE );
}

/**
 * Get active stylesheet
 *
 * @return string
 */
function qnap_active_stylesheet() {
	return get_option( QNAP_ACTIVE_STYLESHEET );
}

/**
 * Set active template
 *
 * @param  string  $template Template name
 * @return boolean
 */
function qnap_activate_template( $template ) {
	return update_option( QNAP_ACTIVE_TEMPLATE, $template );
}

/**
 * Set active stylesheet
 *
 * @param  string  $stylesheet Stylesheet name
 * @return boolean
 */
function qnap_activate_stylesheet( $stylesheet ) {
	return update_option( QNAP_ACTIVE_STYLESHEET, $stylesheet );
}

/**
 * Set inactive plugins (inspired by WordPress deactivate_plugins() function)
 *
 * @param  array   $plugins List of plugins
 * @return boolean
 */
function qnap_deactivate_plugins( $plugins ) {
	$current = get_option( QNAP_ACTIVE_PLUGINS, array() );

	// Remove plugins
	foreach ( $plugins as $plugin ) {
		if ( ( $key = array_search( $plugin, $current ) ) !== false ) {
			unset( $current[ $key ] );
		}
	}

	return update_option( QNAP_ACTIVE_PLUGINS, $current );
}

/**
 * Deactivate Jetpack modules
 *
 * @param  array   $modules List of modules
 * @return boolean
 */
function qnap_deactivate_jetpack_modules( $modules ) {
	$current = get_option( QNAP_JETPACK_ACTIVE_MODULES, array() );

	// Remove modules
	foreach ( $modules as $module ) {
		if ( ( $key = array_search( $module, $current ) ) !== false ) {
			unset( $current[ $key ] );
		}
	}

	return update_option( QNAP_JETPACK_ACTIVE_MODULES, $current );
}

/**
 * Deactivate Swift Optimizer rules
 *
 * @param  array   $rules List of rules
 * @return boolean
 */
function qnap_deactivate_swift_optimizer_rules( $rules ) {
	$current = get_option( QNAP_SWIFT_OPTIMIZER_PLUGIN_ORGANIZER, array() );

	// Remove rules
	foreach ( $rules as $rule ) {
		unset( $current['rules'][ $rule ] );
	}

	return update_option( QNAP_SWIFT_OPTIMIZER_PLUGIN_ORGANIZER, $current );
}

/**
 * Deactivate Revolution Slider
 *
 * @param  string  $basename Plugin basename
 * @return boolean
 */
function qnap_deactivate_revolution_slider( $basename ) {
	if ( ( $plugins = get_plugins() ) ) {
		if ( isset( $plugins[ $basename ]['Version'] ) && ( $version = $plugins[ $basename ]['Version'] ) ) {
			if ( version_compare( PHP_VERSION, '7.3', '>=' ) && version_compare( $version, '5.4.8.3', '<' ) ) {
				return qnap_deactivate_plugins( array( $basename ) );
			}

			if ( version_compare( PHP_VERSION, '7.2', '>=' ) && version_compare( $version, '5.4.6', '<' ) ) {
				return qnap_deactivate_plugins( array( $basename ) );
			}

			if ( version_compare( PHP_VERSION, '7.1', '>=' ) && version_compare( $version, '5.4.1', '<' ) ) {
				return qnap_deactivate_plugins( array( $basename ) );
			}

			if ( version_compare( PHP_VERSION, '7.0', '>=' ) && version_compare( $version, '4.6.5', '<' ) ) {
				return qnap_deactivate_plugins( array( $basename ) );
			}
		}
	}

	return false;
}

/**
 * Initial DB version
 *
 * @return boolean
 */
function qnap_initial_db_version() {
	if ( ! get_option( QNAP_DB_VERSION ) ) {
		return update_option( QNAP_DB_VERSION, get_option( QNAP_INITIAL_DB_VERSION ) );
	}

	return false;
}

/**
 * Discover plugin basename
 *
 * @param  string $basename Plugin basename
 * @return string
 */
function qnap_discover_plugin_basename( $basename ) {
	if ( ( $plugins = get_plugins() ) ) {
		foreach ( $plugins as $plugin => $info ) {
			if ( strpos( dirname( $plugin ), dirname( $basename ) ) !== false ) {
				if ( basename( $plugin ) === basename( $basename ) ) {
					return $plugin;
				}
			}
		}
	}

	return $basename;
}

/**
 * Validate plugin basename
 *
 * @param  string  $basename Plugin basename
 * @return boolean
 */
function qnap_validate_plugin_basename( $basename ) {
	if ( ( $plugins = get_plugins() ) ) {
		foreach ( $plugins as $plugin => $info ) {
			if ( $plugin === $basename ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Validate theme basename
 *
 * @param  string  $basename Theme basename
 * @return boolean
 */
function qnap_validate_theme_basename( $basename ) {
	if ( ( $themes = search_theme_directories() ) ) {
		foreach ( $themes as $theme => $info ) {
			if ( $info['theme_file'] === $basename ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Flush WP options cache
 *
 * @return void
 */
function qnap_cache_flush() {
	wp_cache_init();
	wp_cache_flush();

	// Reset WP options cache
	wp_cache_set( 'alloptions', array(), 'options' );
	wp_cache_set( 'notoptions', array(), 'options' );

	// Reset WP sitemeta cache
	wp_cache_set( '1:notoptions', array(), 'site-options' );
	wp_cache_set( '1:ms_files_rewriting', false, 'site-options' );
	wp_cache_set( '1:active_sitewide_plugins', false, 'site-options' );

	// Delete WP options cache
	wp_cache_delete( 'alloptions', 'options' );
	wp_cache_delete( 'notoptions', 'options' );

	// Delete WP sitemeta cache
	wp_cache_delete( '1:notoptions', 'site-options' );
	wp_cache_delete( '1:ms_files_rewriting', 'site-options' );
	wp_cache_delete( '1:active_sitewide_plugins', 'site-options' );

	// Remove WP options filter
	remove_all_filters( 'sanitize_option_home' );
	remove_all_filters( 'sanitize_option_siteurl' );
	remove_all_filters( 'default_site_option_ms_files_rewriting' );
}

/**
 * Flush Elementor cache
 *
 * @return void
 */
function qnap_elementor_cache_flush() {
	delete_post_meta_by_key( '_elementor_css' );
	delete_option( '_elementor_global_css' );
	delete_option( 'elementor-custom-breakpoints-files' );
}

/**
 * Set URL scheme
 *
 * @param  string $url    URL value
 * @param  string $scheme URL scheme
 * @return string
 */
function qnap_url_scheme( $url, $scheme = '' ) {
	if ( empty( $scheme ) ) {
		return preg_replace( '#^\w+://#', '//', $url );
	}

	return preg_replace( '#^\w+://#', $scheme . '://', $url );
}

/**
 * Opens a file in specified mode
 *
 * @param  string   $file Path to the file to open
 * @param  string   $mode Mode in which to open the file
 *
 * @return resource
 * @throws QNAP_Not_Accessible_Exception
 */
function qnap_open( $file, $mode ) {
	$file_handle = @fopen( $file, $mode );
	if ( false === $file_handle ) {
		throw new QNAP_Not_Accessible_Exception( sprintf( __( 'Unable to open %s with mode %s.', QNAP_PLUGIN_NAME ), $file, $mode ) );
	}

	return $file_handle;
}

/**
 * Write contents to a file
 *
 * @param  resource $handle  File handle to write to
 * @param  string   $content Contents to write to the file
 * @return integer
 * @throws QNAP_Not_Writable_Exception
 * @throws QNAP_Quota_Exceeded_Exception
 */
function qnap_write( $handle, $content ) {
	$write_result = @fwrite( $handle, $content );
	if ( false === $write_result ) {
		if ( ( $meta = stream_get_meta_data( $handle ) ) ) {
			throw new QNAP_Not_Writable_Exception( sprintf( __( 'Unable to write to: %s.', QNAP_PLUGIN_NAME ), $meta['uri'] ) );
		}
	} elseif ( strlen( $content ) !== $write_result ) {
		if ( ( $meta = stream_get_meta_data( $handle ) ) ) {
			throw new QNAP_Quota_Exceeded_Exception( sprintf( __( 'Out of disk space. Unable to write to: %s.', QNAP_PLUGIN_NAME ), $meta['uri'] ) );
		}
	}

	return $write_result;
}

/**
 * Read contents from a file
 *
 * @param  resource $handle   File handle to read from
 * @param  string   $filesize File size
 * @return integer
 * @throws QNAP_Not_Readable_Exception
 */
function qnap_read( $handle, $filesize ) {
	$read_result = @fread( $handle, $filesize );
	if ( false === $read_result ) {
		if ( ( $meta = stream_get_meta_data( $handle ) ) ) {
			throw new QNAP_Not_Readable_Exception( sprintf( __( 'Unable to read file: %s.', QNAP_PLUGIN_NAME ), $meta['uri'] ) );
		}
	}

	return $read_result;
}

/**
 * Closes a file handle
 *
 * @param  resource $handle File handle to close
 * @return boolean
 */
function qnap_close( $handle ) {
	return @fclose( $handle );
}

/**
 * Deletes a file
 *
 * @param  string  $file Path to file to delete
 * @return boolean
 */
function qnap_unlink( $file ) {
	return @unlink( $file );
}

/**
 * Copies one file's contents to another
 *
 * @param  string $source_file      File to copy the contents from
 * @param  string $destination_file File to copy the contents to
 */
function qnap_copy( $source_file, $destination_file ) {
	$source_handle      = qnap_open( $source_file, 'rb' );
	$destination_handle = qnap_open( $destination_file, 'ab' );
	while ( $buffer = qnap_read( $source_handle, 4096 ) ) {
		qnap_write( $destination_handle, $buffer );
	}
	qnap_close( $source_handle );
	qnap_close( $destination_handle );
}

/**
 * Check whether file size is supported by current PHP version
 *
 * @param  string  $file         Path to file
 * @param  integer $php_int_size Size of PHP integer
 * @return boolean $php_int_max  Max value of PHP integer
 */
function qnap_is_filesize_supported( $file, $php_int_size = PHP_INT_SIZE, $php_int_max = PHP_INT_MAX ) {
	$size_result = true;

	// Check whether file size is less than 2GB in PHP 32bits
	if ( $php_int_size === 4 ) {
		if ( ( $file_handle = @fopen( $file, 'r' ) ) ) {
			if ( @fseek( $file_handle, $php_int_max, SEEK_SET ) !== -1 ) {
				if ( @fgetc( $file_handle ) !== false ) {
					$size_result = false;
				}
			}

			@fclose( $file_handle );
		}
	}

	return $size_result;
}

/**
 * Verify secret key
 *
 * @param  string  $secret_key Secret key
 * @return boolean
 * @throws QNAP_Not_Valid_Secret_Key_Exception
 */
function qnap_verify_secret_key( $secret_key ) {
	if ( $secret_key !== get_option( QNAP_SECRET_KEY ) ) {
		throw new QNAP_Not_Valid_Secret_Key_Exception( __( 'Unable to authenticate the secret key.', QNAP_PLUGIN_NAME ) );
	}

	return true;
}

/**
 * Is scheduled backup?
 *
 * @return boolean
 */
function qnap_is_scheduled_backup() {
	if ( isset( $_GET['qnap_manual_export'] ) || isset( $_POST['qnap_manual_export'] ) ) {
		return false;
	}

	if ( isset( $_GET['qnap_manual_import'] ) || isset( $_POST['qnap_manual_import'] ) ) {
		return false;
	}

	if ( isset( $_GET['qnap_manual_restore'] ) || isset( $_POST['qnap_manual_restore'] ) ) {
		return false;
	}

	return true;
}

/**
 * PHP setup environment
 *
 * @return void
 */
function qnap_setup_environment() {
	// Set whether a client disconnect should abort script execution
	@ignore_user_abort( true );

	// Set maximum execution time
	@set_time_limit( 0 );

	// Set maximum time in seconds a script is allowed to parse input data
	@ini_set( 'max_input_time', '-1' );

	// Set maximum backtracking steps
	@ini_set( 'pcre.backtrack_limit', PHP_INT_MAX );

	// Set binary safe encoding
	if ( @function_exists( 'mb_internal_encoding' ) && ( @ini_get( 'mbstring.func_overload' ) & 2 ) ) {
		@mb_internal_encoding( 'ISO-8859-1' );
	}

	// Clean (erase) the output buffer and turn off output buffering
	if ( @ob_get_length() ) {
		@ob_end_clean();
	}

	// Set error handler
	@set_error_handler( 'qnap\QNAP_Handler::error' );

	// Set shutdown handler
	@register_shutdown_function( 'qnap\QNAP_Handler::shutdown' );
}

/**
 * Get WordPress filter hooks
 *
 * @param  string $tag The name of the filter hook
 * @return array
 */
function qnap_get_filters( $tag ) {
	global $wp_filter;

	// Get WordPress filter hooks
	$filters = array();
	if ( isset( $wp_filter[ $tag ] ) ) {
		if ( ( $filters = $wp_filter[ $tag ] ) ) {
			// WordPress 4.7 introduces new class for working with filters/actions called WP_Hook
			// which adds another level of abstraction and we need to address it.
			if ( isset( $filters->callbacks ) ) {
				$filters = $filters->callbacks;
			}
		}

		ksort( $filters );
	}

	return $filters;
}

/**
 * Get WordPress uploads directory
 *
 * @return string
 */
function qnap_get_uploads_dir() {
	if ( ( $upload_dir = wp_upload_dir() ) ) {
		if ( isset( $upload_dir['basedir'] ) ) {
			return untrailingslashit( $upload_dir['basedir'] );
		}
	}
}

/**
 * Get WordPress uploads URL
 *
 * @return string
 */
function qnap_get_uploads_url() {
	if ( ( $upload_dir = wp_upload_dir() ) ) {
		if ( isset( $upload_dir['baseurl'] ) ) {
			return trailingslashit( $upload_dir['baseurl'] );
		}
	}
}

/**
 * Get WordPress uploads path
 *
 * @return string
 */
function qnap_get_uploads_path() {
	if ( ( $upload_dir = wp_upload_dir() ) ) {
		if ( isset( $upload_dir['basedir'] ) ) {
			return str_replace( ABSPATH, '', $upload_dir['basedir'] );
		}
	}
}

/**
 * i18n friendly version of basename()
 *
 * @param  string $path   File path
 * @param  string $suffix If the filename ends in suffix this will also be cut off
 * @return string
 */
function qnap_basename( $path, $suffix = '' ) {
	return urldecode( basename( str_replace( array( '%2F', '%5C' ), '/', urlencode( $path ) ), $suffix ) );
}

/**
 * i18n friendly version of dirname()
 *
 * @param  string $path File path
 * @return string
 */
function qnap_dirname( $path ) {
	return urldecode( dirname( str_replace( array( '%2F', '%5C' ), '/', urlencode( $path ) ) ) );
}

/**
 * Replace forward slash with current directory separator
 *
 * @param  string $path Path
 * @return string
 */
function qnap_replace_forward_slash_with_directory_separator( $path ) {
	return str_replace( '/', DIRECTORY_SEPARATOR, $path );
}

/**
 * Replace current directory separator with forward slash
 *
 * @param  string $path Path
 * @return string
 */
function qnap_replace_directory_separator_with_forward_slash( $path ) {
	return str_replace( DIRECTORY_SEPARATOR, '/', $path );
}

/**
 * Escape Windows directory separator
 *
 * @param  string $path Path
 * @return string
 */
function qnap_escape_windows_directory_separator( $path ) {
	return preg_replace( '/[\\\\]+/', '\\\\\\\\', $path );
}

/**
 * Should reset WordPress permalinks?
 *
 * @param  array   $params Request parameters
 * @return boolean
 */
function qnap_should_reset_permalinks( $params ) {
	global $wp_rewrite, $is_apache;

	// Permalinks are not supported
	if ( empty( $params['using_permalinks'] ) ) {
		if ( $wp_rewrite->using_permalinks() ) {
			if ( $is_apache ) {
				if ( ! apache_mod_loaded( 'mod_rewrite', false ) ) {
					return true;
				}
			}
		}
	}

	return false;
}

/**
 * Get .htaccess file content
 *
 * @return string
 */
function qnap_get_htaccess() {
	if ( is_file( QNAP_WORDPRESS_HTACCESS ) ) {
		return @file_get_contents( QNAP_WORDPRESS_HTACCESS );
	}
}

/**
 * Get web.config file content
 *
 * @return string
 */
function qnap_get_webconfig() {
	if ( is_file( QNAP_WORDPRESS_WEBCONFIG ) ) {
		return @file_get_contents( QNAP_WORDPRESS_WEBCONFIG );
	}
}

/**
 * Get QNAP log client IP & NAS Name
 *
 * @param  array   $params Request parameters
 * @return string
 */
function qnap_get_log_client( $params ) {
	$ret = sanitize_text_field( $_SERVER['REMOTE_ADDR'] );
	if ( isset( $params['nas_name'] ) ) {
		$ret .= ' (' . $params['nas_name'] . ')';
	}
	return $ret;
}

/**
 * Get sanitized params
 *
 * @return array
 */
function qnap_sanitized_params() {
	$params = array();

	if (isset($_REQUEST['completed'])) {
		if ($_REQUEST['completed']) {
			$params['completed'] = true;
		} else {
			$params['completed'] = false;
		}
	}

	$param_keys = array('priority', 'qnap_manual_export', 'file', "action", "qnap_import", "qnap_manual_import");
	foreach ($param_keys as $v) {
		if (!isset($_REQUEST[$v])) {
			continue;
		}
		$params[$v] = sanitize_key($_REQUEST[$v]);
	}

	$param_filenames = array('storage', 'archive');
	foreach ($param_filenames as $v) {
		if (!isset($_REQUEST[$v])) {
			continue;
		}
		$params[$v] = sanitize_file_name($_REQUEST[$v]);
	}

	$param_texts = array('secret_key', 'nas_name', 'query_offset', 'total_queries_size', 'table_index', 'table_offset', 'table_rows', 'total_tables_count', 'archive_bytes_offset', 'database_bytes_offset', 'total_database_size', 'package_bytes_offset', 'total_package_size', 'content_bytes_offset', 'processed_files_size', 'total_content_files_s    ize', 'total_content_files_count', 'total_media_files_count', 'total_media_files_size', 'file_bytes_offset', 'media_bytes_offset');
	foreach ($param_texts as $v) {
		if (!isset($_REQUEST[$v])) {
			continue;
		}
		$params[$v] = sanitize_text_field($_REQUEST[$v]);
	}

	if (isset($_REQUEST['options'])) {
		$params['options'] = array();
		$param_options = array('no_spam_comments', 'no_post_revisions', 'no_media', 'no_themes', 'no_inactive_themes', 'no_muplugins', 'no_plugins', 'no_inactive_plugins', 'no_cache', 'no_email_replace', 'no_database');
		foreach ($param_options as $v) {
			if (!isset($_REQUEST['options'][$v])) {
				continue;
			}
			$params['options'][$v] = sanitize_text_field($_REQUEST['options'][$v]);
		}

		if (isset($_REQUEST['options']['replace'])) {
			$params['options']['replace']['new_value'] = array();
			foreach ($_REQUEST['options']['replace']['new_value'] as $v) {
				array_push($params['options']['replace']['new_value'], sanitize_text_field($v));
			}
			$params['options']['replace']['old_value'] = array();
			foreach ($_REQUEST['options']['replace']['old_value'] as $v) {
				array_push($params['options']['replace']['old_value'], sanitize_text_field($v));
			}
		}
	}

	return $params;
}