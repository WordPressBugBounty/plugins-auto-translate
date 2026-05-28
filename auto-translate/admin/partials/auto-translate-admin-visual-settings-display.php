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
$wpat_supported_languages = Auto_Translate_Config::get_supported_languages();
$wpat_minimalist    = $vars['tabs']['visual_settings']['minimalist'];
$wpat_label_mode    = $vars['tabs']['language_setting']['wpat_language_name_display'];
$wpat_columns       = $vars['tabs']['visual_settings']['columns'];
$wpat_widget_type   = $vars['tabs']['visual_settings']['wpat_widget_type'];
$wpat_base_language = Auto_Translate_Config::normalize_lang_code( (string) ( $vars['tabs']['language_setting']['wpat_base_language'] ?? 'en' ) );
$wpat_selected_languages = $vars['tabs']['language_setting']['wpat_supported_languages'] ?? array();
$wpat_language_order = (string) ( $vars['tabs']['language_setting']['wpat_language_order'] ?? '' );
$wpat_preview_flags = is_array( $wpat_minimalist['wpat_language_flags'] ?? null ) ? $wpat_minimalist['wpat_language_flags'] : array();

if ( ! is_array( $wpat_selected_languages ) || empty( $wpat_selected_languages ) || in_array( 'all', $wpat_selected_languages, true ) ) {
    $wpat_preview_lang_codes = array_keys( $wpat_supported_languages );
} else {
    $wpat_preview_lang_codes = array_values(
        array_filter(
            array_map(
                array( 'Auto_Translate_Config', 'normalize_lang_code' ),
                $wpat_selected_languages
            ),
            static function ( $lang_code ) use ( $wpat_supported_languages ) {
                return isset( $wpat_supported_languages[ $lang_code ] );
            }
        )
    );
}

if ( '' !== $wpat_language_order ) {
    $wpat_ordered_codes = array_values(
        array_filter(
            array_map(
                array( 'Auto_Translate_Config', 'normalize_lang_code' ),
                array_map( 'trim', explode( ',', $wpat_language_order ) )
            ),
            static function ( $lang_code ) use ( $wpat_preview_lang_codes ) {
                return in_array( $lang_code, $wpat_preview_lang_codes, true );
            }
        )
    );

    $wpat_remaining_codes = array_values( array_diff( $wpat_preview_lang_codes, $wpat_ordered_codes ) );
    $wpat_preview_lang_codes = array_merge( $wpat_ordered_codes, $wpat_remaining_codes );
}

if ( isset( $wpat_supported_languages[ $wpat_base_language ] ) && ! in_array( $wpat_base_language, $wpat_preview_lang_codes, true ) ) {
    array_unshift( $wpat_preview_lang_codes, $wpat_base_language );
}

$wpat_preview_lang_codes = array_values( array_unique( $wpat_preview_lang_codes ) );
$wpat_languages_data = Auto_Translate_Languages::get_languages_data( $wpat_preview_lang_codes, (string) $wpat_label_mode, $wpat_preview_flags );
?>
<script>
    var wpatLanguagesCountries = <?php echo wp_json_encode( $wpat_languages_data ); ?>;
    var wpatWidgetType = <?php echo wp_json_encode( $wpat_widget_type )?>;
    var wpatBaseLanguage = <?php echo wp_json_encode( $wpat_base_language ); ?>;
</script>
<div id="wpat_admin_visual" class="wpat-settings-panel wpat_widget_type_<?php echo esc_attr( $wpat_widget_type )?>">
    <input type="hidden" name="wpat_widget_type" value="minimalist"/>

    <div class="wpat-card wpat-styling-preview-card">
        <h3 class="wpat-card-title"><?php esc_html_e('Live preview', 'auto-translate'); ?></h3>
        <div class="wpat-card-content">
            <div class="wpat-preview-frame">
                <div id="wpat_dropdown_preview" class="styling-preview col-2-3" data-preload-class="styling-preview col-2-3"
                    style="
                        --wpat-preview-color-1: #000000;
                        --wpat-preview-color-2: #000000;
                        --wpat-preview-border-radius: 0px;
                        --wpat-preview-border-thickness: 1px;
                        --wpat-preview-border-color: #ffffff;
                        --wpat-preview-font-color: #ffffff;
                        --wpat-preview-font-family: inherit;
                        --wpat-preview-dropdown-border-thickness: 1px;
                        --wpat-preview-dropdown-border-color: #000000;
                        --wpat-preview-dropdown-background-color: #ffffff;
                        --wpat-preview-dropdown-font-color: #000000;
                        --wpat-preview-dropdown-font-family: inherit;
                        --wpat-preview-dropdown-font-selected-color: #356177;
                        --wpat-preview-dropdown-hover-color: #356177;
                        --wpat-preview-dropdown-font-hover-color: #ffffff;
                    ">
                    <div class="auto_translate_button_wrapper" data-wpat-preview="true">
                        <?php
                            $wpat_min_style = $wpat_minimalist['wpat_min_style'];
                            $wpat_min_layout = $wpat_minimalist['wpat_min_layout'];
                            $wpat_min_icon = $wpat_minimalist['wpat_min_icon'];
                            $wpat_min_txt_display = $wpat_minimalist['wpat_min_txt_display'];
                            $wpat_min_txt_underline = $wpat_minimalist['wpat_min_txt_underline'];
                            $wpat_min_border_thickness = $wpat_minimalist['wpat_min_border_thickness'];
                            $wpat_min_border_color = $wpat_minimalist['wpat_min_border_color'];
                            $wpat_min_background_color = $wpat_minimalist['wpat_min_background_color'];
                            $wpat_min_font_color = $wpat_minimalist['wpat_min_font_color'];
                            $wpat_min_font_family = $wpat_minimalist['wpat_min_font_family'];
                            $wpat_min_hover_color = $wpat_minimalist['wpat_min_hover_color'];
                            $wpat_min_font_hover_color = $wpat_minimalist['wpat_min_font_hover_color'];
                            $wpat_min_chevron = $wpat_minimalist['wpat_min_chevron'];
                            require( plugin_dir_path( dirname( __FILE__ ) ) . '../public/partials/widgets/auto-translate-widget-minimalist.php' );
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require 'auto-translate-admin-visual-settings-minimalist-display.php'; ?>
</div>
