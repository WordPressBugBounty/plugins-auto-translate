<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://pampa.dev
 * @since      1.0.0
 *
 * @package    Auto_Translate
 * @subpackage Auto_Translate/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Auto_Translate
 * @subpackage Auto_Translate/public
 * @author     Pampa Dev <intouch@pampa.dev>
 */
class Auto_Translate_Public
{
    /**
     * Cache key for Divi Theme Builder shortcode detection.
     *
     * @var string
     */
    private $divi_shortcode_cache_key = 'wpat_divi_shortcode_layouts_have_button';

    /**
     * Whether inline runtime fallback markup has already been emitted.
     *
     * @var bool
     */
    private $inline_runtime_rendered = false;

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        if ( ! $this->should_boot_translator() ) {
            return;
        }

        $this->enqueue_public_styles();
    }

    private function enqueue_public_styles() {

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
        
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( dirname(__FILE__) ) . 'public/css/auto-translate-public.min.css',
            array(),
            $this->get_asset_version( 'public/css/auto-translate-public.min.css' ),
            'all'
        );
		wp_enqueue_style(
            $this->plugin_name . '-global',
            plugin_dir_url( dirname(__FILE__) ) . 'global/css/auto-translate-global.min.css',
            array(),
            $this->get_asset_version( 'global/css/auto-translate-global.min.css' ),
            'all'
        );
        wp_enqueue_style( 'dashicons' );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        if ( ! $this->should_boot_translator() ) {
            return;
        }

        $this->enqueue_public_scripts();
    }

    /**
     * Mark plugin frontend scripts as defer-safe.
     *
     * The translator bootstraps from DOMContentLoaded-compatible markup and does not
     * rely on synchronous parser execution.
     *
     * @since 2.1.0
     * @param string $tag    Script tag HTML.
     * @param string $handle Registered script handle.
     * @return string
     */
    public function add_defer_to_public_script_tags( $tag, $handle ) {
        $deferred_handles = array(
            $this->plugin_name,
            $this->plugin_name . '-global',
        );

        if ( ! in_array( $handle, $deferred_handles, true ) || false !== strpos( $tag, ' defer' ) ) {
            return $tag;
        }

        return str_replace( ' src=', ' defer src=', $tag );
    }

    private function enqueue_public_scripts() {

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

		wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/auto-translate-public.min.js',
            array('jquery'),
            $this->get_asset_version( 'public/js/auto-translate-public.min.js' ),
            true
        );
		wp_enqueue_script(
            $this->plugin_name . '-global',
            plugin_dir_url( dirname(__FILE__) ) . 'global/js/auto-translate-global.min.js',
            array(),
            $this->get_asset_version( 'global/js/auto-translate-global.min.js' ),
            true
        );
    }

    /**
     * Resolve a cache-busting version from the compiled asset mtime.
     *
     * @since 1.6.0
     * @param string $relative_path Relative path from plugin root.
     * @return string
     */
    private function get_asset_version( $relative_path ) {
        $path = plugin_dir_path( dirname( __FILE__ ) ) . ltrim( $relative_path, '/' );
        $mtime = file_exists( $path ) ? filemtime( $path ) : false;

        return $mtime ? (string) $mtime : (string) $this->version;
    }


    public function hook_javascript_translator()
    {
        if ( ! $this->should_boot_translator() ) {
            return;
        }

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Returns plugin-generated config/style markup.
        echo $this->get_translator_bootstrap_markup();
    }

    private function get_translator_bootstrap_markup() {
        ob_start();

        /* Language settings */
        $included_languages = implode(',', array_keys($this->get_included_languages()));
        $wpat_language_flags = get_option( 'wpat_language_flags', array() );
        $languages_data = Auto_Translate_Languages::get_languages_data(
            explode(',', $included_languages),
            (string) get_option( 'wpat_language_name_display', 'native' ),
            is_array( $wpat_language_flags ) ? $wpat_language_flags : array()
        );

        /* Styling settings */
        $wpat_widget_type = Auto_Translate_Config::normalize_widget_type( get_option('wpat_widget_type') );
        // Classic settings
        $wpat_button_icon = get_option('wpat_button_icon');
        $wpat_show_icon = get_option('wpat_show_icon');
        $wpat_base_language = Auto_Translate_Config::get_resolved_base_language();
        $wpat_color_1 = get_option('wpat_color_1');
        $wpat_color_2 = get_option('wpat_color_2');
        $wpat_border_radius = get_option('wpat_border_radius');
        $wpat_border_thickness = get_option('wpat_border_thickness');
        $wpat_border_color = get_option('wpat_border_color');
        $wpat_font_color = get_option('wpat_font_color');
        $wpat_font_family = get_option('wpat_font_family');
        $wpat_dropdown_shadow = get_option('wpat_dropdown_shadow');
        $wpat_dropdown_border_thickness = get_option('wpat_dropdown_border_thickness');
        $wpat_dropdown_border_color = get_option('wpat_dropdown_border_color');
        $wpat_dropdown_background_color = get_option('wpat_dropdown_background_color');
        $wpat_dropdown_hover_color = get_option('wpat_dropdown_hover_color');
        $wpat_dropdown_font_hover_color = get_option('wpat_dropdown_font_hover_color');
        $wpat_dropdown_font_selected_color = get_option('wpat_dropdown_font_selected_color');
        $wpat_dropdown_font_color = get_option('wpat_dropdown_font_color');
        $wpat_dropdown_font_family = get_option('wpat_dropdown_font_family');
        $wpat_auto_detect           = get_option('wpat_auto_detect');
        $wpat_custom_css            = get_option('wpat_custom_css', '');
        // Keep host language cache-safe; visitor detection now runs client-side.
        $wpat_host_language         = $wpat_base_language;
        $wpat_google_mount_id       = 'wpat-google-translate-element';
        $wpat_google_script_url     = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
        include 'partials/auto-translate-public-header-display.php';

        return ob_get_clean();
    }

    public function register_selector_block() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        $editor_asset_path = plugin_dir_path( dirname( __FILE__ ) ) . 'admin/js/auto-translate-block-editor.min.js';
        if ( file_exists( $editor_asset_path ) ) {
            wp_register_script(
                $this->plugin_name . '-block-editor',
                plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/auto-translate-block-editor.min.js',
                array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor', 'wp-components' ),
                $this->get_asset_version( 'admin/js/auto-translate-block-editor.min.js' ),
                true
            );
        }

        $block_args = array(
            'api_version'     => 2,
            'render_callback' => array( $this, 'render_selector_block' ),
        );
        if ( wp_script_is( $this->plugin_name . '-block-editor', 'registered' ) ) {
            $block_args['editor_script'] = $this->plugin_name . '-block-editor';
        }

        register_block_type( 'auto-translate/selector', $block_args );
    }

    /**
     * Decide if frontend translator assets/bootstrap should load on this request.
     *
     * Keeps third-party script loading scoped to pages where the translator can
     * actually render (default button, menu injection, shortcode, or widget).
     *
     * @since 1.6.0
     * @return bool
     */
    private function should_boot_translator() {
        if ( is_admin() ) {
            return false;
        }

        if ( get_option( 'wpat_default_location', true ) ) {
            return true;
        }

        $selection = $this->parse_menu_selection( get_option( 'wpat_show_in_menu', '' ) );
        if ( $selection ) {
            if ( 'menu' === $selection['type'] && wp_get_nav_menu_object( $selection['id'] ) ) {
                return true;
            }

            if ( 'navigation' === $selection['type'] ) {
                $navigation_post = get_post( $selection['id'] );
                if ( $navigation_post && 'wp_navigation' === $navigation_post->post_type ) {
                    return true;
                }
            }

            if ( 'location' === $selection['type'] && has_nav_menu( $selection['value'] ) ) {
                return true;
            }
        }

        if ( is_singular() ) {
            $post = get_post();
            if ( $post instanceof WP_Post && ( has_shortcode( (string) $post->post_content, 'auto_translate_button' ) || ( function_exists( 'has_block' ) && has_block( 'auto-translate/selector', $post ) ) ) ) {
                return true;
            }
        }

        if ( $this->shortcode_in_divi_layouts() ) {
            return true;
        }

        if ( is_active_widget( false, false, 'wpat_button_widget', true ) ) {
            return true;
        }

        if ( '' !== trim( (string) get_option( 'wpat_wrapper_selector', '' ) ) ) {
            return true;
        }

        return false;
    }

    /**
     * Detect shortcode usage in published Divi Theme Builder layouts.
     *
     * @since 2.0.1
     * @return bool
     */
    private function shortcode_in_divi_layouts() {
        $divi_layout_post_types = $this->get_divi_layout_post_types();
        if ( empty( $divi_layout_post_types ) ) {
            return false;
        }

        $cached_result = get_transient( $this->divi_shortcode_cache_key );
        if ( false !== $cached_result ) {
            return (bool) $cached_result;
        }

        $layout_posts = get_posts(
            array(
                'post_type'              => $divi_layout_post_types,
                'post_status'            => 'publish',
                'numberposts'            => -1,
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
            )
        );

        $has_shortcode = false;
        foreach ( $layout_posts as $layout_post ) {
            if ( $layout_post instanceof WP_Post && false !== strpos( (string) $layout_post->post_content, '[auto_translate_button' ) ) {
                $has_shortcode = true;
                break;
            }
        }

        set_transient( $this->divi_shortcode_cache_key, $has_shortcode, defined( 'HOUR_IN_SECONDS' ) ? HOUR_IN_SECONDS : 3600 );

        return $has_shortcode;
    }

    /**
     * Return the active Divi Theme Builder layout post types available on this site.
     *
     * @since 2.0.1
     * @return array<int, string>
     */
    private function get_divi_layout_post_types() {
        $post_types = array(
            'et_header_layout',
            'et_body_layout',
            'et_footer_layout',
        );

        return array_values(
            array_filter(
                $post_types,
                'post_type_exists'
            )
        );
    }

    /**
     * Invalidate the cached Divi shortcode detection on relevant layout saves.
     *
     * @since 2.0.1
     * @return void
     */
    public function clear_divi_shortcode_layout_cache() {
        delete_transient( $this->divi_shortcode_cache_key );
    }

    private function get_included_languages(){
        $wpat_supported_languages = Auto_Translate_Config::get_supported_languages();
        $wpat_base_language = Auto_Translate_Config::get_resolved_base_language();
        $wpat_selected_languages = get_option('wpat_supported_languages');
        $wpat_language_order = get_option( 'wpat_language_order', '' );

        if (is_array($wpat_selected_languages) && !in_array('all', $wpat_selected_languages, true)) {
            $included_languages = array();

            $wpat_selected_languages[] = $wpat_base_language;
            foreach($wpat_selected_languages as $language){
                if ( isset( $wpat_supported_languages[ $language ] ) ) {
                    $included_languages[$language] = $wpat_supported_languages[$language];
                }
            }
        } else {
            $included_languages = $wpat_supported_languages;
        }

        return $this->apply_language_order( $included_languages, $wpat_language_order, $wpat_base_language );
    }

    private function apply_language_order( $included_languages, $order_csv, $base_language ) {
        if ( ! is_array( $included_languages ) ) {
            return array();
        }

        if ( ! is_string( $order_csv ) || '' === trim( $order_csv ) ) {
            return $included_languages;
        }

        $ordered = array();
        $order = array_filter( array_map( 'trim', explode( ',', $order_csv ) ) );
        foreach ( $order as $code ) {
            if ( isset( $included_languages[ $code ] ) ) {
                $ordered[ $code ] = $included_languages[ $code ];
            }
        }

        foreach ( $included_languages as $code => $label ) {
            if ( ! isset( $ordered[ $code ] ) ) {
                $ordered[ $code ] = $label;
            }
        }

        if ( isset( $ordered[ $base_language ] ) ) {
            $base_label = $ordered[ $base_language ];
            unset( $ordered[ $base_language ] );
            $ordered = array( $base_language => $base_label ) + $ordered;
        }

        return $ordered;
    }

    /**
     * Inject translator markup into the configured menu location.
     *
     * @param string   $items Existing menu items HTML.
     * @param stdClass $args  Menu rendering args from wp_nav_menu().
     * @return string
     */
    function hook_menu_item($items, $args){
        $menu_markup = '';
        if ( $this->should_render_in_menu( $args ) ) {
            $menu_markup = $this->content_translator( false, true );
        }
        if ( '' === $menu_markup ) {
            return $items;
        }

        if ( 'start' === get_option( 'wpat_menu_position', 'end' ) ) {
            return $menu_markup . $items;
        }

        return $items . $menu_markup;
      //  var_dump($args);
    }

    /**
     * Inject translator markup into block-theme Navigation blocks.
     *
     * @since 1.7.0
     * @param string $block_content Rendered block HTML.
     * @param array  $block         Parsed block array.
     * @return string
     */
    public function hook_navigation_block( $block_content, $block ) {
        static $wpat_navigation_block_injected = false;

        if ( ! is_string( $block_content ) || '' === $block_content ) {
            return $block_content;
        }

        if ( ! is_array( $block ) || 'core/navigation' !== ( $block['blockName'] ?? '' ) ) {
            return $block_content;
        }

        if ( $wpat_navigation_block_injected ) {
            return $block_content;
        }

        $should_render = $this->should_render_in_navigation_block( $block, $block_content );
        if ( ! $should_render ) {
            $selection = $this->parse_menu_selection( get_option( 'wpat_show_in_menu', '' ) );
            if ( ! $this->should_render_in_primary_navigation_fallback( $selection, $block, $block_content ) ) {
                return $block_content;
            }
        }

        $menu_markup = $this->content_translator( true, true, 'wp-block-navigation-item' );
        if ( '' === $menu_markup ) {
            return $block_content;
        }

        if ( false === strpos( $block_content, 'wp-block-navigation__container' ) ) {
            return $block_content;
        }

        if ( 'start' === get_option( 'wpat_menu_position', 'end' ) ) {
            $updated_content = preg_replace( '/(<ul\b[^>]*>)/', '$1' . $menu_markup, $block_content, 1 ) ?: $block_content;
        } else {
            $updated_content = preg_replace( '/<\/ul>/', $menu_markup . '</ul>', $block_content, 1 ) ?: $block_content;
        }
        $wpat_navigation_block_injected = true;

        return $updated_content;
    }

    /**
     * Render floating translator button in footer when default location is enabled.
     *
     * @return void
     */
    function hook_content_translator()
    {
        $wpat_default_location = get_option('wpat_default_location', true);
        if ( ! $this->should_boot_translator() && ! $this->inline_runtime_rendered ) {
            return;
        }

        if ( ! $this->inline_runtime_rendered ) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Safe static container used by the JS adapter.
            echo '<div id="wpat-google-translate-element" class="google_translate_element wpat-google-translate-root" aria-hidden="true"></div>';
        }

        if ( $wpat_default_location ) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Safe generated markup from plugin templates.
            echo $this->content_translator(true);
        }
    }

    function auto_translate_button_function ($atts = [], $content = null )
    {
        $markup = $this->content_translator(false);
        if ( $this->should_boot_translator() ) {
            return $markup;
        }

        $this->enqueue_public_styles();
        $this->enqueue_public_scripts();

        return $this->get_inline_runtime_markup() . $markup;
    }

    public function auto_translate_link_function( $atts = array(), $content = null ) {
        $atts = shortcode_atts(
            array(
                'lang'  => '',
                'label' => '',
            ),
            $atts,
            'auto_translate_link'
        );

        $lang = Auto_Translate_Config::normalize_lang_code( $atts['lang'] );
        $languages_data = Auto_Translate_Languages::get_languages_data(
            array_keys( $this->get_included_languages() ),
            (string) get_option( 'wpat_language_name_display', 'native' )
        );
        if ( ! isset( $languages_data[ $lang ] ) ) {
            return '';
        }

        $label = is_string( $atts['label'] ) && '' !== trim( $atts['label'] )
            ? sanitize_text_field( $atts['label'] )
            : $languages_data[ $lang ]['lang_name'];

        return '<a href="#" class="wpat-language-link notranslate skiptranslate" data-wpat-lang="' . esc_attr( $lang ) . '">' . esc_html( $label ) . '</a>';
    }

    public function render_selector_block( $attributes = array(), $content = '' ) {
        if ( ! $this->should_boot_translator() ) {
            $this->enqueue_public_styles();
            $this->enqueue_public_scripts();
        }

        $markup = $this->content_translator( false );
        if ( $this->should_boot_translator() ) {
            return $markup;
        }

        return $this->get_inline_runtime_markup() . $markup;
    }

    /**
     * Return one-time bootstrap markup for runtime placements missed by wp_enqueue_scripts.
     *
     * PHP template calls such as echo do_shortcode( '[auto_translate_button]' ) are not
     * discoverable during the normal enqueue pass, so they need the same inline runtime
     * fallback used by server-rendered blocks.
     *
     * @since 2.1.2
     * @return string
     */
    private function get_inline_runtime_markup() {
        if ( $this->inline_runtime_rendered ) {
            return '';
        }

        $this->inline_runtime_rendered = true;

        return $this->get_translator_bootstrap_markup()
            . '<div id="wpat-google-translate-element" class="google_translate_element wpat-google-translate-root" aria-hidden="true"></div>';
    }

    private function content_translator($default_location,$menu = false, $menu_item_classes = '')
    {
        ob_start();
        $wpat_widget_type           = Auto_Translate_Config::normalize_widget_type( get_option('wpat_widget_type') );
        $wpat_min_style             = get_option('wpat_min_style');
        $wpat_min_layout            = get_option('wpat_min_layout', 'dropdown');
        $wpat_min_icon              = get_option('wpat_min_icon');
        $wpat_min_txt_display       = get_option('wpat_min_txt_display');
        $wpat_min_chevron           = get_option('wpat_min_chevron');
        $wpat_min_txt_underline     = get_option('wpat_min_txt_underline');
        $wpat_min_border_thickness  = get_option('wpat_min_border_thickness');
        $wpat_min_border_color      = get_option('wpat_min_border_color');
        $wpat_min_background_color  = get_option('wpat_min_background_color');
        $wpat_min_font_color        = get_option('wpat_min_font_color');
        $wpat_min_font_family       = get_option('wpat_min_font_family');
        $wpat_min_hover_color       = get_option('wpat_min_hover_color');
        $wpat_min_font_hover_color  = get_option('wpat_min_font_hover_color');
        $wpat_widget_size           = get_option('wpat_widget_size');
        $wpat_base_language         = Auto_Translate_Config::get_resolved_base_language();
        $wpat_auto_detect           = get_option('wpat_auto_detect');
        $wpat_menu_item_classes     = $menu_item_classes;
        $wpat_floating_position     = sanitize_key( (string) get_option('wpat_floating_position', 'top_left') );
        $wpat_floating_offset_x     = absint( get_option('wpat_floating_offset_x', 16) );
        $wpat_floating_offset_y     = absint( get_option('wpat_floating_offset_y', 16) );
        $wpat_floating_classes      = $this->get_floating_classes( $default_location, $wpat_floating_position );
        $wpat_floating_style        = sprintf( '--wpat-float-offset-x:%dpx;--wpat-float-offset-y:%dpx;', $wpat_floating_offset_x, $wpat_floating_offset_y );

        $included_languages     = $this->get_included_languages();
        $wpat_language_flags    = get_option( 'wpat_language_flags', array() );
        $wpat_languages_data    = Auto_Translate_Languages::get_languages_data(
            array_keys($included_languages),
            (string) get_option( 'wpat_language_name_display', 'native' ),
            is_array( $wpat_language_flags ) ? $wpat_language_flags : array()
        );
        $wpat_selector_instance_id = 'wpat-selector-' . absint( wp_rand() );
        $wpat_dropdown_id          = 'wpat-minimalist-dropdown-' . absint( wp_rand() );
        $wpat_selector_view_model  = $this->get_selector_view_model(
            array(
                'instance_id'     => $wpat_selector_instance_id,
                'dropdown_id'     => $wpat_dropdown_id,
                'base_language'   => $wpat_base_language,
                'host_language'   => $wpat_base_language,
                'auto_detect'     => $wpat_auto_detect,
                'layout'          => $wpat_min_layout,
                'text_display'    => $wpat_min_txt_display,
                'style'           => $wpat_min_style,
                'icon_class'      => $wpat_min_icon,
                'chevron_class'   => $wpat_min_chevron,
                'languages_data'  => $wpat_languages_data,
            )
        );

        if(!$menu){
            include 'partials/auto-translate-public-display.php';
        }else{
            include 'partials/auto-translate-public-display-in-menu.php';
        }
        
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    /**
     * Build the selector view model used by the frontend widget runtime.
     *
     * @since 2.0.1
     * @param array $args Selector instance arguments.
     * @return array
     */
    private function get_selector_view_model( $args ) {
        $base_language = isset( $args['base_language'] ) ? Auto_Translate_Config::normalize_lang_code( $args['base_language'] ) : 'en';
        $host_language = isset( $args['host_language'] ) ? Auto_Translate_Config::normalize_lang_code( $args['host_language'] ) : $base_language;
        $text_display  = isset( $args['text_display'] ) ? sanitize_key( $args['text_display'] ) : 'name';
        $languages     = $this->get_selector_language_view_model(
            isset( $args['languages_data'] ) && is_array( $args['languages_data'] ) ? $args['languages_data'] : array(),
            $text_display
        );

        return array(
            'instanceId'    => isset( $args['instance_id'] ) ? sanitize_html_class( (string) $args['instance_id'] ) : '',
            'dropdownId'    => isset( $args['dropdown_id'] ) ? sanitize_html_class( (string) $args['dropdown_id'] ) : '',
            'widgetType'    => 'minimalist',
            'baseLanguage'  => $base_language,
            'hostLanguage'  => $host_language,
            'autoDetect'    => isset( $args['auto_detect'] ) ? sanitize_text_field( (string) $args['auto_detect'] ) : '',
            'layout'        => isset( $args['layout'] ) ? sanitize_key( $args['layout'] ) : 'dropdown',
            'textDisplay'   => $text_display,
            'style'         => isset( $args['style'] ) ? sanitize_key( $args['style'] ) : '',
            'iconClass'     => isset( $args['icon_class'] ) ? sanitize_html_class( (string) $args['icon_class'] ) : '',
            'chevronClass'  => isset( $args['chevron_class'] ) ? sanitize_html_class( (string) $args['chevron_class'] ) : '',
            'languages'     => $languages,
            'languageCodes' => array_keys( $languages ),
            'messages'      => array(
                'loading' => __( 'Switching language.', 'auto-translate' ),
                'error'   => __( 'Translation unavailable. Please try again.', 'auto-translate' ),
            ),
        );
    }

    /**
     * Normalize language records into a UI-focused selector view model.
     *
     * @since 2.0.1
     * @param array  $languages_data Raw language metadata.
     * @param string $text_display   Configured label style.
     * @return array
     */
    private function get_selector_language_view_model( $languages_data, $text_display ) {
        $view_model = array();

        foreach ( $languages_data as $lang_code => $lang_data ) {
            if ( ! is_array( $lang_data ) ) {
                continue;
            }

            $normalized_lang_code = isset( $lang_data['lang_code'] ) ? sanitize_text_field( (string) $lang_data['lang_code'] ) : sanitize_text_field( (string) $lang_code );
            $lang_name            = isset( $lang_data['lang_name'] ) ? sanitize_text_field( (string) $lang_data['lang_name'] ) : $normalized_lang_code;
            $lang_name_native     = isset( $lang_data['lang_name_native'] ) ? sanitize_text_field( (string) $lang_data['lang_name_native'] ) : $lang_name;
            $country_code         = isset( $lang_data['country_code'] ) ? sanitize_html_class( (string) $lang_data['country_code'] ) : '';

            switch ( $text_display ) {
                case 'code':
                    $display_label = $normalized_lang_code;
                    break;
                case 'name_code':
                    $display_label = $lang_name . ' - ' . $normalized_lang_code;
                    break;
                case 'name':
                default:
                    $display_label = $lang_name;
                    break;
            }

            $search_text = trim( implode( ' ', array_filter( array( $normalized_lang_code, $lang_name, $lang_name_native, $display_label ) ) ) );

            $view_model[ Auto_Translate_Config::normalize_lang_code( (string) $lang_code ) ] = array(
                'lang_code'        => $normalized_lang_code,
                'lang_name'        => $lang_name,
                'lang_name_native' => $lang_name_native,
                'country_code'     => $country_code,
                'display_label'    => $display_label,
                'search_text'      => $search_text,
            );
        }

        return $view_model;
    }

    private function get_floating_classes( $enabled, $position ) {
        if ( ! $enabled ) {
            return '';
        }

        $position = in_array( $position, array( 'top_left', 'top_right', 'bottom_left', 'bottom_right' ), true ) ? $position : 'top_left';

        return 'wpat_float wpat_float_' . $position;
    }

    

    function detect_language($fallback = 'en', $wpat_supported_languages = []) {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return $fallback;
        }
        $supported_map = array();
        foreach ( (array) $wpat_supported_languages as $supported_code ) {
            if ( ! is_string( $supported_code ) ) {
                continue;
            }
            $supported_map[ strtolower( $supported_code ) ] = $supported_code;
        }
        $http_accept_language = sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) );
        foreach (preg_split('/[;,]/', $http_accept_language) as $sub) {
            if (substr($sub, 0, 2) == 'q=') continue;
            $sub = trim( $sub );
            $sub_lower = strtolower( $sub );
            if ( isset( $supported_map[ $sub_lower ] ) ) {
                return $supported_map[ $sub_lower ];
            }
            if (strpos($sub, '-') !== false) {
                $short_code = strtolower( explode('-', $sub)[0] );
                if ( isset( $supported_map[ $short_code ] ) ) {
                    return $supported_map[ $short_code ];
                }
            }
        }
        return $fallback;
    }

    /**
     * Determine whether the translator should render inside the current menu output.
     *
     * Supports the current menu-term based setting and legacy theme-location values
     * stored by earlier plugin versions.
     *
     * @since 1.7.0
     * @param stdClass $args Menu rendering args from wp_nav_menu().
     * @return bool
     */
    private function should_render_in_menu( $args ) {
        $selection = $this->parse_menu_selection( get_option( 'wpat_show_in_menu', '' ) );

        if ( ! $selection ) {
            return false;
        }

        if ( 'menu' === $selection['type'] ) {
            $menu = $this->get_rendered_menu_object( $args );

            return $menu && absint( $menu->term_id ) === $selection['id'];
        }

        if ( 'navigation' === $selection['type'] ) {
            return false;
        }

        $wpat_show_in_menu = $selection['value'];
        $theme_location = isset( $args->theme_location ) ? sanitize_key( (string) $args->theme_location ) : '';

        return '' !== $theme_location && $theme_location === sanitize_key( (string) $wpat_show_in_menu );
    }

    /**
     * Decide whether a rendered Navigation block should receive the translator.
     *
     * @since 1.7.0
     * @param array  $block         Parsed `core/navigation` block.
     * @param string $block_content Rendered navigation block HTML.
     * @return bool
     */
    private function should_render_in_navigation_block( $block, $block_content ) {
        if ( ! is_array( $block ) || ! is_string( $block_content ) ) {
            return false;
        }

        $selection = $this->parse_menu_selection( get_option( 'wpat_show_in_menu', '' ) );
        if ( ! $selection ) {
            return false;
        }

        $navigation_ref = isset( $block['attrs']['ref'] ) ? absint( $block['attrs']['ref'] ) : 0;
        if ( 'navigation' === $selection['type'] ) {
            if ( $navigation_ref > 0 ) {
                return $navigation_ref === $selection['id'];
            }

            return $this->navigation_block_matches_selection_content( $selection, $block_content );
        }

        if ( 'menu' === $selection['type'] ) {
            if ( $navigation_ref > 0 ) {
                return $this->navigation_block_matches_menu( $navigation_ref, $selection['id'] );
            }

            return $this->navigation_block_matches_selection_content( $selection, $block_content );
        }

        return false;
    }

    /**
     * Resolve the concrete menu being rendered by wp_nav_menu().
     *
     * @since 1.7.0
     * @param stdClass $args Menu rendering args from wp_nav_menu().
     * @return WP_Term|null
     */
    private function get_rendered_menu_object( $args ) {
        if ( isset( $args->menu ) && ! empty( $args->menu ) ) {
            $menu = wp_get_nav_menu_object( $args->menu );
            if ( $menu ) {
                return $menu;
            }
        }

        if ( ! isset( $args->theme_location ) || empty( $args->theme_location ) ) {
            return null;
        }

        $locations = get_nav_menu_locations();
        $theme_location = sanitize_key( (string) $args->theme_location );
        if ( ! isset( $locations[ $theme_location ] ) ) {
            return null;
        }

        return wp_get_nav_menu_object( (int) $locations[ $theme_location ] );
    }

    /**
     * Normalize the stored "Show in Menu" option into a typed selection payload.
     *
     * @since 1.7.0
     * @param mixed $value Raw option value.
     * @return array|null
     */
    private function parse_menu_selection( $value ) {
        if ( ! is_scalar( $value ) ) {
            return null;
        }

        $value = trim( (string) $value );
        if ( '' === $value ) {
            return null;
        }

        if ( preg_match( '/^(menu|navigation):(\d+)$/', $value, $matches ) ) {
            return array(
                'type'  => $matches[1],
                'id'    => absint( $matches[2] ),
                'value' => $value,
            );
        }

        if ( is_numeric( $value ) ) {
            return array(
                'type'  => 'menu',
                'id'    => absint( $value ),
                'value' => $value,
            );
        }

        return array(
            'type'  => 'location',
            'id'    => 0,
            'value' => sanitize_key( $value ),
        );
    }

    /**
     * Compare a block-navigation reference against a classic nav menu selection.
     *
     * This keeps older saved menu selections working on block themes when the
     * corresponding `wp_navigation` entity shares the menu name/slug.
     *
     * @since 1.7.0
     * @param int $navigation_ref Navigation post ID from a rendered block.
     * @param int $menu_id        Classic nav menu term ID selected in plugin settings.
     * @return bool
     */
    private function navigation_block_matches_menu( $navigation_ref, $menu_id ) {
        $menu = wp_get_nav_menu_object( $menu_id );
        $navigation_post = get_post( $navigation_ref );

        if ( ! $menu || ! $navigation_post || 'wp_navigation' !== $navigation_post->post_type ) {
            return false;
        }

        if ( sanitize_title( $menu->name ) === $navigation_post->post_name ) {
            return true;
        }

        return $menu->name === $navigation_post->post_title;
    }

    /**
     * Match a rendered block-navigation instance by its item URLs when no `ref`
     * survives into the parsed block attributes.
     *
     * Block themes can inline referenced navigation content into render-time
     * `core/navigation` blocks. In that case the `ref` attribute is unavailable,
     * so we compare the rendered link sequence against the selected menu entity.
     *
     * @since 1.7.0
     * @param array  $selection     Parsed menu selection payload.
     * @param string $block_content Rendered navigation block HTML.
     * @return bool
     */
    private function navigation_block_matches_selection_content( $selection, $block_content ) {
        $expected_items = $this->get_selected_menu_items( $selection );
        if ( empty( $expected_items ) ) {
            return false;
        }

        $actual_items = $this->extract_navigation_items_from_block_markup( $block_content );
        if ( empty( $actual_items ) ) {
            return false;
        }

        $expected_urls = array_column( $expected_items, 'url' );
        $actual_urls   = array_column( $actual_items, 'url' );
        if ( ! empty( array_filter( $expected_urls ) ) && $expected_urls === $actual_urls ) {
            return true;
        }

        $expected_labels = array_column( $expected_items, 'label' );
        $actual_labels   = array_column( $actual_items, 'label' );

        return ! empty( array_filter( $expected_labels ) ) && $expected_labels === $actual_labels;
    }

    /**
     * Fallback for inline block-theme navigation without a stable `ref`.
     *
     * When the selected target cannot be matched back to a concrete menu entity,
     * prefer the first inline navigation block that renders real front-end links.
     * This avoids injecting into footer placeholder navigation blocks that only
     * contain `#` links.
     *
     * @since 1.7.0
     * @param array|null $selection     Parsed menu selection payload.
     * @param array      $block         Parsed `core/navigation` block.
     * @param string     $block_content Rendered navigation block HTML.
     * @return bool
     */
    private function should_render_in_primary_navigation_fallback( $selection, $block, $block_content ) {
        if ( ! is_array( $selection ) || ! in_array( $selection['type'], array( 'menu', 'navigation' ), true ) ) {
            return false;
        }

        if ( ! is_array( $block ) || ! is_string( $block_content ) ) {
            return false;
        }

        $navigation_ref = isset( $block['attrs']['ref'] ) ? absint( $block['attrs']['ref'] ) : 0;
        if ( $navigation_ref > 0 ) {
            return false;
        }

        $items = $this->extract_navigation_items_from_block_markup( $block_content );
        if ( empty( $items ) ) {
            return false;
        }

        foreach ( $items as $item ) {
            $url = $item['url'] ?? '';
            if ( '' !== $url && '#' !== $url && '/#' !== $url ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resolve the canonical ordered menu signature for a selected menu entity.
     *
     * @since 1.7.0
     * @param array $selection Parsed menu selection payload.
     * @return array<int,array{url:string,label:string}>
     */
    private function get_selected_menu_items( $selection ) {
        if ( ! is_array( $selection ) || empty( $selection['type'] ) ) {
            return array();
        }

        if ( 'menu' === $selection['type'] ) {
            $menu = wp_get_nav_menu_object( $selection['id'] );
            if ( ! $menu ) {
                return array();
            }

            $items = wp_get_nav_menu_items( $menu );
            if ( ! is_array( $items ) ) {
                return array();
            }

            return array_values(
                array_filter(
                    array_map(
                        function ( $item ) {
                            $url   = isset( $item->url ) ? $this->normalize_navigation_url( (string) $item->url ) : '';
                            $label = isset( $item->title ) ? $this->normalize_navigation_label( (string) $item->title ) : '';

                            return array(
                                'url'   => $url,
                                'label' => $label,
                            );
                        },
                        $items
                    ),
                    array( $this, 'is_navigation_item_signature_non_empty' )
                )
            );
        }

        if ( 'navigation' === $selection['type'] ) {
            $post = get_post( $selection['id'] );
            if ( ! $post || 'wp_navigation' !== $post->post_type ) {
                return array();
            }

            return $this->extract_navigation_items_from_block_markup( do_blocks( $post->post_content ) );
        }

        return array();
    }

    /**
     * Extract navigation link signatures from rendered block markup.
     *
     * @since 1.7.0
     * @param string $markup Rendered navigation HTML.
     * @return array<int,array{url:string,label:string}>
     */
    private function extract_navigation_items_from_block_markup( $markup ) {
        if ( ! is_string( $markup ) || '' === $markup ) {
            return array();
        }

        if ( ! preg_match_all( '/<a[^>]*class="[^"]*(?:wp-block-navigation-item__content|wp-block-pages-list__item__link)[^"]*"[^>]*href="([^"]+)"[^>]*>(.*?)<\/a>/si', $markup, $matches, PREG_SET_ORDER ) ) {
            return array();
        }

        return array_values(
            array_filter(
                array_map(
                    function ( $match ) {
                        $label_markup = isset( $match[2] ) ? (string) $match[2] : '';
                        if ( preg_match( '/<span[^>]*class="[^"]*wp-block-navigation-item__label[^"]*"[^>]*>(.*?)<\/span>/si', $label_markup, $label_match ) ) {
                            $label_markup = isset( $label_match[1] ) ? (string) $label_match[1] : $label_markup;
                        }

                        return array(
                            'url'   => $this->normalize_navigation_url( isset( $match[1] ) ? (string) $match[1] : '' ),
                            'label' => $this->normalize_navigation_label( $label_markup ),
                        );
                    },
                    $matches
                ),
                array( $this, 'is_navigation_item_signature_non_empty' )
            )
        );
    }

    /**
     * Normalize a navigation URL for block-menu matching.
     *
     * @since 1.7.0
     * @param string $url Raw URL.
     * @return string
     */
    private function normalize_navigation_url( $url ) {
        $url = trim( html_entity_decode( (string) $url, ENT_QUOTES, 'UTF-8' ) );
        if ( '' === $url ) {
            return '';
        }

        $parts = wp_parse_url( $url );
        if ( false === $parts ) {
            return untrailingslashit( $url );
        }

        $scheme   = isset( $parts['scheme'] ) ? strtolower( (string) $parts['scheme'] ) . '://' : '';
        $host     = isset( $parts['host'] ) ? strtolower( (string) $parts['host'] ) : '';
        $port     = isset( $parts['port'] ) ? ':' . absint( $parts['port'] ) : '';
        $path     = isset( $parts['path'] ) ? untrailingslashit( (string) $parts['path'] ) : '';
        $query    = isset( $parts['query'] ) ? '?' . (string) $parts['query'] : '';
        $fragment = isset( $parts['fragment'] ) ? '#' . (string) $parts['fragment'] : '';

        if ( '' === $path ) {
            $path = '/';
        }

        return $scheme . $host . $port . $path . $query . $fragment;
    }

    /**
     * Normalize navigation labels for block-menu matching.
     *
     * @since 1.7.0
     * @param string $label Raw label.
     * @return string
     */
    private function normalize_navigation_label( $label ) {
        $label = wp_strip_all_tags( html_entity_decode( (string) $label, ENT_QUOTES, 'UTF-8' ) );
        $label = preg_replace( '/\s+/', ' ', $label );

        return strtolower( trim( (string) $label ) );
    }

    /**
     * Check whether a normalized navigation signature still contains useful data.
     *
     * @since 1.7.0
     * @param array $item Menu signature payload.
     * @return bool
     */
    private function is_navigation_item_signature_non_empty( $item ) {
        return is_array( $item ) && ( '' !== ( $item['url'] ?? '' ) || '' !== ( $item['label'] ?? '' ) );
    }

}
