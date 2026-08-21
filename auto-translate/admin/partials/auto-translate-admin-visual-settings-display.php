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
$wpat_min_preset     = $wpat_minimalist['wpat_min_preset'] ?? 'quick_switcher';
$wpat_plugin_file = dirname( dirname( __DIR__ ) ) . '/auto-translate.php';
$wpat_gb_flag_url = plugins_url( 'public/images/flags/svg/gb.svg', $wpat_plugin_file );
$wpat_de_flag_url = plugins_url( 'public/images/flags/svg/de.svg', $wpat_plugin_file );
$wpat_es_flag_url = plugins_url( 'public/images/flags/svg/es.svg', $wpat_plugin_file );

if ( in_array( $wpat_min_preset, array( 'classic', 'pill', 'soft_outline' ), true ) ) {
	$wpat_min_preset = 'quick_switcher';
}

/* The shared workspace preview prepares the selector state used below. */
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
$wpat_preview_selector_languages = array();

foreach ( $wpat_languages_data as $wpat_preview_lang_code => $wpat_preview_lang ) {
    $wpat_preview_lang_name = isset( $wpat_preview_lang['lang_name'] ) ? (string) $wpat_preview_lang['lang_name'] : (string) $wpat_preview_lang_code;
    $wpat_preview_lang_native = isset( $wpat_preview_lang['lang_name_native'] ) ? (string) $wpat_preview_lang['lang_name_native'] : $wpat_preview_lang_name;
    $wpat_preview_lang_display = $wpat_preview_lang_name;

    if ( 'code' === $wpat_minimalist['wpat_min_txt_display'] ) {
        $wpat_preview_lang_display = (string) $wpat_preview_lang_code;
    } elseif ( 'name_code' === $wpat_minimalist['wpat_min_txt_display'] ) {
        $wpat_preview_lang_display = $wpat_preview_lang_name . ' - ' . $wpat_preview_lang_code;
    }

    $wpat_preview_selector_languages[ $wpat_preview_lang_code ] = array(
        'lang_code'        => (string) $wpat_preview_lang_code,
        'lang_name'        => $wpat_preview_lang_name,
        'lang_name_native' => $wpat_preview_lang_native,
        'country_code'     => isset( $wpat_preview_lang['country_code'] ) ? (string) $wpat_preview_lang['country_code'] : '',
        'display_label'    => $wpat_preview_lang_display,
        'search_text'      => strtolower( $wpat_preview_lang_code . ' ' . $wpat_preview_lang_name . ' ' . $wpat_preview_lang_native ),
    );
}

