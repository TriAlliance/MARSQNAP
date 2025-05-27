<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Recursive_Exclude_Filter extends \RecursiveFilterIterator {

	protected $exclude = array();

	public function __construct( \RecursiveIterator $iterator, $exclude = array() ) {
		parent::__construct( $iterator );
		if ( is_array( $exclude ) ) {
			foreach ( $exclude as $path ) {
				$this->exclude[] = qnap_replace_forward_slash_with_directory_separator( $path );
			}
		}
	}

	public function accept() {
		if ( in_array( qnap_replace_forward_slash_with_directory_separator( $this->getInnerIterator()->getSubPathname() ), $this->exclude ) ) {
			return false;
		}

		if ( in_array( qnap_replace_forward_slash_with_directory_separator( $this->getInnerIterator()->getPathname() ), $this->exclude ) ) {
			return false;
		}

		if ( in_array( qnap_replace_forward_slash_with_directory_separator( $this->getInnerIterator()->getPath() ), $this->exclude ) ) {
			return false;
		}

		if ( strpos( $this->getInnerIterator()->getSubPathname(), "\n" ) !== false ) {
			return false;
		}

		if ( strpos( $this->getInnerIterator()->getSubPathname(), "\r" ) !== false ) {
			return false;
		}

		return true;
	}

	public function getChildren() {
		return new self( $this->getInnerIterator()->getChildren(), $this->exclude );
	}
}
