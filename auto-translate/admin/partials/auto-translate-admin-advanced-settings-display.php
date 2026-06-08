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
        <tr valign="top">
            <th scope="row">
                <?php esc_html_e( 'Source language override', 'auto-translate' ); ?><br/>
                <small><?php esc_html_e( 'By default, Automatic Translator uses your WordPress Site Language. Change this only if your site content is written in a different language.', 'auto-translate' ); ?></small>
            </th>
            <td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>">
                <select id="wpat_base_language" name="wpat_base_language">
                    <option value="" <?php selected( (string) $wpat_data['wpat_base_language'], '' ); ?>>
                        <?php
                        echo esc_html(
                            sprintf(
                                /* translators: %s: resolved language name. */
                                __( 'Use WordPress Site Language (%s)', 'auto-translate' ),
                                $wpat_data['supported_languages'][ $wpat_data['wpat_resolved_base_language'] ] ?? $wpat_data['wpat_resolved_base_language']
                            )
                        );
                        ?>
                    </option>
                    <?php foreach ( $wpat_data['supported_languages'] as $wpat_code => $wpat_lang ) : ?>
                        <option value="<?php echo esc_attr( $wpat_code ); ?>" <?php selected( (string) $wpat_data['wpat_base_language'], (string) $wpat_code ); ?>><?php echo esc_html( $wpat_lang ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <span class="wpat-help-label-wrap">
                    <?php esc_html_e('Language label style', 'auto-translate'); ?>
                    <button
                        type="button"
                        class="wpat-help-trigger"
                        aria-label="<?php esc_attr_e( 'More info about language label styles', 'auto-translate' ); ?>"
                        aria-haspopup="dialog"
                        aria-controls="wpat-language-label-style-help-modal"
                    >
                        <span class="dashicons dashicons-editor-help" aria-hidden="true"></span>
                    </button>
                </span><br/>
                <small><?php esc_html_e('How language names appear in the selector dropdown.', 'auto-translate'); ?></small>
            </th>
            <td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>">
                <div class="wpat-pill-group" role="radiogroup" aria-label="<?php esc_attr_e( 'Language names', 'auto-translate' ); ?>">
                    <label class="wpat-pill">
                        <input type="radio" name="wpat_language_name_display" value="native" <?php checked( (string) $wpat_data['wpat_language_name_display'], 'native' ); ?> />
                        <span><?php esc_html_e('Native names', 'auto-translate'); ?></span>
                    </label>
                    <label class="wpat-pill">
                        <input type="radio" name="wpat_language_name_display" value="english" <?php checked( (string) $wpat_data['wpat_language_name_display'], 'english' ); ?> />
                        <span><?php esc_html_e('Localized names', 'auto-translate'); ?></span>
                    </label>
                </div>
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
            <span><?php esc_html_e( 'These rules are injected directly into the isolated minimalist selector shadow roots, including the detached dropdown overlay.', 'auto-translate' ); ?></span>
        </div>
        <textarea id="wpat_min_custom_css" name="wpat_min_custom_css" rows="10" class="large-text code" placeholder="/* Minimalist selector shadow CSS here */&#10;.wpat_lang_item.wpat_lang_selected {&#10;  padding: 10px 14px;&#10;  font-weight: 500;&#10;}"><?php echo esc_textarea( $wpat_data['wpat_min_custom_css'] ); ?></textarea>
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

<div id="wpat-language-label-style-help-modal" class="wpat-help-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="wpat-language-help-title">
    <div class="wpat-help-modal__backdrop" data-wpat-close-help-modal></div>
    <div class="wpat-help-modal__dialog">
        <button type="button" class="wpat-help-modal__close" aria-label="<?php esc_attr_e( 'Close', 'auto-translate' ); ?>" data-wpat-close-help-modal>
            <span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
        </button>
        <h4 id="wpat-language-help-title"><?php esc_html_e( 'Language label styles', 'auto-translate' ); ?></h4>
        <p><?php esc_html_e( 'Native names show each language in its own writing. Localized names show language names in the current WordPress locale.', 'auto-translate' ); ?></p>
        <div class="wpat-help-modal__examples">
            <div class="wpat-help-modal__example">
                <strong><?php esc_html_e( 'Native names', 'auto-translate' ); ?></strong>
                <ul>
                    <li>العربية</li>
                    <li>Français</li>
                    <li>Русский</li>
                    <li>Deutsch</li>
                    <li>Português</li>
                </ul>
            </div>
            <div class="wpat-help-modal__example">
                <strong><?php esc_html_e( 'Localized names', 'auto-translate' ); ?></strong>
                <ul>
                    <li><?php esc_html_e( 'Arabic', 'auto-translate' ); ?></li>
                    <li><?php esc_html_e( 'French', 'auto-translate' ); ?></li>
                    <li><?php esc_html_e( 'Russian', 'auto-translate' ); ?></li>
                    <li><?php esc_html_e( 'German', 'auto-translate' ); ?></li>
                    <li><?php esc_html_e( 'Portuguese', 'auto-translate' ); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>
