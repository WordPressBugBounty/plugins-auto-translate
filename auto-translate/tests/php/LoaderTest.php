<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../auto-translate/includes/class-auto-translate-loader.php';

final class LoaderTest extends TestCase {

	protected function setUp(): void {
		wpat_reset_test_state();
	}

	public function test_run_registers_filter_action_and_shortcode(): void {
		$loader = new Auto_Translate_Loader();
		$component = new stdClass();

		$loader->add_filter( 'the_content', $component, 'filter_cb', 12, 1 );
		$loader->add_action( 'init', $component, 'action_cb', 9, 1 );
		$loader->add_shortcode( 'auto_translate_button', $component, 'shortcode_cb' );
		$loader->run();

		$this->assertCount( 1, $GLOBALS['wpat_test_filters'] );
		$this->assertCount( 1, $GLOBALS['wpat_test_actions'] );
		$this->assertCount( 1, $GLOBALS['wpat_test_shortcodes'] );
		$this->assertSame( 'auto_translate_button', $GLOBALS['wpat_test_shortcodes'][0]['hook'] );
	}
}
