<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://pampa.dev
 * @since      1.2.0
 *
 * @package    Auto_Translate
 * @subpackage Auto_Translate/public/partials
 */
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<style>
    .google_translate_element .goog-te-gadget-simple {
    background-image: linear-gradient(135deg, <?php echo esc_html( $wpat_color_1 )?> 0, <?php echo esc_html( $wpat_color_2 )?> 100%);
    border-radius: <?php echo absint( $wpat_border_radius )?>px !important;
    border: <?php echo absint( $wpat_border_thickness )?>px solid <?php echo esc_html( $wpat_border_color )?> !important;
    }
    .google_translate_element .goog-te-menu-value {
        color: <?php echo esc_html( $wpat_font_color )?> !important;
        <?php if( $wpat_font_family != '' ): ?>
        font-family: <?php echo esc_html( $wpat_font_family )?> !important;
        <?php endif; ?>
    }
    .google_translate_element{
        display:none!important;
    }
    <?php if ( '' !== $wpat_custom_css ) : ?>
    <?php echo esc_html( $wpat_custom_css ); ?>
    <?php endif; ?>
</style>
<script>
window.wpatConfig = <?php echo wp_json_encode(
	array(
		'languages'                => $languages_data,
		'buttonIcon'               => 'on' === $wpat_show_icon ? $wpat_button_icon : '',
		'dropdownBorderThickness'  => absint( $wpat_dropdown_border_thickness ),
		'dropdownBorderColor'      => $wpat_dropdown_border_color,
		'dropdownBackgroundColor'  => $wpat_dropdown_background_color,
		'dropdownHoverColor'       => $wpat_dropdown_hover_color,
		'dropdownFontHoverColor'   => $wpat_dropdown_font_hover_color,
		'dropdownFontSelectedColor'=> $wpat_dropdown_font_selected_color,
		'dropdownFontColor'        => $wpat_dropdown_font_color,
		'dropdownFontFamily'       => $wpat_dropdown_font_family,
		'widgetType'               => $wpat_widget_type,
		'baseLanguage'             => $wpat_base_language,
		'dropdownShadow'           => 'on' === $wpat_dropdown_shadow,
		'autoDetect'               => $wpat_auto_detect,
		'hostLanguage'             => $wpat_host_language,
		'wrapperSelector'          => (string) get_option( 'wpat_wrapper_selector', '' ),
		'excludedSelectors'        => array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) get_option( 'wpat_excluded_selectors', '' ) ) ) ) ),
		'googleMountId'            => $wpat_google_mount_id,
		'googleScriptUrl'          => $wpat_google_script_url,
		'messages'                 => array(
			'loading' => __( 'Switching language.', 'auto-translate' ),
			'error'   => __( 'Translation unavailable. Please try again.', 'auto-translate' ),
		),
	)
); ?>;
var wpatLanguagesCountries = window.wpatConfig.languages;
var wpatButtonIcon = window.wpatConfig.buttonIcon;
var wpatDropdownBorderThickness = window.wpatConfig.dropdownBorderThickness;
var wpatDropdownBorderColor = window.wpatConfig.dropdownBorderColor;
var wpatDropdownBackgroundColor = window.wpatConfig.dropdownBackgroundColor;
var wpatDropdownHoverColor = window.wpatConfig.dropdownHoverColor;
var wpatDropdownFontHoverColor = window.wpatConfig.dropdownFontHoverColor;
var wpatDropdownFontSelectedColor = window.wpatConfig.dropdownFontSelectedColor;
var wpatDropdownFontColor = window.wpatConfig.dropdownFontColor;
var wpatDropdownFontFamily = window.wpatConfig.dropdownFontFamily;
var wpatWidgetType = window.wpatConfig.widgetType;
var wpatBaseLanguage = window.wpatConfig.baseLanguage;
var wpatDropdownShadow = window.wpatConfig.dropdownShadow;
var wpatAutoDetect = window.wpatConfig.autoDetect;
var wpatHostLanguage = window.wpatConfig.hostLanguage;
</script>
