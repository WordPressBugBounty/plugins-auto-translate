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
            false
        );
		wp_enqueue_script(
            $this->plugin_name . '-global',
            plugin_dir_url( dirname(__FILE__) ) . 'global/js/auto-translate-global.min.js',
            array(),
            $this->get_asset_version( 'global/js/auto-translate-global.min.js' ),
            false
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

        wp_enqueue_script(
            'wpat-google-translate',
            'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit',
            array(),
            $this->version,
            false
        );

        /* Language settings */
        $included_languages = implode(',', array_keys($this->get_included_languages()));
        $languages_data = Auto_Translate_Languages::get_languages_data(explode(',',$included_languages));

        /* Styling settings */
        $wpat_widget_type = get_option('wpat_widget_type');
        // Classic settings
        $wpat_button_icon = get_option('wpat_button_icon');
        $wpat_show_icon = get_option('wpat_show_icon');
        $wpat_base_language = get_option('wpat_base_language');
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
        include 'partials/auto-translate-public-header-display.php';
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
            if ( $post instanceof WP_Post && has_shortcode( (string) $post->post_content, 'auto_translate_button' ) ) {
                return true;
            }
        }

        if ( is_active_widget( false, false, 'wpat_button_widget', true ) ) {
            return true;
        }

        return false;
    }

    private function get_included_languages(){
        $wpat_supported_languages = Auto_Translate_Config::get_supported_languages();
        $wpat_base_language = get_option('wpat_base_language');
        $wpat_selected_languages = get_option('wpat_supported_languages');

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

        return $included_languages;
    }

    /**
     * Inject translator markup into the configured menu location.
     *
     * @param string   $items Existing menu items HTML.
     * @param stdClass $args  Menu rendering args from wp_nav_menu().
     * @return string
     */
    function hook_menu_item($items, $args){
        if ( $this->should_render_in_menu( $args ) ) {
            $items .= $this->content_translator( true, true );
        }
        return $items;
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

        $updated_content = preg_replace( '/<\/ul>/', $menu_markup . '</ul>', $block_content, 1 ) ?: $block_content;
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
        if ( $wpat_default_location ) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Safe generated markup from plugin templates.
            echo $this->content_translator(true);
        }
    }

    function auto_translate_button_function ($atts = [], $content = null )
    {
        return $this->content_translator(false);
    }

    private function content_translator($default_location,$menu = false, $menu_item_classes = '')
    {
        ob_start();
        $wpat_widget_type           = get_option('wpat_widget_type');
        $wpat_min_style             = get_option('wpat_min_style');
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
        $wpat_base_language         = get_option('wpat_base_language');
        $wpat_auto_detect           = get_option('wpat_auto_detect');
        $wpat_menu_item_classes     = $menu_item_classes;

        $included_languages     = $this->get_included_languages();
        $wpat_languages_data    = Auto_Translate_Languages::get_languages_data(array_keys($included_languages));

        if(!$menu){
            include 'partials/auto-translate-public-display.php';
        }else{
            include 'partials/auto-translate-public-display-in-menu.php';
        }
        
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
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
