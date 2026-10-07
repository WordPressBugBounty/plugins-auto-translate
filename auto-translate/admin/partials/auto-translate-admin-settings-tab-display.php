<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wpat_active_tab_label = $this->get_settings_tab_label( $vars['active_tab'] );

// The visual partial is also used below the shared preview on a full-page
// request, so provide the small context it normally inherits from that view.
$wpat_supported_languages = Auto_Translate_Config::get_supported_languages();
$wpat_minimalist          = $vars['tabs']['visual_settings']['minimalist'] ?? array();
$wpat_min_base_style      = (string) ( $wpat_minimalist['wpat_min_base_style'] ?? 'compact' );
$wpat_label_mode          = $vars['tabs']['advanced_settings']['wpat_language_name_display'] ?? 'native';
$wpat_widget_type         = $vars['tabs']['visual_settings']['wpat_widget_type'] ?? 'minimalist';
$wpat_base_language       = Auto_Translate_Config::get_resolved_base_language();
$wpat_selected_languages  = $vars['tabs']['language_setting']['wpat_supported_languages'] ?? array();
$wpat_language_order      = (string) ( $vars['tabs']['language_setting']['wpat_language_order'] ?? '' );
$wpat_preview_flags       = is_array( $wpat_minimalist['wpat_language_flags'] ?? null ) ? $wpat_minimalist['wpat_language_flags'] : array();
?>
<div class="wpat-settings-tab-panel" data-wpat-settings-tab-panel data-wpat-active-tab="<?php echo esc_attr( $vars['active_tab'] ); ?>">
    <div class="wpat-tab-section__header">
        <h2 id="wpat-tab-section-title" tabindex="-1"><?php echo esc_html( $wpat_active_tab_label ); ?></h2>
    </div>
    <form id="wpat-settings-form" method="post" action="#">
        <?php
        if ( 'language_settings' === $vars['active_tab'] ) {
            require 'auto-translate-admin-language-settings-display.php';
        } elseif ( 'placement_settings' === $vars['active_tab'] ) {
            require 'auto-translate-admin-placement-settings-display.php';
        } elseif ( 'visual_settings' === $vars['active_tab'] ) {
            require 'auto-translate-admin-visual-settings-display.php';
        } elseif ( 'advanced_settings' === $vars['active_tab'] ) {
            require 'auto-translate-admin-advanced-settings-display.php';
        }
        ?>
    </form>
</div>
