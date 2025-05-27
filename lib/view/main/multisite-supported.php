<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
    die( 'not here' );
}
?>

<div class="notice notice-success is-dismissible">
    <p>
        <?php
        _e(
            'WordPress Multisite is now supported directly in QNAP NAS Backup plugin without requiring an extension.',
            QNAP_PLUGIN_NAME
        );
        ?>
    </p>
</div>