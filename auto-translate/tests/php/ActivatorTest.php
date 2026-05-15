<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

if ( ! defined( 'AUTO_TRANSLATE_VERSION' ) ) {
	define( 'AUTO_TRANSLATE_VERSION', '1.6.0-test' );
}

require_once __DIR__ . '/../../auto-translate/includes/class-auto-translate-activator.php';

final class ActivatorTest extends TestCase {

	protected function setUp(): void {
		wpat_reset_test_state();
	}

	public function test_activate_sets_legacy_defaults_for_existing_installation(): void {
		$GLOBALS['wpat_test_options'] = array(
			'wpat_supported_languages'    => array( 'en' ),
			'wpat_auto_translate_version' => '1.3.9',
		);

		Auto_Translate_Activator::activate();

		$this->assertSame( 'classic', get_option( 'wpat_widget_type' ) );
		$this->assertSame( 'disabled', get_option( 'wpat_auto_detect' ) );
		$this->assertSame( '', get_option( 'wpat_custom_css' ) );
		$this->assertArrayHasKey( 'wpat_delete_data_on_uninstall', $GLOBALS['wpat_test_options'] );
	}

	public function test_activate_sets_minimalist_default_for_fresh_installation(): void {
		Auto_Translate_Activator::activate();

		$this->assertSame( 'minimalist', get_option( 'wpat_widget_type' ) );
		$this->assertSame( 'enabled', get_option( 'wpat_auto_detect' ) );
		$this->assertSame( 'en', get_option( 'wpat_base_language' ) );
	}
}