$wpat_preview_selector_view_model = array(
    'instanceId'    => 'wpat-admin-preview',
    'dropdownId'    => 'wpat-admin-preview-dropdown',
    'widgetType'    => 'minimalist',
    'baseLanguage'  => $wpat_base_language,
    'hostLanguage'  => $wpat_base_language,
    'autoDetect'    => '',
    'layout'        => $wpat_minimalist['wpat_min_layout'],
    'textDisplay'   => $wpat_minimalist['wpat_min_txt_display'],
    'style'         => $wpat_minimalist['wpat_min_style'],
    'iconClass'     => $wpat_minimalist['wpat_min_icon'],
    'chevronClass'  => $wpat_minimalist['wpat_min_chevron'],
    'languages'     => $wpat_preview_selector_languages,
    'languageCodes' => array_keys( $wpat_preview_selector_languages ),
);
?>
<div id="wpat_admin_visual" class="wpat-settings-panel wpat_widget_type_<?php echo esc_attr( $wpat_widget_type )?>">
    <input type="hidden" name="wpat_widget_type" value="minimalist"/>

    <div class="wpat-card wpat-styling-preview-card">
        <div class="wpat-style-studio">
            <section class="wpat-style-studio__choices" aria-labelledby="wpat-style-choice-heading">
                <h3 id="wpat-style-choice-heading"><?php esc_html_e( '1. Choose a starting style', 'auto-translate' ); ?></h3>
                <div class="wpat-base-style-grid" role="radiogroup" aria-label="<?php esc_attr_e( 'Starting style', 'auto-translate' ); ?>">
                    <label class="wpat-base-style-tile <?php echo 'compact' === $wpat_min_base_style ? 'is-active' : ''; ?>">
                        <input type="radio" name="wpat_min_base_style" value="compact" <?php checked( $wpat_min_base_style, 'compact' ); ?> />
                        <span class="wpat-base-style-tile__sample wpat-base-style-tile__sample--compact"><span class="wpat-sample-flag" style="background-image:url('<?php echo esc_url( $wpat_gb_flag_url ); ?>');" aria-hidden="true"></span> EN <span class="dashicons dashicons-arrow-down-alt2"></span></span>
                        <strong><?php esc_html_e( 'Compact selector', 'auto-translate' ); ?></strong>
                        <small><?php esc_html_e( 'A polished selector with optional search.', 'auto-translate' ); ?></small>
                    </label>
                    <label class="wpat-base-style-tile <?php echo 'minimal' === $wpat_min_base_style ? 'is-active' : ''; ?>">
                        <input type="radio" name="wpat_min_base_style" value="minimal" <?php checked( $wpat_min_base_style, 'minimal' ); ?> />
                        <span class="wpat-base-style-tile__sample wpat-base-style-tile__sample--minimal" aria-hidden="true">🇬🇧 🇩🇪</span>
                        <strong><?php esc_html_e( 'Minimal selector', 'auto-translate' ); ?></strong>
                        <small><?php esc_html_e( 'Lightweight flag, name, or code links.', 'auto-translate' ); ?></small>
                    </label>
                    <label class="wpat-base-style-tile <?php echo 'native' === $wpat_min_base_style ? 'is-active' : ''; ?>">
                        <input type="radio" name="wpat_min_base_style" value="native" <?php checked( $wpat_min_base_style, 'native' ); ?> />
                        <span class="wpat-base-style-tile__sample wpat-base-style-tile__sample--native">English <span class="dashicons dashicons-arrow-down-alt2"></span></span>
                        <strong><?php esc_html_e( 'Native dropdown', 'auto-translate' ); ?></strong>
                        <small><?php esc_html_e( 'A familiar browser control.', 'auto-translate' ); ?></small>
                    </label>
                </div>
                <div class="wpat-preset-section">
                    <h3 id="wpat-preset-heading" class="wpat-preset-section__heading"><?php esc_html_e( '2. Select a preset', 'auto-translate' ); ?></h3>
                    <input type="hidden" id="wpat_min_preset" name="wpat_min_preset" value="<?php echo esc_attr( $wpat_min_preset ); ?>" />
                    <div class="wpat-preset-grid" data-wpat-preset-grid>
                        <button type="button" class="wpat-preset-tile" data-wpat-preset="quick_switcher" data-wpat-base-styles="compact"><span class="wpat-preset-sample--quick"><span class="wpat-sample-flag" style="background-image:url('<?php echo esc_url( $wpat_gb_flag_url ); ?>');" aria-hidden="true"></span> EN <span class="dashicons dashicons-arrow-down-alt2"></span></span><span class="wpat-preset-label"><?php esc_html_e( 'Quick switcher', 'auto-translate' ); ?></span></button>
                        <button type="button" class="wpat-preset-tile" data-wpat-preset="searchable_picker" data-wpat-base-styles="compact"><span class="wpat-preset-sample--search"><span class="wpat-preset-sample--search__opener"><span class="wpat-sample-flag" style="background-image:url('<?php echo esc_url( $wpat_gb_flag_url ); ?>');" aria-hidden="true"></span><?php esc_html_e( 'English', 'auto-translate' ); ?><span class="dashicons dashicons-arrow-up-alt2" aria-hidden="true"></span></span><span class="wpat-preset-sample--search__field"><span class="dashicons dashicons-search" aria-hidden="true"></span></span><span class="wpat-preset-label"><?php esc_html_e( 'Searchable picker', 'auto-translate' ); ?></span></button>
                        <button type="button" class="wpat-preset-tile" data-wpat-preset="compact_code_only" data-wpat-base-styles="compact"><span class="wpat-preset-sample--quick">EN <span class="dashicons dashicons-arrow-down-alt2"></span></span><span class="wpat-preset-label"><?php esc_html_e( 'Code only', 'auto-translate' ); ?></span></button>
                        <button type="button" class="wpat-preset-tile" data-wpat-preset="compact_emoji_name" data-wpat-base-styles="compact"><span class="wpat-preset-sample--quick" aria-hidden="true"><span class="wpat-sample-emoji-flag">🇬🇧</span> English <span class="dashicons dashicons-arrow-down-alt2"></span></span><span class="wpat-preset-label"><?php esc_html_e( 'Emoji flags + name', 'auto-translate' ); ?></span></button>
                        <button type="button" class="wpat-preset-tile" data-wpat-preset="minimal_flags" data-wpat-base-styles="minimal"><span class="wpat-preset-sample--minimal" aria-hidden="true">🇬🇧 🇩🇪</span><span class="wpat-preset-label"><?php esc_html_e( 'Emoji flags', 'auto-translate' ); ?></span></button>
                        <button type="button" class="wpat-preset-tile" data-wpat-preset="minimal_flags_code" data-wpat-base-styles="minimal"><span class="wpat-preset-sample--minimal"><span class="wpat-sample-flag" style="background-image:url('<?php echo esc_url( $wpat_gb_flag_url ); ?>');" aria-hidden="true"></span> EN <span class="wpat-sample-flag" style="background-image:url('<?php echo esc_url( $wpat_de_flag_url ); ?>');" aria-hidden="true"></span> DE</span><span class="wpat-preset-label"><?php esc_html_e( 'Flags + code', 'auto-translate' ); ?></span></button>
                        <button type="button" class="wpat-preset-tile" data-wpat-preset="minimal_code_only" data-wpat-base-styles="minimal"><span class="wpat-preset-sample--minimal">EN&nbsp;&nbsp;DE</span><span class="wpat-preset-label"><?php esc_html_e( 'Code only', 'auto-translate' ); ?></span></button>
                        <button type="button" class="wpat-preset-tile" data-wpat-preset="minimal_name_pipe" data-wpat-base-styles="minimal"><span class="wpat-preset-sample--minimal">English&nbsp;|&nbsp;Deutsch</span><span class="wpat-preset-label"><?php esc_html_e( 'Names + divider', 'auto-translate' ); ?></span></button>
                    </div>
                </div>
                <div class="wpat-customize-section" aria-labelledby="wpat-customize-heading"<?php echo 'native' === $wpat_min_base_style ? ' hidden' : ''; ?>>
                    <h3 id="wpat-customize-heading" class="wpat-customize-section__heading"><?php esc_html_e( '3. Customize your preferences (optional)', 'auto-translate' ); ?></h3>
                    <div class="wpat-customize-action">
                        <button type="button" class="button button-primary" data-wpat-customize aria-controls="wpat-custom-styling" aria-expanded="false"><span class="dashicons dashicons-admin-generic" aria-hidden="true"></span><?php esc_html_e( 'Customize', 'auto-translate' ); ?></button>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php require 'auto-translate-admin-visual-settings-minimalist-display.php'; ?>
</div>
