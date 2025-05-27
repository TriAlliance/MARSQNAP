<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
    die( 'not here' );
}

class QNAP_Status_Controller {

    public static function status( $params = array() ) {
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
            // Ensure that unauthorized people cannot access status action
            qnap_verify_secret_key( $secret_key );
        } catch ( QNAP_Not_Valid_Secret_Key_Exception $e ) {
            http_response_code(403);
            exit;
        }

        exit;
    }

    /**
     * Test restore functionality
     *
     * @return void
     */
    public static function test_restore() {
        // Verify nonce/permissions
        if (!current_user_can('export')) {
            wp_send_json_error(array('message' => __('You do not have sufficient permissions', QNAP_PLUGIN_NAME)));
            return;
        }

        // Get backup file
        $backup = isset($_POST['backup']) ? sanitize_text_field($_POST['backup']) : '';
        $path = isset($_POST['path']) ? sanitize_text_field($_POST['path']) : '';
        $secret_key = isset($_POST['secret_key']) ? sanitize_text_field($_POST['secret_key']) : '';

        // Validate inputs
        if (empty($backup) || empty($path)) {
            wp_send_json_error(array('message' => __('Invalid backup information', QNAP_PLUGIN_NAME)));
            return;
        }

        try {
            // Ensure that unauthorized people cannot access this action
            qnap_verify_secret_key($secret_key);
        } catch (QNAP_Not_Valid_Secret_Key_Exception $e) {
            wp_send_json_error(array('message' => __('Authorization failed', QNAP_PLUGIN_NAME)));
            return;
        }

        // Validate backup file path
        if (validate_file($backup) !== 0 || !file_exists($path)) {
            wp_send_json_error(array('message' => __('Invalid backup file path', QNAP_PLUGIN_NAME)));
            return;
        }

        // Test restore functionality
        $result = QNAP_Verification::test_restore($path);

        if ($result['success']) {
            wp_send_json_success(array(
                'message' => __('Restore test successful. This backup can be restored.', QNAP_PLUGIN_NAME),
                'details' => $result['details']
            ));
        } else {
            wp_send_json_error(array(
                'message' => $result['message'],
                'details' => $result['details']
            ));
        }
    }
}