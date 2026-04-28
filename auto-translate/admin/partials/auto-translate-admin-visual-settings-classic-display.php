<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
?>
<style>
.goog-te-gadget-simple {
    white-space: nowrap;
    background-image: linear-gradient(135deg, <?php echo $wpat_classic['wpat_color_1']?> 0, <?php echo $wpat_classic['wpat_color_2']?> 100%);
    border-radius: <?php echo $wpat_classic['wpat_border_radius']?>px;
    border:  <?php echo $wpat_classic['wpat_border_thickness']?>px solid <?php echo $wpat_classic['wpat_border_color']?>;
}
.goog-te-gadget-simple .goog-te-menu-value {
    color: <?php echo esc_html( $wpat_classic['wpat_font_color'] )?>;
    <?php if( $wpat_classic['wpat_font_family'] != '' ): ?>
    font-family: <?php echo esc_html( $wpat_classic['wpat_font_family'] )?>;
    <?php endif; ?>
}
#translate-dropdown {
    border:  <?php echo $wpat_classic['wpat_dropdown_border_thickness']?>px solid <?php echo $wpat_classic['wpat_dropdown_border_color']?>;
    background-color: <?php echo $wpat_classic['wpat_dropdown_background_color']?>;
}
a.goog-te-menu2-item > div {
    color: <?php echo $wpat_classic['wpat_dropdown_font_color']?>;
}
a.goog-te-menu2-item-selected > div {
    color: <?php echo $wpat_classic['wpat_dropdown_font_selected_color']?>;
}
a[class^="goog-te-menu2-item"] > div {
    <?php if( $wpat_classic['wpat_dropdown_font_family'] != '' ): ?>
    font-family: <?php echo esc_html( $wpat_classic['wpat_dropdown_font_family'] )?>;
    <?php endif; ?>
}
a.goog-te-menu2-item:hover > div {
    background-color: <?php echo $wpat_classic['wpat_dropdown_hover_color']?>;
    color: <?php echo $wpat_classic['wpat_dropdown_font_hover_color']?>;
}
</style>
<table class="form-table">
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Add icon', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Whether to add an icon to the button or not','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <div class="custom-control custom-switch">
                <input type="checkbox" id="show-icon" name="wpat_show_icon" <?php if( $wpat_classic['wpat_show_icon'] ){ echo "checked='checked'"; }; ?>/>
                <label for="show-icon-on"><?php esc_html_e('Add icon', 'auto-translate'); ?></label> 
            </div>
        </td>
        <th scope="row" class="icon-selector <?php echo $wpat_classic['wpat_show_icon']==='on'?'':'wpat_grayedout'?>">
            <?php esc_html_e('Icon', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('This is the icon used on the front-end button','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>" class="icon-selector <?php echo $wpat_classic['wpat_show_icon']==='on'?'':'wpat_grayedout'?>">
            <?php
            /*
                * https://github.com/bradvin/dashicons-picker
                */
            ?>
            <input id="button_icon" name="wpat_button_icon" type="hidden" value="<?php echo $wpat_classic['wpat_button_icon']?>"/><span id="display-button-icon" class="dashicons <?php echo $wpat_classic['wpat_button_icon']?>"></span>
            <input id="button_icon_picker" class="button dashicons-picker" type="button" value="<?php esc_html_e('Choose Icon', 'auto-translate'); ?>" 
            data-target="#button_icon" data-preview="#display-button-icon" 
            <?php echo $wpat_classic['wpat_show_icon']==='on'?'':'disabled'?>
            />
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Font color', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Button\'s font color','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input class="color-picker" type="text" id="font_color" name="wpat_font_color" value="<?php echo $wpat_classic['wpat_font_color']?>"/>
        </td>
        <th scope="row">
            <?php esc_html_e('Font name', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Button\'s font name','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input type="text" id="font_family" name="wpat_font_family" value="<?php echo esc_attr( $wpat_classic['wpat_font_family'] )?>" placeholder="<?php esc_attr_e( 'Leave it blank if not sure', 'auto-translate' ); ?>"/>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Color 1', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Button\'s initial color','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input class="color-picker" type="text" id="color_1" name="wpat_color_1" value="<?php echo $wpat_classic['wpat_color_1']?>"/>
        </td>
        <th scope="row">
            <?php esc_html_e('Color 2', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Button\'s last color','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input class="color-picker" type="text" id="color_2" name="wpat_color_2" value="<?php echo $wpat_classic['wpat_color_2']?>"/>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Border thickness', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Buttons\'s border thickness in pixels','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input type="number" id="border_thickness" name="wpat_border_thickness" value="<?php echo $wpat_classic['wpat_border_thickness']?:0?>"/> px
        </td>
        <th scope="row">
            <?php esc_html_e('Border color', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Button\'s border color','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input class="color-picker" type="text" id="border_color" name="wpat_border_color" value="<?php echo $wpat_classic['wpat_border_color']?>"/>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Rounded corners', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('This is the front-end button\'s roundness','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <label for="radius-squared"><?php esc_html_e('Squared', 'auto-translate'); ?></label> <input type="radio" id="radius-squared" name="wpat_border_radius" value="0" <?php if( is_null($wpat_classic['wpat_border_radius']) ||  !($wpat_classic['wpat_border_radius']) ||  ($wpat_classic['wpat_border_radius']=="") || $wpat_classic['wpat_border_radius']=="0"){ echo "checked='checked'"; }; ?>/>
            <label for="radius-rounded"><?php esc_html_e('Rounded', 'auto-translate'); ?></label> <input type="radio" id="radius-rounded" name="wpat_border_radius" value="4" <?php if($wpat_classic['wpat_border_radius']=="4"){ echo "checked='checked'"; }; ?>/>
            <label for="radius-very-rounded"><?php esc_html_e('Very rounded', 'auto-translate'); ?></label> <input type="radio" id="radius-very-rounded" name="wpat_border_radius" value="32" <?php if($wpat_classic['wpat_border_radius']=="32"){ echo "checked='checked'"; }; ?>/>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Size', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('This is the front-end button\'s size','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <label for="size-small"><?php esc_html_e('Small', 'auto-translate'); ?></label> <input type="radio" id="size-small" name="wpat_widget_size" value="small" <?php if($wpat_classic['wpat_widget_size']=="small"){ echo "checked='checked'"; }; ?>/>
            <label for="size-large"><?php esc_html_e('Large', 'auto-translate'); ?></label> <input type="radio" id="size-large" name="wpat_widget_size" value="large" <?php if($wpat_classic['wpat_widget_size']=="large"){ echo "checked='checked'"; }; ?>/>
        </td>
    </tr>


</table>
<h2><?php esc_html_e('Dropdown Styling', 'auto-translate'); ?></h2>
<div class="styling-preview">
    <div 
        id="translate-dropdown" 
        class="<?php if( $wpat_classic['wpat_dropdown_shadow'] ){ echo "dropdown-shadow"; }; ?>"
    >
        <table cellspacing="0" cellpadding="0" border="0">
            <tbody>
                <tr valign="top">
                    <td>
                        <a class="goog-te-menu2-item-selected" href="javascript:void(0)"><div style="white-space: nowrap;"><span class="indicator">›</span><span class="text"><?php esc_html_e('Select Language', 'auto-translate');?></span></div></a>
                        <?php foreach($wpat_demo_langs as $wpat_demo_lang): ?>
                            <a class="goog-te-menu2-item" href="javascript:void(0)"><div style="white-space: nowrap;"><span class="indicator">›</span><span class="text"><?php echo $wpat_demo_lang?></span></div></a>
                        <?php endforeach; ?>
                    </td></tr>
            </tbody></table>
    </div>
</div>
<table class="form-table">
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Box shadow', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Whether to display the box shadow or not','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <div class="custom-control custom-switch">
                <input type="checkbox" id="dropdown-shadow" name="wpat_dropdown_shadow" <?php if( $wpat_classic['wpat_dropdown_shadow'] ){ echo "checked='checked'"; }; ?>/>
                <label for="default-location-on"><?php esc_html_e('Display box shadow', 'auto-translate'); ?></label> 
            </div>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Border thickness', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Dropdown\'s border thickness in pixels','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input type="number" id="dropdown_border_thickness" name="wpat_dropdown_border_thickness" value="<?php echo $wpat_classic['wpat_dropdown_border_thickness']?:0?>"/> px
        </td>
        <th scope="row">
            <?php esc_html_e('Border color', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Dropdown\'s border color','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input class="color-picker" type="text" id="dropdown_border_color" name="wpat_dropdown_border_color" value="<?php echo $wpat_classic['wpat_dropdown_border_color']?>"/>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Background color', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Dropdown\'s background color','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input class="color-picker" type="text" id="dropdown_background_color" name="wpat_dropdown_background_color" value="<?php echo $wpat_classic['wpat_dropdown_background_color']?>"/>
        </td>
        <th scope="row">
            <?php esc_html_e('Hover color', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Dropdown\'s hover over color','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input class="color-picker" type="text" id="dropdown_hover_color" name="wpat_dropdown_hover_color" value="<?php echo $wpat_classic['wpat_dropdown_hover_color']?>"/>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Font color', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Dropdown\'s font color','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input class="color-picker" type="text" id="dropdown_font_color" name="wpat_dropdown_font_color" value="<?php echo $wpat_classic['wpat_dropdown_font_color']?>"/>
        </td>
        <th scope="row">
            <?php esc_html_e('Font name', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Dropdown\'s font name','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input type="text" id="dropdown_font_family" name="wpat_dropdown_font_family" value="<?php echo esc_attr( $wpat_classic['wpat_dropdown_font_family'] )?>" placeholder="<?php esc_attr_e( 'Leave it blank if not sure', 'auto-translate' ); ?>"/>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Font hover over color', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Dropdown\'s font color when hovered over','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input class="color-picker" type="text" id="dropdown_font_hover_color" name="wpat_dropdown_font_hover_color" value="<?php echo $wpat_classic['wpat_dropdown_font_hover_color']?>"/>
        </td>
        <th scope="row">
            <?php esc_html_e('Font selected color', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Dropdown\'s font color when selected','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo $wpat_columns?>">
            <input class="color-picker" type="text" id="dropdown_font_selected_color" name="wpat_dropdown_font_selected_color" value="<?php echo $wpat_classic['wpat_dropdown_font_selected_color']?>"/>
        </td>
    </tr>
</table>
