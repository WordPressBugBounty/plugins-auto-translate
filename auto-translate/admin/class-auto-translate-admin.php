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
	const PLUGIN_STATUS_USER_META = 'wpat_plugin_status_state';
	const PLUGIN_LINKS_NOTICE_USER_META = 'wpat_plugin_links_notice_dismissed_version';
	const PUBLISH_REMINDER_USER_META = 'wpat_publish_reminder_enabled';

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
	 * Private, short-lived settings draft store.
	 *
	 * @var Auto_Translate_Settings_Draft
	 */
	private $settings_draft;

	/**
	 * Private draft values used while rendering the settings workspace.
	 *
	 * @var array<string, mixed>
	 */
	private $settings_draft_values = array();

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
		$this->settings_draft = new Auto_Translate_Settings_Draft(
			Auto_Translate_Settings_Draft::get_default_allowed_options(),
			$this->get_settings_draft_sanitizers()
		);

	}

	/**
	 * Reuse the canonical option sanitizers before settings enter a private draft.
	 *
	 * @return array<string, callable>
	 */
	private function get_settings_draft_sanitizers() {
		return array(
			'wpat_supported_languages'              => array( $this, 'sanitize_supported_languages' ),
			'wpat_language_order'                   => array( $this, 'sanitize_language_order' ),
			'wpat_language_flags'                   => array( $this, 'sanitize_language_flags' ),
			'wpat_widget_type'                      => array( $this, 'sanitize_widget_type' ),
			'wpat_button_icon'                      => array( $this, 'sanitize_html_class_option' ),
			'wpat_show_icon'                        => array( $this, 'sanitize_toggle' ),
			'wpat_color_1'                          => array( $this, 'sanitize_color_1' ),
			'wpat_color_2'                          => array( $this, 'sanitize_color_2' ),
			'wpat_widget_size'                      => array( $this, 'sanitize_widget_size' ),
			'wpat_border_radius'                    => array( $this, 'sanitize_border_radius' ),
			'wpat_border_thickness'                 => array( $this, 'sanitize_border_thickness' ),
			'wpat_border_color'                     => array( $this, 'sanitize_border_color' ),
			'wpat_font_color'                       => array( $this, 'sanitize_font_color' ),
			'wpat_font_family'                      => array( $this, 'sanitize_font_family' ),
			'wpat_dropdown_shadow'                  => array( $this, 'sanitize_toggle' ),
			'wpat_dropdown_border_thickness'        => array( $this, 'sanitize_dropdown_border_thickness' ),
			'wpat_dropdown_border_color'            => array( $this, 'sanitize_dropdown_border_color' ),
			'wpat_dropdown_background_color'        => array( $this, 'sanitize_dropdown_background_color' ),
			'wpat_dropdown_hover_color'             => array( $this, 'sanitize_dropdown_hover_color' ),
			'wpat_dropdown_font_hover_color'        => array( $this, 'sanitize_dropdown_font_hover_color' ),
			'wpat_dropdown_font_selected_color'     => array( $this, 'sanitize_dropdown_font_selected_color' ),
			'wpat_dropdown_font_color'              => array( $this, 'sanitize_dropdown_font_color' ),
			'wpat_dropdown_font_family'             => array( $this, 'sanitize_font_family' ),
			'wpat_min_base_style'                   => array( $this, 'sanitize_min_base_style' ),
			'wpat_min_preset'                       => array( $this, 'sanitize_min_preset' ),
			'wpat_min_style'                        => array( $this, 'sanitize_min_style' ),
			'wpat_min_layout'                       => array( $this, 'sanitize_min_layout' ),
			'wpat_min_icon'                         => array( $this, 'sanitize_html_class_option' ),
			'wpat_min_txt_display'                  => array( $this, 'sanitize_min_txt_display' ),
			'wpat_min_txt_underline'                => array( $this, 'sanitize_min_txt_underline' ),
			'wpat_min_text_divider'                 => array( $this, 'sanitize_min_text_divider' ),
			'wpat_min_border_thickness'             => array( $this, 'sanitize_min_border_thickness' ),
			'wpat_min_border_color'                 => array( $this, 'sanitize_min_border_color' ),
			'wpat_min_border_transparent'           => array( $this, 'sanitize_toggle' ),
			'wpat_min_background_color'             => array( $this, 'sanitize_min_background_color' ),
			'wpat_min_background_transparent'       => array( $this, 'sanitize_toggle' ),
			'wpat_min_font_color'                   => array( $this, 'sanitize_min_font_color' ),
			'wpat_min_font_family'                  => array( $this, 'sanitize_font_family' ),
			'wpat_min_hover_color'                  => array( $this, 'sanitize_min_hover_color' ),
			'wpat_min_hover_transparent'            => array( $this, 'sanitize_toggle' ),
			'wpat_min_font_hover_color'             => array( $this, 'sanitize_min_font_hover_color' ),
			'wpat_min_chevron'                      => array( $this, 'sanitize_min_chevron' ),
			'wpat_default_location'                 => 'rest_sanitize_boolean',
			'wpat_floating_position'                => array( $this, 'sanitize_floating_position' ),
			'wpat_floating_offset_x'                => array( $this, 'sanitize_floating_offset' ),
			'wpat_floating_offset_y'                => array( $this, 'sanitize_floating_offset' ),
			'wpat_show_in_menu'                     => array( $this, 'sanitize_show_in_menu' ),
			'wpat_menu_position'                    => array( $this, 'sanitize_menu_position' ),
			'wpat_wrapper_selector'                 => array( $this, 'sanitize_wrapper_selector' ),
			'wpat_auto_detect'                      => array( $this, 'sanitize_auto_detect' ),
			'wpat_base_language'                    => array( $this, 'sanitize_base_language' ),
			'wpat_language_name_display'            => array( $this, 'sanitize_language_name_display' ),
			'wpat_custom_css'                       => array( $this, 'sanitize_custom_css' ),
			'wpat_min_custom_css'                   => array( $this, 'sanitize_custom_css' ),
			'wpat_excluded_selectors'               => array( $this, 'sanitize_excluded_selectors' ),
			'wpat_delete_data_on_uninstall'         => array( $this, 'sanitize_toggle' ),
		);
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
				'settingsTabs'         => array(
					'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
					'loadingMessage' => __( 'Loading settings…', 'auto-translate' ),
					'errorMessage' => __( 'Could not load this settings tab. Refresh the page and try again.', 'auto-translate' ),
					'requestData'  => array(
						'action' => 'wpat_load_settings_tab',
						'nonce'  => wp_create_nonce( 'wpat_load_settings_tab' ),
					),
				),
				'pluginLinksNoticeNonce' => wp_create_nonce( 'wpat_dismiss_plugin_links_notice' ),
				'draft'                => array(
					'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
					'debounce'           => 700,
					'storageKey'         => 'wpat_settings_draft_' . get_current_user_id(),
					'errorMessage'       => __( 'Could not save your settings draft. Refresh the page and try again.', 'auto-translate' ),
					'discardErrorMessage' => __( 'Could not discard your settings draft. Refresh the page and try again.', 'auto-translate' ),
					'requestData'        => array(
						'action' => 'wpat_save_settings_draft',
						'nonce'  => wp_create_nonce( 'wpat_settings_draft' ),
					),
					'commitRequestData'  => array(
						'action' => 'wpat_commit_settings_draft',
						'nonce'  => wp_create_nonce( 'wpat_settings_draft' ),
					),
					'discardRequestData' => array(
						'action' => 'wpat_discard_settings_draft',
						'nonce'  => wp_create_nonce( 'wpat_settings_draft' ),
					),
					'readinessRequestData' => array(
						'action' => 'wpat_get_publish_readiness',
						'nonce'  => wp_create_nonce( 'wpat_settings_draft' ),
					),
					'readinessErrorMessage' => __( 'Could not refresh publish readiness. Try again before publishing.', 'auto-translate' ),
					'publishReminderCancelledMessage' => __( 'Publishing cancelled.', 'auto-translate' ),
					'publishReminderConfirmedMessage' => __( 'Publishing Automatic Translator.', 'auto-translate' ),
				),
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
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_base_style', array( 'sanitize_callback' => array( $this, 'sanitize_min_base_style' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_preset', array( 'sanitize_callback' => array( $this, 'sanitize_min_preset' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_style', array( 'sanitize_callback' => array( $this, 'sanitize_min_style' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_layout', array( 'sanitize_callback' => array( $this, 'sanitize_min_layout' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_icon', array( 'sanitize_callback' => array( $this, 'sanitize_html_class_option' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_txt_display', array( 'sanitize_callback' => array( $this, 'sanitize_min_txt_display' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_txt_underline', array( 'sanitize_callback' => array( $this, 'sanitize_min_txt_underline' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_text_divider', array( 'sanitize_callback' => array( $this, 'sanitize_min_text_divider' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_border_thickness', array( 'sanitize_callback' => array( $this, 'sanitize_min_border_thickness' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_border_color', array( 'sanitize_callback' => array( $this, 'sanitize_min_border_color' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_border_transparent', array( 'sanitize_callback' => array( $this, 'sanitize_toggle' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_background_color', array( 'sanitize_callback' => array( $this, 'sanitize_min_background_color' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_background_transparent', array( 'sanitize_callback' => array( $this, 'sanitize_toggle' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_font_color', array( 'sanitize_callback' => array( $this, 'sanitize_min_font_color' ) ) );
		register_setting(
			'auto-translate-visual-settings-group',
			'wpat_min_font_family',
			array( 'sanitize_callback' => array( $this, 'sanitize_font_family' ) )
		);
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_hover_color', array( 'sanitize_callback' => array( $this, 'sanitize_min_hover_color' ) ) );
		register_setting( 'auto-translate-visual-settings-group', 'wpat_min_hover_transparent', array( 'sanitize_callback' => array( $this, 'sanitize_toggle' ) ) );
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

	public function sanitize_min_style( $value ) { return $this->sanitize_enum( $value, array( 'flags', 'emoji_flags', 'flat_flags', 'icon', 'clean' ), 'flags' ); }
	public function sanitize_min_base_style( $value ) { return $this->sanitize_enum( $value, array( 'compact', 'minimal', 'native' ), 'compact' ); }
	public function sanitize_min_preset( $value ) { return $this->sanitize_enum( $value, array( 'quick_switcher', 'searchable_picker', 'compact_code_only', 'compact_emoji_name', 'minimal_flags', 'minimal_flags_code', 'minimal_code_only', 'minimal_name_pipe', 'minimal_custom', 'custom', 'classic', 'pill', 'soft_outline', 'links', 'flags', 'names', 'codes', 'standard', 'compact_native' ), 'quick_switcher' ); }
	public function sanitize_min_layout( $value ) { return $this->sanitize_enum( $value, array( 'dropdown', 'popup_search' ), 'dropdown' ); }
	public function sanitize_min_txt_display( $value ) { return $this->sanitize_enum( $value, array( 'name', 'name_code', 'code', 'none' ), 'name' ); }
	public function sanitize_language_name_display( $value ) { return $this->sanitize_enum( $value, array( 'english', 'native' ), 'english' ); }
	public function sanitize_min_txt_underline( $value ) { return $this->sanitize_enum( $value, array( '', 'wpat_min_txt_underline' ), '' ); }
	public function sanitize_min_text_divider( $value ) { return $this->sanitize_enum( $value, array( 'none', 'pipe', 'brackets' ), 'none' ); }
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

		return $sanitized;
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

	private function current_user_can_manage_plugin() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Return the browser-session identifier supplied by the authenticated editor.
	 *
	 * @return string
	 */
	private function get_settings_draft_browser_session() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Every caller verifies the settings-draft AJAX nonce before invoking this helper.
		$session = isset( $_POST['wpat_browser_session'] ) ? sanitize_text_field( wp_unslash( $_POST['wpat_browser_session'] ) ) : '';

		return is_string( $session ) ? $session : '';
	}

	/**
	 * Extract only allow-listed settings from an AJAX draft request.
	 *
	 * @return array<string, mixed>
	 */
	private function get_settings_draft_payload_from_request() {
		$payload = array();
		// phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Every caller verifies the settings-draft AJAX nonce; option-specific sanitizers run before storage.
		foreach ( Auto_Translate_Settings_Draft::get_default_allowed_options() as $option ) {
			if ( isset( $_POST[ $option ] ) ) {
				$payload[ $option ] = wp_unslash( $_POST[ $option ] );
			}
		}
		// phpcs:enable

		return $payload;
	}

	/**
	 * Persist the current browser-session identifier in an HTTP-only cookie so a
	 * normal tab navigation can rehydrate the same private draft.
	 *
	 * @param string $session Browser-session identifier.
	 * @return void
	 */
	private function persist_settings_draft_session_cookie( $session ) {
		setcookie(
			'wpat_settings_draft_session',
			$session,
			array(
				'expires'  => 0,
				'path'     => defined( 'COOKIEPATH' ) ? COOKIEPATH : '/',
				'domain'   => defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '',
				'secure'   => is_ssl(),
				'httponly' => true,
				'samesite' => 'Lax',
			)
		);
	}

	/**
	 * Persist the server-selected draft ID beside the HTTP-only browser-session
	 * cookie. The ID alone cannot read a draft because owner/session checks still
	 * apply on every request.
	 *
	 * @param string $draft_id Draft identifier.
	 * @return void
	 */
	private function persist_settings_draft_id_cookie( $draft_id ) {
		setcookie(
			'wpat_settings_draft_id',
			$draft_id,
			array(
				'expires'  => 0,
				'path'     => defined( 'COOKIEPATH' ) ? COOKIEPATH : '/',
				'domain'   => defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '',
				'secure'   => is_ssl(),
				'httponly' => true,
				'samesite' => 'Lax',
			)
		);
	}

	/**
	 * Clear the browser cookies that point to a completed or discarded draft.
	 *
	 * @return void
	 */
	private function clear_settings_draft_cookies() {
		foreach ( array( 'wpat_settings_draft_session', 'wpat_settings_draft_id' ) as $cookie_name ) {
			setcookie( $cookie_name, '', time() - HOUR_IN_SECONDS, defined( 'COOKIEPATH' ) ? COOKIEPATH : '/', defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '', is_ssl(), true );
		}
	}

	/**
	 * @param string $cookie_name Cookie to read.
	 * @return string
	 */
	private function get_settings_draft_cookie( $cookie_name ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- The returned value is sanitized below and is verified against the draft owner.
		$value = isset( $_COOKIE[ $cookie_name ] ) ? wp_unslash( $_COOKIE[ $cookie_name ] ) : '';

		return is_string( $value ) ? sanitize_text_field( $value ) : '';
	}

	/**
	 * Overlay the current editor's private draft while rendering the plugin page.
	 * The draft remains isolated to its user and browser-session cookie, and this
	 * filter is installed only for the current admin request.
	 *
	 * @return void
	 */
	private function enable_settings_draft_for_admin_page() {
		$session  = $this->get_settings_draft_cookie( 'wpat_settings_draft_session' );
		$draft_id = $this->get_settings_draft_cookie( 'wpat_settings_draft_id' );
		$draft    = $this->settings_draft->get( get_current_user_id(), $session, $draft_id );

		if ( false === $draft || ! is_array( $draft['payload'] ?? null ) ) {
			return;
		}

		$this->settings_draft_values = $draft['payload'];
		foreach ( array_keys( $this->settings_draft_values ) as $option ) {
			add_filter( 'option_' . $option, array( $this, 'filter_settings_draft_admin_option' ), 10, 2 );
		}
	}

	/**
	 * @param mixed  $value  Stored option value.
	 * @param string $option Option name.
	 * @return mixed
	 */
	public function filter_settings_draft_admin_option( $value, $option ) {
		return array_key_exists( $option, $this->settings_draft_values ) ? $this->settings_draft_values[ $option ] : $value;
	}

	/**
	 * Save a private editing draft without changing canonical plugin options.
	 *
	 * @return void
	 */
	public function handle_save_settings_draft_ajax() {
		if ( ! $this->current_user_can_manage_plugin() ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to manage Automatic Translator.', 'auto-translate' ) ), 403 );
		}

		check_ajax_referer( 'wpat_settings_draft', 'nonce' );
		$session  = $this->get_settings_draft_browser_session();
		$payload  = $this->get_settings_draft_payload_from_request();
		$draft_id = isset( $_POST['draft_id'] ) ? sanitize_text_field( wp_unslash( $_POST['draft_id'] ) ) : '';
		$revision = isset( $_POST['revision'] ) ? absint( $_POST['revision'] ) : null;
		$draft    = '' === $draft_id
			? $this->settings_draft->create( get_current_user_id(), $session, $payload )
			: $this->settings_draft->update( get_current_user_id(), $session, $draft_id, $payload, $revision );

		// update() merges stale revisions into the complete owner draft. Only an
		// inaccessible or expired ID reaches create(), which reuses the current
		// owner pointer when available and otherwise starts a fresh draft.
		if ( false === $draft && '' !== $draft_id ) {
			$draft = $this->settings_draft->create( get_current_user_id(), $session, $payload );
		}

		if ( false === $draft ) {
			wp_send_json_error( array( 'message' => __( 'Could not save your settings draft. Refresh the page and try again.', 'auto-translate' ) ), 409 );
		}

		$this->persist_settings_draft_session_cookie( $session );
		$this->persist_settings_draft_id_cookie( $draft['draft_id'] );
		$draft['publish_readiness'] = $this->get_publish_readiness_for_draft( $draft['payload'] );
		wp_send_json_success( $draft );
	}

	/**
	 * Return the latest private-draft readiness snapshot without mutating it.
	 *
	 * @return void
	 */
	public function handle_get_publish_readiness_ajax() {
		if ( ! $this->current_user_can_manage_plugin() ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to manage Automatic Translator.', 'auto-translate' ) ), 403 );
		}

		check_ajax_referer( 'wpat_settings_draft', 'nonce' );
		$session  = $this->get_settings_draft_browser_session();
		$draft_id = isset( $_POST['draft_id'] ) ? sanitize_text_field( wp_unslash( $_POST['draft_id'] ) ) : '';
		$payload  = array();
		$draft    = false;

		if ( '' !== $draft_id ) {
			$draft = $this->settings_draft->get( get_current_user_id(), $session, $draft_id );
			if ( false === $draft || ! is_array( $draft['payload'] ?? null ) ) {
				wp_send_json_error(
					array(
						'code'    => 'wpat_settings_draft_missing',
						'message' => __( 'Your settings draft is no longer available. Refresh the page and try again.', 'auto-translate' ),
					),
					409
				);
			}
			$payload = $draft['payload'];
		}

		wp_send_json_success(
			array(
				'publish_readiness' => $this->get_publish_readiness_for_draft( $payload ),
				'revision'          => isset( $draft['revision'] ) ? (int) $draft['revision'] : 0,
			)
		);
	}

	/**
	 * Commit the complete private draft to canonical options.
	 *
	 * @return void
	 */
	public function handle_commit_settings_draft_ajax() {
		if ( ! $this->current_user_can_manage_plugin() ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to manage Automatic Translator.', 'auto-translate' ) ), 403 );
		}

		check_ajax_referer( 'wpat_settings_draft', 'nonce' );
		$user_id   = get_current_user_id();
		$session   = $this->get_settings_draft_browser_session();
		$draft_id  = isset( $_POST['draft_id'] ) ? sanitize_text_field( wp_unslash( $_POST['draft_id'] ) ) : '';
		$revision  = isset( $_POST['revision'] ) ? absint( $_POST['revision'] ) : null;
		$committed = $this->settings_draft->commit( $user_id, $session, $draft_id, $revision );

		// The browser may retain a draft pointer after its short-lived server
		// transient expires. Recreate only when the record is genuinely gone;
		// a live draft with a stale revision must still fail as a conflict.
		if ( false === $committed && false === $this->settings_draft->get( $user_id, $session, $draft_id ) ) {
			$replacement = $this->settings_draft->create( $user_id, $session, $this->get_settings_draft_payload_from_request() );
			if ( false !== $replacement ) {
				$committed = $this->settings_draft->commit( $user_id, $session, $replacement['draft_id'], $replacement['revision'] );
			}
		}

		if ( false === $committed ) {
			wp_send_json_error( array( 'message' => __( 'Could not save your settings. Refresh the page and try again.', 'auto-translate' ) ), 409 );
		}

		$this->clear_settings_draft_cookies();
		wp_send_json_success( array( 'payload' => $committed ) );
	}

	/**
	 * Discard a private editing draft without changing canonical options.
	 *
	 * @return void
	 */
	public function handle_discard_settings_draft_ajax() {
		if ( ! $this->current_user_can_manage_plugin() ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to manage Automatic Translator.', 'auto-translate' ) ), 403 );
		}

		check_ajax_referer( 'wpat_settings_draft', 'nonce' );
		$draft_id = isset( $_POST['draft_id'] ) ? sanitize_text_field( wp_unslash( $_POST['draft_id'] ) ) : '';
		$discarded = $this->settings_draft->discard( get_current_user_id(), $this->get_settings_draft_browser_session(), $draft_id );

		if ( ! $discarded ) {
			wp_send_json_error( array( 'message' => __( 'Could not discard your settings draft. Refresh the page and try again.', 'auto-translate' ) ), 409 );
		}

		$this->clear_settings_draft_cookies();
		wp_send_json_success();
	}

	private function is_go_live_enabled() {
		return (bool) get_option( 'wpat_go_live', false );
	}

	private function is_plugin_status_open() {
		return 'collapsed' !== get_user_meta( get_current_user_id(), self::PLUGIN_STATUS_USER_META, true );
	}

	private function set_plugin_status_state( $state ) {
		$state = 'open' === $state ? 'open' : 'collapsed';
		update_user_meta( get_current_user_id(), self::PLUGIN_STATUS_USER_META, $state );

		return $state;
	}

	private function should_show_plugin_links_notice() {
		return $this->version !== get_user_meta( get_current_user_id(), self::PLUGIN_LINKS_NOTICE_USER_META, true );
	}

	private function should_show_publish_reminder() {
		return '0' !== get_user_meta( get_current_user_id(), self::PUBLISH_REMINDER_USER_META, true );
	}

	/**
	 * Build the publish reminder's private, admin-only readiness snapshot.
	 *
	 * The values are read after the current private draft filters have been
	 * installed, so they are the complete draft where present and canonical
	 * options everywhere else. Only the fields required by the reminder are
	 * exposed to the page; publication state and unrelated settings stay out of
	 * the client contract.
	 *
	 * @param array<string, mixed> $vars Settings page view model.
	 * @return array<string, mixed>
	 */
	private function get_publish_readiness_snapshot( $vars ) {
		$language_data  = $vars['tabs']['language_setting'] ?? array();
		$visual_data    = $vars['tabs']['visual_settings'] ?? array();
		$placement_data = $vars['tabs']['placement_settings'] ?? array();

		return $this->build_publish_readiness_snapshot(
			array(
				'wpat_supported_languages' => $language_data['wpat_supported_languages'] ?? array(),
				'wpat_default_location'    => $placement_data['wpat_default_location'] ?? false,
				'wpat_show_in_menu'        => $placement_data['wpat_show_in_menu'] ?? '',
				'wpat_wrapper_selector'    => $placement_data['wpat_wrapper_selector'] ?? '',
				'wpat_widget_type'         => $visual_data['wpat_widget_type'] ?? '',
				'wpat_min_base_style'      => $visual_data['minimalist']['wpat_min_base_style'] ?? '',
			)
		);
	}

	/**
	 * Build readiness from the small set of values used by the publish reminder.
	 *
	 * @param array<string, mixed> $values Relevant settings, with draft values
	 *                                      taking precedence over canonical values.
	 * @return array<string, mixed>
	 */
	private function build_publish_readiness_snapshot( $values ) {
		$catalog  = Auto_Translate_Config::get_supported_languages();
		$selected = is_array( $values['wpat_supported_languages'] ?? null ) ? $values['wpat_supported_languages'] : array();
		$selected      = array_values( array_unique( array_map( 'strval', $selected ) ) );
		$valid_selected = array_values(
			array_filter(
				$selected,
				static function ( $language ) use ( $catalog ) {
					return isset( $catalog[ $language ] );
				}
			)
		);
		$has_all       = in_array( 'all', $selected, true );
		$language_count = $has_all ? count( $catalog ) : count( $valid_selected );
		$language_count_detail = sprintf(
			/* translators: %d: number of selected languages. */
			_n( '%d language selected', '%d languages selected', $language_count, 'auto-translate' ),
			$language_count
		);
		if ( $has_all ) {
			$language_count_detail = sprintf(
				/* translators: %s: localized selected-language count. */
				__( 'All available languages selected (%s)', 'auto-translate' ),
				$language_count_detail
			);
		}
		$menu          = trim( (string) ( $values['wpat_show_in_menu'] ?? '' ) );
		$wrapper       = trim( (string) ( $values['wpat_wrapper_selector'] ?? '' ) );
		$floating      = ! empty( $values['wpat_default_location'] );
		$placement_modes = array();

		if ( $floating ) {
			$placement_modes[] = 'floating';
		}
		if ( '' !== $menu ) {
			$placement_modes[] = 'menu';
		}
		if ( '' !== $wrapper ) {
			$placement_modes[] = 'wrapper';
		}

		$widget_type = Auto_Translate_Config::normalize_widget_type( (string) ( $values['wpat_widget_type'] ?? '' ) );
		$base_style  = (string) ( $values['wpat_min_base_style'] ?? '' );
		$styling_complete = '' !== $widget_type && ( 'classic' === $widget_type || in_array( $base_style, array( 'compact', 'minimal', 'native' ), true ) );

		return array(
			'values' => array(
				'wpat_supported_languages' => $selected,
				'wpat_default_location'    => $floating,
				'wpat_show_in_menu'        => $menu,
				'wpat_wrapper_selector'    => $wrapper,
				'wpat_widget_type'         => $widget_type,
				'wpat_min_base_style'      => $base_style,
			),
			'catalog_count' => count( $catalog ),
			'languages' => array(
				'complete' => $language_count > 0,
				'count'    => $language_count,
				'all'      => $has_all,
				'detail'  => $language_count_detail,
			),
			'placement' => array(
				'complete' => ! empty( $placement_modes ),
				'modes'    => $placement_modes,
				'manual'   => empty( $placement_modes ),
			),
			'styling' => array(
				'complete' => $styling_complete,
			),
			'labels' => array(
				'complete'          => __( 'Complete', 'auto-translate' ),
				'incomplete'        => __( 'Incomplete', 'auto-translate' ),
				'placement_floating' => __( 'Floating selector configured', 'auto-translate' ),
				'placement_menu'     => __( 'Navigation menu placement configured', 'auto-translate' ),
				'placement_wrapper'  => __( 'Wrapper selector placement configured', 'auto-translate' ),
				'placement_manual'   => __( 'No automatic placement; add a shortcode, widget, or block.', 'auto-translate' ),
				'styling_ready'      => __( 'Selector style configured', 'auto-translate' ),
				'styling_missing'    => __( 'Choose a selector style.', 'auto-translate' ),
			),
		);
	}

	/**
	 * Build a readiness response after a draft mutation without writing options.
	 * Missing draft fields intentionally fall back to the current canonical
	 * values, while the response exposes only checklist-related fields.
	 *
	 * @param array<string, mixed> $payload Complete private draft payload.
	 * @return array<string, mixed>
	 */
	private function get_publish_readiness_for_draft( $payload ) {
		$values = array(
			'wpat_supported_languages' => get_option( 'wpat_supported_languages', array() ),
			'wpat_default_location'    => get_option( 'wpat_default_location', true ),
			'wpat_show_in_menu'        => get_option( 'wpat_show_in_menu', '' ),
			'wpat_wrapper_selector'    => get_option( 'wpat_wrapper_selector', '' ),
			'wpat_widget_type'         => get_option( 'wpat_widget_type', 'minimalist' ),
			'wpat_min_base_style'      => get_option( 'wpat_min_base_style', 'compact' ),
		);

		foreach ( array_keys( $values ) as $option ) {
			if ( array_key_exists( $option, $payload ) ) {
				$values[ $option ] = $payload[ $option ];
			}
		}

		return $this->build_publish_readiness_snapshot( $values );
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

	public function handle_preview_site_action() {
		if ( ! $this->current_user_can_manage_plugin() ) {
			wp_die( esc_html__( 'You are not allowed to manage Automatic Translator.', 'auto-translate' ) );
		}

		check_admin_referer( 'wpat_preview_site' );

		if ( $this->lifecycle instanceof Auto_Translate_Lifecycle ) {
			Auto_Translate_Lifecycle::record_action( 'preview_site' );
		}

		$session  = $this->get_settings_draft_cookie( 'wpat_settings_draft_session' );
		$draft_id = $this->get_settings_draft_cookie( 'wpat_settings_draft_id' );
		$token    = $this->settings_draft->issue_preview_token( get_current_user_id(), $session, $draft_id );
		$url      = home_url( '/' );

		if ( is_string( $token ) && '' !== $token ) {
			$url = add_query_arg( 'wpat_settings_preview', rawurlencode( $token ), $url );
		}

		wp_safe_redirect( $url );
		exit;
	}

	public function handle_go_live_action() {
		if ( ! $this->current_user_can_manage_plugin() ) {
			wp_die( esc_html__( 'You are not allowed to manage Automatic Translator.', 'auto-translate' ) );
		}

		check_admin_referer( 'wpat_set_go_live' );

		$go_live = rest_sanitize_boolean( wp_unslash( $_POST['wpat_go_live'] ?? 0 ) );
		if ( $go_live ) {
			$session  = $this->get_settings_draft_cookie( 'wpat_settings_draft_session' );
			$draft_id = $this->get_settings_draft_cookie( 'wpat_settings_draft_id' );
			$committed = $this->settings_draft->commit( get_current_user_id(), $session, $draft_id );

			if ( false !== $committed ) {
				$this->clear_settings_draft_cookies();
			}

			if ( isset( $_POST['wpat_publish_reminder_enabled'] ) ) {
				update_user_meta( get_current_user_id(), self::PUBLISH_REMINDER_USER_META, rest_sanitize_boolean( wp_unslash( $_POST['wpat_publish_reminder_enabled'] ) ) ? '1' : '0' );
			}
		}

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

	/**
	 * Dismiss the review and support notice for the current plugin version.
	 *
	 * The notice automatically returns after an update because the dismissed
	 * version stored in user meta no longer matches the running version.
	 */
	public function handle_plugin_links_notice_dismissal_ajax() {
		if ( ! $this->current_user_can_manage_plugin() ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to manage Automatic Translator.', 'auto-translate' ) ), 403 );
		}

		check_ajax_referer( 'wpat_dismiss_plugin_links_notice', 'nonce' );
		update_user_meta( get_current_user_id(), self::PLUGIN_LINKS_NOTICE_USER_META, $this->version );
		wp_send_json_success();
	}

	/**
	 * Render one settings tab through the same view model as the full page.
	 *
	 * The AJAX endpoint deliberately returns only the replaceable panel. The
	 * surrounding admin header, preview, and tab list stay in the document.
	 *
	 * @return void
	 */
	public function handle_load_settings_tab_ajax() {
		if ( ! $this->current_user_can_manage_plugin() ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to manage Automatic Translator.', 'auto-translate' ) ), 403 );
		}

		check_ajax_referer( 'wpat_load_settings_tab', 'nonce' );
		$tab = isset( $_POST['tab'] ) ? sanitize_key( wp_unslash( $_POST['tab'] ) ) : '';
		$tab = $this->normalize_settings_tab( $tab );

		if ( '' === $tab ) {
			wp_send_json_error( array( 'message' => __( 'That settings tab is not available.', 'auto-translate' ) ), 400 );
		}

		$vars = $this->get_settings_page_vars( $tab );
		ob_start();
		require 'partials/auto-translate-admin-settings-tab-display.php';
		$html = ob_get_clean();

		wp_send_json_success(
			array(
				'tab'   => $tab,
				'html'  => $html,
				'title' => $this->get_settings_tab_label( $tab ),
			)
		);
	}

	/**
	 * Render the complete settings page.
	 *
	 * @return void
	 */
	public function auto_translate_settings_page() {
		$vars = $this->get_settings_page_vars();
		require_once 'partials/auto-translate-admin-display.php';
	}

	/**
	 * Build the settings view model. Draft filters must be installed before
	 * reading options so both full-page and fragment requests agree.
	 *
	 * @param string|null $active_tab Optional active tab override.
	 * @return array<string, mixed>
	 */
	private function get_settings_page_vars( $active_tab = null ) {
		$this->enable_settings_draft_for_admin_page();

		if ( $this->lifecycle instanceof Auto_Translate_Lifecycle ) {
			Auto_Translate_Lifecycle::record_dashboard_seen();
		}

		$wpat_supported_languages = Auto_Translate_Config::get_supported_languages();
		$langs_per_column = 27;
		$wpat_min_base_style = get_option( 'wpat_min_base_style', 'compact' );
		$wpat_min_transparent_default = 'minimal' === $wpat_min_base_style ? 'on' : '';
		$vars = [];
		$is_live = $this->is_go_live_enabled();
		$wpat_mode_updated_input = filter_input( INPUT_GET, 'wpat_mode_updated', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$wpat_mode_input = filter_input( INPUT_GET, 'wpat_mode', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$wpat_active_tab_input = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$requested_tab = null !== $active_tab
			? $active_tab
			: ( is_string( $wpat_active_tab_input ) ? sanitize_key( $wpat_active_tab_input ) : '' );
		$vars['active_tab'] = $this->normalize_settings_tab( $requested_tab );
		if ( '' === $vars['active_tab'] ) {
			$vars['active_tab'] = 'visual_settings';
		}
		$vars['plugin_icon_url'] = plugins_url( 'assets/icon-128x128.png', dirname( __DIR__ ) . '/auto-translate.php' );
		$vars['classic_widget_migrated_notice'] = $this->consume_classic_widget_migration_notice();
		$vars['reviews_url'] = $this->get_five_star_reviews_url();
		$vars['support_url'] = self::SUPPORT_URL;
		$vars['preview_site_url'] = $this->get_preview_site_url();
		$vars['go_live_action_url'] = admin_url( 'admin-post.php' );
		$vars['is_live'] = $is_live;
		$vars['is_preview_mode'] = ! $is_live;
		$vars['show_publish_reminder'] = ! $is_live && $this->should_show_publish_reminder();
		$vars['is_plugin_status_open'] = $this->is_plugin_status_open();
		$vars['show_plugin_links_notice'] = $this->should_show_plugin_links_notice();
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
					'wpat_min_base_style' => $wpat_min_base_style,
					'wpat_min_preset' => get_option( 'wpat_min_preset', 'quick_switcher' ),
					'wpat_min_layout' => get_option('wpat_min_layout', 'dropdown'),
					'wpat_min_icon' => get_option('wpat_min_icon'),
					'wpat_min_txt_display' => get_option('wpat_min_txt_display'),
					'wpat_min_txt_underline' => get_option( 'wpat_min_txt_underline', '' ),
					'wpat_min_text_divider' => get_option( 'wpat_min_text_divider', 'none' ),
					'wpat_min_border_thickness' => get_option('wpat_min_border_thickness'),
					'wpat_min_border_color' => get_option('wpat_min_border_color'),
					'wpat_min_border_transparent' => get_option( 'wpat_min_border_transparent', $wpat_min_transparent_default ),
					'wpat_min_border_transparent_is_default' => false === get_option( 'wpat_min_border_transparent', false ),
					'wpat_min_background_color' => get_option('wpat_min_background_color'),
					'wpat_min_background_transparent' => get_option( 'wpat_min_background_transparent', $wpat_min_transparent_default ),
					'wpat_min_background_transparent_is_default' => false === get_option( 'wpat_min_background_transparent', false ),
					'wpat_min_font_color' => get_option('wpat_min_font_color'),
					'wpat_min_font_family' => get_option('wpat_min_font_family'),
					'wpat_min_hover_color' => get_option( 'wpat_min_hover_color', '#f0f0f0' ),
					'wpat_min_hover_transparent' => get_option( 'wpat_min_hover_transparent', $wpat_min_transparent_default ),
					'wpat_min_hover_transparent_is_default' => false === get_option( 'wpat_min_hover_transparent', false ),
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
		$vars['publish_readiness'] = $this->get_publish_readiness_snapshot( $vars );

		return $vars;
	}

	/**
	 * @param string $tab Tab key.
	 * @return string
	 */
	private function normalize_settings_tab( $tab ) {
		$tab = sanitize_key( (string) $tab );

		return in_array( $tab, array( 'visual_settings', 'language_settings', 'placement_settings', 'advanced_settings' ), true ) ? $tab : '';
	}

	/**
	 * @param string $tab Tab key.
	 * @return string
	 */
	private function get_settings_tab_label( $tab ) {
		$labels = array(
			'visual_settings'    => __( 'Styling', 'auto-translate' ),
			'language_settings'  => __( 'Languages', 'auto-translate' ),
			'placement_settings' => __( 'Placement', 'auto-translate' ),
			'advanced_settings'  => __( 'Advanced', 'auto-translate' ),
		);

		return $labels[ $tab ] ?? __( 'Settings', 'auto-translate' );
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
