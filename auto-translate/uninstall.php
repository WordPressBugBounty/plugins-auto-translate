<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @link       https://pampa.dev
 * @since      1.0.0
 *
 * @package    Auto_Translate
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( ! defined( 'AUTO_TRANSLATE_DELETE_ON_UNINSTALL' ) ) {
	define( 'AUTO_TRANSLATE_DELETE_ON_UNINSTALL', false );
}

$wpat_should_delete = AUTO_TRANSLATE_DELETE_ON_UNINSTALL || 'on' === get_option( 'wpat_delete_data_on_uninstall', '' );

if ( ! $wpat_should_delete ) {
	return;
}

$wpat_option_keys = array(
	'wpat_auto_translate_version',
	'wpat_base_language',
	'wpat_widget_type',
	'wpat_button_icon',
	'wpat_show_icon',
	'wpat_supported_languages',
	'wpat_color_1',
	'wpat_color_2',
	'wpat_widget_size',
	'wpat_border_radius',
	'wpat_border_thickness',
	'wpat_border_color',
	'wpat_font_color',
	'wpat_font_family',
	'wpat_dropdown_border_thickness',
	'wpat_dropdown_border_color',
	'wpat_dropdown_background_color',
	'wpat_dropdown_hover_color',
	'wpat_dropdown_font_hover_color',
	'wpat_dropdown_font_selected_color',
	'wpat_dropdown_font_color',
	'wpat_dropdown_shadow',
	'wpat_dropdown_font_family',
	'wpat_min_style',
	'wpat_min_icon',
	'wpat_min_txt_display',
	'wpat_min_chevron',
	'wpat_min_txt_underline',
	'wpat_min_border_thickness',
	'wpat_min_border_color',
	'wpat_min_background_color',
	'wpat_min_font_color',
	'wpat_min_font_family',
	'wpat_min_hover_color',
	'wpat_min_font_hover_color',
	'wpat_default_location',
	'wpat_auto_detect',
	'wpat_show_in_menu',
	'wpat_custom_css',
	'wpat_min_custom_css',
	'wpat_delete_data_on_uninstall',
	'wpat_size', // legacy option key from old versions.
);

foreach ( $wpat_option_keys as $wpat_option_key ) {
	delete_option( $wpat_option_key );
}
