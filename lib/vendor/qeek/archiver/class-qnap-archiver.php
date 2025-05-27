<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

abstract class QNAP_Archiver {

	/**
	 * Filename including path to the file
	 *
	 * @type string
	 */
	protected $file_name = null;

	/**
	 * Handle to the file
	 *
	 * @type resource
	 */
	protected $file_handle = null;

	/**
	 * Header block format of a file
	 *
	 * Field Name    Offset    Length    Contents
	 * name               0       255    filename (no path, no slash)
	 * size             255        14    size of file contents
	 * mtime            269        12    last modification time
	 * prefix           281      4096    path name, no trailing slashes
	 *
	 * @type array
	 */
	protected $block_format = array(
		'a255',  // filename
		'a14',   // size of file contents
		'a12',   // last time modified
		'a4096', // path
	);

	/**
	 * End of file block string
	 *
	 * @type string
	 */
	protected $eof = null;

	/**
	 * Default constructor
	 *
	 * Initializes filename and end of file block
	 *
	 * @param string $file_name Archive file
	 * @param bool   $write     Read/write mode
	 */
	public function __construct( $file_name, $write = false ) {
		$this->file_name = $file_name;

		// Initialize end of file block
		$this->eof = pack( 'a4377', '' );

		// Open archive file
		if ( $write ) {
			// Open archive file for writing
			if ( ( $this->file_handle = @fopen( $file_name, 'cb' ) ) === false ) {
				throw new QNAP_Not_Accessible_Exception( sprintf( __( 'Unable to open file for writing. File: %s', QNAP_PLUGIN_NAME ), $this->file_name ) );
			}

			// Seek to end of archive file
			if ( @fseek( $this->file_handle, 0, SEEK_END ) === -1 ) {
				throw new QNAP_Not_Seekable_Exception( sprintf( __( 'Unable to seek to end of file. File: %s', QNAP_PLUGIN_NAME ), $this->file_name ) );
			}
		} else {
			// Open archive file for reading
			if ( ( $this->file_handle = @fopen( $file_name, 'rb' ) ) === false ) {
				throw new QNAP_Not_Accessible_Exception( sprintf( __( 'Unable to open file for reading. File: %s', QNAP_PLUGIN_NAME ), $this->file_name ) );
			}
		}
	}

	/**
	 * Set current file pointer
	 *
	 * @param int $offset Archive offset
	 *
	 * @throws \QNAP_Not_Seekable_Exception
	 *
	 * @return void
	 */
	public function set_file_pointer( $offset ) {
		if ( @fseek( $this->file_handle, $offset, SEEK_SET ) === -1 ) {
			throw new QNAP_Not_Seekable_Exception( sprintf( __( 'Unable to seek to offset of file. File: %s Offset: %d', QNAP_PLUGIN_NAME ), $this->file_name, $offset ) );
		}
	}

	/**
	 * Get current file pointer
	 *
	 * @throws \QNAP_Not_Tellable_Exception
	 *
	 * @return int
	 */
	public function get_file_pointer() {
		if ( ( $offset = @ftell( $this->file_handle ) ) === false ) {
			throw new QNAP_Not_Tellable_Exception( sprintf( __( 'Unable to tell offset of file. File: %s', QNAP_PLUGIN_NAME ), $this->file_name ) );
		}

		return $offset;
	}

	/**
	 * Appends end of file block to the archive file
	 *
	 * @throws \QNAP_Not_Seekable_Exception
	 * @throws \QNAP_Not_Writable_Exception
	 * @throws \QNAP_Quota_Exceeded_Exception
	 *
	 * @return void
	 */
	protected function append_eof() {
		// Seek to end of archive file
		if ( @fseek( $this->file_handle, 0, SEEK_END ) === -1 ) {
			throw new QNAP_Not_Seekable_Exception( sprintf( __( 'Unable to seek to end of file. File: %s', QNAP_PLUGIN_NAME ), $this->file_name ) );
		}

		// Write end of file block
		if ( ( $file_bytes = @fwrite( $this->file_handle, $this->eof ) ) !== false ) {
			if ( strlen( $this->eof ) !== $file_bytes ) {
				throw new QNAP_Quota_Exceeded_Exception( sprintf( __( 'Out of disk space. Unable to write end of block to file. File: %s', QNAP_PLUGIN_NAME ), $this->file_name ) );
			}
		} else {
			throw new QNAP_Not_Writable_Exception( sprintf( __( 'Unable to write end of block to file. File: %s', QNAP_PLUGIN_NAME ), $this->file_name ) );
		}
	}

	/**
	 * Replace forward slash with current directory separator
	 *
	 * @param string $path Path
	 *
	 * @return string
	 */
	protected function replace_forward_slash_with_directory_separator( $path ) {
		return str_replace( '/', DIRECTORY_SEPARATOR, $path );
	}

	/**
	 * Replace current directory separator with forward slash
	 *
	 * @param string $path Path
	 *
	 * @return string
	 */
	protected function replace_directory_separator_with_forward_slash( $path ) {
		return str_replace( DIRECTORY_SEPARATOR, '/', $path );
	}

	/**
	 * Escape Windows directory separator
	 *
	 * @param string $path Path
	 *
	 * @return string
	 */
	protected function escape_windows_directory_separator( $path ) {
		return preg_replace( '/[\\\\]+/', '\\\\\\\\', $path );
	}

	/**
	 * Validate archive file
	 *
	 * @return bool
	 */
	public function is_valid() {
		// Failed detecting the current file pointer offset
		if ( ( $offset = @ftell( $this->file_handle ) ) === false ) {
			return false;
		}

		// Failed seeking the beginning of EOL block
		if ( @fseek( $this->file_handle, -4377, SEEK_END ) === -1 ) {
			return false;
		}

		// Trailing block does not match EOL: file is incomplete
		if ( @fread( $this->file_handle, 4377 ) !== $this->eof ) {
			return false;
		}

		// Failed returning to original offset
		if ( @fseek( $this->file_handle, $offset, SEEK_SET ) === -1 ) {
			return false;
		}

		return true;
	}

	/**
	 * Truncates the archive file
	 *
	 * @return void
	 */
	public function truncate() {
		if ( ( $offset = @ftell( $this->file_handle ) ) === false ) {
			throw new QNAP_Not_Tellable_Exception( sprintf( __( 'Unable to tell offset of file. File: %s', QNAP_PLUGIN_NAME ), $this->file_name ) );
		}

		if ( @filesize( $this->file_name ) > $offset ) {
			if ( @ftruncate( $this->file_handle, $offset ) === false ) {
				throw new QNAP_Not_Truncatable_Exception( sprintf( __( 'Unable to truncate file. File: %s', QNAP_PLUGIN_NAME ), $this->file_name ) );
			}
		}
	}

	/**
	 * Closes the archive file
	 *
	 * We either close the file or append the end of file block if complete argument is set to true
	 *
	 * @param  bool $complete Flag to append end of file block
	 *
	 * @return void
	 */
	public function close( $complete = false ) {
		// Are we done appending to the file?
		if ( true === $complete ) {
			$this->append_eof();
		}

		if ( @fclose( $this->file_handle ) === false ) {
			throw new QNAP_Not_Closable_Exception( sprintf( __( 'Unable to close file. File: %s', QNAP_PLUGIN_NAME ), $this->file_name ) );
		}
	}
}
