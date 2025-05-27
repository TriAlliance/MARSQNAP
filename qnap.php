<?php
namespace qnap;
/**
 * Plugin Name: QNAP NAS Backup
 * Plugin URI: https://www.qnap.com/
 * Description: Backup, migrate restore your WordPress website with QNAP NAS.
 * Author: QNAP
 * Author URI: https://service.qnap.com/
 * Version: 1.0.5
 * Text Domain: qnap-appbackup
 * Domain Path: /languages
 * Network: True
 *
 * Copyright (C) 2014-2020 Qeek Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

// Check SSL Mode
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && ( $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) ) {
	$_SERVER['HTTPS'] = 'on';
}

// Plugin Basename
define( 'QNAP_PLUGIN_BASENAME', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );

// Plugin Path
define( 'QNAP_PATH', dirname( __FILE__ ) );

// Plugin URL
define( 'QNAP_URL', plugins_url( '', QNAP_PLUGIN_BASENAME ) );

// Plugin Backups URL
define( 'QNAP_BACKUPS_URL', content_url( 'qnap-backups', QNAP_PLUGIN_BASENAME ) );

// Include constants
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'constants.php';

// Include deprecated
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'deprecated.php';

// Include functions
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'functions.php';

// Include exceptions
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'exceptions.php';

// Include loader
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'loader.php';

// =========================================================================
// = All app initialization is done in QNAP_Main_Controller __constructor =
// =========================================================================

$main_controller = new QNAP_Main_Controller();

// define('WP_DEBUG', false)
// define( 'WP_DEBUG_LOG', true );
// define('SAVEQUERIES', true);
