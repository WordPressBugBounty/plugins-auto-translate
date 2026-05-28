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
<li class="<?php echo esc_attr( trim( ( isset( $wpat_menu_item_classes ) ? $wpat_menu_item_classes : '' ) . ' auto_translate_button_wrapper' ) ); ?>" style="min-width: 130px;">
    <?php require( plugin_dir_path( dirname( __FILE__ ) ) . '/partials/widgets/auto-translate-widget-minimalist.php' ); ?>
</li>
