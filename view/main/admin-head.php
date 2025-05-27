<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}
?>

<style type="text/css" media="all">
	@font-face {
		font-family: 'qeek';
		src: url('<?php echo wp_make_link_relative( QNAP_URL ); ?>/lib/view/assets/font/qeek.eot?v=<?php echo QNAP_VERSION; ?>');
		src: url('<?php echo wp_make_link_relative( QNAP_URL ); ?>/lib/view/assets/font/qeek.eot?v=<?php echo QNAP_VERSION; ?>#iefix') format('embedded-opentype'),
		url('<?php echo wp_make_link_relative( QNAP_URL ); ?>/lib/view/assets/font/qeek.woff?v=<?php echo QNAP_VERSION; ?>') format('woff'),
		url('<?php echo wp_make_link_relative( QNAP_URL ); ?>/lib/view/assets/font/qeek.ttf?v=<?php echo QNAP_VERSION; ?>') format('truetype'),
		url('<?php echo wp_make_link_relative( QNAP_URL ); ?>/lib/view/assets/font/qeek.svg?v=<?php echo QNAP_VERSION; ?>#qeek') format('svg');
		font-weight: normal;
		font-style: normal;
	}

	[class^="qnap-icon-"], [class*=" qnap-icon-"] {
		font-family: 'qeek';
		speak: none;
		font-style: normal;
		font-weight: normal;
		font-variant: normal;
		text-transform: none;
		line-height: 1;

		/* Better Font Rendering =========== */
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
	}

	.qnap-icon-notification:before {
		content: "\e619";
	}

	.qnap-label {
		border: 1px solid #5cb85c;
		background-color: transparent;
		color: #5cb85c;
		cursor: pointer;
		text-transform: uppercase;
		font-weight: 600;
		outline: none;
		transition: background-color 0.2s ease-out;
		padding: .2em .6em;
		font-size: 0.8em;
		border-radius: 5px;
		text-decoration: none !important;
	}

	.qnap-label:hover {
		background-color: #5cb85c;
		color: #fff;
	}

	<?php if ( version_compare( $version, '3.8', '<' ) ) : ?>
	.toplevel_page_qnap_export > div.wp-menu-image {
		background: none !important;
	}

	.toplevel_page_qnap_export > div.wp-menu-image:before {
		line-height: 27px !important;
		content: '';
		background: url('<?php echo wp_make_link_relative( QNAP_URL ); ?>/lib/view/assets/img/logo-20x20.png') no-repeat center center;
		speak: none !important;
		font-style: normal !important;
		font-weight: normal !important;
		font-variant: normal !important;
		text-transform: none !important;
		margin-left: 7px;
		/* Better Font Rendering =========== */
		-webkit-font-smoothing: antialiased !important;
		-moz-osx-font-smoothing: grayscale !important;
	}

	<?php else : ?>
	.toplevel_page_qnap_export > div.wp-menu-image:before {
		position: relative;
		display: inline-block;
		content: '';
		background: url('<?php echo wp_make_link_relative( QNAP_URL ); ?>/lib/view/assets/img/logo-20x20.png') no-repeat center center;
		speak: none !important;
		font-style: normal !important;
		font-weight: normal !important;
		font-variant: normal !important;
		text-transform: none !important;
		line-height: 1 !important;
		/* Better Font Rendering =========== */
		-webkit-font-smoothing: antialiased !important;
		-moz-osx-font-smoothing: grayscale !important;
	}

	.wp-menu-open.toplevel_page_qnap_export,
	.wp-menu-open.toplevel_page_qnap_export > a {
		background-color: #111 !important;
	}
	<?php endif; ?>
</style>
