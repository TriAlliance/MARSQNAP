<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Main_Controller {

	/**
	 * Main Application Controller
	 *
	 * @return QNAP_Main_Controller
	 */
	public function __construct() {
		register_activation_hook( QNAP_PLUGIN_BASENAME, array( $this, 'activation_hook' ) );

		// Activate hooks
		$this->activate_actions();
		$this->activate_filters();
	}

	/**
	 * Activation hook callback
	 *
	 * @return void
	 */
	public function activation_hook() {
		if ( extension_loaded( 'litespeed' ) ) {
			$this->create_litespeed_htaccess( QNAP_WORDPRESS_HTACCESS );
		}

		$this->setup_backups_folder();
		$this->setup_storage_folder();
		$this->setup_secret_key();
	}

	/**
	 * Initializes language domain for the plugin
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( QNAP_PLUGIN_NAME, false, false );
	}

	/**
	 * Register listeners for actions
	 *
	 * @return void
	 */
	private function activate_actions() {
		// Init
		add_action( 'admin_init', array( $this, 'init' ) );

		// Router
		add_action( 'admin_init', array( $this, 'router' ) );

		// Enable WP importing
		add_action( 'admin_init', array( $this, 'wp_importing' ), 5 );

		// Setup backups folder
		add_action( 'admin_init', array( $this, 'setup_backups_folder' ) );

		// Setup storage folder
		add_action( 'admin_init', array( $this, 'setup_storage_folder' ) );

		// Setup secret key
		add_action( 'admin_init', array( $this, 'setup_secret_key' ) );

		// Check user role capability
		add_action( 'admin_init', array( $this, 'check_user_role_capability' ) );

		// Schedule crons
		add_action( 'admin_init', array( $this, 'schedule_crons' ) );

		// Load text domain
		add_action( 'admin_init', array( $this, 'load_textdomain' ) );

		// Admin header
		add_action( 'admin_head', array( $this, 'admin_head' ) );

		// QNAP WP Migration
		add_action( 'plugins_loaded', array( $this, 'qnap_loaded' ), 10 );

		// Export and import commands
		add_action( 'plugins_loaded', array( $this, 'qnap_commands' ), 10 );

		// Export and import buttons
		add_action( 'plugins_loaded', array( $this, 'qnap_buttons' ), 10 );

		// Register scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts_and_styles' ), 5 );

		// Enqueue export scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_export_scripts_and_styles' ), 5 );

		// Enqueue import scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_import_scripts_and_styles' ), 5 );

		// Enqueue backups scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backups_scripts_and_styles' ), 5 );

		// Enqueue backups scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_qnap_backups_scripts_and_styles' ), 5 );
	}

	/**
	 * Register listeners for filters
	 *
	 * @return void
	 */
	private function activate_filters() {
		// Add links to plugin list page
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

		// Add custom schedules
		add_filter( 'cron_schedules', array( $this, 'add_cron_schedules' ), 9999 );
	}

	/**
	 * Export and import commands
	 *
	 * @return void
	 */
	public function qnap_commands() {
		// Add export commands
		add_filter( 'qnap_export', array( 'qnap\QNAP_Export_Init', 'execute' ), 5 );
		add_filter( 'qnap_export', array( 'qnap\QNAP_Export_Compatibility', 'execute' ), 5 );
		add_filter( 'qnap_export', array( 'qnap\QNAP_Export_Archive', 'execute' ), 10 );
		add_filter( 'qnap_export', array( 'qnap\QNAP_Export_Config', 'execute' ), 50 );
		add_filter( 'qnap_export', array( 'qnap\QNAP_Export_Config_File', 'execute' ), 60 );
		add_filter( 'qnap_export', array( 'qnap\QNAP_Export_Enumerate_Content', 'execute' ), 100 );
		add_filter( 'qnap_export', array( 'qnap\QNAP_Export_Enumerate_Media', 'execute' ), 110 );
		add_filter( 'qnap_export', array( 'qnap\QNAP_Export_Enumerate_Tables', 'execute' ), 120 );
		add_filter( 'qnap_export', array( 'qnap\QNAP_Export_Content', 'execute' ), 150 );
		add_filter( 'qnap_export', array( 'qnap\QNAP_Export_Media', 'execute' ), 160 );
		add_filter( 'qnap_export', array( 'qnap\QNAP_Export_Database', 'execute' ), 200 );
		add_filter( 'qnap_export', array( 'qnap\QNAP_Export_Database_File', 'execute' ), 220 );
		add_filter( 'qnap_export', array( 'qnap\QNAP_Export_Download', 'execute' ), 250 );
		add_filter( 'qnap_export', array( 'qnap\QNAP_Export_Clean', 'execute' ), 300 );

		// Add import commands
		add_filter( 'qnap_import', array( 'qnap\QNAP_Import_Upload', 'execute' ), 5 );
		add_filter( 'qnap_import', array( 'qnap\QNAP_Import_Compatibility', 'execute' ), 10 );
		add_filter( 'qnap_import', array( 'qnap\QNAP_Import_Validate', 'execute' ), 50 );
		add_filter( 'qnap_import', array( 'qnap\QNAP_Import_Confirm', 'execute' ), 100 );
		add_filter( 'qnap_import', array( 'qnap\QNAP_Import_Blogs', 'execute' ), 150 );
		add_filter( 'qnap_import', array( 'qnap\QNAP_Import_Permalinks', 'execute' ), 170 );
		add_filter( 'qnap_import', array( 'qnap\QNAP_Import_Enumerate', 'execute' ), 200 );
		add_filter( 'qnap_import', array( 'qnap\QNAP_Import_Content', 'execute' ), 250 );
		add_filter( 'qnap_import', array( 'qnap\QNAP_Import_Mu_Plugins', 'execute' ), 270 );
		add_filter( 'qnap_import', array( 'qnap\QNAP_Import_Database', 'execute' ), 300 );
		add_filter( 'qnap_import', array( 'qnap\QNAP_Import_Plugins', 'execute' ), 340 );
		add_filter( 'qnap_import', array( 'qnap\QNAP_Import_Done', 'execute' ), 350 );
		add_filter( 'qnap_import', array( 'qnap\QNAP_Import_Clean', 'execute' ), 400 );
	}

	/**
	 * Export and import buttons
	 *
	 * @return void
	 */
	public function qnap_buttons() {
		add_filter( 'qnap_export_buttons', array( 'qnap\QNAP_Export_Controller', 'buttons' ));
		add_filter( 'qnap_import_buttons', array( 'qnap\QNAP_Import_Controller', 'buttons' ));
		add_filter( 'qnap_pro', array( 'qnap\QNAP_Import_Controller', 'pro'), 10 );
	}

	/**
	 * QNAP WP Migration loaded
	 *
	 * @return void
	 */
	public function qnap_loaded() {
		if ( ! defined( 'QNAPME_PLUGIN_NAME' ) ) {
			if ( is_multisite() ) {
				add_action( 'network_admin_notices', array( $this, 'multisite_notice' ) );
			} else {
				add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			}
		} else {
			if ( is_multisite() ) {
				add_action( 'network_admin_menu', array( $this, 'admin_menu' ) );
			} else {
				add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			}
		}

		// Add HTTP export headers
		add_filter( 'qnap_http_export_headers', array( 'qnap\QNAP_Export_Controller', 'http_export_headers' ));

		// Add HTTP import headers
		add_filter( 'qnap_http_import_headers', array( 'qnap\QNAP_Import_Controller', 'http_import_headers' ));

		// Add chunk size limit
		add_filter( 'qnap_max_chunk_size', array( 'qnap\QNAP_Import_Controller', 'max_chunk_size' ));

		// Add storage folder daily cleanup cron
		add_action( 'qnap_storage_cleanup', array( 'qnap\QNAP_Export_Controller', 'cleanup' ));
	}

	/**
	 * Create backups folder with index.php, index.html, .htaccess and web.config files
	 *
	 * @return void
	 */
	public function setup_backups_folder() {
		$this->create_backups_folder( QNAP_BACKUPS_PATH );
		$this->create_backups_htaccess( QNAP_BACKUPS_HTACCESS );
		$this->create_backups_webconfig( QNAP_BACKUPS_WEBCONFIG );
		$this->create_backups_index_php( QNAP_BACKUPS_INDEX_PHP );
		$this->create_backups_index_html( QNAP_BACKUPS_INDEX_HTML );
	}

	/**
	 * Create storage folder with index.php and index.html files
	 *
	 * @return void
	 */
	public function setup_storage_folder() {
		$this->create_storage_folder( QNAP_STORAGE_PATH );
		$this->create_storage_index_php( QNAP_STORAGE_INDEX_PHP );
		$this->create_storage_index_html( QNAP_STORAGE_INDEX_HTML );
	}

	/**
	 * Create secret key if they don't exist yet
	 *
	 * @return void
	 */
	public function setup_secret_key() {
		if ( ! get_option( QNAP_SECRET_KEY ) ) {
			update_option( QNAP_SECRET_KEY, qnap_generate_random_string( 64 ) );
		}
	}

	/**
	 * Check user role capability
	 *
	 * @return void
	 */
	public function check_user_role_capability() {
		if ( ( $user = wp_get_current_user() ) && in_array( 'administrator', $user->roles ) ) {
			if ( ! $user->has_cap( 'export' ) || ! $user->has_cap( 'import' ) ) {
				if ( is_multisite() ) {
					return add_action( 'network_admin_notices', array( $this, 'missing_role_capability_notice' ) );
				} else {
					return add_action( 'admin_notices', array( $this, 'missing_role_capability_notice' ) );
				}
			}
		}
	}

	/**
	 * Schedule cron tasks for plugin operation, if not done yet
	 *
	 * @return void
	 */
	public function schedule_crons() {
		if ( ! QNAP_Cron::exists( 'qnap_storage_cleanup' ) ) {
			QNAP_Cron::add( 'qnap_storage_cleanup', 'daily', time() );
		}

		QNAP_Cron::clear( 'qnap_cleanup_cron' );
	}

	/**
	 * Create storage folder
	 *
	 * @param  string Path to folder
	 * @return void
	 */
	public function create_storage_folder( $path ) {
		if ( ! QNAP_Directory::create( $path ) ) {
			if ( is_multisite() ) {
				return add_action( 'network_admin_notices', array( $this, 'storage_path_notice' ) );
			} else {
				return add_action( 'admin_notices', array( $this, 'storage_path_notice' ) );
			}
		}
	}

	/**
	 * Create backups folder
	 *
	 * @param  string Path to folder
	 * @return void
	 */
	public function create_backups_folder( $path ) {
		if ( ! QNAP_Directory::create( $path ) ) {
			if ( is_multisite() ) {
				return add_action( 'network_admin_notices', array( $this, 'backups_path_notice' ) );
			} else {
				return add_action( 'admin_notices', array( $this, 'backups_path_notice' ) );
			}
		}
	}

	/**
	 * Create storage index.php file
	 *
	 * @param  string Path to file
	 * @return void
	 */
	public function create_storage_index_php( $path ) {
		if ( ! QNAP_File_Index::create( $path ) ) {
			if ( is_multisite() ) {
				return add_action( 'network_admin_notices', array( $this, 'storage_index_php_notice' ) );
			} else {
				return add_action( 'admin_notices', array( $this, 'storage_index_php_notice' ) );
			}
		}
	}

	/**
	 * Create storage index.html file
	 *
	 * @param  string Path to file
	 * @return void
	 */
	public function create_storage_index_html( $path ) {
		if ( ! QNAP_File_Index::create( $path ) ) {
			if ( is_multisite() ) {
				return add_action( 'network_admin_notices', array( $this, 'storage_index_html_notice' ) );
			} else {
				return add_action( 'admin_notices', array( $this, 'storage_index_html_notice' ) );
			}
		}
	}

	/**
	 * Create backups .htaccess file
	 *
	 * @param  string Path to file
	 * @return void
	 */
	public function create_backups_htaccess( $path ) {
		if ( ! QNAP_File_Htaccess::create( $path ) ) {
			if ( is_multisite() ) {
				return add_action( 'network_admin_notices', array( $this, 'backups_htaccess_notice' ) );
			} else {
				return add_action( 'admin_notices', array( $this, 'backups_htaccess_notice' ) );
			}
		}
	}

	/**
	 * Create backups web.config file
	 *
	 * @param  string Path to file
	 * @return void
	 */
	public function create_backups_webconfig( $path ) {
		if ( ! QNAP_File_Webconfig::create( $path ) ) {
			if ( is_multisite() ) {
				return add_action( 'network_admin_notices', array( $this, 'backups_webconfig_notice' ) );
			} else {
				return add_action( 'admin_notices', array( $this, 'backups_webconfig_notice' ) );
			}
		}
	}

	/**
	 * Create backups index.php file
	 *
	 * @param  string Path to file
	 * @return void
	 */
	public function create_backups_index_php( $path ) {
		if ( ! QNAP_File_Index::create( $path ) ) {
			if ( is_multisite() ) {
				return add_action( 'network_admin_notices', array( $this, 'backups_index_php_notice' ) );
			} else {
				return add_action( 'admin_notices', array( $this, 'backups_index_php_notice' ) );
			}
		}
	}

	/**
	 * Create backups index.html file
	 *
	 * @param  string Path to file
	 * @return void
	 */
	public function create_backups_index_html( $path ) {
		if ( ! QNAP_File_Index::create( $path ) ) {
			if ( is_multisite() ) {
				return add_action( 'network_admin_notices', array( $this, 'backups_index_html_notice' ) );
			} else {
				return add_action( 'admin_notices', array( $this, 'backups_index_html_notice' ) );
			}
		}
	}

	/**
	 * If the "noabort" environment variable has been set,
	 * the script will continue to run even though the connection has been broken
	 *
	 * @return void
	 */
	public function create_litespeed_htaccess( $path ) {
		if ( ! QNAP_File_Htaccess::litespeed( $path ) ) {
			if ( is_multisite() ) {
				return add_action( 'network_admin_notices', array( $this, 'wordpress_htaccess_notice' ) );
			} else {
				return add_action( 'admin_notices', array( $this, 'wordpress_htaccess_notice' ) );
			}
		}
	}

	/**
	 * Display multisite notice
	 *
	 * @return void
	 */
	public function multisite_notice() {
		QNAP_Template::render( 'main/multisite-notice' );
	}

	/**
	 * Display notice for storage directory
	 *
	 * @return void
	 */
	public function storage_path_notice() {
		QNAP_Template::render( 'main/storage-path-notice' );
	}

	/**
	 * Display notice for index.php file in storage directory
	 *
	 * @return void
	 */
	public function storage_index_php_notice() {
		QNAP_Template::render( 'main/storage-index-php-notice' );
	}

	/**
	 * Display notice for index.html file in storage directory
	 *
	 * @return void
	 */
	public function storage_index_html_notice() {
		QNAP_Template::render( 'main/storage-index-html-notice' );
	}

	/**
	 * Display notice for backups directory
	 *
	 * @return void
	 */
	public function backups_path_notice() {
		QNAP_Template::render( 'main/backups-path-notice' );
	}

	/**
	 * Display notice for .htaccess file in backups directory
	 *
	 * @return void
	 */
	public function backups_htaccess_notice() {
		QNAP_Template::render( 'main/backups-htaccess-notice' );
	}

	/**
	 * Display notice for web.config file in backups directory
	 *
	 * @return void
	 */
	public function backups_webconfig_notice() {
		QNAP_Template::render( 'main/backups-webconfig-notice' );
	}

	/**
	 * Display notice for index.php file in backups directory
	 *
	 * @return void
	 */
	public function backups_index_php_notice() {
		QNAP_Template::render( 'main/backups-index-php-notice' );
	}

	/**
	 * Display notice for index.html file in backups directory
	 *
	 * @return void
	 */
	public function backups_index_html_notice() {
		QNAP_Template::render( 'main/backups-index-html-notice' );
	}

	/**
	 * Display notice for .htaccess file in WordPress directory
	 *
	 * @return void
	 */
	public function wordpress_htaccess_notice() {
		QNAP_Template::render( 'main/wordpress-htaccess-notice' );
	}

	/**
	 * Display notice for missing role capability
	 *
	 * @return void
	 */
	public function missing_role_capability_notice() {
		QNAP_Template::render( 'main/missing-role-capability-notice' );
	}

	/**
	 * Add links to plugin list page
	 *
	 * @return array
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( $file === QNAP_PLUGIN_BASENAME ) {
			$links[] = QNAP_Template::get_content( 'main/get-support' );
			$links[] = QNAP_Template::get_content( 'main/translate' );
		}

		return $links;
	}

	/**
	 * Register plugin menus
	 *
	 * @return void
	 */
	public function admin_menu() {
		// Top-level WP Migration menu
		add_menu_page(
			'QNAP1',
			'QNAP Backup',
			'export',
			'qnap_export',
			'qnap\QNAP_Backups_Controller::index',
			'',
			'76.295'
		);

		// // Sub-level Export menu
		// add_submenu_page(
		// 	'qnap_export',
		// 	__( 'Export', QNAP_PLUGIN_NAME ),
		// 	__( 'Export', QNAP_PLUGIN_NAME ),
		// 	'export',
		// 	'qnap_export',
		// 	'qnap\Qnap_Backups_Controller::index'
		// );

		// Sub-level Export menu
		add_submenu_page(
			'qnap_export',
			__( 'Export', QNAP_PLUGIN_NAME ),
			__( 'Export', QNAP_PLUGIN_NAME ),
			'export',
			'qnap_export',
			'qnap\QNAP_Backups_Controller::index'
		);

		// // Sub-level Import menu
		// add_submenu_page(
		// 	'qnap_export',
		// 	__( 'Import', QNAP_PLUGIN_NAME ),
		// 	__( 'Import', QNAP_PLUGIN_NAME ),
		// 	'import',
		// 	'qnap_import',
		// 	'qnap\QNAP_Import_Controller::index'
		// );
	}

	/**
	 * Register scripts and styles
	 *
	 * @return void
	 */
	public function register_scripts_and_styles() {
		if ( is_rtl() ) {
			wp_register_style(
				'qnap_qeek',
				QNAP_Template::asset_link( 'css/qeek.min.rtl.css' )
			);
		} else {
			wp_register_style(
				'qnap_qeek',
				QNAP_Template::asset_link( 'css/qeek.min.css' )
			);
		}

		wp_register_script(
			'qnap_util',
			QNAP_Template::asset_link( 'javascript/util.min.js' ),
			array( 'jquery' )
		);

		wp_register_script(
			'qnap_settings',
			QNAP_Template::asset_link( 'javascript/settings.min.js' ),
			array( 'qnap_util' )
		);

		wp_localize_script(
			'qnap_settings',
			'qnap_locale',
			array(
				'leave_feedback'                      => __( 'Leave plugin developers any feedback here', QNAP_PLUGIN_NAME ),
				'how_may_we_help_you'                 => __( 'How may we help you?', QNAP_PLUGIN_NAME ),
				'thanks_for_submitting_your_feedback' => __( 'Thanks for submitting your feedback!', QNAP_PLUGIN_NAME ),
				'thanks_for_submitting_your_request'  => __( 'Thanks for submitting your request!', QNAP_PLUGIN_NAME ),
			)
		);
	}

	/**
	 * Enqueue scripts and styles for Export Controller
	 *
	 * @param  string $hook Hook suffix
	 * @return void
	 */
	public function enqueue_export_scripts_and_styles( $hook ) {
		if ( stripos( 'toplevel_page_qnap_export', $hook ) === false ) {
			return;
		}

		// We don't want heartbeat to occur when exporting
		wp_deregister_script( 'heartbeat' );

		// We don't want auth check for monitoring whether the user is still logged in
		remove_action( 'admin_enqueue_scripts', 'wp_auth_check_load' );

		if ( is_rtl() ) {
			wp_enqueue_style(
				'qnap_export',
				QNAP_Template::asset_link( 'css/export.min.rtl.css' )
			);
		} else {

			wp_enqueue_style(
				'qnap_export',
				QNAP_Template::asset_link( 'css/export.min.css' )
			);
		}

		wp_enqueue_script(
			'qnap_export',
			QNAP_Template::asset_link( 'javascript/export.min.js' ),
			array( 'qnap_util' )
		);

		wp_localize_script(
			'qnap_export',
			'qnap_export',
			array(
				'ajax'       => array(
					'url' => wp_make_link_relative( add_query_arg( array( 'qnap_import' => 1 ), admin_url( 'admin-ajax.php?action=qnap_export' ) ) ),
				),
				'status'     => array(
					'url' => wp_make_link_relative( add_query_arg( array( 'qnap_import' => 1, 'secret_key' => get_option( QNAP_SECRET_KEY ) ), admin_url( 'admin-ajax.php?action=qnap_status' ) ) ),
				),
				'secret_key' => get_option( QNAP_SECRET_KEY ),
			)
		);

		wp_localize_script(
			'qnap_export',
			'qnap_locale',
			array(
				'stop_exporting_your_website'         => __( 'You are about to stop exporting your website, are you sure?', QNAP_PLUGIN_NAME ),
				'preparing_to_export'                 => __( 'Preparing to export...', QNAP_PLUGIN_NAME ),
				'unable_to_export'                    => __( 'Unable to export', QNAP_PLUGIN_NAME ),
				'unable_to_start_the_export'          => __( 'Unable to start the export. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'unable_to_run_the_export'            => __( 'Unable to run the export. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'unable_to_stop_the_export'           => __( 'Unable to stop the export. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'please_wait_stopping_the_export'     => __( 'Please wait, stopping the export...', QNAP_PLUGIN_NAME ),
				'close_export'                        => __( 'Close', QNAP_PLUGIN_NAME ),
				'stop_export'                         => __( 'Stop export', QNAP_PLUGIN_NAME ),
				'leave_feedback'                      => __( 'Leave plugin developers any feedback here', QNAP_PLUGIN_NAME ),
				'how_may_we_help_you'                 => __( 'How may we help you?', QNAP_PLUGIN_NAME ),
				'thanks_for_submitting_your_feedback' => __( 'Thanks for submitting your feedback!', QNAP_PLUGIN_NAME ),
				'thanks_for_submitting_your_request'  => __( 'Thanks for submitting your request!', QNAP_PLUGIN_NAME ),
			)
		);
	}

	/**
	 * Enqueue scripts and styles for Import Controller
	 *
	 * @param  string $hook Hook suffix
	 * @return void
	 */
	public function enqueue_import_scripts_and_styles( $hook ) {
		if ( stripos( 'qnap-backup_page_qnap_import', $hook ) === false ) {
			return;
		}

		// We don't want heartbeat to occur when importing
		wp_deregister_script( 'heartbeat' );

		// We don't want auth check for monitoring whether the user is still logged in
		remove_action( 'admin_enqueue_scripts', 'wp_auth_check_load' );

		if ( is_rtl() ) {
			wp_enqueue_style(
				'qnap_import',
				QNAP_Template::asset_link( 'css/import.min.rtl.css' )
			);
		} else {
			wp_enqueue_style(
				'qnap_import',
				QNAP_Template::asset_link( 'css/import.min.css' )
			);
		}

		wp_enqueue_script(
			'qnap_import',
			QNAP_Template::asset_link( 'javascript/import.min.js' ),
			array( 'qnap_util' )
		);

		wp_localize_script(
			'qnap_import',
			'qnap_uploader',
			array(
				'max_file_size' => wp_max_upload_size(),
				'url'           => wp_make_link_relative( add_query_arg( array( 'qnap_import' => 1 ), admin_url( 'admin-ajax.php?action=qnap_import' ) ) ),
				'params'        => array(
					'priority'   => 5,
					'secret_key' => get_option( QNAP_SECRET_KEY ),
				),
			)
		);

		wp_localize_script(
			'qnap_import',
			'qnap_import',
			array(
				'ajax'       => array(
					'url' => wp_make_link_relative( add_query_arg( array( 'qnap_import' => 1 ), admin_url( 'admin-ajax.php?action=qnap_import' ) ) ),
				),
				'status'     => array(
					'url' => wp_make_link_relative( add_query_arg( array( 'qnap_import' => 1, 'secret_key' => get_option( QNAP_SECRET_KEY ) ), admin_url( 'admin-ajax.php?action=qnap_status' ) ) ),
				),
				'secret_key' => get_option( QNAP_SECRET_KEY ),
			)
		);

		wp_localize_script(
			'qnap_import',
			'qnap_compatibility',
			array(
				'messages' => QNAP_Compatibility::get( array() ),
			)
		);

		wp_localize_script(
			'qnap_import',
			'qnap_disk_space',
			array(
				'free'   => @disk_free_space( QNAP_STORAGE_PATH ),
				'factor' => QNAP_DISK_SPACE_FACTOR,
				'extra'  => QNAP_DISK_SPACE_EXTRA,
			)
		);

		wp_localize_script(
			'qnap_import',
			'qnap_locale',
			array(
				'stop_importing_your_website'         => __( 'You are about to stop importing your website, are you sure?', QNAP_PLUGIN_NAME ),
				'preparing_to_import'                 => __( 'Preparing to import...', QNAP_PLUGIN_NAME ),
				'unable_to_import'                    => __( 'Unable to import', QNAP_PLUGIN_NAME ),
				'unable_to_start_the_import'          => __( 'Unable to start the import. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'unable_to_confirm_the_import'        => __( 'Unable to confirm the import. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'unable_to_prepare_blogs_on_import'   => __( 'Unable to prepare blogs on import. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'unable_to_stop_the_import'           => __( 'Unable to stop the import. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'please_wait_stopping_the_import'     => __( 'Please wait, stopping the import...', QNAP_PLUGIN_NAME ),
				'close_import'                        => __( 'Close', QNAP_PLUGIN_NAME ),
				'finish_import'                       => __( 'Finish', QNAP_PLUGIN_NAME ),
				'stop_import'                         => __( 'Stop import', QNAP_PLUGIN_NAME ),
				'confirm_import'                      => __( 'Proceed', QNAP_PLUGIN_NAME ),
				'confirm_disk_space'                  => __( 'I have enough disk space', QNAP_PLUGIN_NAME ),
				'continue_import'                     => __( 'Continue', QNAP_PLUGIN_NAME ),
				'please_do_not_close_this_browser'    => __( 'Please do not close this browser window or your import will fail', QNAP_PLUGIN_NAME ),
				'leave_feedback'                      => __( 'Leave plugin developers any feedback here', QNAP_PLUGIN_NAME ),
				'how_may_we_help_you'                 => __( 'How may we help you?', QNAP_PLUGIN_NAME ),
				'thanks_for_submitting_your_feedback' => __( 'Thanks for submitting your feedback!', QNAP_PLUGIN_NAME ),
				'thanks_for_submitting_your_request'  => __( 'Thanks for submitting your request!', QNAP_PLUGIN_NAME ),
				'import_from_file'                    => sprintf(
					__(
						'Your file exceeds the maximum upload size for this site: <strong>%s</strong><br />%s%s',
						QNAP_PLUGIN_NAME
					),
					esc_html( qnap_size_format( wp_max_upload_size() ) ),
					__(
						'<a href="https://help.qeek.com/2018/10/27/how-to-increase-maximum-upload-file-size-in-wordpress/" target="_blank">How-to: Increase maximum upload file size</a> or ',
						QNAP_PLUGIN_NAME
					),
					__(
						'<a href="https://import.wp-migration.com" target="_blank">Get unlimited</a>',
						QNAP_PLUGIN_NAME
					)
				),
				'invalid_archive_extension'           => __(
					'The file type that you have tried to upload is not compatible with this plugin. ' .
					'Please ensure that your file is a <strong>.qwp</strong> file that was created with the QNAP WP migration plugin. ' .
					'<a href="https://help.qeek.com/knowledgebase/invalid-backup-file/" target="_blank">Technical details</a>',
					QNAP_PLUGIN_NAME
				),
				'upgrade'                             => sprintf(
					__(
						'The file that you are trying to import is over the maximum upload file size limit of <strong>%s</strong>.<br />' .
						'You can remove this restriction by purchasing our ' .
						'<a href="https://qeek.com/products/unlimited-extension" target="_blank">Unlimited Extension</a>.',
						QNAP_PLUGIN_NAME
					),
					'512MB'
				),
				'out_of_disk_space'                   => __(
					'There is not enough space available on the disk.<br />' .
					'Free up %s of disk space.',
					QNAP_PLUGIN_NAME
				),
			)
		);
	}

	/**
	 * Enqueue scripts and styles for Backups Controller
	 *
	 * @param  string $hook Hook suffix
	 * @return void
	 */
	public function enqueue_backups_scripts_and_styles( $hook ) {
		if ( stripos( 'qnap-backup_page_qnap_backups', $hook ) === false ) {
			return;
		}
		// We don't want heartbeat to occur when restoring
		wp_deregister_script( 'heartbeat' );

		// We don't want auth check for monitoring whether the user is still logged in
		remove_action( 'admin_enqueue_scripts', 'wp_auth_check_load' );

		if ( is_rtl() ) {
			wp_enqueue_style(
				'qnap_backups',
				QNAP_Template::asset_link( 'css/backups.min.rtl.css' )
			);
		} else {
			wp_enqueue_style(
				'qnap_backups',
				QNAP_Template::asset_link( 'css/backups.min.css' )
			);
		}

		wp_enqueue_script(
			'qnap_backups',
			QNAP_Template::asset_link( 'javascript/backups.min.js' ),
			array( 'qnap_util' )
		);

		wp_localize_script(
			'qnap_backups',
			'qnap_import',
			array(
				'ajax'       => array(
					'url' => wp_make_link_relative( add_query_arg( array( 'qnap_import' => 1 ), admin_url( 'admin-ajax.php?action=qnap_import' ) ) ),
				),
				'status'     => array(
					'url' => wp_make_link_relative( add_query_arg( array( 'qnap_import' => 1, 'secret_key' => get_option( QNAP_SECRET_KEY ) ), admin_url( 'admin-ajax.php?action=qnap_status' ) ) ),
				),
				'secret_key' => get_option( QNAP_SECRET_KEY ),
			)
		);

		wp_localize_script(
			'qnap_backups',
			'qnap_export',
			array(
				'ajax'       => array(
					'url' => wp_make_link_relative( add_query_arg( array( 'qnap_import' => 1 ), admin_url( 'admin-ajax.php?action=qnap_export' ) ) ),
				),
				'status'     => array(
					'url' => wp_make_link_relative( add_query_arg( array( 'qnap_import' => 1, 'secret_key' => get_option( QNAP_SECRET_KEY ) ), admin_url( 'admin-ajax.php?action=qnap_status' ) ) ),
				),
				'secret_key' => get_option( QNAP_SECRET_KEY ),
			)
		);

		wp_localize_script(
			'qnap_backups',
			'qnap_backups',
			array(
				'ajax'       => array(
					'url' => wp_make_link_relative( admin_url( 'admin-ajax.php?action=qnap_backups' ) ),
				),
				'backups'    => array(
					'url' => wp_make_link_relative( admin_url( 'admin-ajax.php?action=qnap_backup_list' ) ),
				),
				'labels'     => array(
					'url' => wp_make_link_relative( admin_url( 'admin-ajax.php?action=qnap_add_backup_label' ) ),
				),
				'secret_key' => get_option( QNAP_SECRET_KEY ),
			)
		);

		wp_localize_script(
			'qnap_backups',
			'qnap_disk_space',
			array(
				'free'   => @disk_free_space( QNAP_STORAGE_PATH ),
				'factor' => QNAP_DISK_SPACE_FACTOR,
				'extra'  => QNAP_DISK_SPACE_EXTRA,
			)
		);

		wp_localize_script(
			'qnap_backups',
			'qnap_locale',
			array(
				'stop_exporting_your_website'         => __( 'You are about to stop exporting your website, are you sure?', QNAP_PLUGIN_NAME ),
				'preparing_to_export'                 => __( 'Preparing to export...', QNAP_PLUGIN_NAME ),
				'unable_to_export'                    => __( 'Unable to export', QNAP_PLUGIN_NAME ),
				'unable_to_start_the_export'          => __( 'Unable to start the export. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'unable_to_run_the_export'            => __( 'Unable to run the export. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'unable_to_stop_the_export'           => __( 'Unable to stop the export. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'please_wait_stopping_the_export'     => __( 'Please wait, stopping the export...', QNAP_PLUGIN_NAME ),
				'close_export'                        => __( 'Close', QNAP_PLUGIN_NAME ),
				'stop_export'                         => __( 'Stop export', QNAP_PLUGIN_NAME ),
				'stop_importing_your_website'         => __( 'You are about to stop importing your website, are you sure?', QNAP_PLUGIN_NAME ),
				'preparing_to_import'                 => __( 'Preparing to import...', QNAP_PLUGIN_NAME ),
				'unable_to_import'                    => __( 'Unable to import', QNAP_PLUGIN_NAME ),
				'unable_to_start_the_import'          => __( 'Unable to start the import. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'unable_to_confirm_the_import'        => __( 'Unable to confirm the import. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'unable_to_prepare_blogs_on_import'   => __( 'Unable to prepare blogs on import. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'unable_to_stop_the_import'           => __( 'Unable to stop the import. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'please_wait_stopping_the_import'     => __( 'Please wait, stopping the import...', QNAP_PLUGIN_NAME ),
				'finish_import'                       => __( 'Finish', QNAP_PLUGIN_NAME ),
				'close_import'                        => __( 'Close', QNAP_PLUGIN_NAME ),
				'stop_import'                         => __( 'Stop import', QNAP_PLUGIN_NAME ),
				'confirm_import'                      => __( 'Proceed', QNAP_PLUGIN_NAME ),
				'confirm_disk_space'                  => __( 'I have enough disk space', QNAP_PLUGIN_NAME ),
				'continue_import'                     => __( 'Continue', QNAP_PLUGIN_NAME ),
				'please_do_not_close_this_browser'    => __( 'Please do not close this browser window or your import will fail', QNAP_PLUGIN_NAME ),
				'leave_feedback'                      => __( 'Leave plugin developers any feedback here', QNAP_PLUGIN_NAME ),
				'how_may_we_help_you'                 => __( 'How may we help you?', QNAP_PLUGIN_NAME ),
				'thanks_for_submitting_your_feedback' => __( 'Thanks for submitting your feedback!', QNAP_PLUGIN_NAME ),
				'thanks_for_submitting_your_request'  => __( 'Thanks for submitting your request!', QNAP_PLUGIN_NAME ),
				'want_to_delete_this_file'            => __( 'Are you sure you want to delete this file?', QNAP_PLUGIN_NAME ),
				'unlimited'                           => __( 'Restoring a backup is available via Unlimited extension. <a href="https://qeek.com/products/unlimited-extension" target="_blank">Get it here</a>', QNAP_PLUGIN_NAME ),
				'restore_from_file'                   => __( '"Restore" functionality is available in a <a href="https://qeek.com/products/unlimited-extension" target="_blank">paid extension</a>.<br />You could also download the backup and then use "Import from file".', QNAP_PLUGIN_NAME ),
				'out_of_disk_space'                   => __(
					'There is not enough space available on the disk.<br />' .
					'Free up %s of disk space.',
					QNAP_PLUGIN_NAME
				),
			)
		);
	}

	/**
	 * Enqueue scripts and styles for Backups Controller
	 *
	 * @param  string $hook Hook suffix
	 * @return void
	 */
	public function enqueue_qnap_backups_scripts_and_styles( $hook ) {
		if ( stripos( 'toplevel_page_qnap_export', $hook ) === false ) {
			return;
		}
		// We don't want heartbeat to occur when restoring
		wp_deregister_script( 'heartbeat' );

		// We don't want auth check for monitoring whether the user is still logged in
		remove_action( 'admin_enqueue_scripts', 'wp_auth_check_load' );

//		if ( is_rtl() ) {
//			wp_enqueue_style(
//				'qnap_backups',
//				QNAP_Template::asset_link( 'css/backups.min.rtl.css' )
//			);
//		} else {
//			wp_enqueue_style(
//				'qnap_backups',
//				QNAP_Template::asset_link( 'css/backups.min.css' )
//			);
//		}
//
//		wp_enqueue_script(
//			'qnap_backups',
//			QNAP_Template::asset_link( 'javascript/backups.min.js' ),
//			array( 'qnap_util' )
//		);

		wp_enqueue_script(
			'qnap_backups',
			QNAP_Template::asset_link( 'javascript/qnap.js' )
		);

		wp_localize_script(
			'qnap_backups',
			'qnap_backups',
			array(
				'ajax'       => array(
					'url' => wp_make_link_relative( add_query_arg( array( 'delete_log' => 1 ), admin_url( 'admin-ajax.php?action=qnap_delete_log' ) ) ),
				),
				'secret_key' => get_option( QNAP_SECRET_KEY ),
			)
		);

		wp_localize_script(
			'qnap_backups',
			'qnap_import',
			array(
				'ajax'       => array(
					'url' => wp_make_link_relative( add_query_arg( array( 'qnap_import' => 1 ), admin_url( 'admin-ajax.php?action=qnap_import' ) ) ),
				),
				'status'     => array(
					'url' => wp_make_link_relative( add_query_arg( array( 'qnap_import' => 1, 'secret_key' => get_option( QNAP_SECRET_KEY ) ), admin_url( 'admin-ajax.php?action=qnap_status' ) ) ),
				),
				'secret_key' => get_option( QNAP_SECRET_KEY ),
			)
		);

		wp_localize_script(
			'qnap_backups',
			'qnap_export',
			array(
				'ajax'       => array(
					'url' => wp_make_link_relative( add_query_arg( array( 'qnap_import' => 1 ), admin_url( 'admin-ajax.php?action=qnap_export' ) ) ),
				),
				'status'     => array(
					'url' => wp_make_link_relative( add_query_arg( array( 'qnap_import' => 1, 'secret_key' => get_option( QNAP_SECRET_KEY ) ), admin_url( 'admin-ajax.php?action=qnap_status' ) ) ),
				),
				'secret_key' => get_option( QNAP_SECRET_KEY ),
			)
		);

		// wp_localize_script(
		// 	'qnap_backups',
		// 	'qnap_backups',
		// 	array(
		// 		'ajax'       => array(
		// 			'url' => wp_make_link_relative( admin_url( 'admin-ajax.php?action=qnap_backups' ) ),
		// 		),
		// 		'backups'    => array(
		// 			'url' => wp_make_link_relative( admin_url( 'admin-ajax.php?action=qnap_backup_list' ) ),
		// 		),
		// 		'labels'     => array(
		// 			'url' => wp_make_link_relative( admin_url( 'admin-ajax.php?action=qnap_add_backup_label' ) ),
		// 		),
		// 		'secret_key' => get_option( QNAP_SECRET_KEY ),
		// 	)
		// );

		wp_localize_script(
			'qnap_backups',
			'qnap_disk_space',
			array(
				'free'   => @disk_free_space( QNAP_STORAGE_PATH ),
				'factor' => QNAP_DISK_SPACE_FACTOR,
				'extra'  => QNAP_DISK_SPACE_EXTRA,
			)
		);

		wp_localize_script(
			'qnap_backups',
			'qnap_locale',
			array(
				'stop_exporting_your_website'         => __( 'You are about to stop exporting your website, are you sure?', QNAP_PLUGIN_NAME ),
				'preparing_to_export'                 => __( 'Preparing to export...', QNAP_PLUGIN_NAME ),
				'unable_to_export'                    => __( 'Unable to export', QNAP_PLUGIN_NAME ),
				'unable_to_start_the_export'          => __( 'Unable to start the export. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'unable_to_run_the_export'            => __( 'Unable to run the export. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'unable_to_stop_the_export'           => __( 'Unable to stop the export. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'please_wait_stopping_the_export'     => __( 'Please wait, stopping the export...', QNAP_PLUGIN_NAME ),
				'close_export'                        => __( 'Close', QNAP_PLUGIN_NAME ),
				'stop_export'                         => __( 'Stop export', QNAP_PLUGIN_NAME ),
				'stop_importing_your_website'         => __( 'You are about to stop importing your website, are you sure?', QNAP_PLUGIN_NAME ),
				'preparing_to_import'                 => __( 'Preparing to import...', QNAP_PLUGIN_NAME ),
				'unable_to_import'                    => __( 'Unable to import', QNAP_PLUGIN_NAME ),
				'unable_to_start_the_import'          => __( 'Unable to start the import. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'unable_to_confirm_the_import'        => __( 'Unable to confirm the import. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'unable_to_prepare_blogs_on_import'   => __( 'Unable to prepare blogs on import. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'unable_to_stop_the_import'           => __( 'Unable to stop the import. Refresh the page and try again', QNAP_PLUGIN_NAME ),
				'please_wait_stopping_the_import'     => __( 'Please wait, stopping the import...', QNAP_PLUGIN_NAME ),
				'finish_import'                       => __( 'Finish', QNAP_PLUGIN_NAME ),
				'close_import'                        => __( 'Close', QNAP_PLUGIN_NAME ),
				'stop_import'                         => __( 'Stop import', QNAP_PLUGIN_NAME ),
				'confirm_import'                      => __( 'Proceed', QNAP_PLUGIN_NAME ),
				'confirm_disk_space'                  => __( 'I have enough disk space', QNAP_PLUGIN_NAME ),
				'continue_import'                     => __( 'Continue', QNAP_PLUGIN_NAME ),
				'please_do_not_close_this_browser'    => __( 'Please do not close this browser window or your import will fail', QNAP_PLUGIN_NAME ),
				'leave_feedback'                      => __( 'Leave plugin developers any feedback here', QNAP_PLUGIN_NAME ),
				'how_may_we_help_you'                 => __( 'How may we help you?', QNAP_PLUGIN_NAME ),
				'thanks_for_submitting_your_feedback' => __( 'Thanks for submitting your feedback!', QNAP_PLUGIN_NAME ),
				'thanks_for_submitting_your_request'  => __( 'Thanks for submitting your request!', QNAP_PLUGIN_NAME ),
				'want_to_delete_this_file'            => __( 'Are you sure you want to delete this file?', QNAP_PLUGIN_NAME ),
				'unlimited'                           => __( 'Restoring a backup is available via Unlimited extension. <a href="https://qeek.com/products/unlimited-extension" target="_blank">Get it here</a>', QNAP_PLUGIN_NAME ),
				'restore_from_file'                   => __( '"Restore" functionality is available in a <a href="https://qeek.com/products/unlimited-extension" target="_blank">paid extension</a>.<br />You could also download the backup and then use "Import from file".', QNAP_PLUGIN_NAME ),
				'out_of_disk_space'                   => __(
					'There is not enough space available on the disk.<br />' .
					'Free up %s of disk space.',
					QNAP_PLUGIN_NAME
				),
			)
		);
	}

	/**
	 * Outputs menu icon between head tags
	 *
	 * @return void
	 */
	public function admin_head() {
		global $wp_version;

		// Admin header
		QNAP_Template::render( 'main/admin-head', array( 'version' => $wp_version ) );
	}

	/**
	 * Register initial parameters
	 *
	 * @return void
	 */
	public function init() {
	}

	/**
	 * Register initial router
	 *
	 * @return void
	 */
	public function router() {
		// Public actions
		add_action( 'wp_ajax_nopriv_qnap_export', array( 'qnap\QNAP_Export_Controller', 'export' ));
		add_action( 'wp_ajax_nopriv_qnap_import', array( 'qnap\QNAP_Import_Controller', 'import' ));
		add_action( 'wp_ajax_nopriv_qnap_status', array( 'qnap\QNAP_Status_Controller', 'status' ));
		add_action( 'wp_ajax_nopriv_qnap_delete_log', array( 'qnap\QNAP_Backups_Controller', 'delete_log' ));

		// Private actions
		add_action( 'wp_ajax_qnap_export', array( 'qnap\QNAP_Export_Controller', 'export' ));
		add_action( 'wp_ajax_qnap_import', array( 'qnap\QNAP_Import_Controller', 'import' ));
		add_action( 'wp_ajax_qnap_status', array( 'qnap\QNAP_Status_Controller', 'status' ));
		add_action( 'wp_ajax_qnap_delete_log', array( 'qnap\QNAP_Backups_Controller', 'delete_log' ));
	}

	/**
	 * Enable WP importing
	 *
	 * @return void
	 */
	public function wp_importing() {
		if ( isset( $_GET['qnap_import'] ) ) {
			if ( ! defined( 'WP_IMPORTING' ) ) {
				define( 'WP_IMPORTING', true );
			}
		}
	}

	/**
	 * Add custom cron schedules
	 *
	 * @param  array $schedules List of schedules
	 * @return array
	 */
	public function add_cron_schedules( $schedules ) {
		$schedules['weekly']  = array(
			'display'  => __( 'Weekly', QNAP_PLUGIN_NAME ),
			'interval' => 60 * 60 * 24 * 7,
		);
		$schedules['monthly'] = array(
			'display'  => __( 'Monthly', QNAP_PLUGIN_NAME ),
			'interval' => ( strtotime( '+1 month' ) - time() ),
		);

		return $schedules;
	}
}
