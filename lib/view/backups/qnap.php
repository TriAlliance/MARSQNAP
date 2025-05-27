<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}
?>
<div class="wrap">
	<h1 class="wp-heading-inline"><?php _e( 'QNAP NAS Backup', QNAP_PLUGIN_NAME) ?></h1>
	<hr class="wp-header-end">
	<div class="form-wrap edit-term-notes">
		<p><?php _e( 'The QNAP NAS Backup allows you to back up WordPress data to a QNAP NAS.', QNAP_PLUGIN_NAME) ?></p>
		<p><?php _e( 'To create a WordPress data backup or restore job in your NAS, register your WordPress account to your QNAP NAS with the generated access key.', QNAP_PLUGIN_NAME) ?> <a href="https://www.qnap.com/"><?php _e( 'Learn More', QNAP_PLUGIN_NAME) ?></a></p>
	</div>
	<table class="form-table" role="presentation">
		<tbody>
			<tr class="user-user-login-wrap">
				<th><?php _e( 'Host URL', QNAP_PLUGIN_NAME) ?></label></th>
				<td class="item">
					<input type="text" value="<?php echo get_option( 'siteurl' ) ?>" disabled="disabled" class="regular-text copy-area" style="color: #50575e;">
					<a href="#" class="button action btn-copy">Copy</a>
				</td>
			</tr>
			<tr class="user-first-name-wrap">
				<th><?php _e( 'Access key', QNAP_PLUGIN_NAME) ?></th>
				<td class="item">
					<input type="text" value="<?php echo esc_html($secret_key); ?>" disabled="disabled" class="regular-text copy-area" style="color: #50575e;">
					<a href="#" class="button action btn-copy">Copy</a>
				</td>
			</tr>
			<tr class="user-first-name-wrap">
				<th><?php _e( 'Maximum upload file size', QNAP_PLUGIN_NAME) ?></th>
				<td class="item">
					<strong><?php echo qnap_size_format( wp_max_upload_size() ) ?></strong>
				</td>
			</tr>
		</tbody>
	</table>

	<p><a href="https://www.qnap.com/en/how-to/tutorial/article/how-to-back-up-and-restore-a-wordpress-website-using-multi-application-recovery-service-mars" style="margin-top: 0px;">How to expand maximum upload file size in WordPress?</a></p>

	<h2 style="display: inline-block;">Job Log History</h2>
	<a href="#" class="button action" style="float: right; margin-top: 10px;" onclick="DeleteQnapLog()">Clear All</a>
	<table class="wp-list-table widefat fixed striped table-view-list">
		<thead style="display: table; width: 100%; table-layout: fixed;">
			<tr>
				<th scope="col" class="manage-column" style="width: 230px;"><span>Date</span></th>
				<th scope="col" class="manage-column" style="width: 230px;">Client IP</th>
				<th scope="col" class="manage-column">Content</th>
			</tr>
		</thead>
		<tbody id="the-list" style="display: block; max-height: 360px; overflow-y: scroll;">
			<?php if ( $logs ) : ?>
			<?php foreach ( $logs as $log ): ?>
			<tr style="display: table; width: 100%; table-layout: auto;">
				<td style="width: 230px;"><?php echo esc_html($log[0]); ?></td>
				<td style="width: 230px;"><?php echo esc_html($log[1]); ?></td>
				<td><?php echo esc_html($log[2]); ?></td>
			</tr>
			<?php endforeach; ?>
			<?php else: ?>
			<tr style="display: table; width: 100%; table-layout: auto;">
				<td colspan=3 style="text-align: center;">No data.</td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<h2 style="margin-top: 32px;">Information</h2>
	<p><a href="https://www.qnap.com/" style="margin-top: 0px;">QNAP Systems, Inc.</a></p>
	<p><a href="https://service.qnap.com/go-to" style="">Support</a></p>
</div>

<div class="qnap-container" style="display: none;">
	<div class="qnap-row">
		<div class="qnap-left">
			<div class="qnap-holder">
				<h1>
					<i class="qnap-icon-export"></i>
					<?php _e( 'Backups', QNAP_PLUGIN_NAME ); ?>
				</h1>

				<?php // include QNAP_TEMPLATES_PATH . '/common/report-problem.php'; ?>

				<?php if ( is_readable( QNAP_BACKUPS_PATH ) && is_writable( QNAP_BACKUPS_PATH ) ) : ?>
					<div id="qnap-backups-list">
						<?php include QNAP_TEMPLATES_PATH . '/backups/backups-list.php'; ?>
					</div>

					<form action="" method="post" id="qnap-export-form" class="qnap-clear">
						<div id="qnap-backups-create">
							<p class="qnap-backups-empty-spinner-holder qnap-hide">
								<span class="spinner"></span>
								<?php _e( 'Refreshing backup list...', QNAP_PLUGIN_NAME ); ?>
							</p>
							<p class="qnap-backups-empty <?php echo empty( $backups ) ? null : 'qnap-hide'; ?>">
								<?php _e( 'There are no backups available at this time, why not create a new one?', QNAP_PLUGIN_NAME ); ?>
							</p>
							<!-- <p>
								<a href="#" id="qnap-create-backup" class="qnap-button-green">
									<i class="qnap-icon-export"></i>
									<?php // _e( 'Create backup', QNAP_PLUGIN_NAME ); ?>
								</a>
							</p> -->
						</div>
						<input type="hidden" name="qnap_manual_export" value="1" />
					</form>

					<p><?php echo esc_html($secret_key); ?></p>
					<?php do_action( 'qnap_backups_left_end' ); ?>

				<?php else : ?>

					<?php include QNAP_TEMPLATES_PATH . '/backups/backups-permissions.php'; ?>

				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
