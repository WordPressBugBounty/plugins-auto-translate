<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wpat_languages_data = is_array( $wpat_languages_data ?? null ) ? $wpat_languages_data : array();
$wpat_selector_view_model = is_array( $wpat_selector_view_model ?? null ) ? $wpat_selector_view_model : array();
$wpat_dropdown_id = isset( $wpat_selector_view_model['dropdownId'] ) ? sanitize_html_class( (string) $wpat_selector_view_model['dropdownId'] ) : 'wpat-minimalist-dropdown-' . absint( wp_rand() );
$wpat_trigger_id = $wpat_dropdown_id . '-trigger';
$wpat_selector_instance_id = isset( $wpat_selector_view_model['instanceId'] ) ? sanitize_html_class( (string) $wpat_selector_view_model['instanceId'] ) : 'wpat-selector-' . absint( wp_rand() );
$wpat_selector_languages = isset( $wpat_selector_view_model['languages'] ) && is_array( $wpat_selector_view_model['languages'] ) ? $wpat_selector_view_model['languages'] : array();
$wpat_min_font_family = isset( $wpat_min_font_family ) ? trim( (string) $wpat_min_font_family ) : '';
$wpat_min_base_style = isset( $wpat_min_base_style ) ? sanitize_key( (string) $wpat_min_base_style ) : 'compact';
$wpat_min_border_color = ! empty( $wpat_min_border_transparent ) ? 'transparent' : $wpat_min_border_color;
$wpat_min_background_color = ! empty( $wpat_min_background_transparent ) ? 'transparent' : $wpat_min_background_color;
$wpat_min_hover_color = ! empty( $wpat_min_hover_transparent ) ? 'transparent' : $wpat_min_hover_color;

if ( ! in_array( $wpat_min_base_style, array( 'compact', 'minimal', 'native' ), true ) ) {
	$wpat_min_base_style = 'compact';
}

$wpat_country_flag_emoji = static function( $country_code ) {
	$country_code = strtoupper( sanitize_key( (string) $country_code ) );

	if ( ! preg_match( '/^[A-Z]{2}$/', $country_code ) ) {
		return '';
	}

	$emoji = '';
	for ( $index = 0; $index < 2; $index++ ) {
		$emoji .= html_entity_decode( '&#' . ( 0x1F1E6 + ord( $country_code[ $index ] ) - ord( 'A' ) ) . ';', ENT_QUOTES, 'UTF-8' );
	}

	return $emoji;
};
?>
<?php if ( 'native' === $wpat_min_base_style ) : ?>
<div class="wpat_native_selector notranslate" data-wpat-selector-instance="<?php echo esc_attr( $wpat_selector_instance_id ); ?>" translate="no">
    <script type="application/json" class="wpat_selector_config"><?php echo wp_json_encode( $wpat_selector_view_model ); ?></script>
    <label class="screen-reader-text" for="<?php echo esc_attr( $wpat_trigger_id ); ?>"><?php esc_html_e( 'Select language', 'auto-translate' ); ?></label>
    <select id="<?php echo esc_attr( $wpat_trigger_id ); ?>" class="wpat_native_select" aria-label="<?php esc_attr_e( 'Select language', 'auto-translate' ); ?>">
        <?php foreach ( $wpat_languages_data as $wpat_lang_code => $wpat_lang ) : ?>
            <?php
            $wpat_selector_lang = isset( $wpat_selector_languages[ $wpat_lang_code ] ) && is_array( $wpat_selector_languages[ $wpat_lang_code ] ) ? $wpat_selector_languages[ $wpat_lang_code ] : array();
            $wpat_display_label = isset( $wpat_selector_lang['display_label'] ) ? (string) $wpat_selector_lang['display_label'] : (string) $wpat_lang['lang_name'];
            ?>
            <option value="<?php echo esc_attr( $wpat_lang_code ); ?>"><?php echo esc_html( $wpat_display_label ); ?></option>
        <?php endforeach; ?>
    </select>
    <span class="wpat_status_message skiptranslate notranslate" aria-live="polite"></span>
