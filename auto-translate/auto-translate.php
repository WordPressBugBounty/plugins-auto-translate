<?php

/**
 *
 * @link              https://pampa.dev
 * @since             1.0.0
 * @package           Auto_Translate
 *
 * @wordpress-plugin
 * Plugin Name:       Automatic Translator with Google Translate
 * Description:       Automatically translate you WordPress site. Easily. This is the easiest plugin to auto translate your website for Free, go multilingual with Automatic Translator with Google. English, Spanish, Chinese and 100+
 * Version:           1.6.0
 * Author:            Pampa Dev
 * Author URI:        https://pampa.dev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       auto-translate
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'AUTO_TRANSLATE_VERSION', '1.6.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-auto-translate-activator.php
 */
function wpat_activate_auto_translate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-auto-translate-activator.php';
	Auto_Translate_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-auto-translate-deactivator.php
 */
function wpat_deactivate_auto_translate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-auto-translate-deactivator.php';
	Auto_Translate_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'wpat_activate_auto_translate' );
register_deactivation_hook( __FILE__, 'wpat_deactivate_auto_translate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-auto-translate.php';

require plugin_dir_path( __FILE__ ) . 'includes/class-auto-translate-languages.php';

/**
 * Load language/country config after init so translation functions are not called too early.
 *
 * @since    1.6.0
 */
function wpat_auto_translate_load_config() {
	static $config_loaded = false;
	global $wpat_supported_languages;
	global $wpat_widget_types;
	global $wpat_languages_countries;

	if ( $config_loaded ) {
		return;
	}

	require plugin_dir_path( __FILE__ ) . 'includes/config.php';
	$config_loaded = true;
}

add_action( 'init', 'wpat_auto_translate_load_config', 0 );


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function wpat_run_auto_translate() {

	$plugin = new Auto_Translate();
	$plugin->run();

}
wpat_run_auto_translate();
