<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Template extends Bandar {

	/**
	 * Renders a file and returns its contents
	 *
	 * @param  string      $view View to render
	 * @param  array       $args Set of arguments
	 * @param  string|bool $path Path to template
	 * @return string            Rendered view
	 */
	public static function render( $view, $args = array(), $path = false ) {
		parent::render( $view, $args, $path );
	}

	/**
	 * Returns link to an asset file
	 *
	 * @param  string $asset  Asset file
	 * @param  string $prefix Asset prefix
	 * @return string         Asset URL
	 */
	public static function asset_link( $asset, $prefix = 'QNAP' ) {
		return constant( $prefix . '_URL' ) . '/lib/view/assets/' . $asset . '?v=' . constant( $prefix . '_VERSION' );
	}

	/**
	 * Renders a file and gets its contents
	 *
	 * @param  string      $view View to render
	 * @param  array       $args Set of arguments
	 * @param  string|bool $path Path to template
	 * @return string            Rendered view
	 */
	public static function get_content( $view, $args = array(), $path = false ) {
		return parent::getTemplateContent( $view, $args, $path );
	}
}
