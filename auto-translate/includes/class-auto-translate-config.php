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
}
