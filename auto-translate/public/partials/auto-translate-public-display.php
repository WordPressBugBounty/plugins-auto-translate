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
<div class="auto_translate_button_wrapper <?php echo esc_attr( $wpat_floating_classes ); ?> auto_detect_<?php echo esc_attr( $wpat_auto_detect ); ?>"<?php echo $default_location ? ' style="' . esc_attr( $wpat_floating_style ) . '"' : ''; ?>>
    <?php require( plugin_dir_path( dirname( __FILE__ ) ) . '/partials/widgets/auto-translate-widget-minimalist.php' ); ?>
</div>
