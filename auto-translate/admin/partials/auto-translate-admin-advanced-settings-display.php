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
$wpat_data = $vars['tabs']['advanced_settings'];
?>
<div class="wpat-settings-panel">
<div class="wpat-card wpat-advanced-card">
    <h3 class="wpat-card-title"><?php esc_html_e( 'Behavior', 'auto-translate' ); ?></h3>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">
                <?php esc_html_e( 'Auto-translate for visitors', 'auto-translate' ); ?><br/>
                <small><?php esc_html_e( 'Automatically translate the site into each visitor\'s browser language on first visit. Visitors can still switch manually at any time.', 'auto-translate' ); ?></small>
            </th>
            <td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>">
                <input type="hidden" id="wpat_auto_detect" name="wpat_auto_detect" value="<?php echo esc_attr( $wpat_data['wpat_auto_detect'] ); ?>"/>
                <label class="wpat-toggle" for="wpat_auto_detect_toggle">
                    <input
                        type="checkbox"
                        id="wpat_auto_detect_toggle"
                        <?php checked( $wpat_data['wpat_auto_detect'], 'enabled' ); ?>
                    />
                    <span class="wpat-toggle-slider" aria-hidden="true"></span>
                </label>
            </td>
        </tr>
    </table>
</div>

<div class="wpat-card wpat-advanced-card">
    <h3 class="wpat-card-title"><?php esc_html_e( 'Translation exclusions', 'auto-translate' ); ?></h3>
    <div class="wpat-card-content wpat-advanced-css-content">
        <div class="wpat-advanced-info">
            <span class="dashicons dashicons-info"></span>
            <span><?php esc_html_e( 'Add one CSS selector per line. Matching elements will be marked with notranslate and translate="no" before Google Translate initializes.', 'auto-translate' ); ?></span>
        </div>
        <div class="wpat-advanced-info wpat-advanced-idea">
            <span class="dashicons dashicons-lightbulb"></span>
            <span><?php esc_html_e( 'Tip: You can also exclude elements from translation by adding the class `notranslate` and the property `translate="no"`.', 'auto-translate' ); ?></span>
        </div>
        <textarea id="wpat_excluded_selectors" name="wpat_excluded_selectors" rows="6" class="large-text code" placeholder=".no-translate&#10;#site-footer&#10;article .author-bio"><?php echo esc_textarea( $wpat_data['wpat_excluded_selectors'] ); ?></textarea>
        <p class="description"><?php esc_html_e( 'Selector validation runs in the browser when saving this screen. Invalid selectors are ignored on the frontend.', 'auto-translate' ); ?></p>
    </div>
</div>

<div class="wpat-card wpat-advanced-card">
    <h3 class="wpat-card-title"><?php esc_html_e( 'Custom CSS', 'auto-translate' ); ?></h3>
    <div class="wpat-card-content wpat-advanced-css-content">
        <div class="wpat-advanced-info">
            <span class="dashicons dashicons-info"></span>
            <span><?php esc_html_e( 'These rules apply to the selector widget only. The Google Translate language list iframe cannot be styled here.', 'auto-translate' ); ?></span>
        </div>
        <textarea id="wpat_custom_css" name="wpat_custom_css" rows="10" class="large-text code" placeholder="/* Custom CSS here */&#10;.auto_translate_minimalist .wpat_lang_item.wpat_lang_selected {&#10;  padding: 10px 14px;&#10;  font-weight: 600;&#10;}"><?php echo esc_textarea( $wpat_data['wpat_custom_css'] ); ?></textarea>
    </div>
</div>

<div class="wpat-card wpat-advanced-card wpat-danger-zone-card">
    <h3 class="wpat-card-title"><span class="dashicons dashicons-warning"></span><?php esc_html_e( 'Danger zone', 'auto-translate' ); ?></h3>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">
                <?php esc_html_e( 'Delete all data on uninstall', 'auto-translate' ); ?><br/>
                <small><?php esc_html_e( 'When enabled, all plugin settings are permanently erased when you uninstall the plugin. This action cannot be undone.', 'auto-translate' ); ?></small>
            </th>
            <td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>">
                <input type="hidden" name="wpat_delete_data_on_uninstall" value=""/>
                <label class="wpat-danger-confirm" for="wpat_delete_data_on_uninstall">
                    <input type="checkbox" id="wpat_delete_data_on_uninstall" name="wpat_delete_data_on_uninstall" value="on" <?php checked( $wpat_data['wpat_delete_data_on_uninstall'], 'on' ); ?>/>
                    <span><?php esc_html_e( 'I understand — permanently delete all plugin data on uninstall', 'auto-translate' ); ?></span>
                </label>
            </td>
        </tr>
    </table>
</div>
</div>