</div>
<?php elseif ( 'minimal' === $wpat_min_base_style ) : ?>
<div class="
            auto_translate_minimalist
            notranslate
            wpat_min_base_style_minimal
            wpat_min_style_<?php echo esc_attr( $wpat_min_style ); ?>
            wpat_min_txt_display_<?php echo esc_attr( $wpat_min_txt_display ); ?>
			wpat_min_text_divider_<?php echo esc_attr( $wpat_min_text_divider ); ?>
            wpat_invisible
            <?php echo esc_attr( $wpat_min_txt_underline ); ?>
            "
     data-wpat-selector-instance="<?php echo esc_attr( $wpat_selector_instance_id ); ?>"
     style="
        --wpat-min-border-color: <?php echo esc_attr( $wpat_min_border_color ); ?>;
        --wpat-min-border-thickness: <?php echo absint( $wpat_min_border_thickness ); ?>px;
        --wpat-min-background-color: <?php echo esc_attr( $wpat_min_background_color ); ?>;
        --wpat-min-font-color: <?php echo esc_attr( $wpat_min_font_color ); ?>;
        <?php if ( '' !== $wpat_min_font_family ) : ?>
        --wpat-min-font-family: <?php echo esc_attr( $wpat_min_font_family ); ?>;
        <?php endif; ?>
        --wpat-min-hover-color: <?php echo esc_attr( $wpat_min_hover_color ); ?>;
        --wpat-min-font-hover-color: <?php echo esc_attr( $wpat_min_font_hover_color ); ?>;
     "
     translate="no">
    <script type="application/json" class="wpat_selector_config"><?php echo wp_json_encode( $wpat_selector_view_model ); ?></script>
    <div class="wpat_minimal_links" role="list">
        <?php $wpat_minimal_language_index = 0; ?>
        <?php $wpat_minimal_language_count = count( $wpat_languages_data ); ?>
        <?php foreach ( $wpat_languages_data as $wpat_lang_code => $wpat_lang ) : ?>
            <?php
            $wpat_selector_lang = isset( $wpat_selector_languages[ $wpat_lang_code ] ) && is_array( $wpat_selector_languages[ $wpat_lang_code ] ) ? $wpat_selector_languages[ $wpat_lang_code ] : array();
            $wpat_display_label = isset( $wpat_selector_lang['display_label'] ) ? (string) $wpat_selector_lang['display_label'] : '';
            ?>
            <button type="button" class="wpat_minimal_link wpat_lang_item" data-lang-code="<?php echo esc_attr( $wpat_lang_code ); ?>" data-wpat-label="<?php echo esc_attr( $wpat_display_label ); ?>" aria-label="<?php echo esc_attr( $wpat_display_label ); ?>" role="listitem">
                <div class="wpat_flag <?php echo esc_attr( $wpat_lang['country_code'] ); ?>" data-wpat-flag-emoji="<?php echo esc_attr( $wpat_country_flag_emoji( $wpat_lang['country_code'] ) ); ?>"></div>
                <div class="wpat_lang_name skiptranslate"><?php echo esc_html( $wpat_lang['lang_name'] ); ?></div>
                <div class="wpat_lang_name_code skiptranslate">&nbsp;-&nbsp;</div>
                <div class="wpat_lang_code skiptranslate"><?php echo esc_html( $wpat_lang['lang_code'] ); ?></div>
            </button>
            <?php if ( $wpat_minimal_language_index < $wpat_minimal_language_count - 1 ) : ?>
                <span class="wpat_text_divider" aria-hidden="true">|</span>
            <?php endif; ?>
            <?php $wpat_minimal_language_index++; ?>
        <?php endforeach; ?>
    </div>
    <span class="wpat_status_message skiptranslate notranslate" aria-live="polite"></span>
