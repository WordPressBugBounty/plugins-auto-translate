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
$wpat_data = $vars['tabs']['advanced_settings'];
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<table class="form-table">
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Default Location', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Turn this off if you don\'t want the \'Automatic Translator\' button to be displayed on the top left corner.','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>">
            <label for="default-location-on"><?php esc_html_e('On', 'auto-translate'); ?></label> <input type="radio" id="default-location-on" name="wpat_default_location" value=1 <?php if( $wpat_data['wpat_default_location'] ){ echo "checked='checked'"; }; ?>/>
            <label for="default-location-off"><?php esc_html_e('Off', 'auto-translate'); ?></label> <input type="radio" id="default-location-off" name="wpat_default_location" value=0 <?php if( !$wpat_data['wpat_default_location'] ){ echo "checked='checked'"; }; ?>/>
        </td>
        <td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>">
            <div class="suggestion">
                <p><?php echo wp_kses_post( __( 'For optimization reasons we suggest you to turn this option <b>Off</b> if you are using a <b>shortcode</b>, <b>menu</b> or a <b>widget</b> to display the translation button.', 'auto-translate' ) ); ?></p>
            </div>
        </td>
    </tr>
    <tr>
    <th scope="row">
            <?php esc_html_e('Show in Menu', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Select the menu where you want the translation button to be displayed','auto-translate'); ?></small>
        </th>
        <td><select id="wpat_show_in_menu" name="wpat_show_in_menu">
                <option value="" selected> - <?php esc_html_e('None', 'auto-translate'); ?> - </option>
                <?php $wpat_menus = get_registered_nav_menus(); ?>
                <?php foreach($wpat_menus as $wpat_location => $wpat_desc): ?>
                        <option value="<?php echo esc_attr( $wpat_location ); ?>" <?php selected( $wpat_data['wpat_show_in_menu'], $wpat_location ); ?> > <?php echo esc_html( $wpat_desc ); ?></option>
                <?php endforeach; ?>
            </select></td>
    </tr>
    <tr valign="top">
        <th scope="row" class="admin-shortcode-wrapper">
            <?php esc_html_e('Shortcode', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('You can use this shortcode to display the translation button in a custom location.', 'auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>" class="admin-shortcode-wrapper">
            <code>[auto_translate_button]</code>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="admin-shortcode-wrapper">
            <?php esc_html_e('Widget', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('You can use a widget to display the translation button within a widget area.', 'auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>" class="admin-shortcode-wrapper">
        <?php echo wp_kses_post( __( 'Go to <b>Appearance -> Widgets</b> and look for the <i>"Automatic Translator Button"</i> widget to place it where you need.', 'auto-translate' ) ); ?>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Auto Translate', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Turn this on if you want the visitors to have the site automatically translated into their host language','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>">
            <label for="auto-detect-on"><?php esc_html_e('On', 'auto-translate'); ?></label> <input type="radio" id="auto-detect-on" name="wpat_auto_detect" value="enabled" <?php if( $wpat_data['wpat_auto_detect'] == 'enabled' ){ echo "checked='checked'"; }; ?>/>
            <label for="auto-detect-off"><?php esc_html_e('Off', 'auto-translate'); ?></label> <input type="radio" id="auto-detect-off" name="wpat_auto_detect" value="disabled" <?php if( $wpat_data['wpat_auto_detect'] == 'disabled' ){ echo "checked='checked'"; }; ?>/>
        </td>
    </tr>
</table>
