<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://pampa.dev
 * @since      1.0.0
 *
 * @package    Auto_Translate
 * @subpackage Auto_Translate/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Auto_Translate
 * @subpackage Auto_Translate/includes
 * @author     Pampa Dev <intouch@pampa.dev>
 */
// phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid -- Preserve the historical public class name.
class Auto_Translate_i18n {

	/**
	 * Bundled locale fallback targets by WordPress language prefix.
	 *
	 * @var array<string,string>
	 */
	private const LOCALE_FALLBACKS = array(
		'de' => 'de_DE',
		'es' => 'es_ES',
		'fr' => 'fr_FR',
		'it' => 'it_IT',
		'ja' => 'ja',
		'pt' => 'pt_BR',
	);

	/**
	 * Register locale fallback handling for bundled translation files.
	 *
	 * @since    1.0.0
	 */
	public function register_locale_fallbacks() {
		add_filter( 'load_textdomain_mofile', array( $this, 'resolve_locale_fallback_mofile' ), 10, 2 );
	}

	/**
	 * Resolve bundled locale fallback files for locales WordPress does not find exactly.
	 *
	 * For example, es_AR can reuse the bundled es_ES catalog when no exact es_AR
	 * catalog is available.
	 *
	 * @since    2.0.0
	 * @param string $mofile Translation file WordPress is trying to load.
	 * @param string $domain Text domain being loaded.
	 * @return string
	 */
	public function resolve_locale_fallback_mofile( $mofile, $domain ) {
		if ( 'auto-translate' !== $domain || file_exists( $mofile ) ) {
			return $mofile;
		}

		if ( ! preg_match( '/auto-translate-([A-Za-z]{2,3})(?:[_-][A-Za-z0-9_-]+)?\.mo$/', basename( $mofile ), $matches ) ) {
			return $mofile;
		}

		$language        = strtolower( $matches[1] );
		$fallback_locale = self::LOCALE_FALLBACKS[ $language ] ?? '';

		if ( '' === $fallback_locale ) {
			return $mofile;
		}

		$fallback_mofile = dirname( __DIR__ ) . '/languages/auto-translate-' . $fallback_locale . '.mo';

		return file_exists( $fallback_mofile ) ? $fallback_mofile : $mofile;
	}
}
