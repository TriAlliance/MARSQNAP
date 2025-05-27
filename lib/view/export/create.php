<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
    die( 'not here' );
}
?>

<div class="qnap-container">
    <div class="qnap-row">
        <div class="qnap-left">
            <div class="qnap-holder">
                <h1>
                    <i class="qnap-icon-export"></i>
                    <?php _e( 'Create Backup', QNAP_PLUGIN_NAME ); ?>
                </h1>

                <?php include QNAP_TEMPLATES_PATH . '/common/report-problem.php'; ?>

                <p>
                    <?php _e( 'This page allows you to create a complete backup of your WordPress site. The backup will include your database, media files, plugins, and themes.', QNAP_PLUGIN_NAME ); ?>
                </p>
                
                <div class="qnap-message-warning">
                    <p>
                        <strong><?php _e( 'Important:', QNAP_PLUGIN_NAME ); ?></strong>
                        <?php _e( 'Backup files will be stored in your WordPress content directory. You can download them for safekeeping after they\'re created.', QNAP_PLUGIN_NAME ); ?>
                    </p>
                </div>

                <form method="post" id="qnap-export-form">
                    <article class="qnap-accordion">
                        <h4>
                            <i class="qnap-icon-arrow-right"></i>
                            <?php _e( 'Advanced Options', QNAP_PLUGIN_NAME ); ?>
                            <small>(<?php _e( 'optional', QNAP_PLUGIN_NAME ); ?>)</small>
                        </h4>
                        <ul>
                            <li>
                                <input type="checkbox" id="qnap-export-no-spam" name="options[no_spam_comments]" />
                                <label for="qnap-export-no-spam"><?php _e( 'Do not export spam comments', QNAP_PLUGIN_NAME ); ?></label>
                            </li>
                            <li>
                                <input type="checkbox" id="qnap-export-no-revisions" name="options[no_post_revisions]" />
                                <label for="qnap-export-no-revisions"><?php _e( 'Do not export post revisions', QNAP_PLUGIN_NAME ); ?></label>
                            </li>
                            <li>
                                <input type="checkbox" id="qnap-export-no-media" name="options[no_media]" />
                                <label for="qnap-export-no-media"><?php _e( 'Do not export media library (images, videos, audio, and document files)', QNAP_PLUGIN_NAME ); ?></label>
                            </li>
                            <li>
                                <input type="checkbox" id="qnap-export-no-themes" name="options[no_themes]" />
                                <label for="qnap-export-no-themes"><?php _e( 'Do not export themes (all themes will be excluded)', QNAP_PLUGIN_NAME ); ?></label>
                            </li>
                            <li>
                                <input type="checkbox" id="qnap-export-no-inactive-themes" name="options[no_inactive_themes]" />
                                <label for="qnap-export-no-inactive-themes"><?php _e( 'Do not export inactive themes (only active theme will be exported)', QNAP_PLUGIN_NAME ); ?></label>
                            </li>
                            <li>
                                <input type="checkbox" id="qnap-export-no-muplugins" name="options[no_muplugins]" />
                                <label for="qnap-export-no-muplugins"><?php _e( 'Do not export must-use plugins (files in wp-content/mu-plugins)', QNAP_PLUGIN_NAME ); ?></label>
                            </li>
                            <li>
                                <input type="checkbox" id="qnap-export-no-plugins" name="options[no_plugins]" />
                                <label for="qnap-export-no-plugins"><?php _e( 'Do not export plugins (all plugins will be excluded)', QNAP_PLUGIN_NAME ); ?></label>
                            </li>
                            <li>
                                <input type="checkbox" id="qnap-export-no-inactive-plugins" name="options[no_inactive_plugins]" />
                                <label for="qnap-export-no-inactive-plugins"><?php _e( 'Do not export inactive plugins (only active plugins will be exported)', QNAP_PLUGIN_NAME ); ?></label>
                            </li>
                            <li>
                                <input type="checkbox" id="qnap-export-no-cache" name="options[no_cache]" />
                                <label for="qnap-export-no-cache"><?php _e( 'Do not export cache files', QNAP_PLUGIN_NAME ); ?></label>
                            </li>
                            <li>
                                <input type="checkbox" id="qnap-export-no-database" name="options[no_database]" />
                                <label for="qnap-export-no-database"><?php _e( 'Do not export database (WordPress core and plugin tables)', QNAP_PLUGIN_NAME ); ?></label>
                            </li>
                            <li>
                                <input type="checkbox" id="qnap-export-no-email-replace" name="options[no_email_replace]" />
                                <label for="qnap-export-no-email-replace"><?php _e( 'Do not replace email domain (applicable for migration between different domains)', QNAP_PLUGIN_NAME ); ?></label>
                            </li>
                        </ul>
                    </article>

                    <?php if (is_multisite()): ?>
                    <?php include QNAP_TEMPLATES_PATH . '/export/sites.php'; ?>
                    <?php endif; ?>

                    <div id="qnap-backup-progress-container" class="qnap-progress-container" style="display: none;">
                        <div id="qnap-backup-progress-bar" class="qnap-progress-bar qnap-progress-bar-animated" style="width: 0%"></div>
                        <div class="qnap-progress-info">
                            <span id="qnap-backup-progress-percentage" class="qnap-progress-percentage">0%</span>
                            <span id="qnap-backup-progress-eta" class="qnap-progress-eta"></span>
                        </div>
                        <div id="qnap-backup-progress-status" class="qnap-progress-status"></div>
                    </div>

                    <p>
                        <a href="#" id="qnap-create-backup" class="qnap-button-green">
                            <i class="qnap-icon-export"></i>
                            <?php _e( 'Create Backup', QNAP_PLUGIN_NAME ); ?>
                        </a>
                    </p>

                    <input type="hidden" name="qnap_manual_export" value="1" />
                </form>
            </div>
        </div>

        <div class="qnap-right">
            <div class="qnap-sidebar">
                <div class="qnap-segment">
                    <span class="qnap-title">
                        <i class="qnap-icon-help"></i>
                        <?php _e( 'Tips', QNAP_PLUGIN_NAME ); ?>
                    </span>

                    <p>
                        <?php _e( 'Backup files are stored in your WordPress content directory. For large sites, consider excluding unnecessary files to reduce backup size.', QNAP_PLUGIN_NAME ); ?>
                    </p>
                    
                    <p>
                        <?php _e( 'After backup completion, verify the backup file to ensure it can be successfully restored when needed.', QNAP_PLUGIN_NAME ); ?>
                    </p>
                </div>

                <div class="qnap-segment">
                    <span class="qnap-title">
                        <i class="qnap-icon-gear"></i>
                        <?php _e( 'What\'s included', QNAP_PLUGIN_NAME ); ?>
                    </span>
                    
                    <ul style="list-style: disc; margin-left: 20px;">
                        <li><?php _e('WordPress database', QNAP_PLUGIN_NAME); ?></li>
                        <li><?php _e('Media library', QNAP_PLUGIN_NAME); ?></li>
                        <li><?php _e('Themes', QNAP_PLUGIN_NAME); ?></li>
                        <li><?php _e('Plugins', QNAP_PLUGIN_NAME); ?></li>
                        <li><?php _e('Uploads and other content', QNAP_PLUGIN_NAME); ?></li>
                        <?php if (is_multisite()): ?>
                        <li><strong><?php _e('All sites in your multisite network', QNAP_PLUGIN_NAME); ?></strong></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Initialize export model
    var exportModel = new QNAP.Export();
    
    // Listen for progress updates
    $(document).on('qnap-export-status', function(e, params) {
        if (params.type === 'progress') {
            // Update inline progress bar
            $('#qnap-backup-progress-bar').width(params.percent + '%');
            $('#qnap-backup-progress-percentage').text(params.percent + '%');
            
            // Extract ETA if available
            if (params.message) {
                var etaMatch = params.message.match(/ETA: ([0-9]+ (?:min|sec)[^-]*)/);
                if (etaMatch && etaMatch[1]) {
                    $('#qnap-backup-progress-eta').text(etaMatch[1]);
                }
                
                // Update status text
                $('#qnap-backup-progress-status').html(params.message);
            }
            
            // Show progress container if hidden
            $('#qnap-backup-progress-container').show();
        }
    });
    
    // Create backup button click handler
    $('#qnap-create-backup').on('click', function(e) {
        e.preventDefault();
        
        // Show progress bar initially at 0%
        $('#qnap-backup-progress-bar').width('0%');
        $('#qnap-backup-progress-percentage').text('0%');
        $('#qnap-backup-progress-eta').text('');
        $('#qnap-backup-progress-status').text('<?php _e('Preparing backup...', QNAP_PLUGIN_NAME); ?>');
        $('#qnap-backup-progress-container').show();
        
        // Get form data
        var storage = QNAP.Util.random(12);
        var options = QNAP.Util.form('#qnap-export-form').concat({
            name: 'storage',
            value: storage
        }).concat({
            name: 'file',
            value: 1
        });
        
        // Set global params
        exportModel.setParams(options);
        
        // Start export
        exportModel.start();
    });
});
</script>