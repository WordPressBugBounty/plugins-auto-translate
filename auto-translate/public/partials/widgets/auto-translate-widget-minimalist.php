<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<style>
    .auto_translate_minimalist .wpat_lang_item.wpat_lang_selected {
        border-color: <?php echo esc_html( $wpat_min_border_color )?>;
        border-width: <?php echo absint( $wpat_min_border_thickness )?>px;
    }
    .auto_translate_minimalist .wpat_minimalist_dropdown {
        border-color: <?php echo esc_html( $wpat_min_border_color )?>;
        border-width: 0 <?php echo absint( $wpat_min_border_thickness )?>px <?php echo absint( $wpat_min_border_thickness )?>px <?php echo absint( $wpat_min_border_thickness )?>px;
    }
    .auto_translate_minimalist .wpat_lang_item {
        background-color: <?php echo esc_html( $wpat_min_background_color )?>;
        color: <?php echo esc_html( $wpat_min_font_color )?>;
        font-family: <?php echo esc_html( $wpat_min_font_family )?>;
    }
    .wpat_minimalist_dropdown {
        background-color: <?php echo esc_html( $wpat_min_background_color )?>;
    }
    .wpat_minimalist_dropdown .wpat_lang_item:hover {
        background-color: <?php echo esc_html( $wpat_min_hover_color )?>;
        color: <?php echo esc_html( $wpat_min_font_hover_color )?>;
    }
</style>
<div class="
            auto_translate_minimalist
            wpat_min_style_<?php echo esc_attr( $wpat_min_style )?>
            wpat_min_txt_display_<?php echo esc_attr( $wpat_min_txt_display )?>
            wpat_invisible
            <?php echo esc_attr( $wpat_min_txt_underline )?>
            ">
    <div class="wpat_lang_item wpat_lang_selected">
        <div class="wpat_flag_wrapper">
            <div class="wpat_flag" data-icon-class="<?php echo esc_attr( $wpat_min_icon )?>"></div>
            <div class="wpat_lang_name"></div>
            <div class="wpat_lang_name_code">&nbsp;-&nbsp;</div>
            <div class="wpat_lang_code skiptranslate"></div>
        </div>
        <span class="dashicons wpat_chevron <?php echo esc_attr( $wpat_min_chevron )?>"></span>
    </div>
    <div class="wpat_minimalist_dropdown wpat_closed" >
        <?php foreach($languages_data as $wpat_lang_code => $wpat_lang):?>
        <div class="wpat_lang_item" data-lang-code="<?php echo esc_attr( $wpat_lang_code )?>">
            <div class="wpat_flag <?php echo esc_attr( $wpat_lang['country_code'] )?>"></div>
            <div class="wpat_lang_name"><?php echo esc_html( $wpat_lang['lang_name'] )?></div>
            <div class="wpat_lang_name_code">&nbsp;-&nbsp;</div>
            <div class="wpat_lang_code skiptranslate"><?php echo esc_html( $wpat_lang['lang_code'] )?></div>
        </div>
        <?php endforeach;?>
    </div>
</div>
