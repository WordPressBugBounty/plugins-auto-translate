<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../auto-translate/includes/class-auto-translate-config.php';

final class ConfigTest extends TestCase {

	protected function setUp(): void {
		$GLOBALS['wpat_supported_languages'] = array();
		$GLOBALS['wpat_widget_types'] = array();
		$GLOBALS['wpat_languages_countries'] = array();
	}

	public function test_supported_languages_returns_array_or_empty(): void {
		$GLOBALS['wpat_supported_languages'] = array( 'en' => 'English' );
		$this->assertSame( array( 'en' => 'English' ), Auto_Translate_Config::get_supported_languages() );

		$GLOBALS['wpat_supported_languages'] = 'invalid';
		$this->assertSame( array(), Auto_Translate_Config::get_supported_languages() );
	}

	public function test_widget_types_returns_array_or_empty(): void {
		$GLOBALS['wpat_widget_types'] = array( 'classic' => 'Classic' );
		$this->assertSame( array( 'classic' => 'Classic' ), Auto_Translate_Config::get_widget_types() );

		$GLOBALS['wpat_widget_types'] = null;
		$this->assertSame( array(), Auto_Translate_Config::get_widget_types() );
	}

	public function test_languages_countries_returns_array_or_empty(): void {
		$GLOBALS['wpat_languages_countries'] = array(
			'en' => array(
				'lang_name' => 'English',
			),
		);
		$this->assertSame( $GLOBALS['wpat_languages_countries'], Auto_Translate_Config::get_languages_countries() );

		$GLOBALS['wpat_languages_countries'] = false;
		$this->assertSame( array(), Auto_Translate_Config::get_languages_countries() );
	}

	public function test_normalize_lang_code_sanitizes_text(): void {
		$this->assertSame( 'en-US', Auto_Translate_Config::normalize_lang_code( ' en-US ' ) );
	}
}
