<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://pampa.dev
 * @since      1.0.0
 *
 * @package    Auto_Translate
 * @subpackage Auto_Translate/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Auto_Translate
 * @subpackage Auto_Translate/admin
 * @author     Pampa Dev <intouch@pampa.dev>
 */
class Auto_Translate_Admin {
	const REVIEWS_URL = 'https://wordpress.org/support/plugin/auto-translate/reviews/';
	const SUPPORT_URL = 'https://wordpress.org/support/plugin/auto-translate/';

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Auto_Translate_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Auto_Translate_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/auto-translate-admin.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-global', plugin_dir_url( dirname(__FILE__) ) . 'global/css/auto-translate-global.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'wp-color-picker' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Auto_Translate_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Auto_Translate_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/auto-translate-admin.min.js', array( 'jquery', 'jquery-ui-sortable', 'wp-color-picker' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name . '-global', plugin_dir_url( dirname(__FILE__) ) . 'global/js/auto-translate-global.min.js', array(), $this->version, true );

	}

	/**
	 * Load the plugin's widgets.
	 *
	 * @since    1.3.0
	 */
	public function load_widgets() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-auto-translate-button-widget.php';
		register_widget('wpat_button_widget');
	}

	/**
	 * Check the plugin version and update options.
	 *
	 * @since    1.4.0
	 */
	public function check_version() {
		// Run migrations only when plugin version changes. This method is intentionally
		// idempotent because it delegates to add_option()-based defaults backfill.
		if (AUTO_TRANSLATE_VERSION !== get_option('wpat_auto_translate_version')){
			require_once plugin_dir_path( __FILE__ ) . '../includes/class-auto-translate-activator.php';
			Auto_Translate_Activator::activate();
		}
	}

	public function create_admin_menu() {
		//create new top-level menu
		add_menu_page( __( 'Automatic Translator Settings', 'auto-translate' ), __( 'Translator', 'auto-translate' ), 'manage_options', 'auto_translate', array( $this, 'auto_translate_settings_page' ), 'dashicons-translation', 98 );
	}

	function plugin_settings() {
		/* Language settings */
		register_setting(
			'auto-translate-language-settings-group',
			'wpat_supported_languages',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_supported_languages' ),
			)
		);
		register_setting( 'auto-translate-language-settings-group', 'wpat_language_order', array( 'sanitize_callback' => array( $this, 'sanitize_language_order' ) ) );
		register_setting( 'auto-translate-language-settings-group', 'wpat_language_flags', array( 'sanitize_callback' => array( $this, 'sanitize_language_flags' ) ) );

		/* Styling settings */
		register_setting( 'auto-translate-visual-settings-group', 'wpat_widget_type', array( 'sanitize_callback' => array( $this, 'sanitize_widget_type' ) ) );
		// Classic settings
		register_setting( 'auto-translate-visual-settings-group', 'wpat_button_icon', array( 'sanitize_callback' => array( $this, 'sanitize_html_class_option' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_show_icon', array( 'sanitize_callback' => array( $this, 'sanitize_toggle' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_color_1', array( 'sanitize_callback' => array( $this, 'sanitize_color_1' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_color_2', array( 'sanitize_callback' => array( $this, 'sanitize_color_2' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_widget_size', array( 'sanitize_callback' => array( $this, 'sanitize_widget_size' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_border_radius', array( 'sanitize_callback' => array( $this, 'sanitize_border_radius' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_border_thickness', array( 'sanitize_callback' => array( $this, 'sanitize_border_thickness' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_border_color', array( 'sanitize_callback' => array( $this, 'sanitize_border_color' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_font_color', array( 'sanitize_callback' => array( $this, 'sanitize_font_color' ) ) );
		register_setting(
			'auto-translate-visual-settings-group',
			'wpat_font_family',
			array( 'sanitize_callback' => array( $this, 'sanitize_font_family' ) )
		);
		register_setting( 'auto-translate-visual-settings-group', 'wpat_dropdown_shadow', array( 'sanitize_callback' => array( $this, 'sanitize_toggle' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_dropdown_border_thickness', array( 'sanitize_callback' => array( $this, 'sanitize_dropdown_border_thickness' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_dropdown_border_color', array( 'sanitize_callback' => array( $this, 'sanitize_dropdown_border_color' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_dropdown_background_color', array( 'sanitize_callback' => array( $this, 'sanitize_dropdown_background_color' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_dropdown_hover_color', array( 'sanitize_callback' => array( $this, 'sanitize_dropdown_hover_color' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_dropdown_font_hover_color', array( 'sanitize_callback' => array( $this, 'sanitize_dropdown_font_hover_color' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_dropdown_font_selected_color', array( 'sanitize_callback' => array( $this, 'sanitize_dropdown_font_selected_color' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_dropdown_font_color', array( 'sanitize_callback' => array( $this, 'sanitize_dropdown_font_color' ) ) );
		register_setting(
			'auto-translate-visual-settings-group',
			'wpat_dropdown_font_family',
			array( 'sanitize_callback' => array( $this, 'sanitize_font_family' ) )
		);
		// Minimalist settings
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_style', array( 'sanitize_callback' => array( $this, 'sanitize_min_style' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_layout', array( 'sanitize_callback' => array( $this, 'sanitize_min_layout' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_icon', array( 'sanitize_callback' => array( $this, 'sanitize_html_class_option' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_txt_display', array( 'sanitize_callback' => array( $this, 'sanitize_min_txt_display' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_txt_underline', array( 'sanitize_callback' => array( $this, 'sanitize_min_txt_underline' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_border_thickness', array( 'sanitize_callback' => array( $this, 'sanitize_min_border_thickness' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_border_color', array( 'sanitize_callback' => array( $this, 'sanitize_min_border_color' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_background_color', array( 'sanitize_callback' => array( $this, 'sanitize_min_background_color' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_font_color', array( 'sanitize_callback' => array( $this, 'sanitize_min_font_color' ) ) );
		register_setting(
			'auto-translate-visual-settings-group',
			'wpat_min_font_family',
			array( 'sanitize_callback' => array( $this, 'sanitize_font_family' ) )
		);
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_hover_color', array( 'sanitize_callback' => array( $this, 'sanitize_min_hover_color' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_font_hover_color', array( 'sanitize_callback' => array( $this, 'sanitize_min_font_hover_color' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_chevron', array( 'sanitize_callback' => array( $this, 'sanitize_min_chevron' ) ) );

		/* Placement settings */
		register_setting(
			'auto-translate-placement-settings-group',
			'wpat_default_location',
			array(
				'type'              => 'boolean',
				'default'           => true,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);
		register_setting( 'auto-translate-placement-settings-group', 'wpat_floating_position', array( 'sanitize_callback' => array( $this, 'sanitize_floating_position' ) ) );
		register_setting( 'auto-translate-placement-settings-group', 'wpat_floating_offset_x', array( 'sanitize_callback' => array( $this, 'sanitize_floating_offset' ) ) );
		register_setting( 'auto-translate-placement-settings-group', 'wpat_floating_offset_y', array( 'sanitize_callback' => array( $this, 'sanitize_floating_offset' ) ) );
		register_setting( 'auto-translate-placement-settings-group', 'wpat_show_in_menu', array( 'sanitize_callback' => array( $this, 'sanitize_show_in_menu' ) ) );
		register_setting( 'auto-translate-placement-settings-group', 'wpat_menu_position', array( 'sanitize_callback' => array( $this, 'sanitize_menu_position' ) ) );
		register_setting( 'auto-translate-placement-settings-group', 'wpat_wrapper_selector', array( 'sanitize_callback' => array( $this, 'sanitize_wrapper_selector' ) ) );

		/* Advanced settings */
		register_setting( 'auto-translate-advanced-settings-group', 'wpat_auto_detect', array( 'sanitize_callback' => array( $this, 'sanitize_auto_detect' ) ) );
		register_setting( 'auto-translate-advanced-settings-group', 'wpat_base_language', array( 'sanitize_callback' => array( $this, 'sanitize_base_language' ) ) );
		register_setting( 'auto-translate-advanced-settings-group', 'wpat_language_name_display', array( 'sanitize_callback' => array( $this, 'sanitize_language_name_display' ) ) );
		register_setting( 'auto-translate-advanced-settings-group', 'wpat_custom_css', array( 'sanitize_callback' => array( $this, 'sanitize_custom_css' ) ) );
		register_setting( 'auto-translate-advanced-settings-group', 'wpat_min_custom_css', array( 'sanitize_callback' => array( $this, 'sanitize_custom_css' ) ) );
		register_setting( 'auto-translate-advanced-settings-group', 'wpat_excluded_selectors', array( 'sanitize_callback' => array( $this, 'sanitize_excluded_selectors' ) ) );
		register_setting( 'auto-translate-advanced-settings-group', 'wpat_delete_data_on_uninstall', array( 'sanitize_callback' => array( $this, 'sanitize_toggle' ) ) );
	}

	private function sanitize_enum( $value, $allowed_values, $default ) {
		$value = sanitize_text_field( (string) $value );
		return in_array( $value, $allowed_values, true ) ? $value : $default;
	}

	public function sanitize_html_class_option( $value ) {
		$value = is_scalar( $value ) ? (string) $value : '';

		return sanitize_html_class( $value );
	}

	private function sanitize_hex_color_option( $value, $default ) {
		$sanitized = sanitize_hex_color( (string) $value );
		return $sanitized ? $sanitized : $default;
	}

	private function sanitize_bounded_int( $value, $min, $max, $default ) {
		$sanitized = absint( $value );
		if ( $sanitized < $min || $sanitized > $max ) {
			return $default;
		}
		return $sanitized;
	}

	public function sanitize_base_language( $value ) {
		$value = Auto_Translate_Config::normalize_lang_code( $value );
		if ( '' === $value ) {
			return '';
		}
		$supported_languages = Auto_Translate_Config::get_supported_languages();
		if ( isset( $supported_languages[ $value ] ) ) {
			return $value;
		}
		$wp_language = Auto_Translate_Config::get_wordpress_locale_language();
		if ( isset( $supported_languages[ $wp_language ] ) ) {
			return $wp_language;
		}
		if ( ! empty( $supported_languages ) ) {
			return array_key_first( $supported_languages );
		}
		return 'en';
	}

	public function sanitize_widget_type( $value ) { return Auto_Translate_Config::normalize_widget_type( $value ); }
	public function sanitize_widget_size( $value ) { return $this->sanitize_enum( $value, array( 'small', 'large' ), 'small' ); }
	public function sanitize_toggle( $value ) { return empty( $value ) ? '' : 'on'; }
	public function sanitize_border_radius( $value ) { return $this->sanitize_bounded_int( $value, 0, 64, 0 ); }
	public function sanitize_border_thickness( $value ) { return $this->sanitize_bounded_int( $value, 0, 12, 1 ); }
	public function sanitize_dropdown_border_thickness( $value ) { return $this->sanitize_bounded_int( $value, 0, 12, 1 ); }
	public function sanitize_min_border_thickness( $value ) { return $this->sanitize_bounded_int( $value, 0, 12, 1 ); }

	public function sanitize_color_1( $value ) { return $this->sanitize_hex_color_option( $value, '#000000' ); }
	public function sanitize_color_2( $value ) { return $this->sanitize_hex_color_option( $value, '#000000' ); }
	public function sanitize_border_color( $value ) { return $this->sanitize_hex_color_option( $value, '#ffffff' ); }
	public function sanitize_font_color( $value ) { return $this->sanitize_hex_color_option( $value, '#ffffff' ); }
	public function sanitize_dropdown_border_color( $value ) { return $this->sanitize_hex_color_option( $value, '#000000' ); }
	public function sanitize_dropdown_background_color( $value ) { return $this->sanitize_hex_color_option( $value, '#ffffff' ); }
	public function sanitize_dropdown_hover_color( $value ) { return $this->sanitize_hex_color_option( $value, '#356177' ); }
	public function sanitize_dropdown_font_hover_color( $value ) { return $this->sanitize_hex_color_option( $value, '#ffffff' ); }
	public function sanitize_dropdown_font_selected_color( $value ) { return $this->sanitize_hex_color_option( $value, '#356177' ); }
	public function sanitize_dropdown_font_color( $value ) { return $this->sanitize_hex_color_option( $value, '#000000' ); }
	public function sanitize_min_border_color( $value ) { return $this->sanitize_hex_color_option( $value, '#f0f0f0' ); }
	public function sanitize_min_background_color( $value ) { return $this->sanitize_hex_color_option( $value, '#ffffff' ); }
	public function sanitize_min_font_color( $value ) { return $this->sanitize_hex_color_option( $value, '#000000' ); }
	public function sanitize_min_hover_color( $value ) { return $this->sanitize_hex_color_option( $value, '#ffffff' ); }
	public function sanitize_min_font_hover_color( $value ) { return $this->sanitize_hex_color_option( $value, '#000000' ); }

	public function sanitize_min_style( $value ) { return $this->sanitize_enum( $value, array( 'flags', 'flat_flags', 'icon', 'clean' ), 'flags' ); }
	public function sanitize_min_layout( $value ) { return $this->sanitize_enum( $value, array( 'dropdown', 'popup_search' ), 'dropdown' ); }
	public function sanitize_min_txt_display( $value ) { return $this->sanitize_enum( $value, array( 'name', 'name_code', 'code' ), 'name' ); }
	public function sanitize_language_name_display( $value ) { return $this->sanitize_enum( $value, array( 'english', 'native' ), 'english' ); }
	public function sanitize_min_txt_underline( $value ) { return $this->sanitize_enum( $value, array( '', 'wpat_min_txt_underline' ), '' ); }
	public function sanitize_min_chevron( $value ) {
		return $this->sanitize_enum( $value, array( 'dashicons-arrow-down-alt2', 'dashicons-arrow-down', 'dashicons-arrow-down-none' ), 'dashicons-arrow-down-alt2' );
	}
	public function sanitize_auto_detect( $value ) { return $this->sanitize_enum( $value, array( 'enabled', 'disabled' ), 'disabled' ); }
	public function sanitize_floating_position( $value ) {
		return $this->sanitize_enum( $value, array( 'top_left', 'top_right', 'bottom_left', 'bottom_right' ), 'top_left' );
	}
	public function sanitize_floating_offset( $value ) { return $this->sanitize_bounded_int( $value, 0, 128, 16 ); }
	public function sanitize_menu_position( $value ) { return $this->sanitize_enum( $value, array( 'start', 'end' ), 'end' ); }
	public function sanitize_wrapper_selector( $value ) {
		$value = is_scalar( $value ) ? trim( (string) $value ) : '';
		$value = wp_strip_all_tags( $value );
		return substr( $value, 0, 190 );
	}
	public function sanitize_show_in_menu( $value ) {
		$value = is_scalar( $value ) ? trim( (string) $value ) : '';

		if ( '' === $value || null === $value ) {
			return '';
		}

		if ( is_numeric( $value ) ) {
			return (string) absint( $value );
		}

		if ( preg_match( '/^(menu|navigation):\d+$/', $value ) ) {
			list( $type, $id ) = explode( ':', $value, 2 );
			return sanitize_key( $type ) . ':' . absint( $id );
		}

		return sanitize_key( (string) $value );
	}

	/**
	 * Sanitize font-family values stored in options.
	 *
	 * @since 1.6
	 * @param string $value Raw option value.
	 * @return string
	 */
	public function sanitize_font_family( $value ) {
		$value = wp_strip_all_tags( (string) $value );
		$value = preg_replace( '/[^A-Za-z0-9,\-"\'\s_]/', '', $value );
		$value = preg_replace( '/\s+/', ' ', $value );

		return trim( $value );
	}

	/**
	 * Sanitize selected supported languages option.
	 *
	 * @since 1.5.6
	 * @param mixed $value Raw option value.
	 * @return array
	 */
	public function sanitize_supported_languages( $value ) {
		$supported_languages = Auto_Translate_Config::get_supported_languages();
		$allowed = array_keys( $supported_languages );

		if ( ! is_array( $value ) ) {
			return array( 'all' );
		}

		$sanitized = array_values(
			array_filter(
				array_map( 'sanitize_text_field', $value ),
				static function( $item ) use ( $allowed ) {
					return is_string( $item ) && ( 'all' === $item || in_array( $item, $allowed, true ) );
				}
			)
		);

		return empty( $sanitized ) ? array( 'all' ) : $sanitized;
	}

	public function sanitize_language_order( $value ) {
		$value = is_scalar( $value ) ? (string) $value : '';
		if ( '' === trim( $value ) ) {
			return '';
		}

		$supported_languages = Auto_Translate_Config::get_supported_languages();
		$allowed = array_keys( $supported_languages );
		$codes = array_filter( array_map( 'trim', explode( ',', $value ) ) );
		$codes = array_map( array( 'Auto_Translate_Config', 'normalize_lang_code' ), $codes );
		$codes = array_values( array_unique( array_filter( $codes, static function( $code ) use ( $allowed ) {
			return in_array( $code, $allowed, true );
		} ) ) );

		return implode( ',', $codes );
	}

	/**
	 * Sanitize custom CSS textarea content.
	 *
	 * @since 1.7.0
	 * @param mixed $value Raw CSS value.
	 * @return string
	 */
	public function sanitize_custom_css( $value ) {
		$value = is_scalar( $value ) ? (string) $value : '';
		$value = str_replace( array( "\r\n", "\r" ), "\n", $value );
		$value = wp_strip_all_tags( $value );
		$value = str_replace( '</style>', '', $value );

		return trim( $value );
	}

	public function sanitize_excluded_selectors( $value ) {
		$value = is_scalar( $value ) ? (string) $value : '';
		$lines = preg_split( '/\r\n|\r|\n/', $value );
		$selectors = array();

		foreach ( $lines as $line ) {
			$line = trim( preg_replace( '/[\x00-\x1F\x7F]/u', '', (string) $line ) );
			if ( '' === $line ) {
				continue;
			}
			$line = substr( $line, 0, 190 );
			if ( preg_match( '/</', $line ) ) {
				continue;
			}
			$selectors[] = $line;
		}

		$selectors = array_values( array_unique( $selectors ) );

		return implode( "\n", $selectors );
	}

	public function sanitize_language_flags( $value ) {
		$value = is_array( $value ) ? $value : array();
		$languages_countries = Auto_Translate_Config::get_languages_countries();
		$sanitized = array();

		foreach ( $value as $lang_code => $country_code ) {
			$lang_code = Auto_Translate_Config::normalize_lang_code( $lang_code );
			$country_code = sanitize_text_field( (string) $country_code );

			if ( ! isset( $languages_countries[ $lang_code ]['countries'] ) || ! is_array( $languages_countries[ $lang_code ]['countries'] ) ) {
				continue;
			}

			$allowed_codes = array();
			foreach ( $languages_countries[ $lang_code ]['countries'] as $country ) {
				if ( isset( $country['country_code'] ) && is_scalar( $country['country_code'] ) ) {
					$allowed_codes[] = sanitize_text_field( (string) $country['country_code'] );
				}
			}

			if ( in_array( $country_code, $allowed_codes, true ) ) {
				$sanitized[ $lang_code ] = $country_code;
			}
		}

		return $sanitized;
	}

	private function consume_classic_widget_migration_notice() {
		$notice_version = get_option( 'wpat_classic_widget_migrated_notice', '' );

		if ( ! is_string( $notice_version ) || '' === $notice_version ) {
			return '';
		}

		delete_option( 'wpat_classic_widget_migrated_notice' );

		return $notice_version;
	}

	private function supports_selector_block() {
		return function_exists( 'register_block_type' );
	}

	public function auto_translate_settings_page(){
		$wpat_supported_languages = Auto_Translate_Config::get_supported_languages();
		$langs_per_column = 27;
		$vars = [];
		$wpat_active_tab_input = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$vars['active_tab'] = is_string( $wpat_active_tab_input )
			? sanitize_key( $wpat_active_tab_input )
			: 'language_settings';
		$vars['plugin_icon_url'] = plugins_url( 'assets/icon-128x128.png', dirname( __DIR__ ) . '/auto-translate.php' );
		$vars['classic_widget_migrated_notice'] = $this->consume_classic_widget_migration_notice();
		$vars['reviews_url'] = $this->get_five_star_reviews_url();
		$vars['support_url'] = self::SUPPORT_URL;
		$vars['tabs'] = [
			'language_setting' => [
				'supported_languages' => $wpat_supported_languages,
				'count' => 1,
				'langs_per_column' => $langs_per_column,
				'columns' => max( 1, (int) ceil( count( $wpat_supported_languages ) / $langs_per_column ) ),
				'wpat_supported_languages' => get_option('wpat_supported_languages'),
				'wpat_base_language' => Auto_Translate_Config::get_resolved_base_language(),
				'wpat_language_order' => get_option('wpat_language_order', ''),
				'wpat_language_flags' => get_option( 'wpat_language_flags', array() ),
				'wpat_languages_countries' => Auto_Translate_Config::get_languages_countries(),
			],
			'visual_settings' => [
				'wpat_widget_type' => Auto_Translate_Config::normalize_widget_type( get_option('wpat_widget_type') ),
				'classic' => [
					'wpat_widget_size' => get_option('wpat_widget_size'),
					'wpat_color_1' => get_option('wpat_color_1'),
					'wpat_color_2' => get_option('wpat_color_2'),
					'wpat_border_radius' => get_option('wpat_border_radius'),
					'wpat_border_thickness' => get_option('wpat_border_thickness'),
					'wpat_border_color' => get_option('wpat_border_color'),
					'wpat_button_icon' => get_option('wpat_button_icon'),
					'wpat_show_icon' => get_option('wpat_show_icon'),
					'wpat_font_color' => get_option('wpat_font_color'),
					'wpat_font_family' => get_option('wpat_font_family'),
					'wpat_dropdown_shadow' => get_option('wpat_dropdown_shadow'),
					'wpat_dropdown_border_thickness' => get_option('wpat_dropdown_border_thickness'),
					'wpat_dropdown_border_color' => get_option('wpat_dropdown_border_color'),
					'wpat_dropdown_background_color' => get_option('wpat_dropdown_background_color'),
					'wpat_dropdown_hover_color' => get_option('wpat_dropdown_hover_color'),
					'wpat_dropdown_font_hover_color' => get_option('wpat_dropdown_font_hover_color'),
					'wpat_dropdown_font_selected_color' => get_option('wpat_dropdown_font_selected_color'),
					'wpat_dropdown_font_color' => get_option('wpat_dropdown_font_color'),
					'wpat_dropdown_font_family' => get_option('wpat_dropdown_font_family'),
				],
				'minimalist' => [
					'wpat_min_style' => get_option('wpat_min_style'),
					'wpat_min_layout' => get_option('wpat_min_layout', 'dropdown'),
					'wpat_min_icon' => get_option('wpat_min_icon'),
					'wpat_min_txt_display' => get_option('wpat_min_txt_display'),
					'wpat_min_txt_underline' => get_option('wpat_min_txt_underline'),
					'wpat_min_border_thickness' => get_option('wpat_min_border_thickness'),
					'wpat_min_border_color' => get_option('wpat_min_border_color'),
					'wpat_min_background_color' => get_option('wpat_min_background_color'),
					'wpat_min_font_color' => get_option('wpat_min_font_color'),
					'wpat_min_font_family' => get_option('wpat_min_font_family'),
					'wpat_min_hover_color' => get_option('wpat_min_hover_color'),
					'wpat_min_font_hover_color' => get_option('wpat_min_font_hover_color'),
					'wpat_min_chevron' => get_option('wpat_min_chevron'),
					'wpat_language_flags' => get_option( 'wpat_language_flags', array() ),
				],
				'columns' => 1
			],
			'placement_settings' => [
				'wpat_default_location'   => get_option('wpat_default_location', true),
				'wpat_floating_position'  => get_option('wpat_floating_position', 'top_left'),
				'wpat_floating_offset_x'  => absint( get_option('wpat_floating_offset_x', 16) ),
				'wpat_floating_offset_y'  => absint( get_option('wpat_floating_offset_y', 16) ),
				'wpat_show_in_menu'       => get_option('wpat_show_in_menu', ''),
				'wpat_menu_position'      => get_option('wpat_menu_position', 'end'),
				'wpat_wrapper_selector'   => get_option('wpat_wrapper_selector', ''),
				'supports_selector_block' => $this->supports_selector_block(),
				'columns'                 => 1
			],
			'advanced_settings' => [
				'wpat_auto_detect' => get_option('wpat_auto_detect'),
				'wpat_base_language' => Auto_Translate_Config::normalize_lang_code( (string) get_option( 'wpat_base_language', '' ) ),
				'wpat_resolved_base_language' => Auto_Translate_Config::get_resolved_base_language(),
				'wpat_language_name_display' => get_option('wpat_language_name_display', 'native'),
				'supported_languages' => $wpat_supported_languages,
				'wpat_custom_css' => get_option('wpat_custom_css', ''),
				'wpat_min_custom_css' => get_option('wpat_min_custom_css', ''),
				'wpat_excluded_selectors' => get_option( 'wpat_excluded_selectors', '' ),
				'wpat_delete_data_on_uninstall' => get_option('wpat_delete_data_on_uninstall', ''),
				'columns' => 1
			]
		];
		require_once 'partials/auto-translate-admin-display.php';
	}

	public function add_plugin_action_links( $links ) {
		$action_links = array(
			'settings' => sprintf(
				'<a href="%s">%s</a>',
				esc_url( admin_url( 'admin.php?page=auto_translate' ) ),
				esc_html__( 'Settings', 'auto-translate' )
			),
		);

		return array_merge( $action_links, $links );
	}

	public function add_plugin_row_meta( $links, $file ) {
		if ( plugin_basename( dirname( __DIR__ ) . '/auto-translate.php' ) !== $file ) {
			return $links;
		}

		$links[] = sprintf(
			'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
			esc_url( self::SUPPORT_URL ),
			esc_html__( 'Support forum', 'auto-translate' )
		);
		$links[] = sprintf(
			'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
			esc_url( $this->get_five_star_reviews_url() ),
			esc_html__( 'Rate us ★★★★★', 'auto-translate' )
		);

		return $links;
	}

	private function get_five_star_reviews_url() {
		return add_query_arg( 'filter', '5', self::REVIEWS_URL );
	}
}
