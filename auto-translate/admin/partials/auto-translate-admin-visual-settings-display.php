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
global $wpat_widget_types;
global $wpat_supported_languages;
$wpat_classic       = $vars['tabs']['visual_settings']['classic'];
$wpat_minimalist    = $vars['tabs']['visual_settings']['minimalist'];
$wpat_columns       = $vars['tabs']['visual_settings']['columns'];
$wpat_widget_type   = $vars['tabs']['visual_settings']['wpat_widget_type'];
$wpat_demo_langs    = [
    'ar' => __('Arabic', 'auto-translate'), 
    'bn' => __('Bengali', 'auto-translate'), 
    'fr' => __('French', 'auto-translate'), 
    'hi' => __('Hindi', 'auto-translate'), 
    'id' => __('Indonesian', 'auto-translate'), 
    'pt' => __('Portuguese', 'auto-translate'), 
    'ru' => __('Russian', 'auto-translate'), 
    'es' => __('Spanish', 'auto-translate'), 
    'en' => __('English', 'auto-translate'),
];
$wpat_languages_data = Auto_Translate_Languages::get_languages_data(array_keys($wpat_demo_langs));
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<script>
    var wpatLanguagesCountries = <?php echo wp_json_encode( $wpat_languages_data ); ?>;
    var wpatWidgetType = <?php echo wp_json_encode( $wpat_widget_type )?>;
    var wpatBaseLanguage = 'en';
</script>
<div id="wpat_admin_visual" class="wpat_widget_type_<?php echo esc_attr( $wpat_widget_type )?>">
    <h2><?php esc_html_e('Widget Styling', 'auto-translate'); ?></h2>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">
                <?php esc_html_e('Widget type', 'auto-translate'); ?><br/>
                <small><?php esc_html_e('This is the widget front-end type','auto-translate'); ?></small>
            </th>
            <td colspan="<?php echo esc_attr( $wpat_columns ); ?>">
            <select name="wpat_widget_type">
            <?php foreach ( $wpat_widget_types as $wpat_key => $wpat_type ) : ?>
                <option value="<?php echo esc_attr( $wpat_key ); ?>" <?php selected( $wpat_widget_type, $wpat_key ); ?>><?php echo esc_html( $wpat_type ); ?></option>
            <?php endforeach;?>
            </select>
            </td>
            <td colspan="2">
                <div id="wpat_dropdown_preview" class="styling-preview col-2-3" data-preload-class="styling-preview col-2-3">
                    <div class="wpat_widget_type_classic_only">
                        <div id="google_translate_element_<?php echo absint( wp_rand() ); ?>" class="google_translate_element <?php echo esc_attr( $wpat_classic['wpat_widget_size'] ); ?> dashicons-before">
                            <div class="skiptranslate goog-te-gadget" dir="ltr">
                                <div id=":0.targetLanguage" class="goog-te-gadget-simple">
                                    <span class="goog-te-gadget-icon" aria-hidden="true"></span>
                                    <span style="vertical-align: middle;">
                                        <a aria-haspopup="true" class="goog-te-menu-value <?php echo esc_attr( $wpat_classic['wpat_show_icon'] === 'on' ? $wpat_classic['wpat_button_icon'] : '' ); ?>" href="javascript:void(0)">
                                            <span><?php esc_html_e('Select Language', 'auto-translate');?></span>
                                            <span style="border-left: 1px solid rgb(187, 187, 187);">&#8203;</span>
                                            <span aria-hidden="true" style="color: rgb(118, 118, 118);">▼</span>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wpat_widget_type_minimalist_only">
                        <div id="auto_translate_button_wrapper">
                            <?php 
                                $wpat_min_style = $wpat_minimalist['wpat_min_style'];
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
                                require_once( plugin_dir_path( dirname( __FILE__ ) ) . '../public/partials/widgets/auto-translate-widget-minimalist.php' ); 
                            ?>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <div class="wpat_widget_type_classic_only">
        <?php require 'auto-translate-admin-visual-settings-classic-display.php'; ?>
    </div>
    <div class="wpat_widget_type_minimalist_only">
        <?php require 'auto-translate-admin-visual-settings-minimalist-display.php'; ?>
    </div>
</div>
