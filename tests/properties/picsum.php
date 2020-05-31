<?php

/**
 * @coversDefaultClass Image_Tag_Picsum_Attributes
 *
 * @todo add tests
 */
class Image_Tag_Picsum_Attribute_Test extends Image_Tag_Attributes_Test {

	protected function class_name() {
		return Image_Tag_Picsum_Attributes::class;
	}

	function test_base_url() {
		$this->assertSame( 'https://picsum.photos/', constant( $this->class_name() . '::BASE_URL' ) );
	}

	/**
	 * Test Image_Tag_Attributes::__toString().
	 *
	 * @covers ::__toString()
	 * @group instance
	 * @group magic
	 * @group output
	 */
	function test__toString() {
		$attributes = array(
			'class' => array( 'foo', 'bar' ),
			'id' => __FUNCTION__,
			'sizes' => '( max-width: 800px ) 50vw, 100vw',
			'data-preloaded' => 1, // test attribute not in DEFAULTS
		);

		$defaults = array(
			'width' => 1600,
			'height' => 900,
		);

		$instance = $this->new_instance( $attributes, $defaults );

		$expected = 'id="' . esc_attr( __FUNCTION__ ) . '" ' .
			'src="https://picsum.photos/" ' .
			'sizes="( max-width: 800px ) 50vw, 100vw" ' .
			'class="foo bar" ' .
			'width="1600" ' .
			'height="900" ' .
			'alt="" ' .
			'data-preloaded="1"';

		$this->assertSame( $expected, $instance->__toString() );
	}

	/**
	 * Data provider for Image_Tag_Attributes_Test::test_get().
	 *
	 * Add attribute specific tests.
	 *
	 * @see Image_Tag_Properties_Test::test_get()
	 * @uses Image_Tag_Attributes_Test::data_get()
	 * @return array[]
	 *
	 * @todo add test data
	 */
	function data_get() {
		$data = parent::data_get();

		$data['null view'][2] = array(
			'id' => __FUNCTION__,
			'alt' => '',
			'src' => 'https://picsum.photos/',
			'sizes' => '100vw',
			'class' => 'foo bar',
		);

		$image_id = mt_rand( 1, 100 );
		$image_tag = Image_Tag::create( 'picsum', array(), array(
			'image_id' => $image_id,
		) );
		$data['image id'] = array(
			$image_tag->attributes,
			'src',
			'https://picsum.photos/id/' . $image_id,
			'view',
		);

		return $data;
	}

}

/**
 * @coversDefaultClass Image_Tag_Picsum_Settings
 *
 * @todo add tests
 */
class Image_Tag_Picsum_Settings_Test extends Image_Tag_Settings_Test {

	const DEFAULTS = array(
		'before_output' => array(),
		'after_output' => array(),
		'blur' => false,
		'seed' => null,
		'width' => null,
		'height' => null,
		'random' => false,
		'image_id' => null,
		'grayscale' => false,
	);

	protected function class_name() {
		return Image_Tag_Picsum_Settings::class;
	}

}

?>