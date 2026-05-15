<?php

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

$GLOBALS['wpat_test_options'] = array();
$GLOBALS['wpat_test_filters'] = array();
$GLOBALS['wpat_test_actions'] = array();
$GLOBALS['wpat_test_shortcodes'] = array();

if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $value ) {
		return is_scalar( $value ) ? trim( (string) $value ) : '';
	}
}

if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		$GLOBALS['wpat_test_filters'][] = compact( 'hook', 'callback', 'priority', 'accepted_args' );
	}
}

if ( ! function_exists( 'add_action' ) ) {
	function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		$GLOBALS['wpat_test_actions'][] = compact( 'hook', 'callback', 'priority', 'accepted_args' );
	}
}

if ( ! function_exists( 'add_shortcode' ) ) {
	function add_shortcode( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		$GLOBALS['wpat_test_shortcodes'][] = compact( 'hook', 'callback', 'priority', 'accepted_args' );
	}
}

if ( ! function_exists( 'add_option' ) ) {
	function add_option( $name, $value ) {
		if ( ! array_key_exists( $name, $GLOBALS['wpat_test_options'] ) ) {
			$GLOBALS['wpat_test_options'][ $name ] = $value;
		}
	}
}

if ( ! function_exists( 'update_option' ) ) {
	function update_option( $name, $value ) {
		$GLOBALS['wpat_test_options'][ $name ] = $value;
	}
}

if ( ! function_exists( 'get_option' ) ) {
	function get_option( $name, $default = false ) {
		return array_key_exists( $name, $GLOBALS['wpat_test_options'] ) ? $GLOBALS['wpat_test_options'][ $name ] : $default;
	}
}

if ( ! function_exists( 'get_bloginfo' ) ) {
	function get_bloginfo( $field ) {
		if ( 'language' === $field ) {
			return 'en-US';
		}
		return '';
	}
}

if ( ! function_exists( '__' ) ) {
	function __( $text ) {
		return $text;
	}
}

if ( ! function_exists( 'wp_unslash' ) ) {
	function wp_unslash( $value ) {
		return $value;
	}
}

if ( ! function_exists( 'absint' ) ) {
	function absint( $value ) {
		return abs( (int) $value );
	}
}

/**
 * Reset mutable test globals between test cases.
 *
 * @return void
 */
function wpat_reset_test_state() {
	$GLOBALS['wpat_test_options'] = array();
	$GLOBALS['wpat_test_filters'] = array();
	$GLOBALS['wpat_test_actions'] = array();
	$GLOBALS['wpat_test_shortcodes'] = array();
}
