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
				'QNAP WP Migration is not able to create <strong>%s</strong> folder. ' .
				'You will need to create this folder and grant it read/write/execute permissions (0777) ' .
				'for the QNAP WP Migration plugin to function properly.',
				QNAP_PLUGIN_NAME
			),
			QNAP_STORAGE_PATH
		)
		?>
	</p>
</div>
