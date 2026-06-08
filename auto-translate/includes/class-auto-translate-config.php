<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Auto_Translate_Config {

	public static function get_supported_languages() {
		global $wpat_supported_languages;
		return is_array( $wpat_supported_languages ) ? $wpat_supported_languages : array();
	}

	public static function get_widget_types() {
		global $wpat_widget_types;
		return is_array( $wpat_widget_types ) ? $wpat_widget_types : array();
	}

	public static function normalize_widget_type( $widget_type ) {
		return 'minimalist';
	}

	public static function get_languages_countries() {
		global $wpat_languages_countries;
		return is_array( $wpat_languages_countries ) ? $wpat_languages_countries : array();
	}

	public static function normalize_lang_code( $lang_code ) {
		return sanitize_text_field( (string) $lang_code );
	}

	public static function get_resolved_base_language() {
		$supported_languages = self::get_supported_languages();
		$stored_language     = self::normalize_lang_code( (string) get_option( 'wpat_base_language', '' ) );

		if ( '' !== $stored_language && isset( $supported_languages[ $stored_language ] ) ) {
			return $stored_language;
		}

		$wp_language = self::get_wordpress_locale_language();
		if ( '' !== $wp_language && isset( $supported_languages[ $wp_language ] ) ) {
			return $wp_language;
		}

		if ( isset( $supported_languages['en'] ) ) {
			return 'en';
		}

		if ( ! empty( $supported_languages ) ) {
			return array_key_first( $supported_languages );
		}

		return 'en';
	}

	public static function get_wordpress_locale_language() {
		$locale = '';

		if ( function_exists( 'get_locale' ) ) {
			$locale = (string) get_locale();
		}

		if ( '' === $locale && function_exists( 'get_bloginfo' ) ) {
			$locale = (string) get_bloginfo( 'language' );
		}

		return self::normalize_locale_language( $locale );
	}

	public static function normalize_locale_language( $locale ) {
		$locale = self::normalize_lang_code( str_replace( '_', '-', (string) $locale ) );

		if ( '' === $locale ) {
			return '';
		}

		$parts   = explode( '-', $locale );
		$primary = strtolower( (string) $parts[0] );
		$region  = isset( $parts[1] ) ? strtoupper( (string) $parts[1] ) : '';

		if ( 'zh' === $primary ) {
			if ( in_array( $region, array( 'TW', 'HK', 'MO' ), true ) ) {
				return 'zh-TW';
			}

			return 'zh-CN';
		}

		if ( 'he' === $primary ) {
			return 'iw';
		}

		if ( 'jv' === $primary ) {
			return 'jw';
		}

		return $primary;
	}
}