</div>
<?php else : ?>
<div class="
            auto_translate_minimalist
            notranslate
			wpat_min_base_style_<?php echo esc_attr( $wpat_min_base_style ); ?>
            wpat_min_style_<?php echo esc_attr( $wpat_min_style )?>
            wpat_min_layout_<?php echo esc_attr( $wpat_min_layout )?>
            wpat_min_txt_display_<?php echo esc_attr( $wpat_min_txt_display )?>
			wpat_min_text_divider_<?php echo esc_attr( $wpat_min_text_divider ); ?>
            wpat_invisible
            <?php echo esc_attr( $wpat_min_txt_underline )?>
            "
     data-wpat-selector-instance="<?php echo esc_attr( $wpat_selector_instance_id ); ?>"
     style="
        --wpat-min-border-color: <?php echo esc_attr( $wpat_min_border_color ); ?>;
        --wpat-min-border-thickness: <?php echo absint( $wpat_min_border_thickness ); ?>px;
        --wpat-min-background-color: <?php echo esc_attr( $wpat_min_background_color ); ?>;
        --wpat-min-font-color: <?php echo esc_attr( $wpat_min_font_color ); ?>;
        <?php if ( '' !== $wpat_min_font_family ) : ?>
        --wpat-min-font-family: <?php echo esc_attr( $wpat_min_font_family ); ?>;
        <?php endif; ?>
        --wpat-min-hover-color: <?php echo esc_attr( $wpat_min_hover_color ); ?>;
        --wpat-min-font-hover-color: <?php echo esc_attr( $wpat_min_font_hover_color ); ?>;
     "
     translate="no">
    <script type="application/json" class="wpat_selector_config"><?php echo wp_json_encode( $wpat_selector_view_model ); ?></script>
    <button type="button"
        id="<?php echo esc_attr( $wpat_trigger_id ); ?>"
        class="wpat_lang_item wpat_lang_selected"
        aria-controls="<?php echo esc_attr( $wpat_dropdown_id ); ?>"
        aria-expanded="false"
        aria-haspopup="listbox">
        <div class="wpat_flag_wrapper">
            <div class="wpat_flag" data-icon-class="<?php echo esc_attr( $wpat_min_icon )?>" data-wpat-flag-emoji=""></div>
            <div class="wpat_lang_name skiptranslate"></div>
            <div class="wpat_lang_name_code skiptranslate">&nbsp;-&nbsp;</div>
            <div class="wpat_lang_code skiptranslate"></div>
        </div>
        <span class="dashicons wpat_chevron <?php echo esc_attr( $wpat_min_chevron )?>"></span>
    </button>
    <span class="wpat_status_message skiptranslate notranslate" aria-live="polite"></span>
    <div id="<?php echo esc_attr( $wpat_dropdown_id ); ?>" class="wpat_minimalist_dropdown wpat_closed" role="listbox" aria-labelledby="<?php echo esc_attr( $wpat_trigger_id ); ?>">
        <div class="wpat_dropdown_panel">
        <div class="wpat_popup_search_wrap">
            <label class="screen-reader-text" for="<?php echo esc_attr( $wpat_dropdown_id ); ?>-search"><?php esc_html_e( 'Search language', 'auto-translate' ); ?></label>
            <span class="dashicons dashicons-search wpat_popup_search_icon" aria-hidden="true"></span>
            <input id="<?php echo esc_attr( $wpat_dropdown_id ); ?>-search" class="wpat_popup_search_input" type="search" autocomplete="off" aria-controls="<?php echo esc_attr( $wpat_dropdown_id ); ?>" placeholder=" " />
            <span class="wpat_popup_search_placeholder" aria-hidden="true"><?php esc_html_e( 'Search', 'auto-translate' ); ?></span>
        </div>
        <?php foreach($wpat_languages_data as $wpat_lang_code => $wpat_lang):?>
        <?php
        $wpat_selector_lang = isset( $wpat_selector_languages[ $wpat_lang_code ] ) && is_array( $wpat_selector_languages[ $wpat_lang_code ] ) ? $wpat_selector_languages[ $wpat_lang_code ] : array();
        $wpat_search_text = isset( $wpat_selector_lang['search_text'] ) ? (string) $wpat_selector_lang['search_text'] : '';
        $wpat_display_label = isset( $wpat_selector_lang['display_label'] ) ? (string) $wpat_selector_lang['display_label'] : '';
        $wpat_option_id = $wpat_dropdown_id . '-option-' . sanitize_html_class( (string) $wpat_lang_code );
        ?>
        <button type="button" id="<?php echo esc_attr( $wpat_option_id ); ?>" class="wpat_lang_item" data-lang-code="<?php echo esc_attr( $wpat_lang_code )?>" data-lang-name="<?php echo esc_attr( strtolower( $wpat_lang['lang_name'] ) ); ?>" data-lang-native="<?php echo esc_attr( strtolower( $wpat_lang['lang_name_native'] ) ); ?>" data-wpat-search="<?php echo esc_attr( strtolower( $wpat_search_text ) ); ?>" data-wpat-label="<?php echo esc_attr( $wpat_display_label ); ?>" role="option" aria-selected="false" tabindex="-1">
            <div class="wpat_flag <?php echo esc_attr( $wpat_lang['country_code'] )?>" data-wpat-flag-emoji="<?php echo esc_attr( $wpat_country_flag_emoji( $wpat_lang['country_code'] ) ); ?>"></div>
            <div class="wpat_lang_name skiptranslate"><?php echo esc_html( $wpat_lang['lang_name'] )?></div>
            <div class="wpat_lang_name_code skiptranslate">&nbsp;-&nbsp;</div>
            <div class="wpat_lang_code skiptranslate"><?php echo esc_html( $wpat_lang['lang_code'] )?></div>
        </button>
        <?php endforeach;?>
        </div>
    </div>
</div>
<?php endif; ?>
