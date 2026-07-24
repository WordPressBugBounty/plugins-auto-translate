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
<div class="wrap" id="wpat_admin">
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
    <div class="wpat-admin-form">
        <div class="wpat-admin-header">
            <div class="wpat-admin-title" role="heading" aria-level="1">
                <span><?php esc_html_e( 'Automatic Translator', 'auto-translate' ); ?></span>
            </div>
            <div class="wpat-admin-header__center">
                <h2 class="nav-tab-wrapper">
                    <a href="?page=auto_translate&tab=language_settings" class="nav-tab <?php echo esc_attr( $vars['active_tab'] === 'language_settings' ? 'nav-tab-active' : '' ); ?>"><?php esc_html_e('Language settings', 'auto-translate'); ?></a>
                    <a href="?page=auto_translate&tab=placement_settings" class="nav-tab <?php echo esc_attr( $vars['active_tab'] === 'placement_settings' ? 'nav-tab-active' : '' ); ?>"><?php esc_html_e('Placement', 'auto-translate'); ?></a>
                    <a href="?page=auto_translate&tab=visual_settings" class="nav-tab <?php echo esc_attr( $vars['active_tab'] === 'visual_settings' ? 'nav-tab-active' : '' ); ?>"><?php esc_html_e('Styling', 'auto-translate'); ?></a>
                    <a href="?page=auto_translate&tab=advanced_settings" class="nav-tab <?php echo esc_attr( $vars['active_tab'] === 'advanced_settings' ? 'nav-tab-active' : '' ); ?>"><?php esc_html_e('Advanced', 'auto-translate'); ?></a>
                </h2>
            </div>
        </div>
        <div class="wpat-admin-body">
            <section class="wpat-dashboard-section" aria-label="<?php esc_attr_e( 'Plugin status', 'auto-translate' ); ?>">
                <div class="wpat-admin-links" aria-label="<?php esc_attr_e( 'Plugin links', 'auto-translate' ); ?>">
                    <a href="<?php echo esc_url( $vars['reviews_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( '⭐️ Love this plugin? Give us 5 stars on WordPress.org :)', 'auto-translate' ); ?></a>
                    <a href="<?php echo esc_url( $vars['support_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( '⭐️ Need help? Visit the support forum. ⭐️', 'auto-translate' ); ?></a>
                </div>
                <section class="wpat-dashboard-card <?php echo esc_attr( ! empty( $vars['is_live'] ) ? 'wpat-dashboard-card--live' : 'wpat-dashboard-card--preview' ); ?>" aria-labelledby="wpat-dashboard-title">
                    <div class="wpat-dashboard-card__icon" aria-hidden="true">
                        <span class="dashicons <?php echo esc_attr( ! empty( $vars['is_live'] ) ? 'dashicons-yes-alt' : 'dashicons-visibility' ); ?>"></span>
                    </div>
                    <div class="wpat-dashboard-card__content">
                        <div class="wpat-dashboard-card__status">
                            <div class="wpat-dashboard-card__meta">
                                <span class="wpat-dashboard-card__eyebrow"><?php esc_html_e( 'Plugin status', 'auto-translate' ); ?></span>
                                <span class="wpat-dashboard-card__pill">
                                    <?php echo esc_html( ! empty( $vars['is_live'] ) ? __( 'Visible to visitors', 'auto-translate' ) : __( 'Not visible to visitors', 'auto-translate' ) ); ?>
                                </span>
                            </div>
                            <h2 id="wpat-dashboard-title" class="wpat-dashboard-card__title">
                                <?php echo esc_html( ! empty( $vars['is_live'] ) ? __( 'Live', 'auto-translate' ) : __( 'Preview mode', 'auto-translate' ) ); ?>
                            </h2>
                            <p class="wpat-dashboard-card__description">
                                <?php
                                if ( ! empty( $vars['is_live'] ) ) {
                                    esc_html_e( 'Visitors can see and use Automatic Translator on the frontend wherever you place it.', 'auto-translate' );
                                } else {
                                    esc_html_e( 'Only administrators can see the translator. Visitors see nothing until you publish.', 'auto-translate' );
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="wpat-dashboard-card__actions">
                        <a class="button button-secondary" href="<?php echo esc_url( $vars['preview_site_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Preview site', 'auto-translate' ); ?></a>
                        <form method="post" action="<?php echo esc_url( $vars['go_live_action_url'] ); ?>" class="wpat-dashboard-card__form">
                            <input type="hidden" name="action" value="wpat_set_go_live" />
                            <input type="hidden" name="wpat_go_live" value="<?php echo esc_attr( ! empty( $vars['is_live'] ) ? '0' : '1' ); ?>" />
                            <?php wp_nonce_field( 'wpat_set_go_live' ); ?>
                            <button type="submit" class="button <?php echo esc_attr( ! empty( $vars['is_live'] ) ? '' : 'button-primary' ); ?>">
                                <?php echo esc_html( ! empty( $vars['is_live'] ) ? __( 'Switch to preview', 'auto-translate' ) : __( 'Go Live', 'auto-translate' ) ); ?>
                            </button>
                        </form>
                    </div>
                </section>
                <?php if ( ! empty( $vars['launch_overview_items'] ) && is_array( $vars['launch_overview_items'] ) ) : ?>
                    <?php $wpat_launch_checklist_is_open = ! empty( $vars['is_launch_checklist_open'] ); ?>
                    <section
                        class="wpat-launch-overview"
                        id="wpat-launch-overview"
                        aria-labelledby="wpat-launch-overview-title"
                        <?php echo $wpat_launch_checklist_is_open ? '' : 'hidden'; ?>
                    >
                        <div class="wpat-launch-overview__header">
                            <div class="wpat-launch-overview__heading">
                                <span class="wpat-dashboard-card__eyebrow"><?php esc_html_e( 'Setup checklist', 'auto-translate' ); ?></span>
                                <h2 id="wpat-launch-overview-title"><?php esc_html_e( 'Review before going live', 'auto-translate' ); ?></h2>
                                <p><?php esc_html_e( 'Check the main choices that affect what visitors will see first.', 'auto-translate' ); ?></p>
                            </div>
                            <a
                                class="wpat-launch-overview__toggle"
                                href="<?php echo esc_url( $vars['hide_launch_checklist_url'] ); ?>"
                                data-wpat-launch-checklist-toggle="collapsed"
                                aria-controls="wpat-launch-overview"
                                aria-expanded="<?php echo esc_attr( $wpat_launch_checklist_is_open ? 'true' : 'false' ); ?>"
                            >
                                <?php esc_html_e( 'Hide setup checklist', 'auto-translate' ); ?>
                            </a>
                        </div>
                        <div class="wpat-launch-overview__grid">
                            <?php foreach ( $vars['launch_overview_items'] as $wpat_launch_item ) : ?>
                                <?php $wpat_launch_is_ready = ! empty( $wpat_launch_item['is_ready'] ); ?>
                                <article class="wpat-launch-overview__item <?php echo esc_attr( $wpat_launch_is_ready ? 'is-ready' : 'is-incomplete' ); ?>">
                                    <span
                                        class="wpat-launch-overview__check"
                                        aria-label="<?php echo esc_attr( $wpat_launch_is_ready ? __( 'Complete', 'auto-translate' ) : __( 'Incomplete', 'auto-translate' ) ); ?>"
                                        role="img"
                                    >
                                        <span class="dashicons <?php echo esc_attr( $wpat_launch_is_ready ? 'dashicons-yes' : 'dashicons-minus' ); ?>" aria-hidden="true"></span>
                                    </span>
                                    <div class="wpat-launch-overview__copy">
                                        <h3><?php echo esc_html( $wpat_launch_item['title'] ?? '' ); ?></h3>
                                        <p><?php echo esc_html( $wpat_launch_item['summary'] ?? '' ); ?></p>
                                        <?php if ( ! empty( $wpat_launch_item['meta'] ) ) : ?>
                                            <span><?php echo esc_html( $wpat_launch_item['meta'] ); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="wpat-launch-overview__action">
                                        <a class="button button-secondary" href="<?php echo esc_url( $wpat_launch_item['action_url'] ?? '#' ); ?>">
                                            <?php echo esc_html( $wpat_launch_item['action_label'] ?? __( 'Edit', 'auto-translate' ) ); ?>
                                        </a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                    <div class="wpat-launch-overview-link" <?php echo $wpat_launch_checklist_is_open ? 'hidden' : ''; ?>>
                        <a
                            href="<?php echo esc_url( $vars['show_launch_checklist_url'] ); ?>"
                            data-wpat-launch-checklist-toggle="open"
                            aria-controls="wpat-launch-overview"
                            aria-expanded="<?php echo esc_attr( $wpat_launch_checklist_is_open ? 'true' : 'false' ); ?>"
                        >
                            <?php esc_html_e( 'Show setup checklist', 'auto-translate' ); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </section>
            <?php
            $wpat_active_tab_labels = array(
                'language_settings'  => __( 'Language settings', 'auto-translate' ),
                'placement_settings' => __( 'Placement', 'auto-translate' ),
                'visual_settings'    => __( 'Styling', 'auto-translate' ),
                'advanced_settings'  => __( 'Advanced', 'auto-translate' ),
            );
            $wpat_active_tab_label = $wpat_active_tab_labels[ $vars['active_tab'] ] ?? __( 'Settings', 'auto-translate' );
            ?>
            <section class="wpat-tab-section" aria-labelledby="wpat-tab-section-title">
                <div class="wpat-tab-section__header">
                    <h2 id="wpat-tab-section-title"><?php echo esc_html( $wpat_active_tab_label ); ?></h2>
                </div>
                <form id="wpat-settings-form" method="post" action="options.php">
                    <input type="hidden" name="wpat_lifecycle_last_saved_tab" value="<?php echo esc_attr( $vars['active_tab'] ); ?>" />
        <?php
            if( $vars['active_tab'] == 'language_settings' ) {
                settings_fields( 'auto-translate-language-settings-group' );
                do_settings_sections( 'auto-translate-language-settings-group' );
                require 'auto-translate-admin-language-settings-display.php';
            }
            elseif( $vars['active_tab'] == 'placement_settings' ) {
                settings_fields( 'auto-translate-placement-settings-group' );
                do_settings_sections( 'auto-translate-placement-settings-group' );
                require 'auto-translate-admin-placement-settings-display.php';
            }
            elseif( $vars['active_tab'] == 'visual_settings' ) {
                settings_fields( 'auto-translate-visual-settings-group' );
                do_settings_sections( 'auto-translate-visual-settings-group' );
                require 'auto-translate-admin-visual-settings-display.php'; 
            }
            elseif( $vars['active_tab'] == 'advanced_settings' ) {
                settings_fields( 'auto-translate-advanced-settings-group' );
                do_settings_sections( 'auto-translate-advanced-settings-group' );
                require 'auto-translate-admin-advanced-settings-display.php'; 
            }
        ?>                
                </form>
                <div class="wpat-admin-footer">
                    <button type="submit" form="wpat-settings-form" class="button button-primary button-hero wpat-save-button"><?php esc_html_e( 'Save changes', 'auto-translate' ); ?></button>
                </div>
            </section>
        </div>
    </div>
</div>
<?php endif ?>
