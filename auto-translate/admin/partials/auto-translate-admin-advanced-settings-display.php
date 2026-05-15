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
            <small><?php esc_html_e('Select the WordPress menu or Navigation block where you want the translation button to be displayed','auto-translate'); ?></small>
        </th>
        <td><select id="wpat_show_in_menu" name="wpat_show_in_menu">
                <option value="" <?php selected( $wpat_data['wpat_show_in_menu'], '' ); ?>> - <?php esc_html_e('None', 'auto-translate'); ?> - </option>
                <?php $wpat_menus = wp_get_nav_menus(); ?>
                <?php if ( ! empty( $wpat_menus ) ) : ?>
                    <optgroup label="<?php esc_attr_e( 'Classic Menus', 'auto-translate' ); ?>">
                        <?php foreach ( $wpat_menus as $wpat_menu ) : ?>
                            <?php
                            $wpat_menu_value = 'menu:' . $wpat_menu->term_id;
                            $wpat_selected_menu = (string) $wpat_data['wpat_show_in_menu'];
                            ?>
                            <option value="<?php echo esc_attr( $wpat_menu_value ); ?>" <?php selected( $wpat_selected_menu, $wpat_menu_value ); ?> <?php selected( $wpat_selected_menu, (string) $wpat_menu->term_id ); ?>><?php echo esc_html( $wpat_menu->name ); ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>
                <?php
                $wpat_navigation_posts = get_posts(
                    array(
                        'post_type'      => 'wp_navigation',
                        'post_status'    => 'publish',
                        'numberposts'    => -1,
                        'orderby'        => 'title',
                        'order'          => 'ASC',
                    )
                );
                ?>
                <?php if ( ! empty( $wpat_navigation_posts ) ) : ?>
                    <optgroup label="<?php esc_attr_e( 'Navigation Blocks', 'auto-translate' ); ?>">
                        <?php foreach ( $wpat_navigation_posts as $wpat_navigation_post ) : ?>
                            <?php $wpat_navigation_value = 'navigation:' . $wpat_navigation_post->ID; ?>
                            <option value="<?php echo esc_attr( $wpat_navigation_value ); ?>" <?php selected( (string) $wpat_data['wpat_show_in_menu'], $wpat_navigation_value ); ?>><?php echo esc_html( get_the_title( $wpat_navigation_post ) ); ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>
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
            <p class="description">
                <?php esc_html_e( "Auto Translate is only supported with the 'Minimalist' widget type.", 'auto-translate' ); ?>
            </p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Custom CSS', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Add CSS rules to customize the translator widget on your site. Google\'s language list iframe cannot be styled here.', 'auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>">
            <textarea id="wpat_custom_css" name="wpat_custom_css" rows="10" class="large-text code"><?php echo esc_textarea( $wpat_data['wpat_custom_css'] ); ?></textarea>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Delete data on uninstall', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('When enabled, all plugin settings are removed when uninstalling the plugin.', 'auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>">
            <div class="custom-control custom-switch">
                <input type="checkbox" id="wpat_delete_data_on_uninstall" name="wpat_delete_data_on_uninstall" <?php checked( $wpat_data['wpat_delete_data_on_uninstall'], 'on' ); ?>/>
                <label for="wpat_delete_data_on_uninstall"><?php esc_html_e('Delete plugin data on uninstall', 'auto-translate'); ?></label>
            </div>
        </td>
    </tr>
</table>
