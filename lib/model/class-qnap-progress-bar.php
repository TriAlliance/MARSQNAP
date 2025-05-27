<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
    die( 'not here' );
}

/**
 * Progress Bar class
 * Handles the creation and updating of progress bars for various operations
 */
class QNAP_Progress_Bar {
    
    /**
     * Initialize progress bar tracking
     * 
     * @param array $params Parameters
     * @param string $operation Operation name (export or import)
     * @return array Updated parameters
     */
    public static function init($params, $operation = 'export') {
        $params['progress_start_time'] = microtime(true);
        $params['progress_operation'] = $operation;
        $params['progress_last_update'] = 0;
        
        return $params;
    }
    
    /**
     * Update progress status
     * 
     * @param array $params Parameters
     * @param int $current Current progress value
     * @param int $total Total progress value
     * @param string $message Optional status message
     * @return array Updated parameters
     */
    public static function update($params, $current, $total, $message = '') {
        // Ensure we don't flood with updates (max once per second)
        $current_time = microtime(true);
        if (isset($params['progress_last_update']) && 
            ($current_time - $params['progress_last_update']) < 1 && 
            $current < $total) {
            return $params;
        }
        
        // Calculate percentage
        $percent = ($total > 0) ? min(round(($current / $total) * 100, 1), 100) : 0;
        
        // Calculate estimated time remaining if we have enough data
        $time_remaining = '';
        if (isset($params['progress_start_time']) && $percent > 0) {
            $elapsed = $current_time - $params['progress_start_time'];
            $estimated_total = $elapsed * 100 / $percent;
            $remaining_seconds = max(0, $estimated_total - $elapsed);
            
            if ($remaining_seconds > 60) {
                $minutes = floor($remaining_seconds / 60);
                $seconds = $remaining_seconds % 60;
                $time_remaining = sprintf(__('ETA: %d min %d sec', QNAP_PLUGIN_NAME), $minutes, $seconds);
            } else {
                $time_remaining = sprintf(__('ETA: %d sec', QNAP_PLUGIN_NAME), round($remaining_seconds));
            }
        }
        
        // Set status with progress bar
        if (!empty($message)) {
            $status_message = $message . '<br/>';
        } else {
            $status_message = '';
        }
        
        // Only add the time remaining if we have a valid estimate
        if (!empty($time_remaining) && $percent < 100 && $percent > 0) {
            $status_message .= sprintf(
                __('Progress: %d%% complete - %s', QNAP_PLUGIN_NAME),
                $percent,
                $time_remaining
            );
        } else {
            $status_message .= sprintf(
                __('Progress: %d%% complete', QNAP_PLUGIN_NAME),
                $percent
            );
        }
        
        // Set the status
        QNAP_Status::progress($percent, $status_message);
        
        // Update last update time
        $params['progress_last_update'] = $current_time;
        
        return $params;
    }
    
    /**
     * Complete progress tracking
     * 
     * @param array $params Parameters
     * @param string $message Optional completion message
     * @return array Updated parameters
     */
    public static function complete($params, $message = '') {
        // Calculate total elapsed time
        $elapsed = microtime(true) - $params['progress_start_time'];
        
        if (empty($message)) {
            if ($params['progress_operation'] === 'export') {
                $message = __('Export completed successfully!', QNAP_PLUGIN_NAME);
            } else {
                $message = __('Import completed successfully!', QNAP_PLUGIN_NAME);
            }
        }
        
        // Format elapsed time
        if ($elapsed > 60) {
            $minutes = floor($elapsed / 60);
            $seconds = $elapsed % 60;
            $time_message = sprintf(__('Total time: %d min %d sec', QNAP_PLUGIN_NAME), $minutes, round($seconds));
        } else {
            $time_message = sprintf(__('Total time: %d sec', QNAP_PLUGIN_NAME), round($elapsed));
        }
        
        // Set 100% completion status
        QNAP_Status::progress(100, $message . '<br/>' . $time_message);
        
        // Clean up progress tracking data
        unset($params['progress_start_time']);
        unset($params['progress_operation']);
        unset($params['progress_last_update']);
        
        return $params;
    }
}