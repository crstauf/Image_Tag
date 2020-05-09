<?php

require_once 'abstract-properties-tests.php';

/**
 * @coversDefaultClass Image_Tag_Attributes
 * @group attributes
 */
class Image_Tag_Attributes_Test extends Image_Tag_Properties_Tests {

	protected function class_name() {
		return Image_Tag_Attributes::class;
	}

	/**
	 * Test Image_Tag_Attributes::NAME constant value.
	 *
	 * @group constant
	 */
	function test_name_constant() {
		$this->assertSame( 'attribute', constant( $this->class_name() . '::NAME' ) );
	}

	/**
	 * Test Image_Tag_Attribute::DEFAULTS constant.
	 *
	 * @group constant
	 */
	function test_defaults_constant() {
		$this->assertSame( array(
			'id' => null,
			'alt' => null,
			'src' => null,
			'title' => null,
			'width' => null,
			'height' => null,
			'data-src' => null,
			'data-srcset' => array(),
			'data-sizes' => array(),
			'srcset' => array(),
			'style' => array(),
			'sizes' => array(),
			'class' => array(),
		), constant( $this->class_name() . '::DEFAULTS' ) );
	}

	function data__construct() {
		$this->markTestIncomplete();
	}

}

?>