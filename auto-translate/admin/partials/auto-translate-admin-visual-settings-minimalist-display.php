<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wpat_plugin_file = dirname( dirname( __DIR__ ) ) . '/auto-translate.php';
$wpat_fr_flag_url = plugins_url( 'public/images/flags/svg/fr.svg', $wpat_plugin_file );
$wpat_minimal_base_style = isset( $wpat_min_base_style ) ? (string) $wpat_min_base_style : 'compact';
$wpat_min_text_display = (string) $wpat_minimalist['wpat_min_txt_display'];

if ( 'clean' === $wpat_minimalist['wpat_min_style'] && 'none' === $wpat_min_text_display ) {
	$wpat_min_text_display = 'name';
}
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
?>
<input type="hidden" name="wpat_launch_checklist_reviewed[style]" value="1" />
<div id="wpat-custom-styling"<?php echo in_array( $wpat_min_preset, array( 'custom', 'minimal_custom' ), true ) ? '' : ' hidden'; ?>>
<div class="wpat-card" id="wpat-launch-style">
    <h3 class="wpat-card-title"><?php esc_html_e('Style your selector', 'auto-translate'); ?></h3>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">
                <?php esc_html_e('Icon style', 'auto-translate'); ?><br/>
                <small><?php esc_html_e('How the language is visually represented in the selector button.', 'auto-translate'); ?></small>
            </th>
            <td colspan="<?php echo $wpat_columns?>">
                <div class="wpat-tile-group">
                    <label class="wpat-style-tile wpat-style-tile-flags <?php echo $wpat_minimalist['wpat_min_style'] === 'flags' ? 'is-active' : ''; ?>">
                        <input type="radio" name="wpat_min_style" value="flags" <?php checked( $wpat_minimalist['wpat_min_style'], 'flags' ); ?>/>
                        <span class="wpat-style-tile-icon wpat-style-tile-flag" style="background-image:url('<?php echo esc_url( $wpat_fr_flag_url ); ?>');" aria-hidden="true"></span><span>Flags</span>
                    </label>
                    <label class="wpat-style-tile <?php echo $wpat_minimalist['wpat_min_style'] === 'emoji_flags' ? 'is-active' : ''; ?>">
                        <input type="radio" name="wpat_min_style" value="emoji_flags" <?php checked( $wpat_minimalist['wpat_min_style'], 'emoji_flags' ); ?>/>
                        <span class="wpat-style-tile-icon wpat-style-tile-emoji" aria-hidden="true">🇫🇷</span><span><?php esc_html_e( 'Emoji flags', 'auto-translate' ); ?></span>
                    </label>
                    <?php if ( 'flat_flags' === $wpat_minimalist['wpat_min_style'] ) : ?>
                    <label class="wpat-style-tile wpat-style-tile-flat-flags wpat-legacy-style-tile is-active">
                        <input type="radio" name="wpat_min_style" value="flat_flags" <?php checked( $wpat_minimalist['wpat_min_style'], 'flat_flags' ); ?>/>
                        <span class="wpat-style-tile-icon wpat-style-tile-flag" style="background-image:url('<?php echo esc_url( $wpat_fr_flag_url ); ?>');" aria-hidden="true"></span><span>Flat flags</span>
                    </label>
                    <?php endif; ?>
                    <label class="wpat-style-tile wpat-style-tile-icon-option <?php echo $wpat_minimalist['wpat_min_style'] === 'icon' ? 'is-active' : ''; ?>"<?php echo 'minimal' === $wpat_minimal_base_style ? ' hidden' : ''; ?>>
                        <input type="radio" name="wpat_min_style" value="icon" <?php checked( $wpat_minimalist['wpat_min_style'], 'icon' ); ?>/>
                        <span class="wpat-style-tile-icon">🌐</span><span>Icon</span>
                    </label>
                    <label class="wpat-style-tile <?php echo $wpat_minimalist['wpat_min_style'] === 'clean' ? 'is-active' : ''; ?>">
                        <input type="radio" name="wpat_min_style" value="clean" <?php checked( $wpat_minimalist['wpat_min_style'], 'clean' ); ?>/>
                        <span class="wpat-style-tile-icon">EN</span><span>Text only</span>
                    </label>
                </div>
                <div class="wpat_min_icon_wrapper <?php echo $wpat_minimalist['wpat_min_style']==='icon'?'':'wpat_hidden'?> wpat-icon-picker-row">
                    <input id="wpat_min_icon" name="wpat_min_icon" type="hidden" value="<?php echo esc_attr( $wpat_minimalist['wpat_min_icon'] );?>"/>
                    <span id="wpat_min_icon_display" class="dashicons <?php echo esc_attr( $wpat_minimalist['wpat_min_icon'] );?>"></span>
                    <input id="wpat_min_icon_picker" class="button dashicons-picker" type="button" value="<?php esc_html_e('Choose Icon', 'auto-translate'); ?>"
                    data-target="#wpat_min_icon" data-preview="#wpat_min_icon_display" />
                </div>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <?php esc_html_e('Label text', 'auto-translate'); ?><br/>
                <small><?php esc_html_e('What text appears alongside the icon in the button.', 'auto-translate'); ?></small>
            </th>
            <td colspan="<?php echo $wpat_columns?>">
                <div class="wpat-pill-group wpat-label-text-options">
                    <label class="wpat-pill"><input type="radio" name="wpat_min_txt_display" value="name" <?php checked( $wpat_min_text_display, 'name' ); ?>/><span><?php esc_html_e('Name', 'auto-translate'); ?></span></label>
                    <label class="wpat-pill"><input type="radio" name="wpat_min_txt_display" value="code" <?php checked( $wpat_min_text_display, 'code' ); ?>/><span><?php esc_html_e('Code', 'auto-translate'); ?></span></label>
                    <label class="wpat-pill"><input type="radio" name="wpat_min_txt_display" value="name_code" <?php checked( $wpat_min_text_display, 'name_code' ); ?>/><span><?php esc_html_e('Name + code', 'auto-translate'); ?></span></label>
                    <label class="wpat-pill wpat-no-text-option"<?php echo 'clean' === $wpat_minimalist['wpat_min_style'] ? ' hidden' : ''; ?>><input type="radio" name="wpat_min_txt_display" value="none" <?php checked( $wpat_min_text_display, 'none' ); ?>/><span><?php esc_html_e('No text', 'auto-translate'); ?></span></label>
                </div>
            </td>
        </tr>
        <tr valign="top" class="wpat-text-divider-option"<?php echo 'minimal' === $wpat_minimal_base_style && 'clean' === $wpat_minimalist['wpat_min_style'] ? '' : ' hidden'; ?>>
            <th scope="row">
                <?php esc_html_e( 'Text divider', 'auto-translate' ); ?><br/>
                <small><?php esc_html_e( 'How text-only links are separated.', 'auto-translate' ); ?></small>
            </th>
            <td colspan="<?php echo $wpat_columns?>">
                <div class="wpat-pill-group">
                    <label class="wpat-pill"><input type="radio" name="wpat_min_text_divider" value="none" <?php checked( $wpat_minimalist['wpat_min_text_divider'], 'none' ); ?>/><span><?php esc_html_e( 'None', 'auto-translate' ); ?></span></label>
                    <label class="wpat-pill"><input type="radio" name="wpat_min_text_divider" value="pipe" <?php checked( $wpat_minimalist['wpat_min_text_divider'], 'pipe' ); ?>/><span>|</span></label>
                    <label class="wpat-pill"><input type="radio" name="wpat_min_text_divider" value="brackets" <?php checked( $wpat_minimalist['wpat_min_text_divider'], 'brackets' ); ?>/><span>[]</span></label>
                </div>
            </td>
        </tr>
        <tr valign="top" class="wpat-compact-chevron-option"<?php echo 'compact' === $wpat_minimal_base_style ? '' : ' hidden'; ?>>
            <th scope="row">
                <?php esc_html_e( 'Chevron style', 'auto-translate' ); ?><br/>
                <small><?php esc_html_e( 'Dropdown indicator shown next to the selector text.', 'auto-translate' ); ?></small>
            </th>
            <td colspan="<?php echo $wpat_columns; ?>">
                <div class="wpat-pill-group">
                    <label class="wpat-pill"><input type="radio" name="wpat_min_chevron" value="dashicons-arrow-down-alt2" <?php checked( $wpat_minimalist['wpat_min_chevron'], 'dashicons-arrow-down-alt2' ); ?>/><span>Default <span class="dashicons dashicons-arrow-down-alt2"></span></span></label>
                    <label class="wpat-pill"><input type="radio" name="wpat_min_chevron" value="dashicons-arrow-down" <?php checked( $wpat_minimalist['wpat_min_chevron'], 'dashicons-arrow-down' ); ?>/><span>Alt <span class="dashicons dashicons-arrow-down"></span></span></label>
                    <label class="wpat-pill"><input type="radio" name="wpat_min_chevron" value="dashicons-arrow-down-none" <?php checked( $wpat_minimalist['wpat_min_chevron'], 'dashicons-arrow-down-none' ); ?>/><span><?php esc_html_e( 'None', 'auto-translate' ); ?></span></label>
                </div>
            </td>
        </tr>
        <tr valign="top" class="wpat-compact-layout-option"<?php echo 'compact' === $wpat_minimal_base_style ? '' : ' hidden'; ?>>
            <th scope="row">
                <?php esc_html_e('Selector behavior', 'auto-translate'); ?><br/>
                <small><?php esc_html_e('Standard dropdown works well for a few languages. Popup with search is better for 10+.', 'auto-translate'); ?></small>
            </th>
            <td colspan="<?php echo $wpat_columns?>">
                <div class="wpat-pill-group">
                    <label class="wpat-pill"><input type="radio" name="wpat_min_layout" value="dropdown" <?php checked( $wpat_minimalist['wpat_min_layout'], 'dropdown' ); ?>/><span><?php esc_html_e('Dropdown', 'auto-translate'); ?></span></label>
                    <label class="wpat-pill"><input type="radio" name="wpat_min_layout" value="popup_search" <?php checked( $wpat_minimalist['wpat_min_layout'], 'popup_search' ); ?>/><span><?php esc_html_e('Popup with search', 'auto-translate'); ?></span></label>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="wpat-card">
    <h3 class="wpat-card-title"><?php esc_html_e('Colors', 'auto-translate'); ?></h3>
    <table class="form-table wpat-colors-table">
        <tr valign="top">
            <th scope="row"><?php esc_html_e('Border color', 'auto-translate'); ?></th>
            <td colspan="<?php echo $wpat_columns?>">
                <div class="wpat-color-setting">
                    <input class="color-picker" type="text" id="wpat_min_border_color" name="wpat_min_border_color" value="<?php echo esc_attr( $wpat_minimalist['wpat_min_border_color'] );?>"/>
                    <input type="hidden" id="wpat_min_border_transparent" name="wpat_min_border_transparent" value="<?php echo 'on' === $wpat_minimalist['wpat_min_border_transparent'] ? 'on' : ''; ?>" data-wpat-transparency-default="<?php echo $wpat_minimalist['wpat_min_border_transparent_is_default'] ? '1' : '0'; ?>"/>
                    <label class="wpat-color-use-toggle"><input type="checkbox" id="wpat_min_border_use_color" data-wpat-use-color data-wpat-transparent-input="#wpat_min_border_transparent" data-wpat-color-target="#wpat_min_border_color" <?php checked( $wpat_minimalist['wpat_min_border_transparent'], '' ); ?>/><span class="wpat-toggle-slider" aria-hidden="true"></span><span><?php esc_html_e( 'Use color', 'auto-translate' ); ?></span></label>
                </div>
            </td>
            <th scope="row"><?php esc_html_e('Background color', 'auto-translate'); ?></th>
            <td colspan="<?php echo $wpat_columns?>">
                <div class="wpat-color-setting">
                    <input class="color-picker" type="text" id="wpat_min_background_color" name="wpat_min_background_color" value="<?php echo esc_attr( $wpat_minimalist['wpat_min_background_color'] );?>"/>
                    <input type="hidden" id="wpat_min_background_transparent" name="wpat_min_background_transparent" value="<?php echo 'on' === $wpat_minimalist['wpat_min_background_transparent'] ? 'on' : ''; ?>" data-wpat-transparency-default="<?php echo $wpat_minimalist['wpat_min_background_transparent_is_default'] ? '1' : '0'; ?>"/>
                    <label class="wpat-color-use-toggle"><input type="checkbox" id="wpat_min_background_use_color" data-wpat-use-color data-wpat-transparent-input="#wpat_min_background_transparent" data-wpat-color-target="#wpat_min_background_color" <?php checked( $wpat_minimalist['wpat_min_background_transparent'], '' ); ?>/><span class="wpat-toggle-slider" aria-hidden="true"></span><span><?php esc_html_e( 'Use color', 'auto-translate' ); ?></span></label>
                </div>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php esc_html_e('Font color', 'auto-translate'); ?></th>
            <td colspan="<?php echo $wpat_columns?>">
                <div class="wpat-color-setting">
                    <input class="color-picker" type="text" id="wpat_min_font_color" name="wpat_min_font_color" value="<?php echo esc_attr( $wpat_minimalist['wpat_min_font_color'] );?>"/>
                </div>
            </td>
            <th scope="row"><?php esc_html_e('Hover background', 'auto-translate'); ?></th>
            <td colspan="<?php echo $wpat_columns?>">
                <div class="wpat-color-setting">
                    <input class="color-picker" type="text" id="wpat_min_hover_color" name="wpat_min_hover_color" value="<?php echo esc_attr( $wpat_minimalist['wpat_min_hover_color'] );?>"/>
                    <input type="hidden" id="wpat_min_hover_transparent" name="wpat_min_hover_transparent" value="<?php echo 'on' === $wpat_minimalist['wpat_min_hover_transparent'] ? 'on' : ''; ?>" data-wpat-transparency-default="<?php echo $wpat_minimalist['wpat_min_hover_transparent_is_default'] ? '1' : '0'; ?>"/>
                    <label class="wpat-color-use-toggle"><input type="checkbox" id="wpat_min_hover_use_color" data-wpat-use-color data-wpat-transparent-input="#wpat_min_hover_transparent" data-wpat-color-target="#wpat_min_hover_color" <?php checked( $wpat_minimalist['wpat_min_hover_transparent'], '' ); ?>/><span class="wpat-toggle-slider" aria-hidden="true"></span><span><?php esc_html_e( 'Use color', 'auto-translate' ); ?></span></label>
                </div>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php esc_html_e('Font hover color', 'auto-translate'); ?></th>
            <td colspan="<?php echo $wpat_columns?>">
                <div class="wpat-color-setting">
                    <input class="color-picker" type="text" id="wpat_min_font_hover_color" name="wpat_min_font_hover_color" value="<?php echo esc_attr( $wpat_minimalist['wpat_min_font_hover_color'] );?>"/>
                </div>
            </td>
            <th scope="row"><?php esc_html_e('Border thickness', 'auto-translate'); ?></th>
            <td colspan="<?php echo $wpat_columns?>">
                <div class="wpat-number-inline">
                    <input type="number" id="wpat_min_border_thickness" name="wpat_min_border_thickness" value="<?php echo absint( $wpat_minimalist['wpat_min_border_thickness'] );?>"/>
                    <span>px</span>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="wpat-card">
    <h3 class="wpat-card-title"><?php esc_html_e('Typography', 'auto-translate'); ?></h3>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">
                <?php esc_html_e('Font name', 'auto-translate'); ?><br/>
                <small><?php esc_html_e('Override the selector font. Leave blank to inherit from your theme.', 'auto-translate'); ?></small>
            </th>
            <td colspan="<?php echo $wpat_columns?>">
                <input type="text" id="wpat_min_font_family" name="wpat_min_font_family" value="<?php echo esc_attr( $wpat_minimalist['wpat_min_font_family'] )?>" placeholder="<?php esc_attr_e( 'e.g. Roboto, Open Sans', 'auto-translate' ); ?>"/>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <?php esc_html_e('Underline on hover', 'auto-translate'); ?><br/>
                <small><?php esc_html_e('Underline the hovered dropdown item text.', 'auto-translate'); ?></small>
            </th>
            <td colspan="<?php echo $wpat_columns?>">
                <label class="wpat-toggle" for="wpat_min_txt_underline">
                    <input type="checkbox" id="wpat_min_txt_underline" value="wpat_min_txt_underline" name="wpat_min_txt_underline" <?php checked( $wpat_minimalist['wpat_min_txt_underline'], 'wpat_min_txt_underline' ); ?>/>
                    <span class="wpat-toggle-slider" aria-hidden="true"></span>
                </label>
            </td>
        </tr>
    </table>
</div>

<?php
// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
?>
</div>
