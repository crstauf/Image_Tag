<?php

require_once 'abstract-properties-tests.php';

/**
 * @coversDefaultClass Image_Tag_Properties
 */
class Image_Tag_Properties_Test extends Image_Tag_Properties_Tests {

	protected function class_name() {
		return Image_Tag_Properties::class;
	}

	/**
	 * Test Image_Tag_Properties::NAME constant value.
	 *
	 * @group constant
	 */
	function test_name_constant() {
		$this->assertSame( 'property', constant( $this->class_name() . '::NAME' ) );
	}

	/**
	 * Test Image_Tag_Properties::DEFAULTS constant.
	 *
	 * @group constant
	 */
	function test_defaults_constant() {
		$this->assertEmpty(   Image_Tag_Properties::DEFAULTS );
		$this->assertIsArray( Image_Tag_Properties::DEFAULTS );
	}

}

?>