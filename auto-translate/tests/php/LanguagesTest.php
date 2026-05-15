<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../auto-translate/includes/class-auto-translate-config.php';
require_once __DIR__ . '/../../auto-translate/includes/class-auto-translate-languages.php';

final class LanguagesTest extends TestCase {

	protected function setUp(): void {
		$GLOBALS['wpat_languages_countries'] = array(
			'en' => array(
				'lang_name'  => 'English',
				'lang_code'  => 'en',
				'countries'  => array(
					array(
						'country_code' => 'us',
						'country_name' => 'United States',
					),
				),
			),
			'es' => array(
				'lang_name'  => 'Spanish',
				'lang_code'  => 'es',
				'countries'  => array(
					array(
						'country_code' => 'es',
						'country_name' => 'Spain',
					),
				),
			),
		);
	}

	public function test_get_languages_data_includes_supported_language(): void {
		$valid = Auto_Translate_Languages::get_languages_data( array( 'es' ) );

		$this->assertSame( 'Spanish', $valid['es']['lang_name'] );
		$this->assertSame( 'es', $valid['es']['country_code'] );
	}

	public function test_get_languages_data_falls_back_for_unknown_language(): void {
		$invalid = Auto_Translate_Languages::get_languages_data( array( 'zz' ) );

		$this->assertSame( 'English', $invalid['zz']['lang_name'] );
		$this->assertContains( $invalid['zz']['country_code'], array( 'us', 'xx' ) );
	}
}
