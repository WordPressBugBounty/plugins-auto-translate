<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wpat_data = $vars['tabs']['placement_settings'];
$wpat_selected_menu = (string) $wpat_data['wpat_show_in_menu'];
$wpat_menus = wp_get_nav_menus();
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
<div class="wpat-settings-panel wpat-placement-settings">
	<input type="hidden" name="wpat_launch_checklist_reviewed[placement]" value="1" />
	<div class="wpat-card" id="wpat-launch-placement">
		<h3 class="wpat-card-title"><?php esc_html_e( 'Floating selector', 'auto-translate' ); ?></h3>
		<table class="form-table" role="presentation">
			<tr valign="top">
				<th scope="row">
					<?php esc_html_e( 'Show floating selector', 'auto-translate' ); ?><br/>
					<small><?php esc_html_e( 'A persistent selector anchored to a viewport corner, visible on every page. Independent from menu or shortcode placements.', 'auto-translate' ); ?></small>
				</th>
				<td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>">
					<label class="wpat-toggle" for="wpat_default_location">
						<input type="hidden" name="wpat_default_location" value="0" />
						<input type="checkbox" id="wpat_default_location" name="wpat_default_location" value="1" <?php checked( (bool) $wpat_data['wpat_default_location'], true ); ?> />
						<span class="wpat-toggle-slider" aria-hidden="true"></span>
					</label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php esc_html_e( 'Position', 'auto-translate' ); ?><br/>
					<small><?php esc_html_e( 'Which corner of the viewport to anchor the selector.', 'auto-translate' ); ?></small>
				</th>
				<td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>">
					<select id="wpat_floating_position" name="wpat_floating_position">
						<option value="top_left" <?php selected( $wpat_data['wpat_floating_position'], 'top_left' ); ?>><?php esc_html_e( 'Top left', 'auto-translate' ); ?></option>
						<option value="top_right" <?php selected( $wpat_data['wpat_floating_position'], 'top_right' ); ?>><?php esc_html_e( 'Top right', 'auto-translate' ); ?></option>
						<option value="bottom_right" <?php selected( $wpat_data['wpat_floating_position'], 'bottom_right' ); ?>><?php esc_html_e( 'Bottom right', 'auto-translate' ); ?></option>
						<option value="bottom_left" <?php selected( $wpat_data['wpat_floating_position'], 'bottom_left' ); ?>><?php esc_html_e( 'Bottom left', 'auto-translate' ); ?></option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php esc_html_e( 'Offset from edge', 'auto-translate' ); ?><br/>
					<small><?php esc_html_e( 'Distance from the viewport edge and admin bar, in pixels.', 'auto-translate' ); ?></small>
				</th>
				<td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>">
					<div class="wpat-offset-fields">
						<label for="wpat_floating_offset_x"><?php esc_html_e( 'Horizontal', 'auto-translate' ); ?></label>
						<input type="number" min="0" max="128" id="wpat_floating_offset_x" name="wpat_floating_offset_x" value="<?php echo absint( $wpat_data['wpat_floating_offset_x'] ); ?>" /> <span>px</span>
						<label for="wpat_floating_offset_y"><?php esc_html_e( 'Vertical', 'auto-translate' ); ?></label>
						<input type="number" min="0" max="128" id="wpat_floating_offset_y" name="wpat_floating_offset_y" value="<?php echo absint( $wpat_data['wpat_floating_offset_y'] ); ?>" /> <span>px</span>
					</div>
				</td>
			</tr>
		</table>
	</div>

	<div class="wpat-card">
		<h3 class="wpat-card-title"><?php esc_html_e( 'Navigation menu', 'auto-translate' ); ?></h3>
		<table class="form-table" role="presentation">
			<tr valign="top">
				<th scope="row">
					<?php esc_html_e( 'Menu placement', 'auto-translate' ); ?><br/>
					<small><?php esc_html_e( 'Inject the selector into a registered WordPress navigation menu or Navigation block. If your theme does not expose menus here, use a shortcode, widget, or block.', 'auto-translate' ); ?></small>
				</th>
				<td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>">
					<select id="wpat_show_in_menu" name="wpat_show_in_menu">
						<option value="" <?php selected( $wpat_selected_menu, '' ); ?>> - <?php esc_html_e( 'Disabled', 'auto-translate' ); ?> - </option>
						<?php if ( ! empty( $wpat_menus ) ) : ?>
							<optgroup label="<?php esc_attr_e( 'Classic Menus', 'auto-translate' ); ?>">
								<?php foreach ( $wpat_menus as $wpat_menu ) : ?>
									<?php $wpat_menu_value = 'menu:' . $wpat_menu->term_id; ?>
									<option value="<?php echo esc_attr( $wpat_menu_value ); ?>" <?php selected( $wpat_selected_menu, $wpat_menu_value ); ?> <?php selected( $wpat_selected_menu, (string) $wpat_menu->term_id ); ?>><?php echo esc_html( $wpat_menu->name ); ?></option>
								<?php endforeach; ?>
							</optgroup>
						<?php endif; ?>
						<?php if ( ! empty( $wpat_navigation_posts ) ) : ?>
							<optgroup label="<?php esc_attr_e( 'Navigation Blocks', 'auto-translate' ); ?>">
								<?php foreach ( $wpat_navigation_posts as $wpat_navigation_post ) : ?>
									<?php $wpat_navigation_value = 'navigation:' . $wpat_navigation_post->ID; ?>
									<option value="<?php echo esc_attr( $wpat_navigation_value ); ?>" <?php selected( $wpat_selected_menu, $wpat_navigation_value ); ?>><?php echo esc_html( get_the_title( $wpat_navigation_post ) ); ?></option>
								<?php endforeach; ?>
							</optgroup>
						<?php endif; ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php esc_html_e( 'Position in menu', 'auto-translate' ); ?><br/>
					<small><?php esc_html_e( 'Insert the selector at the start or end of the selected menu.', 'auto-translate' ); ?></small>
				</th>
				<td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>">
					<div class="wpat-pill-group" id="wpat_menu_position_group">
						<label class="wpat-pill">
							<input type="radio" name="wpat_menu_position" value="start" <?php checked( $wpat_data['wpat_menu_position'], 'start' ); ?> />
							<span><?php esc_html_e( 'Start', 'auto-translate' ); ?></span>
						</label>
						<label class="wpat-pill">
							<input type="radio" name="wpat_menu_position" value="end" <?php checked( $wpat_data['wpat_menu_position'], 'end' ); ?> />
							<span><?php esc_html_e( 'End of menu', 'auto-translate' ); ?></span>
						</label>
					</div>
				</td>
			</tr>
		</table>
	</div>

	<div class="wpat-card">
		<h3 class="wpat-card-title"><?php esc_html_e( 'CSS injection', 'auto-translate' ); ?></h3>
		<table class="form-table" role="presentation">
			<tr valign="top">
				<th scope="row">
					<?php esc_html_e( 'Wrapper CSS selector', 'auto-translate' ); ?><br/>
					<small><?php esc_html_e( 'Appends selector instances to matching elements on page load. Use a valid CSS selector, e.g. .site-header .language-slot', 'auto-translate' ); ?></small>
				</th>
				<td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>">
					<input type="text" id="wpat_wrapper_selector" name="wpat_wrapper_selector" value="<?php echo esc_attr( (string) $wpat_data['wpat_wrapper_selector'] ); ?>" placeholder=".site-header .language-slot" class="regular-text" />
				</td>
			</tr>
		</table>
	</div>

	<div class="wpat-card">
		<h3 class="wpat-card-title"><?php esc_html_e( 'Manual placement', 'auto-translate' ); ?></h3>
		<table class="widefat striped wpat-manual-placement-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Method', 'auto-translate' ); ?></th>
					<th><?php esc_html_e( 'Best for', 'auto-translate' ); ?></th>
					<th><?php esc_html_e( 'How to use', 'auto-translate' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<strong><?php esc_html_e( 'Selector block', 'auto-translate' ); ?></strong>
						<span class="wpat-method-tag wpat-method-tag-blue"><?php esc_html_e( 'Block editor', 'auto-translate' ); ?></span>
					</td>
					<td><?php esc_html_e( 'Block editor, Site Editor, template parts', 'auto-translate' ); ?></td>
					<td>
						<?php if ( ! empty( $wpat_data['supports_selector_block'] ) ) : ?>
							<?php esc_html_e( 'Search for "Automatic Translator Selector" in the block inserter and place it anywhere in your layout.', 'auto-translate' ); ?>
						<?php else : ?>
							<?php esc_html_e( 'This WordPress install does not expose the required block APIs yet. Use the widget or shortcode placement methods instead.', 'auto-translate' ); ?>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td>
						<strong><?php esc_html_e( 'Widget', 'auto-translate' ); ?></strong>
						<span class="wpat-method-tag wpat-method-tag-purple"><?php esc_html_e( 'Widget areas', 'auto-translate' ); ?></span>
					</td>
					<td><?php esc_html_e( 'Widget areas and sidebars', 'auto-translate' ); ?></td>
					<td><?php echo wp_kses_post( __( 'Go to <b>Appearance → Widgets</b> or the Site Editor, then add the Automatic Translator Button widget to any area.', 'auto-translate' ) ); ?></td>
				</tr>
				<tr>
					<td>
						<strong><?php esc_html_e( 'Shortcode', 'auto-translate' ); ?></strong>
						<span class="wpat-method-tag wpat-method-tag-green"><?php esc_html_e( 'Classic editor', 'auto-translate' ); ?></span>
					</td>
					<td><?php esc_html_e( 'Pages, builders, classic editor, templates', 'auto-translate' ); ?></td>
					<td>
						<div class="wpat-copy-row"><code>[auto_translate_button]</code><button type="button" class="button" data-wpat-copy-text="[auto_translate_button]" data-wpat-copy-label="Copy"><?php esc_html_e( 'Copy', 'auto-translate' ); ?></button></div>
					</td>
				</tr>
				<tr>
					<td>
						<strong><?php esc_html_e( 'Language link', 'auto-translate' ); ?></strong>
						<span class="wpat-method-tag wpat-method-tag-sky"><?php esc_html_e( 'Menus', 'auto-translate' ); ?></span>
					</td>
					<td><?php esc_html_e( 'Menus and custom language switch links', 'auto-translate' ); ?></td>
					<td>
						<div class="wpat-copy-row"><code>[auto_translate_link lang="es"]</code><button type="button" class="button" data-wpat-copy-text="[auto_translate_link lang=&quot;es&quot;]" data-wpat-copy-label="Copy"><?php esc_html_e( 'Copy', 'auto-translate' ); ?></button></div>
						<div class="wpat-copy-row"><code>[auto_translate_link lang="fr" label="Français"]</code><button type="button" class="button" data-wpat-copy-text="[auto_translate_link lang=&quot;fr&quot; label=&quot;Français&quot;]" data-wpat-copy-label="Copy"><?php esc_html_e( 'Copy', 'auto-translate' ); ?></button></div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
