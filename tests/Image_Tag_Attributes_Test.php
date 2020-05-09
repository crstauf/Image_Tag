<?php

require_once 'Image_Tag_Properties_Test.php';

/**
 * @coversDefaultClass Image_Tag_Attributes
 * @group properties
 * @group attributes
 */
class Image_Tag_Attributes_Test extends Image_Tag_Properties_Test {

	/**
	 * @group constant
	 * @group defaults
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
		), Image_Tag_Attributes::DEFAULTS );
	}

}

?>