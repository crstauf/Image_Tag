<?php

/**
 * @coversDefaultClass Image_Tag_Picsum_Attributes
 *
 * @todo add tests
 */
class Image_Tag_Picsum_Attributes_Test extends Image_Tag_Attributes_Test {

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

		$image = Image_Tag::create( 'picsum', array(), array(
			'width' => 400,
			'height' => 300,
			'image_id' => 50,
			'blur' => 5,
			'grayscale' => true,
		) );
		$data['src view'] = array(
			$image->attributes,
			'src',
			'https://picsum.photos/id/50/400/300?blur=5&grayscale=1',
			'view',
		);

		$image = Image_Tag::create( 'picsum', array(), array(
			'width' => 400,
			'seed' => __FUNCTION__,
		) );
		$data['src seed view'] = array(
			$image->attributes,
			'src',
			'https://picsum.photos/seed/' . __FUNCTION__ . '/400',
			'view',
		);

		$image = Image_Tag::create( 'picsum', array(), array(
			'width' => 200,
			'random' => 5,
		) );
		$data['src random view'] = array(
			$image->attributes,
			'src',
			'https://picsum.photos/200?random=5',
			'view',
		);

		return $data;
	}

	/**
	 * @param Image_Tag_Properties_Abstract $instance
	 * @param string|array $get_properties
	 * @param mixed $expected
	 * @param string $context
	 * @see static::test_get()
	 *
	 * @covers ::get()
	 * @covers ::trim()
	 * @covers ::get_properties()
	 * @covers ::get_property()
	 * @covers ::get_src_attribute_for_view()
	 * @covers Image_Tag_Properties_Abstract::_get()
	 * @group instance
	 * @group get
	 *
	 * @dataProvider data_get
	 */
	function test_get( Image_Tag_Properties_Abstract $instance, $get_properties, $expected, $context = 'edit' ) {
		parent::test_get( $instance, $get_properties, $expected, $context );
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