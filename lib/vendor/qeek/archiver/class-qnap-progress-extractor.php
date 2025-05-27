<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
    die( 'not here' );
}

/**
 * Extended QNAP_Extractor class with progress tracking
 */
class QNAP_Progress_Extractor extends QNAP_Extractor {

    /**
     * Progress callback
     *
     * @var callable
     */
    private $progress_callback;
    
    /**
     * Current progress
     *
     * @var int
     */
    private $current_progress = 0;
    
    /**
     * Total progress
     *
     * @var int
     */
    private $total_progress = 0;
    
    /**
     * Last update time
     *
     * @var float
     */
    private $last_update_time = 0;

    /**
     * Constructor with progress callback
     *
     * @param string $file_name File to use as archive
     * @param callable $progress_callback Progress callback function
     */
    public function __construct($file_name, $progress_callback = null) {
        parent::__construct($file_name);
        $this->progress_callback = $progress_callback;
        $this->last_update_time = microtime(true);
        $this->total_progress = $this->get_total_files_size();
    }

    /**
     * Update progress
     *
     * @param int $increment Amount to increment progress by
     * @param string $file_name Current file name
     * @return void
     */
    private function update_progress($increment, $file_name = '') {
        $this->current_progress += $increment;
        
        // Only update progress at most once per second to avoid performance issues
        $current_time = microtime(true);
        if (($current_time - $this->last_update_time) >= 1.0) {
            $this->last_update_time = $current_time;
            
            // Call progress callback if set
            if (is_callable($this->progress_callback)) {
                call_user_func(
                    $this->progress_callback,
                    $this->current_progress,
                    $this->total_progress,
                    $file_name
                );
            }
        }
    }

    /**
     * Extract one file to location with progress tracking
     *
     * @param string $location Destination path
     * @param array $exclude_files Exclude files by name
     * @param array $exclude_extensions Exclude files by extension
     * @param array $old_paths Old replace paths
     * @param array $new_paths New replace paths
     * @param int $file_written File written (in bytes)
     * @param int $file_offset File offset (in bytes)
     * @return bool
     */
    public function extract_one_file_to($location, $exclude_files = array(), $exclude_extensions = array(), $old_paths = array(), $new_paths = array(), &$file_written = 0, &$file_offset = 0) {
        $result = parent::extract_one_file_to($location, $exclude_files, $exclude_extensions, $old_paths, $new_paths, $file_written, $file_offset);
        
        // Update progress with the amount written
        $this->update_progress($file_written);
        
        return $result;
    }

    /**
     * Extract specific files from archive with progress tracking
     *
     * @param string $location Destination path
     * @param array $include_files Include files by name
     * @param array $exclude_files Exclude files by name
     * @param array $exclude_extensions Exclude files by extension
     * @param int $file_written File written (in bytes)
     * @param int $file_offset File offset (in bytes)
     * @return bool
     */
    public function extract_by_files_array($location, $include_files = array(), $exclude_files = array(), $exclude_extensions = array(), &$file_written = 0, &$file_offset = 0) {
        $result = parent::extract_by_files_array($location, $include_files, $exclude_files, $exclude_extensions, $file_written, $file_offset);
        
        // Update progress with the amount written
        $this->update_progress($file_written);
        
        return $result;
    }
}