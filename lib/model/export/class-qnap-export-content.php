<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
    die( 'not here' );
}

class QNAP_Export_Content {

    public static function execute( $params ) {

        // Set archive bytes offset
        if ( isset( $params['archive_bytes_offset'] ) ) {
            $archive_bytes_offset = (int) $params['archive_bytes_offset'];
        } else {
            $archive_bytes_offset = qnap_archive_bytes( $params );
        }

        // Set file bytes offset
        if ( isset( $params['file_bytes_offset'] ) ) {
            $file_bytes_offset = (int) $params['file_bytes_offset'];
        } else {
            $file_bytes_offset = 0;
        }

        // Set content bytes offset
        if ( isset( $params['content_bytes_offset'] ) ) {
            $content_bytes_offset = (int) $params['content_bytes_offset'];
        } else {
            $content_bytes_offset = 0;
        }

        // Get processed files size
        if ( isset( $params['processed_files_size'] ) ) {
            $processed_files_size = (int) $params['processed_files_size'];
        } else {
            $processed_files_size = 0;
        }

        // Get total content files size
        if ( isset( $params['total_content_files_size'] ) ) {
            $total_content_files_size = (int) $params['total_content_files_size'];
        } else {
            $total_content_files_size = 1;
        }

        // Get total content files count
        if ( isset( $params['total_content_files_count'] ) ) {
            $total_content_files_count = (int) $params['total_content_files_count'];
        } else {
            $total_content_files_count = 1;
        }

        // What percent of files have we processed?
        if ( empty( $total_content_files_size ) ) {
            $progress = 100;
        } else {
            $progress = (int) min( ( $processed_files_size / $total_content_files_size ) * 100, 100 );
        }

        // Set progress
        $message = sprintf( __( 'Archiving %d content files...', QNAP_PLUGIN_NAME ), $total_content_files_count);
        $params = QNAP_Progress_Bar::update($params, $processed_files_size, $total_content_files_size, $message);

        // Flag to hold if file data has been processed
        $completed = true;

        // Start time
        $start = microtime( true );

        // Get content list file
        $content_list = qnap_open( qnap_content_list_path( $params ), 'r' );

        // Set content pointer at the current index
        if ( fseek( $content_list, $content_bytes_offset ) !== -1 ) {

            // Open the archive file for writing
            $archive = new QNAP_Compressor( qnap_archive_path( $params ) );

            // Set the file pointer to the one that we have saved
            $archive->set_file_pointer( $archive_bytes_offset );

            // Loop over files
            while ( $file_path = trim( fgets( $content_list ) ) ) {
                $file_bytes_written = 0;

                // Add file to archive
                if ( ( $completed = $archive->add_file( WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $file_path, $file_path, $file_bytes_written, $file_bytes_offset ) ) ) {
                    $file_bytes_offset = 0;

                    // Get content bytes offset
                    $content_bytes_offset = ftell( $content_list );
                }

                // Increment processed files size
                $processed_files_size += $file_bytes_written;

                // What percent of files have we processed?
                if ( empty( $total_content_files_size ) ) {
                    $progress = 100;
                } else {
                    $progress = (int) min( ( $processed_files_size / $total_content_files_size ) * 100, 100 );
                }

                // Update progress every 10 files or 5MB processed
                if ($processed_files_size % (5 * 1024 * 1024) < $file_bytes_written || $processed_files_size % 10 == 0) {
                    $message = sprintf( __( 'Archiving %d content files...', QNAP_PLUGIN_NAME ), $total_content_files_count);
                    $params = QNAP_Progress_Bar::update($params, $processed_files_size, $total_content_files_size, $message);
                }

                // More than 10 seconds have passed, break and do another request
                if ( ( $timeout = apply_filters( 'qnap_completed_timeout', 10 ) ) ) {
                    if ( ( microtime( true ) - $start ) > $timeout ) {
                        $completed = false;
                        break;
                    }
                }
            }

            // Get archive bytes offset
            $archive_bytes_offset = $archive->get_file_pointer();

            // Truncate the archive file
            $archive->truncate();

            // Close the archive file
            $archive->close();
        }

        // End of the content list?
        if ( feof( $content_list ) ) {

            // Unset archive bytes offset
            unset( $params['archive_bytes_offset'] );

            // Unset file bytes offset
            unset( $params['file_bytes_offset'] );

            // Unset content bytes offset
            unset( $params['content_bytes_offset'] );

            // Unset processed files size
            unset( $params['processed_files_size'] );

            // Unset total content files size
            unset( $params['total_content_files_size'] );

            // Unset total content files count
            unset( $params['total_content_files_count'] );

            // Unset completed flag
            unset( $params['completed'] );

            // Set progress to 100%
            $message = sprintf( __( 'Finished archiving %d content files.', QNAP_PLUGIN_NAME ), $total_content_files_count);
            $params = QNAP_Progress_Bar::update($params, $total_content_files_size, $total_content_files_size, $message);

        } else {

            // Set archive bytes offset
            $params['archive_bytes_offset'] = $archive_bytes_offset;

            // Set file bytes offset
            $params['file_bytes_offset'] = $file_bytes_offset;

            // Set content bytes offset
            $params['content_bytes_offset'] = $content_bytes_offset;

            // Set processed files size
            $params['processed_files_size'] = $processed_files_size;

            // Set total content files size
            $params['total_content_files_size'] = $total_content_files_size;

            // Set total content files count
            $params['total_content_files_count'] = $total_content_files_count;

            // Set completed flag
            $params['completed'] = $completed;
        }

        // Close the content list file
        qnap_close( $content_list );

        return $params;
    }
}