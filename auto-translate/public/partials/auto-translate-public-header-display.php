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
    iframe.goog-te-banner-frame,
    .goog-te-banner-frame.skiptranslate,
    iframe.skiptranslate:not(.goog-te-menu-frame),
    body > .skiptranslate,
    #goog-gt-tt,
    .goog-te-balloon-frame {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
    }
    html,
    body {
        top: 0 !important;
        position: static !important;
        margin-top: 0 !important;
        padding-top: 0 !important;
    }
    .skiptranslate.goog-te-gadget + iframe {
        display: none !important;
    }
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
</style>
<script>
function wpatHideGoogleBanner() {
    var bannerFrames = document.querySelectorAll(
        'iframe.goog-te-banner-frame, .goog-te-banner-frame.skiptranslate, iframe.skiptranslate:not(.goog-te-menu-frame), .goog-te-balloon-frame'
    );
    for (var i = 0; i < bannerFrames.length; i++) {
        bannerFrames[i].style.setProperty('display', 'none', 'important');
        bannerFrames[i].style.setProperty('visibility', 'hidden', 'important');
        bannerFrames[i].style.setProperty('height', '0', 'important');
    }

    var topSkiptranslate = document.querySelector('body > .skiptranslate');
    if (topSkiptranslate) {
        topSkiptranslate.style.setProperty('display', 'none', 'important');
        topSkiptranslate.style.setProperty('visibility', 'hidden', 'important');
        topSkiptranslate.style.setProperty('height', '0', 'important');
    }

    var tooltip = document.getElementById('goog-gt-tt');
    if (tooltip) {
        tooltip.style.setProperty('display', 'none', 'important');
    }

    if (document.documentElement) {
        document.documentElement.style.setProperty('top', '0', 'important');
        document.documentElement.style.setProperty('margin-top', '0', 'important');
        document.documentElement.style.setProperty('padding-top', '0', 'important');
    }

    if (document.body) {
        document.body.style.setProperty('top', '0', 'important');
        document.body.style.setProperty('position', 'static', 'important');
        document.body.style.setProperty('margin-top', '0', 'important');
        document.body.style.setProperty('padding-top', '0', 'important');
    }
}

function wpatStartGoogleBannerGuard() {
    wpatHideGoogleBanner();
    if (typeof MutationObserver !== 'undefined') {
        var observer = new MutationObserver(function () {
            wpatHideGoogleBanner();
        });
        observer.observe(document.documentElement, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class', 'style']
        });
    }
    setInterval(wpatHideGoogleBanner, 500);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', wpatStartGoogleBannerGuard);
} else {
    wpatStartGoogleBannerGuard();
}
window.addEventListener('load', wpatHideGoogleBanner);

function googleTranslateElementInit() {
    var googleTranslateElements = document.getElementsByClassName('google_translate_element');
    new google.translate.TranslateElement({
    pageLanguage: <?php echo wp_json_encode( $wpat_base_language )?>,
    includedLanguages: <?php echo wp_json_encode( $included_languages )?>,
    layout: google.translate.TranslateElement.InlineLayout.<?php echo  $wpat_widget_type === 'classic' ? 'SIMPLE' : 'VERTICAL' ?>,
    autoDisplay: false}, googleTranslateElements[0].id);
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
