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
			'QNAP WP Migration: Your current profile role does not have Export/Import capabilities enabled. ' .
			'<a href="https://help.qeek.com/knowledgebase/how-to-add-import-and-export-capabilities-to-wordpress-users/" target="_blank">Technical details</a>',
			QNAP_PLUGIN_NAME
		);
		?>
	</p>
</div>
