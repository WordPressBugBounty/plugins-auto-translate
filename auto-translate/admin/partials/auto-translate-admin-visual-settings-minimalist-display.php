<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
?>
<table class="form-table">
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Style', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Select one styling option','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input type="radio" name="wpat_min_style" value="flags" <?php if( $wpat_minimalist['wpat_min_style'] === 'flags' ){ echo "checked='checked'"; }; ?>/> <label for="wpat_min_style_flags"><?php esc_html_e('Flags', 'auto-translate'); ?></label><br/>
            <input type="radio" name="wpat_min_style" value="flat_flags" <?php if( $wpat_minimalist['wpat_min_style'] === 'flat_flags' ){ echo "checked='checked'"; }; ?>/> <label for="wpat_min_style_flat_flags"><?php esc_html_e('Flat flags', 'auto-translate'); ?></label><br/>
            <input type="radio" name="wpat_min_style" value="icon" <?php if( $wpat_minimalist['wpat_min_style'] === 'icon' ){ echo "checked='checked'"; }; ?>/> <label for="wpat_min_style_icon"><?php esc_html_e('Icon', 'auto-translate'); ?></label><br/>
            <input type="radio" name="wpat_min_style" value="clean" <?php if( $wpat_minimalist['wpat_min_style'] === 'clean' ){ echo "checked='checked'"; }; ?>/> <label for="wpat_min_style_clean"><?php esc_html_e('Text only', 'auto-translate'); ?></label>
        </td>
        <th scope="row" class="wpat_min_icon_wrapper <?php echo $wpat_minimalist['wpat_min_style']==='icon'?'':'wpat_hidden'?>">
            <?php esc_html_e('Icon', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('This is the icon for the widget','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>" class="wpat_min_icon_wrapper <?php echo $wpat_minimalist['wpat_min_style']==='icon'?'':'wpat_hidden'?>">
            <?php
            /*
                * https://github.com/bradvin/dashicons-picker
                */
            ?>
            <input id="wpat_min_icon" name="wpat_min_icon" type="hidden" value="<?php echo $wpat_minimalist['wpat_min_icon']?>"/><span id="wpat_min_icon_display" class="dashicons <?php echo $wpat_minimalist['wpat_min_icon']?>"></span>
            <input id="wpat_min_icon_picker" class="button dashicons-picker" type="button" value="<?php esc_html_e('Choose Icon', 'auto-translate'); ?>" 
            data-target="#wpat_min_icon" data-preview="#wpat_min_icon_display" />
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Text displayed', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Select a text display option','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input type="radio" name="wpat_min_txt_display" value="name" <?php if( $wpat_minimalist['wpat_min_txt_display'] === 'name' ){ echo "checked='checked'"; }; ?>/> <label for="wpat_min_txt_display"><?php esc_html_e('Language name', 'auto-translate'); ?></label><br/>
            <input type="radio" name="wpat_min_txt_display" value="name_code" <?php if( $wpat_minimalist['wpat_min_txt_display'] === 'name_code' ){ echo "checked='checked'"; }; ?>/> <label for="wpat_min_txt_display"><?php esc_html_e('Language name', 'auto-translate'); ?> - <?php esc_html_e('Language code', 'auto-translate'); ?></label><br/>
            <input type="radio" name="wpat_min_txt_display" value="code" <?php if( $wpat_minimalist['wpat_min_txt_display'] === 'code' ){ echo "checked='checked'"; }; ?>/> <label for="wpat_min_txt_display"><?php esc_html_e('Language code', 'auto-translate'); ?></label><br/>
        </td>
        <th scope="row">
            <?php esc_html_e('Chevron style', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Chevron style','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input type="radio" name="wpat_min_chevron" value="dashicons-arrow-down-alt2" <?php if( $wpat_minimalist['wpat_min_chevron'] === 'dashicons-arrow-down-alt2' ){ echo "checked='checked'"; }; ?>/> <label for="wpat_min_chevron"><?php esc_html_e('Default', 'auto-translate'); ?><span class="dashicons dashicons-arrow-down-alt2"></span></label><br/>
            <input type="radio" name="wpat_min_chevron" value="dashicons-arrow-down" <?php if( $wpat_minimalist['wpat_min_chevron'] === 'dashicons-arrow-down' ){ echo "checked='checked'"; }; ?>/> <label for="wpat_min_chevron"><?php esc_html_e('Alternative', 'auto-translate'); ?><span class="dashicons dashicons-arrow-down"></span></label><br/>
            <input type="radio" name="wpat_min_chevron" value="dashicons-arrow-down-none" <?php if( $wpat_minimalist['wpat_min_chevron'] === 'dashicons-arrow-down-none' ){ echo "checked='checked'"; }; ?>/> <label for="wpat_min_chevron"><?php esc_html_e('None', 'auto-translate'); ?></label><br/>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Border thickness', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Widgets\'s border thickness in pixels','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input type="number" id="wpat_min_border_thickness" name="wpat_min_border_thickness" value="<?php echo $wpat_minimalist['wpat_min_border_thickness']?>"/> px
        </td>
        <th scope="row">
            <?php esc_html_e('Border color', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Widget\'s border color','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input class="color-picker" type="text" id="wpat_min_border_color" name="wpat_min_border_color" value="<?php echo $wpat_minimalist['wpat_min_border_color']?>"/>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Background color', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Widget\'s background color','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input class="color-picker" type="text" id="wpat_min_background_color" name="wpat_min_background_color" value="<?php echo $wpat_minimalist['wpat_min_background_color']?>"/>
        </td>
        <th scope="row">
            <?php esc_html_e('Hover color', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('The hovered dropdown text background color','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input class="color-picker" type="text" id="wpat_min_hover_color" name="wpat_min_hover_color" value="<?php echo $wpat_minimalist['wpat_min_hover_color']?>"/>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Font color', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Font color','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input class="color-picker" type="text" id="wpat_min_font_color" name="wpat_min_font_color" value="<?php echo $wpat_minimalist['wpat_min_font_color']?>"/>
        </td>
        <th scope="row">
            <?php esc_html_e('Font name', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Font name','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input type="text" id="wpat_min_font_family" name="wpat_min_font_family" value="<?php echo esc_attr( $wpat_minimalist['wpat_min_font_family'] )?>" placeholder="<?php esc_attr_e( 'Leave it blank if not sure', 'auto-translate' ); ?>"/>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Font hover color', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('The hovered text color','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input class="color-picker" type="text" id="wpat_min_font_hover_color" name="wpat_min_font_hover_color" value="<?php echo $wpat_minimalist['wpat_min_font_hover_color']?>"/>
        </td>
        <th scope="row">
            <?php esc_html_e('Underline text', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Select to underline the hovered dropdown text','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <div class="custom-control custom-switch">
                <input type="checkbox" id="wpat_min_txt_underline" value="wpat_min_txt_underline" name="wpat_min_txt_underline" <?php if( $wpat_minimalist['wpat_min_txt_underline'] ){ echo "checked='checked'"; }; ?>/>
                <label for="wpat_min_txt_underline"><?php esc_html_e('Underline on hover', 'auto-translate'); ?></label>
            </div>
        </td>
    </tr>
</table>
<?php
// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
?>
