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
	const LAUNCH_CHECKLIST_COMPLETED_OPTION = 'wpat_launch_checklist_completed';
	const LAUNCH_CHECKLIST_REVIEWED_OPTION = 'wpat_launch_checklist_reviewed';
	const LAUNCH_CHECKLIST_USER_META = 'wpat_launch_checklist_state';
	const LIFECYCLE_SAVE_MARKER_OPTION = 'wpat_lifecycle_last_saved_tab';

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
	 * Local lifecycle tracker.
	 *
	 * @var Auto_Translate_Lifecycle|null
	 */
	private $lifecycle;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 * @param      Auto_Translate_Lifecycle|null $lifecycle Lifecycle tracker.
	 */
	public function __construct( $plugin_name, $version, $lifecycle = null ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->lifecycle = $lifecycle;

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
		wp_localize_script(
			$this->plugin_name,
			'wpatAdmin',
			array(
				'ajaxUrl'              => admin_url( 'admin-ajax.php' ),
				'launchChecklistNonce' => wp_create_nonce( 'wpat_set_launch_checklist_state' ),
			)
		);
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
		register_setting( 'auto-translate-language-settings-group', self::LAUNCH_CHECKLIST_REVIEWED_OPTION, array( 'sanitize_callback' => array( $this, 'sanitize_launch_checklist_reviewed' ) ) );
		register_setting( 'auto-translate-language-settings-group', self::LIFECYCLE_SAVE_MARKER_OPTION, array( 'sanitize_callback' => array( $this, 'sanitize_lifecycle_save_marker' ) ) );

		/* Styling settings */
		register_setting( 'auto-translate-visual-settings-group', 'wpat_widget_type', array( 'sanitize_callback' => array( $this, 'sanitize_widget_type' ) ) );
		register_setting( 'auto-translate-visual-settings-group', self::LAUNCH_CHECKLIST_REVIEWED_OPTION, array( 'sanitize_callback' => array( $this, 'sanitize_launch_checklist_reviewed' ) ) );
		register_setting( 'auto-translate-visual-settings-group', self::LIFECYCLE_SAVE_MARKER_OPTION, array( 'sanitize_callback' => array( $this, 'sanitize_lifecycle_save_marker' ) ) );
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
		register_setting( 'auto-translate-placement-settings-group', self::LAUNCH_CHECKLIST_REVIEWED_OPTION, array( 'sanitize_callback' => array( $this, 'sanitize_launch_checklist_reviewed' ) ) );
		register_setting( 'auto-translate-placement-settings-group', self::LIFECYCLE_SAVE_MARKER_OPTION, array( 'sanitize_callback' => array( $this, 'sanitize_lifecycle_save_marker' ) ) );

		/* Advanced settings */
		register_setting( 'auto-translate-advanced-settings-group', self::LIFECYCLE_SAVE_MARKER_OPTION, array( 'sanitize_callback' => array( $this, 'sanitize_lifecycle_save_marker' ) ) );
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
		return $this->sanitize_enum( $value, array( 'top_left', 'top_right', 'bottom_left', 'bottom_right' ), 'bottom_left' );
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

	public function sanitize_launch_checklist_reviewed( $value ) {
		$reviewed = self::normalize_launch_checklist_reviewed( get_option( self::LAUNCH_CHECKLIST_REVIEWED_OPTION, array() ) );
		$value    = is_array( $value ) ? $value : array();

		foreach ( array_keys( $reviewed ) as $key ) {
			if ( ! empty( $value[ $key ] ) ) {
				$reviewed[ $key ] = true;
			}
		}

		return $reviewed;
	}

	public function sanitize_lifecycle_save_marker( $value ) {
		$tab = sanitize_key( is_scalar( $value ) ? (string) $value : '' );

		if ( $this->lifecycle instanceof Auto_Translate_Lifecycle ) {
			Auto_Translate_Lifecycle::record_settings_save( $tab );
		}

		return $tab;
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

	private function current_user_can_manage_plugin() {
		return current_user_can( 'manage_options' );
	}

	private function is_go_live_enabled() {
		return (bool) get_option( 'wpat_go_live', false );
	}

	private function is_launch_checklist_completed() {
		return (bool) get_option( self::LAUNCH_CHECKLIST_COMPLETED_OPTION, false );
	}

	private static function normalize_launch_checklist_reviewed( $value ) {
		$value = is_array( $value ) ? $value : array();

		return array(
			'languages' => ! empty( $value['languages'] ),
			'style'     => ! empty( $value['style'] ),
			'placement' => ! empty( $value['placement'] ),
		);
	}

	public static function get_default_launch_checklist_reviewed( $is_existing_installation ) {
		$reviewed = array(
			'languages' => false,
			'style'     => false,
			'placement' => false,
		);

		return $is_existing_installation ? array_fill_keys( array_keys( $reviewed ), true ) : $reviewed;
	}

	private function get_launch_checklist_reviewed() {
		return self::normalize_launch_checklist_reviewed( get_option( self::LAUNCH_CHECKLIST_REVIEWED_OPTION, array() ) );
	}

	private function mark_launch_checklist_reviewed() {
		update_option( self::LAUNCH_CHECKLIST_REVIEWED_OPTION, self::get_default_launch_checklist_reviewed( true ) );
	}

	private function get_launch_checklist_user_state() {
		$user_state = get_user_meta( get_current_user_id(), self::LAUNCH_CHECKLIST_USER_META, true );

		return in_array( $user_state, array( 'open', 'collapsed' ), true ) ? $user_state : '';
	}

	private function is_launch_checklist_open( $is_live ) {
		$user_state = $this->get_launch_checklist_user_state();

		if ( 'open' === $user_state ) {
			return true;
		}

		if ( 'collapsed' === $user_state ) {
			return false;
		}

		return ! $is_live && ! $this->is_launch_checklist_completed();
	}

	private function get_launch_checklist_state_url( $state ) {
		$state = 'open' === $state ? 'open' : 'collapsed';
		$url   = add_query_arg(
			array(
				'action'     => 'wpat_set_launch_checklist_state',
				'wpat_state' => $state,
			),
			admin_url( 'admin-post.php' )
		);

		return wp_nonce_url( $url, 'wpat_set_launch_checklist_state' );
	}

	private function set_launch_checklist_state( $state ) {
		$state = 'open' === $state ? 'open' : 'collapsed';
		update_user_meta( get_current_user_id(), self::LAUNCH_CHECKLIST_USER_META, $state );

		return $state;
	}

	private function get_preview_site_url() {
		return wp_nonce_url(
			add_query_arg(
				array( 'action' => 'wpat_preview_site' ),
				admin_url( 'admin-post.php' )
			),
			'wpat_preview_site'
		);
	}

	private function get_admin_tab_url( $tab, $target = '' ) {
		$url = add_query_arg(
			array(
				'page' => 'auto_translate',
				'tab'  => sanitize_key( $tab ),
			),
			admin_url( 'admin.php' )
		);

		if ( '' !== $target ) {
			$url .= '#' . sanitize_key( $target );
		}

		return $url;
	}

	private function get_tracked_admin_tab_url( $tab, $target, $action ) {
		return wp_nonce_url(
			add_query_arg(
				array(
					'action'      => 'wpat_record_launch_action',
					'wpat_action' => sanitize_key( $action ),
					'wpat_tab'    => sanitize_key( $tab ),
					'wpat_target' => sanitize_key( $target ),
				),
				admin_url( 'admin-post.php' )
			),
			'wpat_record_launch_action'
		);
	}

	private function get_language_launch_overview( $supported_languages ) {
		$base_language = Auto_Translate_Config::get_resolved_base_language();
		$base_label    = isset( $supported_languages[ $base_language ] )
			? $supported_languages[ $base_language ]
			: strtoupper( $base_language );
		$selected      = get_option( 'wpat_supported_languages', array() );
		$is_ready      = isset( $supported_languages[ $base_language ] );

		if ( ! is_array( $selected ) || empty( $selected ) || in_array( 'all', $selected, true ) ) {
			$summary = __( 'All languages selected', 'auto-translate' );
		} else {
			$selected = array_values(
				array_unique(
					array_filter(
						array_map(
							array( 'Auto_Translate_Config', 'normalize_lang_code' ),
							$selected
						),
						static function ( $language ) use ( $supported_languages ) {
							return isset( $supported_languages[ $language ] );
						}
					)
				)
			);
			$count    = count( $selected );
			$is_ready = $is_ready && $count > 0;
			$summary  = sprintf(
				/* translators: %d: number of selected languages. */
				__( 'Selected languages: %d', 'auto-translate' ),
				$count
			);
		}

		return array(
			'is_ready' => $is_ready,
			'summary' => $summary,
			'meta'    => sprintf(
				/* translators: %s: base language name. */
				__( 'Base language: %s', 'auto-translate' ),
				$base_label
			),
		);
	}

	private function get_selector_style_launch_summary() {
		$style  = (string) get_option( 'wpat_min_style', 'flags' );
		$layout = (string) get_option( 'wpat_min_layout', 'dropdown' );
		$text   = (string) get_option( 'wpat_min_txt_display', 'name' );

		$style_labels = array(
			'flags'      => __( 'Flags', 'auto-translate' ),
			'flat_flags' => __( 'Flat flags', 'auto-translate' ),
			'icon'       => __( 'Icon', 'auto-translate' ),
			'clean'      => __( 'Text only', 'auto-translate' ),
		);
		$layout_labels = array(
			'dropdown'     => __( 'Dropdown', 'auto-translate' ),
			'popup_search' => __( 'Popup with search', 'auto-translate' ),
		);
		$text_labels = array(
			'name'      => __( 'Language name', 'auto-translate' ),
			'code'      => __( 'Language code', 'auto-translate' ),
			'name_code' => __( 'Name + code', 'auto-translate' ),
		);

		return array(
			'is_ready' => isset( $style_labels[ $style ], $layout_labels[ $layout ], $text_labels[ $text ] ),
			'summary' => sprintf(
				/* translators: 1: selector icon style, 2: selector text style. */
				__( '%1$s, %2$s', 'auto-translate' ),
				$style_labels[ $style ] ?? $style_labels['flags'],
				$text_labels[ $text ] ?? $text_labels['name']
			),
			'meta'    => $layout_labels[ $layout ] ?? $layout_labels['dropdown'],
		);
	}

	private function get_placement_launch_overview() {
		$show_floating     = (bool) get_option( 'wpat_default_location', true );
		$floating_position = (string) get_option( 'wpat_floating_position', 'bottom_left' );
		$selected_menu     = (string) get_option( 'wpat_show_in_menu', '' );
		$wrapper_selector  = trim( (string) get_option( 'wpat_wrapper_selector', '' ) );

		$position_labels = array(
			'top_left'     => __( 'Top left', 'auto-translate' ),
			'top_right'    => __( 'Top right', 'auto-translate' ),
			'bottom_right' => __( 'Bottom right', 'auto-translate' ),
			'bottom_left'  => __( 'Bottom left', 'auto-translate' ),
		);

		if ( $show_floating ) {
			$summary = __( 'Floating selector', 'auto-translate' );
			$meta    = $position_labels[ $floating_position ] ?? $position_labels['bottom_left'];
			$is_ready = true;
		} elseif ( '' !== $selected_menu ) {
			$summary = __( 'Navigation menu', 'auto-translate' );
			$meta    = __( 'Menu placement enabled', 'auto-translate' );
			$is_ready = true;
		} elseif ( '' !== $wrapper_selector ) {
			$summary = __( 'CSS injection', 'auto-translate' );
			$meta    = $wrapper_selector;
			$is_ready = true;
		} else {
			$summary = __( 'Manual placement', 'auto-translate' );
			$meta    = __( 'Use a block, widget, or shortcode', 'auto-translate' );
			$is_ready = false;
		}

		return array(
			'is_ready' => $is_ready,
			'summary' => $summary,
			'meta'    => $meta,
		);
	}

	private function get_launch_overview_items( $supported_languages ) {
		$languages = $this->get_language_launch_overview( $supported_languages );
		$style     = $this->get_selector_style_launch_summary();
		$placement = $this->get_placement_launch_overview();
		$reviewed  = $this->get_launch_checklist_reviewed();

		return array(
			array(
				'title'        => __( 'Language settings', 'auto-translate' ),
				'is_ready'     => $reviewed['languages'],
				'summary'      => $languages['summary'],
				'meta'         => $languages['meta'],
				'action_label' => __( 'Choose languages', 'auto-translate' ),
				'action_url'   => $this->get_tracked_admin_tab_url( 'language_settings', 'wpat-launch-languages', 'choose_languages' ),
			),
			array(
				'title'        => __( 'Placement', 'auto-translate' ),
				'is_ready'     => $reviewed['placement'],
				'summary'      => $placement['summary'],
				'meta'         => $placement['meta'],
				'action_label' => __( 'Set placement', 'auto-translate' ),
				'action_url'   => $this->get_tracked_admin_tab_url( 'placement_settings', 'wpat-launch-placement', 'set_placement' ),
			),
			array(
				'title'        => __( 'Styling', 'auto-translate' ),
				'is_ready'     => $reviewed['style'],
				'summary'      => $style['summary'],
				'meta'         => $style['meta'],
				'action_label' => __( 'Adjust style', 'auto-translate' ),
				'action_url'   => $this->get_tracked_admin_tab_url( 'visual_settings', 'wpat-launch-style', 'adjust_style' ),
			),
		);
	}

	public function handle_preview_site_action() {
		if ( ! $this->current_user_can_manage_plugin() ) {
			wp_die( esc_html__( 'You are not allowed to manage Automatic Translator.', 'auto-translate' ) );
		}

		check_admin_referer( 'wpat_preview_site' );

		if ( $this->lifecycle instanceof Auto_Translate_Lifecycle ) {
			Auto_Translate_Lifecycle::record_action( 'preview_site' );
		}

		wp_safe_redirect( home_url( '/' ) );
		exit;
	}

	public function handle_record_launch_action() {
		if ( ! $this->current_user_can_manage_plugin() ) {
			wp_die( esc_html__( 'You are not allowed to manage Automatic Translator.', 'auto-translate' ) );
		}

		check_admin_referer( 'wpat_record_launch_action' );

		$action = isset( $_GET['wpat_action'] ) ? sanitize_key( wp_unslash( $_GET['wpat_action'] ) ) : '';
		if ( $this->lifecycle instanceof Auto_Translate_Lifecycle ) {
			Auto_Translate_Lifecycle::record_action( $action );
		}

		$tab         = isset( $_GET['wpat_tab'] ) ? sanitize_key( wp_unslash( $_GET['wpat_tab'] ) ) : '';
		$target      = isset( $_GET['wpat_target'] ) ? sanitize_key( wp_unslash( $_GET['wpat_target'] ) ) : '';
		$redirect_to = ( '' !== $tab )
			? $this->get_admin_tab_url( $tab, $target )
			: ( isset( $_GET['redirect_to'] ) ? sanitize_text_field( wp_unslash( $_GET['redirect_to'] ) ) : '' );
		if ( ! is_string( $redirect_to ) || '' === $redirect_to ) {
			$redirect_to = admin_url( 'admin.php?page=auto_translate' );
		}

		wp_safe_redirect( wp_validate_redirect( $redirect_to, admin_url( 'admin.php?page=auto_translate' ) ) );
		exit;
	}

	public function handle_go_live_action() {
		if ( ! $this->current_user_can_manage_plugin() ) {
			wp_die( esc_html__( 'You are not allowed to manage Automatic Translator.', 'auto-translate' ) );
		}

		check_admin_referer( 'wpat_set_go_live' );

		$go_live = rest_sanitize_boolean( wp_unslash( $_POST['wpat_go_live'] ?? 0 ) );

		if ( $this->lifecycle instanceof Auto_Translate_Lifecycle ) {
			Auto_Translate_Lifecycle::set_transition_context( 'dashboard_action', get_current_user_id() );
			if ( $go_live ) {
				Auto_Translate_Lifecycle::record_action( 'go_live' );
			}
		}

		update_option( 'wpat_go_live', $go_live );

		if ( $this->lifecycle instanceof Auto_Translate_Lifecycle ) {
			Auto_Translate_Lifecycle::clear_transition_context();
		}

		$redirect_to = wp_get_referer();
		if ( ! is_string( $redirect_to ) || '' === $redirect_to ) {
			$redirect_to = admin_url( 'admin.php?page=auto_translate' );
		}

		$redirect_to = add_query_arg(
			array(
				'wpat_mode_updated' => '1',
				'wpat_mode'         => $go_live ? 'live' : 'preview',
			),
			$redirect_to
		);

		wp_safe_redirect( $redirect_to );
		exit;
	}

	public function handle_launch_checklist_state_action() {
		if ( ! $this->current_user_can_manage_plugin() ) {
			wp_die( esc_html__( 'You are not allowed to manage Automatic Translator.', 'auto-translate' ) );
		}

		check_admin_referer( 'wpat_set_launch_checklist_state' );

		$state = isset( $_GET['wpat_state'] ) ? sanitize_key( wp_unslash( $_GET['wpat_state'] ) ) : 'collapsed';
		$this->set_launch_checklist_state( $state );

		$redirect_to = wp_get_referer();
		if ( ! is_string( $redirect_to ) || '' === $redirect_to ) {
			$redirect_to = admin_url( 'admin.php?page=auto_translate' );
		}

		wp_safe_redirect( $redirect_to );
		exit;
	}

	public function handle_launch_checklist_state_ajax() {
		if ( ! $this->current_user_can_manage_plugin() ) {
			wp_send_json_error(
				array( 'message' => __( 'You are not allowed to manage Automatic Translator.', 'auto-translate' ) ),
				403
			);
		}

		check_ajax_referer( 'wpat_set_launch_checklist_state', 'nonce' );

		$state = isset( $_POST['wpat_state'] ) ? sanitize_key( wp_unslash( $_POST['wpat_state'] ) ) : 'collapsed';
		$state = $this->set_launch_checklist_state( $state );

		wp_send_json_success(
			array(
				'state' => $state,
			)
		);
	}

	public function auto_translate_settings_page(){
		if ( $this->lifecycle instanceof Auto_Translate_Lifecycle ) {
			Auto_Translate_Lifecycle::record_dashboard_seen();
		}

		$wpat_supported_languages = Auto_Translate_Config::get_supported_languages();
		$langs_per_column = 27;
		$vars = [];
		$is_live = $this->is_go_live_enabled();
		$wpat_mode_updated_input = filter_input( INPUT_GET, 'wpat_mode_updated', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$wpat_mode_input = filter_input( INPUT_GET, 'wpat_mode', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$wpat_active_tab_input = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$vars['active_tab'] = is_string( $wpat_active_tab_input )
			? sanitize_key( $wpat_active_tab_input )
			: 'language_settings';
		$vars['plugin_icon_url'] = plugins_url( 'assets/icon-128x128.png', dirname( __DIR__ ) . '/auto-translate.php' );
		$vars['classic_widget_migrated_notice'] = $this->consume_classic_widget_migration_notice();
		$vars['reviews_url'] = $this->get_five_star_reviews_url();
		$vars['support_url'] = self::SUPPORT_URL;
		$vars['preview_site_url'] = $this->get_preview_site_url();
		$vars['go_live_action_url'] = admin_url( 'admin-post.php' );
		$vars['is_live'] = $is_live;
		$vars['is_preview_mode'] = ! $is_live;
		$vars['launch_overview_items'] = $this->get_launch_overview_items( $wpat_supported_languages );
		$vars['is_launch_checklist_open'] = $this->is_launch_checklist_open( $is_live );
		$vars['show_launch_checklist_url'] = $this->get_launch_checklist_state_url( 'open' );
		$vars['hide_launch_checklist_url'] = $this->get_launch_checklist_state_url( 'collapsed' );
		$vars['mode_updated'] = '1' === $wpat_mode_updated_input;
		$vars['updated_mode'] = is_string( $wpat_mode_input ) ? sanitize_key( $wpat_mode_input ) : '';
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
				'wpat_go_live'            => $is_live,
				'wpat_default_location'   => get_option('wpat_default_location', true),
				'wpat_floating_position'  => get_option('wpat_floating_position', 'bottom_left'),
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

	/**
	 * Add Automatic Translator-specific Appsero deactivation reasons.
	 *
	 * The local Appsero fork lets the plugin prepend context-aware reasons to the
	 * primary modal list while preserving the SDK's default fallback choices.
	 *
	 * @param array<int, array<string, string>> $reasons Existing reasons.
	 * @param object|null                       $client  Appsero client instance.
	 * @return array<int, array<string, string>>
	 */
	public function filter_appsero_deactivation_reasons( $reasons, $client = null ) {
		if ( ! is_array( $reasons ) ) {
			$reasons = array();
		}

		return $this->get_contextual_deactivation_reasons();
	}

	/**
	 * Build Automatic Translator-specific deactivation reasons.
	 *
	 * @return array<int, array<string, string>>
	 */
	private function get_contextual_deactivation_reasons() {
		return array(
			$this->get_contextual_deactivation_reason(
				'wpat_not_ready_to_go_live',
				__( 'Could not go live', 'auto-translate' ),
				__( 'What stopped you from getting Automatic Translator ready to go live?', 'auto-translate' ),
				'01-not-ready-to-go-live.png'
			),
			$this->get_contextual_deactivation_reason(
				'wpat_switcher_design_fit',
				__( 'Design did not fit', 'auto-translate' ),
				__( 'What looked wrong about the language switcher?', 'auto-translate' ),
				'02-design-fit.png'
			),
			$this->get_contextual_deactivation_reason(
				'wpat_switcher_placement',
				__( 'Wrong placement', 'auto-translate' ),
				__( 'Where did the language switcher appear incorrectly?', 'auto-translate' ),
				'03-wrong-place.png'
			),
			$this->get_contextual_deactivation_reason(
				'wpat_translation_quality',
				__( 'Poor translation quality', 'auto-translate' ),
				__( 'Which languages or content gave you translation quality problems?', 'auto-translate' ),
				'04-translation-quality.png'
			),
			$this->get_contextual_deactivation_reason(
				'wpat_technical_issue',
				__( 'Error or performance issue', 'auto-translate' ),
				__( 'What error, conflict, or performance issue did you see?', 'auto-translate' ),
				'05-technical-issue.png'
			),
			$this->get_contextual_deactivation_reason(
				'wpat_missing_feature',
				__( 'Missing feature', 'auto-translate' ),
				__( 'Which feature were you looking for?', 'auto-translate' ),
				'06-missing-feature.png'
			),
			$this->get_contextual_deactivation_reason(
				'wpat_just_testing',
				__( 'Just testing', 'auto-translate' ),
				__( 'What were you trying to test?', 'auto-translate' ),
				'07-only-testing.png'
			),
			$this->get_contextual_deactivation_reason(
				'wpat_something_else',
				__( 'Something else', 'auto-translate' ),
				__( 'Tell us what happened.', 'auto-translate' ),
				'08-something-else.png'
			),
		);
	}

	/**
	 * Format a contextual deactivation reason for Appsero.
	 *
	 * @param string $id          Stable reason ID.
	 * @param string $text        Visible reason label.
	 * @param string $placeholder Follow-up prompt.
	 * @param string $icon_file   Icon file name.
	 * @return array<string, string>
	 */
	private function get_contextual_deactivation_reason( $id, $text, $placeholder, $icon_file ) {
		return array(
			'id'          => $id,
			'text'        => $text,
			'placeholder' => $placeholder,
			'icon'        => plugins_url( 'assets/deactivation-icons/' . sanitize_file_name( $icon_file ), dirname( __DIR__ ) . '/auto-translate.php' ),
		);
	}

	private function get_five_star_reviews_url() {
		return add_query_arg( 'filter', '5', self::REVIEWS_URL );
	}
}
