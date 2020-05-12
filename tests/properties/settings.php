<?php

require_once 'abstract-properties-tests.php';

/**
 * @coversDefaultClass Image_Tag_Settings
 * @group settings
 */
class Image_Tag_Settings_Test extends Image_Tag_Properties_Tests {

	const DEFAULTS = array(
		'before_output' => array(),
		'after_output' => array(),
	);

	protected function class_name() {
		return Image_Tag_Settings::class;
	}

	/**
	 * Test Image_Tag_Settings::NAME constant value.
	 *
	 * @group constant
	 */
	function test_name_constant() {
		$this->assertSame( 'setting', constant( $this->class_name() . '::NAME' ) );
	}

}

?>