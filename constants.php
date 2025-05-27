<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

// ==================
// = Plugin Version =
// ==================
define( 'QNAP_VERSION', '1.0.5' );

// ===============
// = Plugin Name =
// ===============
define( 'QNAP_PLUGIN_NAME', 'qnap-nas-backup' );

// ================
// = Storage Path =
// ================
define( 'QNAP_STORAGE_PATH', QNAP_PATH . DIRECTORY_SEPARATOR . 'storage' );

// ============
// = Lib Path =
// ============
define( 'QNAP_LIB_PATH', QNAP_PATH . DIRECTORY_SEPARATOR . 'lib' );

// ===================
// = Controller Path =
// ===================
define( 'QNAP_CONTROLLER_PATH', QNAP_LIB_PATH . DIRECTORY_SEPARATOR . 'controller' );

// ==============
// = Model Path =
// ==============
define( 'QNAP_MODEL_PATH', QNAP_LIB_PATH . DIRECTORY_SEPARATOR . 'model' );

// ===============
// = Export Path =
// ===============
define( 'QNAP_EXPORT_PATH', QNAP_MODEL_PATH . DIRECTORY_SEPARATOR . 'export' );

// ===============
// = Import Path =
// ===============
define( 'QNAP_IMPORT_PATH', QNAP_MODEL_PATH . DIRECTORY_SEPARATOR . 'import' );

// =============
// = View Path =
// =============
define( 'QNAP_TEMPLATES_PATH', QNAP_LIB_PATH . DIRECTORY_SEPARATOR . 'view' );

// ===================
// = Set Bandar Path =
// ===================
define( 'QNAP_BANDAR_TEMPLATES_PATH', QNAP_TEMPLATES_PATH );

// ===============
// = Vendor Path =
// ===============
define( 'QNAP_VENDOR_PATH', QNAP_LIB_PATH . DIRECTORY_SEPARATOR . 'vendor' );

// =========================
// = Qeek Table Prefix =
// =========================
define( 'QNAP_TABLE_PREFIX', 'QEEK_PREFIX_' );

// ========================
// = Archive Backups Name =
// ========================
define( 'QNAP_BACKUPS_NAME', 'qnap-backups' );

// =========================
// = Archive Database Name =
// =========================
define( 'QNAP_DATABASE_NAME', 'database.sql' );

// ========================
// = Archive Package Name =
// ========================
define( 'QNAP_PACKAGE_NAME', 'package.json' );

// ==========================
// = Archive Multisite Name =
// ==========================
define( 'QNAP_MULTISITE_NAME', 'multisite.json' );

// ======================
// = Archive Blogs Name =
// ======================
define( 'QNAP_BLOGS_NAME', 'blogs.json' );

// =============================
// = Archive Content List Name =
// =============================
define( 'QNAP_CONTENT_LIST_NAME', 'content.list' );

// ===========================
// = Archive Media List Name =
// ===========================
define( 'QNAP_MEDIA_LIST_NAME', 'media.list' );

// ============================
// = Archive Tables List Name =
// ============================
define( 'QNAP_TABLES_LIST_NAME', 'tables.list' );

// =================================
// = Archive Must-Use Plugins Name =
// =================================
define( 'QNAP_MUPLUGINS_NAME', 'mu-plugins' );

// =============================
// = Less Cache Extension Name =
// =============================
define( 'QNAP_LESS_CACHE_NAME', '.less.cache' );

// ============================
// = Elementor CSS Cache Name =
// ============================
define( 'QNAP_ELEMENTOR_CSS_NAME', 'uploads' . DIRECTORY_SEPARATOR . 'elementor' . DIRECTORY_SEPARATOR . 'css' );

// =============================
// = Endurance Page Cache Name =
// =============================
define( 'QNAP_ENDURANCE_PAGE_CACHE_NAME', 'endurance-page-cache.php' );

// ===========================
// = Endurance PHP Edge Name =
// ===========================
define( 'QNAP_ENDURANCE_PHP_EDGE_NAME', 'endurance-php-edge.php' );

// ================================
// = Endurance Browser Cache Name =
// ================================
define( 'QNAP_ENDURANCE_BROWSER_CACHE_NAME', 'endurance-browser-cache.php' );

// =========================
// = GD System Plugin Name =
// =========================
define( 'QNAP_GD_SYSTEM_PLUGIN_NAME', 'gd-system-plugin.php' );

// =======================
// = WP Stack Cache Name =
// =======================
define( 'QNAP_WP_STACK_CACHE_NAME', 'wp-stack-cache.php' );

// ===========================
// = WP.com Site Loader Name =
// ===========================
define( 'QNAP_WP_COMSH_LOADER_NAME', 'wpcomsh-loader.php' );

// ===========================
// = WP.com Site Helper Name =
// ===========================
define( 'QNAP_WP_COMSH_HELPER_NAME', 'wpcomsh' );

// ================================
// = WP Engine System Plugin Name =
// ================================
define( 'QNAP_WP_ENGINE_SYSTEM_PLUGIN_NAME', 'mu-plugin.php' );

// ===========================
// = WPE Sign On Plugin Name =
// ===========================
define( 'QNAP_WPE_SIGN_ON_PLUGIN_NAME', 'wpe-wp-sign-on-plugin.php' );

// ===================================
// = WP Engine Security Auditor Name =
// ===================================
define( 'QNAP_WP_ENGINE_SECURITY_AUDITOR_NAME', 'wpengine-security-auditor.php' );

