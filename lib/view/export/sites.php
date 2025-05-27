<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
    die( 'not here' );
}
?>

<div id="qnap-sites" style="margin: 20px 0;">
    <h2><?php _e('Select sites to export', QNAP_PLUGIN_NAME); ?></h2>
    <p><?php _e('Select the sites you want to include in your backup. By default, all sites are selected.', QNAP_PLUGIN_NAME); ?></p>
    
    <div class="qnap-sites-list">
        <?php $sites = QNAP_Multisite_Manager::get_sites(); ?>
        <?php foreach ($sites as $site): ?>
            <div class="qnap-site-item">
                <label>
                    <input type="checkbox" name="options[sites][]" value="<?php echo $site['blog_id']; ?>" checked="checked">
                    <?php echo $site['name']; ?> (<?php echo $site['domain'] . $site['path']; ?>)
                </label>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.qnap-sites-list {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #ddd;
    padding: 10px;
    margin-bottom: 20px;
    background: #f9f9f9;
}
.qnap-site-item {
    margin-bottom: 5px;
    padding: 5px 0;
    border-bottom: 1px solid #eee;
}
.qnap-site-item:last-child {
    border-bottom: none;
}
</style>