<?php

require_once 'abstract-properties-tests.php';

/**
 * @coversDefaultClass Image_Tag_Settings
 * @group properties
 * @group settings
 */
class Image_Tag_Settings_Test extends Image_Tag_Properties_Tests {

	protected function class_name() {
		return Image_Tag_Settings::class;
	}

	/**
	 * @group constant
	 * @group defaults
	 */
	function test_defaults_constant() {
		$this->markTestIncomplete();
	}

	function data__construct() {
		$this->markTestIncomplete();
	}

}

?>