// ===========================
// = WP Cerber Security Name =
// ===========================
define( 'QNAP_WP_CERBER_SECURITY_NAME', 'aaa-wp-cerber.php' );

// ==================
// = Error Log Name =
// ==================
define( 'QNAP_ERROR_NAME', 'error.log' );

// ==============
// = Secret Key =
// ==============
define( 'QNAP_SECRET_KEY', 'qnap_secret_key' );

// =============
// = Auth User =
// =============
define( 'QNAP_AUTH_USER', 'qnap_auth_user' );

// =================
// = Auth Password =
// =================
define( 'QNAP_AUTH_PASSWORD', 'qnap_auth_password' );

// ============
// = Site URL =
// ============
define( 'QNAP_SITE_URL', 'siteurl' );

// ============
// = Home URL =
// ============
define( 'QNAP_HOME_URL', 'home' );

// ================
// = Uploads Path =
// ================
define( 'QNAP_UPLOADS_PATH', 'upload_path' );

// ====================
// = Uploads URL Path =
// ====================
define( 'QNAP_UPLOADS_URL_PATH', 'upload_url_path' );

// ==================
// = Active Plugins =
// ==================
define( 'QNAP_ACTIVE_PLUGINS', 'active_plugins' );

// ==========================
// = Jetpack Active Modules =
// ==========================
define( 'QNAP_JETPACK_ACTIVE_MODULES', 'jetpack_active_modules' );

// ====================================
// = Swift Optimizer Plugin Organizer =
// ====================================
define( 'QNAP_SWIFT_OPTIMIZER_PLUGIN_ORGANIZER', 'swift_performance_plugin_organizer' );

// ===================
// = Active Template =
// ===================
define( 'QNAP_ACTIVE_TEMPLATE', 'template' );

// =====================
// = Active Stylesheet =
// =====================
define( 'QNAP_ACTIVE_STYLESHEET', 'stylesheet' );

// ==============
// = DB Version =
// ==============
define( 'QNAP_DB_VERSION', 'db_version' );

// ======================
// = Initial DB Version =
// ======================
define( 'QNAP_INITIAL_DB_VERSION', 'initial_db_version' );

// ============
// = Cron Key =
// ============
define( 'QNAP_CRON', 'cron' );

// ===================
// = Backups Labels  =
// ===================
define( 'QNAP_BACKUPS_LABELS', 'qnap_backups_labels' );

// ===============
// = Sites Links =
// ===============
define( 'QNAP_SITES_LINKS', 'qnap_sites_links' );

// ==============
// = Status Key =
// ==============
define( 'QNAP_STATUS', 'qnap_status' );

// ================
// = Messages Key =
// ================
define( 'QNAP_MESSAGES', 'qnap_messages' );

// ==================
// = Max Chunk Size =
// ==================
define( 'QNAP_MAX_CHUNK_SIZE', 5 * 1024 * 1024 );

// ===========================
// = Max Transaction Queries =
// ===========================
define( 'QNAP_MAX_TRANSACTION_QUERIES', 1000 );

// ======================
// = Max Select Records =
// ======================
define( 'QNAP_MAX_SELECT_RECORDS', 1000 );

// =======================
// = Max Storage Cleanup =
// =======================
define( 'QNAP_MAX_STORAGE_CLEANUP', 24 * 60 * 60 );

// =====================
// = Disk Space Factor =
// =====================
define( 'QNAP_DISK_SPACE_FACTOR', 2 );

// ====================
// = Disk Space Extra =
//=====================
define( 'QNAP_DISK_SPACE_EXTRA', 300 * 1024 * 1024 );

// ================
// = Backups Path =
// ================
define( 'QNAP_BACKUPS_PATH', WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'qnap-backups' );

// ==========================
// = Storage index.php File =
// ==========================
define( 'QNAP_STORAGE_INDEX_PHP', QNAP_STORAGE_PATH . DIRECTORY_SEPARATOR . 'index.php' );

// ===========================
// = Storage index.html File =
// ===========================
define( 'QNAP_STORAGE_INDEX_HTML', QNAP_STORAGE_PATH . DIRECTORY_SEPARATOR . 'index.html' );

// ==========================
// = Backups index.php File =
// ==========================
define( 'QNAP_BACKUPS_INDEX_PHP', QNAP_BACKUPS_PATH . DIRECTORY_SEPARATOR . 'index.php' );

// ===========================
// = Backups index.html File =
// ===========================
define( 'QNAP_BACKUPS_INDEX_HTML', QNAP_BACKUPS_PATH . DIRECTORY_SEPARATOR . 'index.html' );

// ==========================
// = Backups .htaccess File =
// ==========================
define( 'QNAP_BACKUPS_HTACCESS', QNAP_BACKUPS_PATH . DIRECTORY_SEPARATOR . '.htaccess' );

// ===========================
// = Backups web.config File =
// ===========================
define( 'QNAP_BACKUPS_WEBCONFIG', QNAP_BACKUPS_PATH . DIRECTORY_SEPARATOR . 'web.config' );

// ============================
// = WordPress .htaccess File =
// ============================
define( 'QNAP_WORDPRESS_HTACCESS', ABSPATH . DIRECTORY_SEPARATOR . '.htaccess' );

// =============================
// = WordPress web.config File =
// =============================
define( 'QNAP_WORDPRESS_WEBCONFIG', ABSPATH . DIRECTORY_SEPARATOR . 'web.config' );

