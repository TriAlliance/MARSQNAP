<?php

namespace qnap;

if (!defined('ABSPATH')) {
	die('not here');
}

class QNAP_Export_Controller
{

	public static function index()
	{
		QNAP_Template::render('export/index');
	}

	public static function export($params = array())
	{
		qnap_setup_environment();

		if (empty( $params )) {
			$params = qnap_sanitized_params();
		}

		// Set priority
		if (!isset($params['priority'])) {
			$params['priority'] = 5;
		}

		// Set secret key
		$secret_key = null;
		if (isset($params['secret_key'])) {
			$secret_key = trim($params['secret_key']);
		}

		try {
			// Ensure that unauthorized people cannot access export action
			qnap_verify_secret_key($secret_key);
		} catch (QNAP_Not_Valid_Secret_Key_Exception $e) {
			echo json_encode(array('error' => array('code' => 403)));
			exit;
		}

		// Loop over filters
		if (($filters = qnap_get_filters('qnap_export'))) {
			while ($hooks = current($filters)) {
				if (intval($params['priority']) === key($filters)) {
					foreach ($hooks as $hook) {
						try {
							// Run function hook
							$params = call_user_func_array($hook['function'], array($params));
						} catch (QNAP_Database_Exception $e) {
							if (defined('WP_CLI')) {
								WP_CLI::error(sprintf(__('Unable to export. Error code: %s. %s', QNAP_PLUGIN_NAME), $e->getCode(), $e->getMessage()));
							} else {
								status_header($e->getCode());
								echo json_encode(array('error' => array('code' => $e->getCode(), 'message' => $e->getMessage())));
							}
							Qnap_Log::append(qnap_get_log_client($params), '[Multi-Application Recovery Service] Failed to backup WordPress. ' . $e->getMessage());
							QNAP_Directory::delete(qnap_storage_path($params));
							exit;
						} catch (\Exception $e) {
							if (defined('WP_CLI')) {
								WP_CLI::error(sprintf(__('Unable to export: %s', QNAP_PLUGIN_NAME), $e->getMessage()));
							} else {
								QNAP_Status::error(__('Unable to export', QNAP_PLUGIN_NAME), $e->getMessage());
								QNAP_Notification::error(__('Unable to export', QNAP_PLUGIN_NAME), $e->getMessage());
								echo json_encode(array('error' => array('code' => $e->getCode(), 'message' => $e->getMessage())));
							}
							Qnap_Log::append(qnap_get_log_client($params), '[Multi-Application Recovery Service] Failed to backup WordPress. ' . $e->getMessage());
							QNAP_Directory::delete(qnap_storage_path($params));
							exit;
						}
					}

					// Set completed
					$completed = true;
					if (isset($params['completed'])) {
						$completed = (bool) $params['completed'];
					}

					// Do request
					if ($completed === false || ($next = next($filters)) && ($params['priority'] = key($filters))) {
						if (defined('WP_CLI')) {
							if (!defined('DOING_CRON')) {
								continue;
							}
						}

						if (isset($params['qnap_manual_export'])) {
							$output = $params;
							unset( $output['secret_key'] );
							echo json_encode( $output );
							exit;
						}

						wp_remote_post(
							apply_filters('qnap_http_export_url', add_query_arg(array('qnap_import' => 1), admin_url('admin-ajax.php?action=qnap_export'))),
							array(
								'timeout'   => apply_filters('qnap_http_export_timeout', 10),
								'blocking'  => apply_filters('qnap_http_export_blocking', false),
								'sslverify' => apply_filters('qnap_http_export_sslverify', false),
								'headers'   => apply_filters('qnap_http_export_headers', array()),
								'body'      => apply_filters('qnap_http_export_body', $params),
							)
						);
						exit;
					}
				}

				next($filters);
			}
		}

		return $params;
	}

