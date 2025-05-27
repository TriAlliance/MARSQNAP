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
                    <?php _e( 'Export', QNAP_PLUGIN_NAME ); ?>
                </h1>

                <?php include QNAP_TEMPLATES_PATH . '/common/report-problem.php'; ?>

                <p>
                    <?php _e( 'This screen allows you to export a complete copy of your website including media, themes, plugins, and database. You can use the backup to restore your site to the same or different server.', QNAP_PLUGIN_NAME ); ?><br />
                    <?php _e( 'The export file will be saved to your QNAP NAS device. To create and schedule backup jobs, use the Multi-Application Recovery Service (MARS) on your QNAP NAS.', QNAP_PLUGIN_NAME ); ?>
                </p>

                <div class="qnap-message-warning">
                    <p>
                        <strong><?php _e( 'Tip:', QNAP_PLUGIN_NAME ); ?></strong>
                        <?php _e( 'For large or complex sites, we recommend excluding unnecessary files to reduce backup size and improve performance.', QNAP_PLUGIN_NAME ); ?>
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

                    <p>
                        <a href="#" id="qnap-export-file" class="qnap-button-green">
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
                        <?php _e( 'Support', QNAP_PLUGIN_NAME ); ?>
                    </span>

                    <p>
                        <?php _e( 'Need help with the plugin? Visit our support site for documentation and troubleshooting assistance.', QNAP_PLUGIN_NAME ); ?>
                    </p>

                    <p>
                        <a href="https://service.qnap.com/" class="qnap-button-green" target="_blank">
                            <i class="qnap-icon-help"></i>
                            <?php _e( 'Get Support', QNAP_PLUGIN_NAME ); ?>
                        </a>
                    </p>
                </div>

                <div class="qnap-segment">
                    <h2>
                        <?php _e( 'Leave Feedback', QNAP_PLUGIN_NAME ); ?>
                    </h2>

                    <div class="qnap-feedback">
                        <ul class="qnap-feedback-types">
                            <li>
                                <input type="radio" class="qnap-feedback-type" name="qnap_feedback_type" id="qnap-feedback-type-1" value="idea" />
                                <a href="#" class="qnap-feedback-type-link" id="qnap-feedback-type-link-1">
                                    <i class="qnap-icon-bulb"></i>
                                    <span>
                                        <?php _e( 'I have an idea to improve this plugin', QNAP_PLUGIN_NAME ); ?>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <input type="radio" class="qnap-feedback-type" name="qnap_feedback_type" id="qnap-feedback-type-2" value="help" />
                                <label for="qnap-feedback-type-2">
                                    <i class="qnap-icon-notification"></i>
                                    <span>
                                        <?php _e( 'I need help with this plugin', QNAP_PLUGIN_NAME ); ?>
                                    </span>
                                </label>
                            </li>
                        </ul>

                        <div class="qnap-feedback-form">
                            <p>
                                <input 
                                    type="text" 
                                    class="qnap-feedback-email" 
                                    placeholder="<?php _e( 'Your email', QNAP_PLUGIN_NAME ); ?>" 
                                />
                            </p>

                            <p>
                                <textarea 
                                    class="qnap-feedback-message" 
                                    placeholder="<?php _e( 'Leave plugin developers any feedback here', QNAP_PLUGIN_NAME ); ?>"
                                    rows="3"
                                ></textarea>
                            </p>

                            <div class="qnap-feedback-terms-segment">
                                <input 
                                    type="checkbox" 
                                    class="qnap-feedback-terms" 
                                    id="qnap-feedback-terms" 
                                />
                                <label for="qnap-feedback-terms">
                                    <?php _e( 'I agree to send my email and feedback', QNAP_PLUGIN_NAME ); ?>
                                </label>
                            </div>

                            <p>
                                <a href="#" id="qnap-feedback-cancel" class="qnap-feedback-cancel">
                                    <?php _e( 'Cancel', QNAP_PLUGIN_NAME ); ?>
                                </a>
                                <button type="button" id="qnap-feedback-submit" class="qnap-button-green qnap-form-submit">
                                    <i class="qnap-icon-paperplane"></i>
                                    <?php _e( 'Send', QNAP_PLUGIN_NAME ); ?>
                                </button>
                                <span class="spinner"></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>