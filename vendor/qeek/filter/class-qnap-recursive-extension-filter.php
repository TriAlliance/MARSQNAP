<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Recursive_Extension_Filter extends \RecursiveFilterIterator {

	protected $include = array();

	public function __construct( \RecursiveIterator $iterator, $include = array() ) {
		parent::__construct( $iterator );
		if ( is_array( $include ) ) {
			$this->include = $include;
		}
	}

	public function accept() {
		if ( $this->getInnerIterator()->isFile() ) {
			if ( ! in_array( pathinfo( $this->getInnerIterator()->getFilename(), PATHINFO_EXTENSION ), $this->include ) ) {
				return false;
			}
		}

		return true;
	}

	public function getChildren() {
		return new self( $this->getInnerIterator()->getChildren(), $this->include );
	}
}
