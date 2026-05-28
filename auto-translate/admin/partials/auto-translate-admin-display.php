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
    <form method="post" action="options.php" class="wpat-admin-form">
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
            <button type="submit" class="button button-primary button-hero wpat-save-button"><?php esc_html_e( 'Save changes', 'auto-translate' ); ?></button>
        </div>
        <div class="wpat-admin-body">                
            <div class="wpat-admin-links" aria-label="<?php esc_attr_e( 'Plugin links', 'auto-translate' ); ?>">
                <a href="<?php echo esc_url( $vars['reviews_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( '⭐️ Love this plugin? Give us 5 stars on WordPress.org :)', 'auto-translate' ); ?></a>
                <a href="<?php echo esc_url( $vars['support_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( '⭐️ Need help? Visit the support forum. ⭐️', 'auto-translate' ); ?></a>
            </div>
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
        </div>
        <div class="wpat-admin-footer">
            <button type="submit" class="button button-primary button-hero wpat-save-button"><?php esc_html_e( 'Save changes', 'auto-translate' ); ?></button>
        </div>
    </form>
</div>
<?php endif ?>
