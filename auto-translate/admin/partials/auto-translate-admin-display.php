<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://pampa.dev
 * @since      1.0.0
 *
 * @package    Auto_Translate
 * @subpackage Auto_Translate/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php if( is_admin()): ?>
<div class="wrap" id="wpat_admin" data-wpat-draft-channel="wpat-settings-draft" data-wpat-draft-storage-key="wpat_settings_draft" data-wpat-draft-debounce="700" data-wpat-show-publish-reminder="<?php echo esc_attr( ! empty( $vars['show_publish_reminder'] ) ? 'true' : 'false' ); ?>" data-wpat-publish-readiness="<?php echo esc_attr( wp_json_encode( $vars['publish_readiness'] ?? array() ) ); ?>">
    <h1 class="wp-heading-inline screen-reader-text"><?php esc_html_e( 'Automatic Translator', 'auto-translate' ); ?></h1>
    <hr class="wp-header-end" />
    <?php if ( ! empty( $vars['classic_widget_migrated_notice'] ) ) : ?>
    <div class="notice notice-warning">
        <p><?php esc_html_e( 'Your site was using the legacy Classic widget. It has been migrated automatically to the Custom selector in this version.', 'auto-translate' ); ?></p>
    </div>
    <?php endif; ?>
    <?php if ( ! empty( $vars['mode_updated'] ) ) : ?>
    <div class="notice notice-success is-dismissible">
        <p>
            <?php
            if ( 'live' === ( $vars['updated_mode'] ?? '' ) ) {
                esc_html_e( 'Automatic Translator is now live for all visitors.', 'auto-translate' );
            } else {
                esc_html_e( 'Automatic Translator is back in preview mode. Only admins can see it on the frontend.', 'auto-translate' );
            }
            ?>
        </p>
    </div>
    <?php endif; ?>
    <?php if ( ! empty( $vars['show_plugin_links_notice'] ) ) : ?>
    <div class="notice notice-info is-dismissible wpat-plugin-links-notice" aria-label="<?php esc_attr_e( 'Plugin links', 'auto-translate' ); ?>">
        <p>
            <a href="<?php echo esc_url( $vars['reviews_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( '⭐️ Love this plugin? Give us 5 stars on WordPress.org :)', 'auto-translate' ); ?></a>
            <span aria-hidden="true">&nbsp; · &nbsp;</span>
            <a href="<?php echo esc_url( $vars['support_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( '⭐️ Need help? Visit the support forum. ⭐️', 'auto-translate' ); ?></a>
        </p>
    </div>
    <?php endif; ?>
    <div class="wpat-admin-form">
        <div class="wpat-admin-header">
            <div class="wpat-admin-title" role="heading" aria-level="1">
                <span><?php esc_html_e( 'Automatic Translator', 'auto-translate' ); ?></span>
                <span class="wpat-admin-title__status">
                    <span aria-hidden="true"></span>
                    <?php echo esc_html( ! empty( $vars['is_live'] ) ? __( 'Visible to visitors', 'auto-translate' ) : __( 'Not visible to visitors', 'auto-translate' ) ); ?>
                </span>
            </div>
            <div class="wpat-admin-header__actions">
                <span data-wpat-draft-status data-wpat-publish-announcement aria-live="polite" aria-atomic="true"></span>
                <span class="wpat-admin-header__discard-slot">
                    <button type="button" class="button button-link" data-wpat-draft-retry hidden><?php esc_html_e( 'Discard changes', 'auto-translate' ); ?></button>
                    <button type="button" class="button button-secondary" data-wpat-draft-discard hidden disabled><?php esc_html_e( 'Discard changes', 'auto-translate' ); ?></button>
                </span>
                <button type="button" class="button button-secondary" data-wpat-draft-save disabled><?php echo esc_html( ! empty( $vars['is_live'] ) ? __( 'Save', 'auto-translate' ) : __( 'Save draft', 'auto-translate' ) ); ?></button>
                <a class="button button-secondary" data-wpat-preview-site href="<?php echo esc_url( $vars['preview_site_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Preview site', 'auto-translate' ); ?></a>
                <form method="post" action="<?php echo esc_url( $vars['go_live_action_url'] ); ?>" data-wpat-publish-form>
                    <input type="hidden" name="action" value="wpat_set_go_live" />
                    <input type="hidden" name="wpat_go_live" value="<?php echo esc_attr( ! empty( $vars['is_live'] ) ? '0' : '1' ); ?>" />
					<input type="hidden" name="wpat_publish_confirmed" value="0" />
                    <?php wp_nonce_field( 'wpat_set_go_live' ); ?>
                    <button type="submit" class="button <?php echo esc_attr( ! empty( $vars['is_live'] ) ? '' : 'button-primary' ); ?>" data-wpat-publish-submit>
                        <?php echo esc_html( ! empty( $vars['is_live'] ) ? __( 'Unpublish', 'auto-translate' ) : __( 'Publish', 'auto-translate' ) ); ?>
                    </button>
					<div class="wpat-publish-reminder" data-wpat-publish-reminder hidden role="dialog" aria-modal="true" aria-labelledby="wpat-publish-reminder-title" aria-describedby="wpat-publish-reminder-description">
						<div class="wpat-publish-reminder__panel" tabindex="-1">
							<h2 id="wpat-publish-reminder-title"><?php esc_html_e( 'Ready to publish?', 'auto-translate' ); ?></h2>
							<p id="wpat-publish-reminder-description"><?php esc_html_e( 'Double-check these settings before making Automatic Translator visible to visitors.', 'auto-translate' ); ?></p>
							<ul class="wpat-publish-checklist" aria-label="<?php esc_attr_e( 'Publish readiness checks', 'auto-translate' ); ?>">
								<li data-wpat-publish-check="languages" data-wpat-publish-complete="false">
									<strong><?php esc_html_e( 'Languages', 'auto-translate' ); ?></strong>
									<span data-wpat-publish-check-status></span>
									<span data-wpat-publish-check-detail></span>
								</li>
								<li data-wpat-publish-check="placement" data-wpat-publish-complete="false">
									<strong><?php esc_html_e( 'Placement', 'auto-translate' ); ?></strong>
									<span data-wpat-publish-check-status></span>
									<span data-wpat-publish-check-detail></span>
								</li>
								<li data-wpat-publish-check="styling" data-wpat-publish-complete="false">
									<strong><?php esc_html_e( 'Styling', 'auto-translate' ); ?></strong>
									<span data-wpat-publish-check-status></span>
									<span data-wpat-publish-check-detail></span>
								</li>
							</ul>
							<label><input type="hidden" name="wpat_publish_reminder_enabled" value="0" /><input type="checkbox" name="wpat_publish_reminder_enabled" value="1" checked /> <?php esc_html_e( 'Always show this reminder before publishing.', 'auto-translate' ); ?></label>
							<div class="wpat-publish-reminder__actions">
								<button type="button" class="button button-secondary" data-wpat-publish-cancel><?php esc_html_e( 'Cancel', 'auto-translate' ); ?></button>
								<button type="button" class="button button-primary" data-wpat-publish-confirm><?php esc_html_e( 'Publish', 'auto-translate' ); ?></button>
							</div>
						</div>
					</div>
                </form>
            </div>
        </div>
        <div class="wpat-admin-workspace">
            <?php require 'auto-translate-admin-preview-display.php'; ?>
            <div class="wpat-admin-body">
            <div class="nav-tab-wrapper wpat-settings-tabs">
                <a href="?page=auto_translate&tab=visual_settings" class="nav-tab <?php echo esc_attr( $vars['active_tab'] === 'visual_settings' ? 'nav-tab-active' : '' ); ?>"<?php echo 'visual_settings' === $vars['active_tab'] ? ' aria-current="page"' : ''; ?>><?php esc_html_e('Styling', 'auto-translate'); ?></a>
                <a href="?page=auto_translate&tab=language_settings" class="nav-tab <?php echo esc_attr( $vars['active_tab'] === 'language_settings' ? 'nav-tab-active' : '' ); ?>"<?php echo 'language_settings' === $vars['active_tab'] ? ' aria-current="page"' : ''; ?>><?php esc_html_e('Languages', 'auto-translate'); ?></a>
                <a href="?page=auto_translate&tab=placement_settings" class="nav-tab <?php echo esc_attr( $vars['active_tab'] === 'placement_settings' ? 'nav-tab-active' : '' ); ?>"<?php echo 'placement_settings' === $vars['active_tab'] ? ' aria-current="page"' : ''; ?>><?php esc_html_e('Placement', 'auto-translate'); ?></a>
                <a href="?page=auto_translate&tab=advanced_settings" class="nav-tab <?php echo esc_attr( $vars['active_tab'] === 'advanced_settings' ? 'nav-tab-active' : '' ); ?>"<?php echo 'advanced_settings' === $vars['active_tab'] ? ' aria-current="page"' : ''; ?>><?php esc_html_e('Advanced', 'auto-translate'); ?></a>
            </div>
            <section class="wpat-tab-section" aria-labelledby="wpat-tab-section-title">
                <div class="wpat-settings-tab-status screen-reader-text" data-wpat-settings-tab-status role="status" aria-live="polite" aria-atomic="true"></div>
                <?php require 'auto-translate-admin-settings-tab-display.php'; ?>
            </section>
            </div>
        </div>
    </div>
</div>
<?php endif ?>
