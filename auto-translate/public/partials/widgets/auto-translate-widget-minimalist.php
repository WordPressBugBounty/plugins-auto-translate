<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wpat_languages_data = is_array( $wpat_languages_data ?? null ) ? $wpat_languages_data : array();
$wpat_dropdown_id = 'wpat-minimalist-dropdown-' . absint( wp_rand() );
?>
<div class="
            auto_translate_minimalist
            notranslate
            wpat_min_style_<?php echo esc_attr( $wpat_min_style )?>
            wpat_min_layout_<?php echo esc_attr( $wpat_min_layout )?>
            wpat_min_txt_display_<?php echo esc_attr( $wpat_min_txt_display )?>
            wpat_invisible
            <?php echo esc_attr( $wpat_min_txt_underline )?>
            "
     style="
        --wpat-min-border-color: <?php echo esc_attr( $wpat_min_border_color ); ?>;
        --wpat-min-border-thickness: <?php echo absint( $wpat_min_border_thickness ); ?>px;
        --wpat-min-background-color: <?php echo esc_attr( $wpat_min_background_color ); ?>;
        --wpat-min-font-color: <?php echo esc_attr( $wpat_min_font_color ); ?>;
        --wpat-min-font-family: <?php echo esc_attr( $wpat_min_font_family ); ?>;
        --wpat-min-hover-color: <?php echo esc_attr( $wpat_min_hover_color ); ?>;
        --wpat-min-font-hover-color: <?php echo esc_attr( $wpat_min_font_hover_color ); ?>;
     "
     translate="no">
    <button type="button"
        class="wpat_lang_item wpat_lang_selected"
        aria-controls="<?php echo esc_attr( $wpat_dropdown_id ); ?>"
        aria-expanded="false"
        aria-haspopup="listbox">
        <div class="wpat_flag_wrapper">
            <div class="wpat_flag" data-icon-class="<?php echo esc_attr( $wpat_min_icon )?>"></div>
            <div class="wpat_lang_name skiptranslate"></div>
            <div class="wpat_lang_name_code skiptranslate">&nbsp;-&nbsp;</div>
            <div class="wpat_lang_code skiptranslate"></div>
        </div>
        <span class="dashicons wpat_chevron <?php echo esc_attr( $wpat_min_chevron )?>"></span>
    </button>
    <span class="wpat_status_message skiptranslate notranslate" aria-live="polite"></span>
    <div id="<?php echo esc_attr( $wpat_dropdown_id ); ?>" class="wpat_minimalist_dropdown wpat_closed" role="listbox">
        <div class="wpat_popup_search_wrap">
            <label class="screen-reader-text" for="<?php echo esc_attr( $wpat_dropdown_id ); ?>-search"><?php esc_html_e( 'Search language', 'auto-translate' ); ?></label>
            <input id="<?php echo esc_attr( $wpat_dropdown_id ); ?>-search" class="wpat_popup_search_input" type="search" autocomplete="off" placeholder="<?php esc_attr_e( 'Search language…', 'auto-translate' ); ?>" />
        </div>
        <?php foreach($wpat_languages_data as $wpat_lang_code => $wpat_lang):?>
        <button type="button" class="wpat_lang_item" data-lang-code="<?php echo esc_attr( $wpat_lang_code )?>" data-lang-name="<?php echo esc_attr( strtolower( $wpat_lang['lang_name'] ) ); ?>" data-lang-native="<?php echo esc_attr( strtolower( $wpat_lang['lang_name_native'] ) ); ?>" role="option" aria-selected="false" tabindex="-1">
            <div class="wpat_flag <?php echo esc_attr( $wpat_lang['country_code'] )?>"></div>
            <div class="wpat_lang_name skiptranslate"><?php echo esc_html( $wpat_lang['lang_name'] )?></div>
            <div class="wpat_lang_name_code skiptranslate">&nbsp;-&nbsp;</div>
            <div class="wpat_lang_code skiptranslate"><?php echo esc_html( $wpat_lang['lang_code'] )?></div>
        </button>
        <?php endforeach;?>
    </div>
</div>
