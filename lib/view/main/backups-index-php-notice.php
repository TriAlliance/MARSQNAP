<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}
?>

<div class="error">
	<p>
		<?php
		printf(
			__(
				'QNAP WP Migration is not able to create <strong>%s</strong> file. ' .
				'Try to change permissions of the parent folder or send us an email at ' .
				'<a href="mailto:support@qeek.com">support@qeek.com</a> for assistance.',
				QNAP_PLUGIN_NAME
			),
			QNAP_BACKUPS_INDEX_PHP
		)
		?>
	</p>
</div>
