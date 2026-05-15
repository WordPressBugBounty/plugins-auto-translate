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
    <?php if( $wpat_widget_type !== 'classic' ): ?>
    .google_translate_element{
        display:none!important;
    }
    <?php endif; ?>
    <?php if ( '' !== $wpat_custom_css ) : ?>
    <?php echo esc_html( $wpat_custom_css ); ?>
    <?php endif; ?>
</style>
<script>
function googleTranslateElementInit() {
    if (!window.google || !google.translate || !google.translate.TranslateElement) {
        return;
    }

    var googleTranslateElements = document.getElementsByClassName('google_translate_element');
    for (var i = 0; i < googleTranslateElements.length; i++) {
        var element = googleTranslateElements[i];
        if (!element || !element.id || element.getAttribute('data-wpat-google-init') === '1') {
            continue;
        }

        new google.translate.TranslateElement({
            pageLanguage: <?php echo wp_json_encode( $wpat_base_language )?>,
            includedLanguages: <?php echo wp_json_encode( $included_languages )?>,
            layout: google.translate.TranslateElement.InlineLayout.<?php echo  $wpat_widget_type === 'classic' ? 'SIMPLE' : 'VERTICAL' ?>,
            autoDisplay: false
        }, element.id);

        element.setAttribute('data-wpat-google-init', '1');
    }
}
var wpatLanguagesCountries = <?php echo wp_json_encode( $languages_data )?>;
var wpatButtonIcon = <?php echo wp_json_encode( $wpat_show_icon === 'on' ? $wpat_button_icon : '' )?>;
var wpatDropdownBorderThickness = <?php echo absint( $wpat_dropdown_border_thickness )?>;
var wpatDropdownBorderColor = <?php echo wp_json_encode( $wpat_dropdown_border_color )?>;
var wpatDropdownBackgroundColor = <?php echo wp_json_encode( $wpat_dropdown_background_color )?>;
var wpatDropdownHoverColor = <?php echo wp_json_encode( $wpat_dropdown_hover_color )?>;
var wpatDropdownFontHoverColor = <?php echo wp_json_encode( $wpat_dropdown_font_hover_color )?>;
var wpatDropdownFontSelectedColor = <?php echo wp_json_encode( $wpat_dropdown_font_selected_color )?>;
var wpatDropdownFontColor = <?php echo wp_json_encode( $wpat_dropdown_font_color )?>;
var wpatDropdownFontFamily = <?php echo wp_json_encode( $wpat_dropdown_font_family )?>;
var wpatWidgetType = <?php echo wp_json_encode( $wpat_widget_type )?>;
var wpatBaseLanguage = <?php echo wp_json_encode( $wpat_base_language )?>;

/* dropdown styling */
var wpatDropdownShadow = <?php echo $wpat_dropdown_shadow==='on'?'true':'false'; ?>;
var wpatAutoDetect = <?php echo wp_json_encode( $wpat_auto_detect )?>;
var wpatHostLanguage = <?php echo wp_json_encode( $wpat_host_language )?>;
</script>
