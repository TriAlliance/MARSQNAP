<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
    die( 'not here' );
}

class QNAP_Compressor extends QNAP_Archiver {

    /**
     * Overloaded constructor that opens the passed file for writing
     *
     * @param string $file_name File to use as archive
     */
    public function __construct( $file_name ) {
        parent::__construct( $file_name, true );
    }

    /**
     * Add a file to the archive
     *
     * @param string $file_name     File to add to the archive
     * @param string $new_file_name Write the file with a different name
     * @param int    $file_written  File written (in bytes)
     * @param int    $file_offset   File offset (in bytes)
     *
     * @throws \QNAP_Not_Seekable_Exception
     * @throws \QNAP_Not_Writable_Exception
     * @throws \QNAP_Quota_Exceeded_Exception
     *
     * @return bool
     */
    public function add_file( $file_name, $new_file_name = '', &$file_written = 0, &$file_offset = 0 ) {
        $file_written = 0;

        // Replace forward slash with current directory separator in file name
        $file_name = qnap_replace_forward_slash_with_directory_separator( $file_name );

        // Escape Windows directory separator in file name
        $file_name = qnap_escape_windows_directory_separator( $file_name );

        // Flag to hold if file data has been processed
        $completed = true;

        // Start time
        $start = microtime( true );

        // Open the file for reading in binary mode (fopen may return null for quarantined files)
        if ( ( $file_handle = @fopen( $file_name, 'rb' ) ) ) {
            $file_bytes = 0;

            // Get header block
            if ( ( $block = $this->get_file_block( $file_name, $new_file_name ) ) ) {

                // Write header block
                if ( $file_offset === 0 ) {
                    if ( ( $file_bytes = @fwrite( $this->file_handle, $block ) ) !== false ) {
                        if ( strlen( $block ) !== $file_bytes ) {
                            throw new QNAP_Quota_Exceeded_Exception( sprintf( __( 'Out of disk space. Unable to write header to file. File: %s', QNAP_PLUGIN_NAME ), $this->file_name ) );
                        }
                    } else {
                        throw new QNAP_Not_Writable_Exception( sprintf( __( 'Unable to write header to file. File: %s', QNAP_PLUGIN_NAME ), $this->file_name ) );
                    }
                }

                // Set file offset
                if ( @fseek( $file_handle, $file_offset, SEEK_SET ) !== -1 ) {

                    // Read the file in 512KB chunks
                    while ( false === @feof( $file_handle ) ) {

                        // Read the file in chunks of 512KB
                        if ( ( $file_content = @fread( $file_handle, 512000 ) ) !== false ) {
                            if ( ( $file_bytes = @fwrite( $this->file_handle, $file_content ) ) !== false ) {
                                if ( strlen( $file_content ) !== $file_bytes ) {
                                    throw new QNAP_Quota_Exceeded_Exception( sprintf( __( 'Out of disk space. Unable to write content to file. File: %s', QNAP_PLUGIN_NAME ), $this->file_name ) );
                                }
                            } else {
                                throw new QNAP_Not_Writable_Exception( sprintf( __( 'Unable to write content to file. File: %s', QNAP_PLUGIN_NAME ), $this->file_name ) );
                            }

                            // Set file written
                            $file_written += $file_bytes;
                        }

                        // Time elapsed
                        if ( ( $timeout = apply_filters( 'qnap_completed_timeout', 10 ) ) ) {
                            if ( ( microtime( true ) - $start ) > $timeout ) {
                                $completed = false;
                                break;
                            }
                        }
                    }
                }

                // Set file offset
                $file_offset += $file_written;

                // Write file size to file header
                if ( ( $block = $this->get_file_size_block( $file_offset ) ) ) {

                    // Seek to beginning of file size
                    if ( @fseek( $this->file_handle, - $file_offset - 4096 - 12 - 14, SEEK_CUR ) === -1 ) {
                        throw new QNAP_Not_Seekable_Exception( __( 'Your PHP is 32-bit. In order to export your file, please change your PHP version to 64-bit and try again.', QNAP_PLUGIN_NAME ) );
                    }

                    // Write file size to file header
                    if ( ( $file_bytes = @fwrite( $this->file_handle, $block ) ) !== false ) {
                        if ( strlen( $block ) !== $file_bytes ) {
                            throw new QNAP_Quota_Exceeded_Exception( sprintf( __( 'Out of disk space. Unable to write size to file. File: %s', QNAP_PLUGIN_NAME ), $this->file_name ) );
                        }
                    } else {
                        throw new QNAP_Not_Writable_Exception( sprintf( __( 'Unable to write size to file. File: %s', QNAP_PLUGIN_NAME ), $this->file_name ) );
                    }

                    // Seek to end of file content
                    if ( @fseek( $this->file_handle, + $file_offset + 4096 + 12, SEEK_CUR ) === -1 ) {
                        throw new QNAP_Not_Seekable_Exception( __( 'Your PHP is 32-bit. In order to export your file, please change your PHP version to 64-bit and try again.', QNAP_PLUGIN_NAME ) );
                    }
                }
            }

            // Close the handle
            @fclose( $file_handle );
        }

        return $completed;
    }

    /**
     * Generate binary block header for a file
     *
     * @param string $file_name     Filename to generate block header for
     * @param string $new_file_name Write the file with a different name
     *
     * @return string
     */
    private function get_file_block( $file_name, $new_file_name = '' ) {
        $block = '';

        // Get stats about the file
        if ( ( $stat = @stat( $file_name ) ) !== false ) {

            // Filename of the file we are accessing
            if ( empty( $new_file_name ) ) {
                $name = qnap_basename( $file_name );
            } else {
                $name = qnap_basename( $new_file_name );
            }

            // Size in bytes of the file
            $size = $stat['size'];

            // Last time the file was modified
            $date = $stat['mtime'];

            // Replace current directory separator with backward slash in file path
            if ( empty( $new_file_name ) ) {
                $path = qnap_replace_directory_separator_with_forward_slash( qnap_dirname( $file_name ) );
            } else {
                $path = qnap_replace_directory_separator_with_forward_slash( qnap_dirname( $new_file_name ) );
            }

            // Concatenate block format parts
            $format = implode( '', $this->block_format );

            // Pack file data into binary string
            $block = pack( $format, $name, $size, $date, $path );
        }

        return $block;
    }

    /**
     * Generate file size binary block header for a file
     *
     * @param int $file_size File size
     *
     * @return string
     */
    public function get_file_size_block( $file_size ) {
        $block = '';

        // Pack file data into binary string
        if ( isset( $this->block_format[1] ) ) {
            $block = pack( $this->block_format[1], $file_size );
        }

        return $block;
    }
}