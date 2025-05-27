<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}
?>

<div class="error">
	<p>
		<?php
		_e(
			'WordPress Multisite is supported via our QNAP WP Migration Multisite Extension. ' .
			'You can get a copy of it here',
			QNAP_PLUGIN_NAME
		);
		?>
		<a href="https://qeek.com/products/multisite-extension" target="_blank" class="qnap-label">
			<i class="qnap-icon-notification"></i>
			<?php _e( 'Get multisite', QNAP_PLUGIN_NAME ); ?>
		</a>
	</p>
</div>
