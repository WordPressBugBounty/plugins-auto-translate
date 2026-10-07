<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared live preview for the Automatic Translator settings workspace.
 *
 * The preview deliberately lives outside the Styling tab so it remains
 * available while configuring languages, placement, and advanced options.
 *
 * @package Auto_Translate
 */

$wpat_supported_languages = Auto_Translate_Config::get_supported_languages();
$wpat_minimalist          = $vars['tabs']['visual_settings']['minimalist'];
$wpat_label_mode          = $vars['tabs']['advanced_settings']['wpat_language_name_display'];
$wpat_widget_type         = $vars['tabs']['visual_settings']['wpat_widget_type'];
$wpat_min_base_style      = $wpat_minimalist['wpat_min_base_style'] ?? 'compact';
$wpat_base_language       = Auto_Translate_Config::get_resolved_base_language();
$wpat_selected_languages  = is_array( $vars['tabs']['language_setting']['wpat_supported_languages'] ?? null )
	? $vars['tabs']['language_setting']['wpat_supported_languages']
	: array( 'all' );
$wpat_language_order      = (string) ( $vars['tabs']['language_setting']['wpat_language_order'] ?? '' );
$wpat_preview_flags       = is_array( $wpat_minimalist['wpat_language_flags'] ?? null ) ? $wpat_minimalist['wpat_language_flags'] : array();
$wpat_placement           = $vars['tabs']['placement_settings'] ?? array();

// Keep the complete catalog in the persistent preview. The Language tab is
// replaceable and can be mounted after this markup; rendering every option up
// front lets its draft controls show newly selected languages immediately.
$wpat_preview_lang_codes = array_keys( $wpat_supported_languages );

if ( '' !== $wpat_language_order ) {
	$wpat_ordered_codes = array_values( array_filter( array_map( array( 'Auto_Translate_Config', 'normalize_lang_code' ), array_map( 'trim', explode( ',', $wpat_language_order ) ) ), static function ( $lang_code ) use ( $wpat_preview_lang_codes ) {
		return in_array( $lang_code, $wpat_preview_lang_codes, true );
	} ) );
	$wpat_preview_lang_codes = array_merge( $wpat_ordered_codes, array_values( array_diff( $wpat_preview_lang_codes, $wpat_ordered_codes ) ) );
}

if ( isset( $wpat_supported_languages[ $wpat_base_language ] ) && ! in_array( $wpat_base_language, $wpat_preview_lang_codes, true ) ) {
	array_unshift( $wpat_preview_lang_codes, $wpat_base_language );
}

$wpat_languages_data = Auto_Translate_Languages::get_languages_data( array_values( array_unique( $wpat_preview_lang_codes ) ), (string) $wpat_label_mode, $wpat_preview_flags );
$wpat_preview_selector_languages = array();
foreach ( $wpat_languages_data as $wpat_preview_lang_code => $wpat_preview_lang ) {
	$wpat_preview_lang_name = isset( $wpat_preview_lang['lang_name'] ) ? (string) $wpat_preview_lang['lang_name'] : (string) $wpat_preview_lang_code;
	$wpat_preview_lang_english = isset( $wpat_preview_lang['lang_name_english'] ) ? (string) $wpat_preview_lang['lang_name_english'] : $wpat_preview_lang_name;
	$wpat_preview_lang_native = isset( $wpat_preview_lang['lang_name_native'] ) ? (string) $wpat_preview_lang['lang_name_native'] : $wpat_preview_lang_name;
	$wpat_preview_lang_display = $wpat_preview_lang_name;
	if ( 'code' === $wpat_minimalist['wpat_min_txt_display'] ) {
		$wpat_preview_lang_display = (string) $wpat_preview_lang_code;
	} elseif ( 'name_code' === $wpat_minimalist['wpat_min_txt_display'] ) {
		$wpat_preview_lang_display = $wpat_preview_lang_name . ' - ' . $wpat_preview_lang_code;
	}
	$wpat_preview_selector_languages[ $wpat_preview_lang_code ] = array(
		'lang_code' => (string) $wpat_preview_lang_code,
		'lang_name' => $wpat_preview_lang_name,
		'lang_name_english' => $wpat_preview_lang_english,
		'lang_name_native' => $wpat_preview_lang_native,
		'country_code' => isset( $wpat_preview_lang['country_code'] ) ? (string) $wpat_preview_lang['country_code'] : '',
		'display_label' => $wpat_preview_lang_display,
		'search_text' => strtolower( $wpat_preview_lang_code . ' ' . $wpat_preview_lang_english . ' ' . $wpat_preview_lang_native . ' ' . $wpat_preview_lang_display ),
	);
}

