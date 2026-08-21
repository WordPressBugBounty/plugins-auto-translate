<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fired during plugin activation
 *
 * @link       https://pampa.dev
 * @since      1.0.0
 *
 * @package    Auto_Translate
 * @subpackage Auto_Translate/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Auto_Translate
 * @subpackage Auto_Translate/includes
 * @author     Pampa Dev <intouch@pampa.dev>
 */
class Auto_Translate_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		require_once plugin_dir_path( __FILE__ ) . 'class-auto-translate-lifecycle.php';
		self::add_options();
	}

	public static function add_options() {
		$default_wpat_widget_type = 'minimalist';
		$is_existing_installation = (bool) get_option('wpat_supported_languages');
		$has_go_live_option       = null !== get_option( 'wpat_go_live', null );
		$default_wpat_floating_position = 'bottom_left';

		// The default value for wpat_auto_detect depends on the plugin version we come from.
		// If we come from a version before 1.4.4, we set the default value to 'disabled'
		// because that means that the website is already using the plugin and we don't want 
		// to force the automatic translatation of the site
		$default_wpat_auto_detect = 'enabled';
		if(	$is_existing_installation && // If the option exists then it's an existing installation
			version_compare((string) get_option('wpat_auto_translate_version', '0.0.0'), '1.5.0') < 0
		) {
			$default_wpat_auto_detect = 'disabled';
		}

		update_option('wpat_auto_translate_version', AUTO_TRANSLATE_VERSION);

		/* Language settings */
		add_option('wpat_base_language', '');

		/* Styling settings */
		add_option('wpat_widget_type', $default_wpat_widget_type);
		// Classic settings
		add_option('wpat_button_icon', 'dashicons-translation');
		add_option('wpat_show_icon', 'on');
		add_option('wpat_supported_languages', array('ar', 'bn', 'de', 'en', 'fr', 'hi', 'id', 'pt', 'ru', 'es'));
		add_option('wpat_language_order', '');
		add_option('wpat_language_name_display', 'native');
		add_option('wpat_color_1', '#000');
		add_option('wpat_color_2', '#000');
		add_option('wpat_widget_size', get_option('wpat_size', 'smaller')==='smaller'?'small':'large');
		add_option('wpat_border_radius', '0');
		add_option('wpat_border_thickness', 1);
		add_option('wpat_border_color', '#fff');
		add_option('wpat_font_color', '#fff');
		add_option('wpat_font_family', '');
		add_option('wpat_dropdown_border_thickness', 1);
		add_option('wpat_dropdown_border_color', '#000');
		add_option('wpat_dropdown_background_color', '#fff');
		add_option('wpat_dropdown_hover_color', '#356177');
		add_option('wpat_dropdown_font_hover_color', '#fff');
		add_option('wpat_dropdown_font_selected_color', '#356177');
		add_option('wpat_dropdown_font_color', '#000');
		add_option('wpat_dropdown_shadow', '');
		add_option('wpat_dropdown_font_family', '');
		// Minimalist settings
		add_option( 'wpat_min_base_style', 'compact' );
		add_option( 'wpat_min_preset', 'quick_switcher' );
		add_option('wpat_min_style', 'flags');
		add_option('wpat_min_layout', 'dropdown');
		add_option('wpat_min_icon', 'dashicons-admin-site-alt3');
		add_option('wpat_min_txt_display', 'name');
		add_option('wpat_min_chevron', 'dashicons-arrow-down-alt2');
		add_option('wpat_min_txt_underline', '');
		add_option('wpat_min_text_divider', 'none');
		add_option('wpat_min_border_thickness', 1);
		add_option('wpat_min_border_color', '#f0f0f0');
		add_option('wpat_min_background_color', '#fff');
		add_option('wpat_min_font_color', '#000');
		add_option('wpat_min_font_family', '');
		add_option('wpat_min_hover_color', '#f0f0f0');
		add_option('wpat_min_font_hover_color', '#000');
		add_option( 'wpat_language_flags', array() );

		/* Advanced settings */
		add_option('wpat_default_location', true);
		add_option( 'wpat_go_live', $is_existing_installation );
		add_option('wpat_floating_position', $default_wpat_floating_position);
		add_option('wpat_floating_offset_x', 16);
		add_option('wpat_floating_offset_y', 16);
		add_option('wpat_auto_detect', $default_wpat_auto_detect);
		add_option('wpat_show_in_menu', '');
		add_option('wpat_menu_position', 'end');
		add_option('wpat_wrapper_selector', '');
		add_option('wpat_custom_css', '');
		add_option('wpat_min_custom_css', '');
		add_option( 'wpat_excluded_selectors', '' );
		add_option('wpat_delete_data_on_uninstall', '');
		add_option( 'wpat_launch_checklist_completed', $is_existing_installation );
		add_option( 'wpat_launch_checklist_reviewed', self::get_default_launch_checklist_reviewed( $is_existing_installation ) );

		self::migrate_widget_type();
		self::migrate_min_custom_css();
		Auto_Translate_Lifecycle::ensure_activation_state( $is_existing_installation );

		if ( ! $has_go_live_option && ! $is_existing_installation ) {
			Auto_Translate_Lifecycle::track_activation_default();
		}
	}

	public static function migrate_widget_type() {
		$current_widget_type = get_option( 'wpat_widget_type', 'minimalist' );

		if ( 'classic' === $current_widget_type ) {
			update_option( 'wpat_widget_type', 'minimalist' );
			update_option( 'wpat_classic_widget_migrated_notice', AUTO_TRANSLATE_VERSION );
		}
	}

	public static function migrate_min_custom_css() {
		$legacy_custom_css = get_option( 'wpat_custom_css', '' );
		$minimalist_custom_css = get_option( 'wpat_min_custom_css', '' );

		if ( ! is_scalar( $legacy_custom_css ) || ! is_scalar( $minimalist_custom_css ) ) {
			return;
		}

		$legacy_custom_css = trim( (string) $legacy_custom_css );
		$minimalist_custom_css = trim( (string) $minimalist_custom_css );

		if ( '' === $minimalist_custom_css && '' !== $legacy_custom_css ) {
			update_option( 'wpat_min_custom_css', $legacy_custom_css );
		}
	}

	private static function get_default_launch_checklist_reviewed( $is_existing_installation ) {
		$reviewed = array(
			'languages' => false,
			'style'     => false,
			'placement' => false,
		);

		return $is_existing_installation ? array_fill_keys( array_keys( $reviewed ), true ) : $reviewed;
	}

}
