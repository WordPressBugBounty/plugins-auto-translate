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
 * @since      1.0.0
 *
 * @package    Auto_Translate
 * @subpackage Auto_Translate/public/partials
 */
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="auto_translate_button_wrapper" class="<?php echo esc_attr( $default_location ? 'wpat_float wpat_float_top_left' : '' )?> auto_detect_<?php echo esc_attr( $wpat_auto_detect )?>">
    <div id="google_translate_element_<?php echo absint( wp_rand() )?>" class="<?php echo esc_attr( $wpat_widget_size )?> dashicons-before google_translate_element"></div>
    <?php if( $wpat_widget_type === 'minimalist' ): ?>
        <?php require_once( plugin_dir_path( dirname( __FILE__ ) ) . '/partials/widgets/auto-translate-widget-minimalist.php' ); ?>
    <?php endif; ?>
</div>