$wpat_preview_selector_view_model = array(
	'instanceId' => 'wpat-admin-preview',
	'dropdownId' => 'wpat-admin-preview-dropdown',
	'widgetType' => 'minimalist',
	'baseLanguage' => $wpat_base_language,
	'hostLanguage' => $wpat_base_language,
	'autoDetect' => '',
	'layout' => $wpat_minimalist['wpat_min_layout'],
	'textDisplay' => $wpat_minimalist['wpat_min_txt_display'],
	'style' => $wpat_minimalist['wpat_min_style'],
	'iconClass' => $wpat_minimalist['wpat_min_icon'],
	'chevronClass' => $wpat_minimalist['wpat_min_chevron'],
	'languages' => $wpat_preview_selector_languages,
	'languageCodes' => array_keys( $wpat_preview_selector_languages ),
);
?>
<script>
var wpatLanguagesCountries = <?php echo wp_json_encode( $wpat_languages_data ); ?>;
var wpatLanguageNameDisplay = <?php echo wp_json_encode( (string) $wpat_label_mode ); ?>;
var wpatWidgetType = <?php echo wp_json_encode( $wpat_widget_type ); ?>;
var wpatBaseLanguage = <?php echo wp_json_encode( $wpat_base_language ); ?>;
var wpatPreviewSelectedLanguages = <?php echo wp_json_encode( is_array( $wpat_selected_languages ) ? array_values( $wpat_selected_languages ) : array() ); ?>;
var wpatPreviewLanguageOrder = <?php echo wp_json_encode( $wpat_language_order ); ?>;
var wpatPlacementConfig = <?php echo wp_json_encode( array(
	'default_location' => ! empty( $wpat_placement['wpat_default_location'] ),
	'floating_position' => (string) ( $wpat_placement['wpat_floating_position'] ?? 'bottom_left' ),
	'floating_offset_x' => absint( $wpat_placement['wpat_floating_offset_x'] ?? 0 ),
	'floating_offset_y' => absint( $wpat_placement['wpat_floating_offset_y'] ?? 0 ),
	'show_in_menu' => (string) ( $wpat_placement['wpat_show_in_menu'] ?? '' ),
	'menu_position' => (string) ( $wpat_placement['wpat_menu_position'] ?? 'start' ),
) ); ?>;
</script>
<aside class="wpat-live-preview" aria-labelledby="wpat-live-preview-title">
	<div class="wpat-live-preview__header">
		<h2 id="wpat-live-preview-title"><span class="dashicons dashicons-visibility" aria-hidden="true"></span><?php esc_html_e( 'Live preview', 'auto-translate' ); ?></h2>
		<button type="button" class="wpat-live-preview__toggle" aria-expanded="true" aria-controls="wpat-live-preview-content" data-wpat-preview-toggle>
			<span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Collapse live preview', 'auto-translate' ); ?></span>
		</button>
	</div>
	<div class="wpat-live-preview__content" id="wpat-live-preview-content">
		<div class="wpat-preview-frame">
			<div class="wpat-preview-browser-bar" aria-hidden="true">
				<span class="wpat-preview-browser-bar__dot"></span>
				<span class="wpat-preview-browser-bar__dot"></span>
				<span class="wpat-preview-browser-bar__dot"></span>
				<span class="wpat-preview-browser-bar__address"></span>
			</div>
			<div id="wpat_dropdown_preview" class="styling-preview" data-preload-class="styling-preview" style="--wpat-preview-color-1:#000; --wpat-preview-color-2:#000; --wpat-preview-border-radius:0px; --wpat-preview-border-thickness:1px; --wpat-preview-border-color:#fff; --wpat-preview-font-color:#fff; --wpat-preview-font-family:inherit; --wpat-preview-dropdown-border-thickness:1px; --wpat-preview-dropdown-border-color:#000; --wpat-preview-dropdown-background-color:#fff; --wpat-preview-dropdown-font-color:#000; --wpat-preview-dropdown-font-family:inherit; --wpat-preview-dropdown-font-selected-color:#356177; --wpat-preview-dropdown-hover-color:#356177; --wpat-preview-dropdown-font-hover-color:#fff;">
				<nav class="wpat-preview-menu" data-wpat-preview-menu hidden aria-label="<?php esc_attr_e( 'Preview navigation menu', 'auto-translate' ); ?>">
					<span class="wpat-preview-menu__slot" data-wpat-preview-menu-slot></span>
					<span class="wpat-preview-menu__links" aria-hidden="true"><i></i><i></i><i></i></span>
				</nav>
				<div class="wpat-preview-selector-stage" data-wpat-preview-selector-stage>
				<?php $wpat_active_base_style = $wpat_min_base_style; ?>
				<?php foreach ( array( 'compact', 'minimal' ) as $wpat_preview_base_style ) : ?>
					<div class="auto_translate_button_wrapper wpat-preview-selector" data-wpat-preview="true" data-wpat-preview-base-style="<?php echo esc_attr( $wpat_preview_base_style ); ?>"<?php echo $wpat_active_base_style === $wpat_preview_base_style ? '' : ' hidden'; ?>>
						<?php
						$wpat_min_style = $wpat_minimalist['wpat_min_style']; $wpat_min_base_style = $wpat_preview_base_style; $wpat_min_layout = $wpat_minimalist['wpat_min_layout']; $wpat_min_icon = $wpat_minimalist['wpat_min_icon']; $wpat_min_txt_display = $wpat_minimalist['wpat_min_txt_display']; $wpat_min_txt_underline = $wpat_minimalist['wpat_min_txt_underline']; $wpat_min_text_divider = $wpat_minimalist['wpat_min_text_divider']; $wpat_min_border_thickness = $wpat_minimalist['wpat_min_border_thickness']; $wpat_min_border_color = $wpat_minimalist['wpat_min_border_color']; $wpat_min_border_transparent = $wpat_minimalist['wpat_min_border_transparent']; $wpat_min_background_color = $wpat_minimalist['wpat_min_background_color']; $wpat_min_background_transparent = $wpat_minimalist['wpat_min_background_transparent']; $wpat_min_font_color = $wpat_minimalist['wpat_min_font_color']; $wpat_min_font_family = $wpat_minimalist['wpat_min_font_family']; $wpat_min_hover_color = $wpat_minimalist['wpat_min_hover_color']; $wpat_min_hover_transparent = $wpat_minimalist['wpat_min_hover_transparent']; $wpat_min_font_hover_color = $wpat_minimalist['wpat_min_font_hover_color']; $wpat_min_chevron = $wpat_minimalist['wpat_min_chevron']; $wpat_selector_view_model = $wpat_preview_selector_view_model; $wpat_selector_view_model['instanceId'] = sanitize_html_class( $wpat_selector_view_model['instanceId'] . '-' . $wpat_preview_base_style ); $wpat_selector_view_model['dropdownId'] = sanitize_html_class( $wpat_selector_view_model['dropdownId'] . '-' . $wpat_preview_base_style ); require plugin_dir_path( dirname( __FILE__ ) ) . '../public/partials/widgets/auto-translate-widget-minimalist.php';
						?>
					</div>
				<?php endforeach; ?>
				<?php $wpat_min_base_style = $wpat_active_base_style; ?>
				<select class="wpat-native-preview" aria-label="<?php esc_attr_e( 'Language selector preview', 'auto-translate' ); ?>"<?php echo 'native' === $wpat_active_base_style ? '' : ' hidden'; ?>><?php foreach ( $wpat_languages_data as $wpat_lang_code => $wpat_lang ) : ?><?php $wpat_native_preview_label = isset( $wpat_preview_selector_languages[ $wpat_lang_code ]['display_label'] ) ? $wpat_preview_selector_languages[ $wpat_lang_code ]['display_label'] : $wpat_lang['lang_name']; ?><option value="<?php echo esc_attr( $wpat_lang_code ); ?>" data-lang-code="<?php echo esc_attr( $wpat_lang_code ); ?>" data-wpat-label="<?php echo esc_attr( $wpat_native_preview_label ); ?>"<?php selected( $wpat_lang_code, $wpat_base_language ); ?>><?php echo esc_html( $wpat_native_preview_label ); ?></option><?php endforeach; ?></select>
				</div>
			</div>
		</div>
		<div class="wpat-preview-placement-notice" data-wpat-placement-notice hidden role="status">
			<span class="dashicons dashicons-info" aria-hidden="true"></span>
			<span><?php esc_html_e( 'No placement selected. Select one under “Placement” to see the preview.', 'auto-translate' ); ?></span>
		</div>
		<div class="wpat-live-preview__toolbar">
			<button type="button" class="wpat-preview-theme-toggle" aria-label="<?php esc_attr_e( 'Toggle preview colour scheme', 'auto-translate' ); ?>" aria-pressed="false">
				<svg class="wpat-preview-theme-toggle__moon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.7 15.2A8.7 8.7 0 0 1 8.8 3.3a8.7 8.7 1 1 0 11.9 11.9Z" /></svg>
				<svg class="wpat-preview-theme-toggle__sun" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4" /><path d="M12 2v2m0 16v2M2 12h2m16 0h2m-2.9-7.1-1.4 1.4M6.3 17.7l-1.4 1.4m0-14.2 1.4 1.4m11.4 11.4 1.4 1.4" /></svg>
			</button>
		</div>
	</div>
</aside>