	public static function buttons()
	{
		$active_filters = array();
		$static_filters = array();

		// QNAP WP Migration
		if (defined('QNAP_PLUGIN_NAME')) {
			$active_filters[] = apply_filters('qnap_export_file', QNAP_Template::get_content('export/button-file'));
		} else {
			$static_filters[] = apply_filters('qnap_export_file', QNAP_Template::get_content('export/button-file'));
		}

		// Add FTP Extension
		if (defined('QNAPFE_PLUGIN_NAME')) {
			$active_filters[] = apply_filters('qnap_export_ftp', QNAP_Template::get_content('export/button-ftp'));
		} else {
			$static_filters[] = apply_filters('qnap_export_ftp', QNAP_Template::get_content('export/button-ftp'));
		}

		// Add Dropbox Extension
		if (defined('QNAPDE_PLUGIN_NAME')) {
			$active_filters[] = apply_filters('qnap_export_dropbox', QNAP_Template::get_content('export/button-dropbox'));
		} else {
			$static_filters[] = apply_filters('qnap_export_dropbox', QNAP_Template::get_content('export/button-dropbox'));
		}

		// Add Google Drive Extension
		if (defined('QNAPGE_PLUGIN_NAME')) {
			$active_filters[] = apply_filters('qnap_export_gdrive', QNAP_Template::get_content('export/button-gdrive'));
		} else {
			$static_filters[] = apply_filters('qnap_export_gdrive', QNAP_Template::get_content('export/button-gdrive'));
		}

		// Add Amazon S3 Extension
		if (defined('QNAPSE_PLUGIN_NAME')) {
			$active_filters[] = apply_filters('qnap_export_s3', QNAP_Template::get_content('export/button-s3'));
		} else {
			$static_filters[] = apply_filters('qnap_export_s3', QNAP_Template::get_content('export/button-s3'));
		}

		// Add Backblaze B2 Extension
		if (defined('QNAPAE_PLUGIN_NAME')) {
			$active_filters[] = apply_filters('qnap_export_b2', QNAP_Template::get_content('export/button-b2'));
		} else {
			$static_filters[] = apply_filters('qnap_export_b2', QNAP_Template::get_content('export/button-b2'));
		}

		// Add OneDrive Extension
		if (defined('QNAPOE_PLUGIN_NAME')) {
			$active_filters[] = apply_filters('qnap_export_onedrive', QNAP_Template::get_content('export/button-onedrive'));
		} else {
			$static_filters[] = apply_filters('qnap_export_onedrive', QNAP_Template::get_content('export/button-onedrive'));
		}

		// Add Box Extension
		if (defined('QNAPBE_PLUGIN_NAME')) {
			$active_filters[] = apply_filters('qnap_export_box', QNAP_Template::get_content('export/button-box'));
		} else {
			$static_filters[] = apply_filters('qnap_export_box', QNAP_Template::get_content('export/button-box'));
		}

		// Add Mega Extension
		if (defined('QNAPEE_PLUGIN_NAME')) {
			$active_filters[] = apply_filters('qnap_export_mega', QNAP_Template::get_content('export/button-mega'));
		} else {
			$static_filters[] = apply_filters('qnap_export_mega', QNAP_Template::get_content('export/button-mega'));
		}

		// Add DigitalOcean Spaces Extension
		if (defined('QNAPIE_PLUGIN_NAME')) {
			$active_filters[] = apply_filters('qnap_export_digitalocean', QNAP_Template::get_content('export/button-digitalocean'));
		} else {
			$static_filters[] = apply_filters('qnap_export_digitalocean', QNAP_Template::get_content('export/button-digitalocean'));
		}

		// Add Google Cloud Storage Extension
		if (defined('QNAPCE_PLUGIN_NAME')) {
			$active_filters[] = apply_filters('qnap_export_gcloud_storage', QNAP_Template::get_content('export/button-gcloud-storage'));
		} else {
			$static_filters[] = apply_filters('qnap_export_gcloud_storage', QNAP_Template::get_content('export/button-gcloud-storage'));
		}

		// Add Microsoft Azure Extension
		if (defined('QNAPZE_PLUGIN_NAME')) {
			$active_filters[] = apply_filters('qnap_export_azure_storage', QNAP_Template::get_content('export/button-azure-storage'));
		} else {
			$static_filters[] = apply_filters('qnap_export_azure_storage', QNAP_Template::get_content('export/button-azure-storage'));
		}

		// Add Amazon Glacier Extension
		if (defined('QNAPRE_PLUGIN_NAME')) {
			$active_filters[] = apply_filters('qnap_export_glacier', QNAP_Template::get_content('export/button-glacier'));
		} else {
			$static_filters[] = apply_filters('qnap_export_glacier', QNAP_Template::get_content('export/button-glacier'));
		}

		// Add pCloud Extension
		if (defined('QNAPPE_PLUGIN_NAME')) {
			$active_filters[] = apply_filters('qnap_export_pcloud', QNAP_Template::get_content('export/button-pcloud'));
		} else {
			$static_filters[] = apply_filters('qnap_export_pcloud', QNAP_Template::get_content('export/button-pcloud'));
		}

		// Add WebDAV Extension
		if (defined('QNAPWE_PLUGIN_NAME')) {
			$active_filters[] = apply_filters('qnap_export_webdav', QNAP_Template::get_content('export/button-webdav'));
		} else {
			$static_filters[] = apply_filters('qnap_export_webdav', QNAP_Template::get_content('export/button-webdav'));
		}

		// Add S3 Client Extension
		if (defined('QNAPNE_PLUGIN_NAME')) {
			$active_filters[] = apply_filters('qnap_export_s3_client', QNAP_Template::get_content('export/button-s3-client'));
		} else {
			$static_filters[] = apply_filters('qnap_export_s3_client', QNAP_Template::get_content('export/button-s3-client'));
		}

		return array_merge($active_filters, $static_filters);
	}

	public static function http_export_headers($headers = array())
	{
		if (($user = get_option(QNAP_AUTH_USER)) && ($password = get_option(QNAP_AUTH_PASSWORD))) {
			if (($hash = base64_encode(sprintf('%s:%s', $user, $password)))) {
				$headers['Authorization'] = sprintf('Basic %s', $hash);
			}
		}

		return $headers;
	}

	public static function cleanup()
	{
		try {
			// Iterate over storage directory
			$iterator = new QNAP_Recursive_Directory_Iterator(QNAP_STORAGE_PATH);

			// Exclude index.php
			$iterator = new QNAP_Recursive_Exclude_Filter($iterator, array('index.php'));

			// Loop over folders and files
			foreach ($iterator as $item) {
				try {
					if ($item->getMTime() < (time() - QNAP_MAX_STORAGE_CLEANUP)) {
						if ($item->isDir()) {
							QNAP_Directory::delete($item->getPathname());
						} else {
							QNAP_File::delete($item->getPathname());
						}
					}
				} catch (Exception $e) {
				}
			}
		} catch (Exception $e) {
		}
	}
}
