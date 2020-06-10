<?php

/**
 * @coversDefaultClass Image_Tag_Placeholder_Attributes
 *
 * @todo add tests
 */
class Image_Tag_Placeholder_Attributes_Test extends Image_Tag_Attributes_Test {

	protected function class_name() {
		return Image_Tag_Placeholder_Attributes::class;
	}

	/**
	 * @group constant
	 */
	function test_constant_base_url() {
		$this->assertSame( 'https://via.placeholder.com/', constant( $this->class_name() . '::BASE_URL' ) );
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
			'src="https://via.placeholder.com/" ' .
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
	 */
	function data_get() {
		$data = parent::data_get();

		$data['null view'][2] = array(
			'id' => __FUNCTION__,
			'alt' => '',
			'src' => 'https://via.placeholder.com/',
			'sizes' => '100vw',
			'class' => 'foo bar',
		);

		$image = Image_Tag::create( 'placeholder.com', array(), array(
			'width' => 400,
			'height' => 300,
		) );
		$data['src view'] = array(
			$image->attributes,
			'src',
			'https://via.placeholder.com/400x300',
			'view',
		);

		$image = Image_Tag::create( 'placeholder.com', array(), array(
			'width' => 400,
			'height' => 300,
			'text' => __FUNCTION__,
			'text_color' => 'FFF',
			'bg_color' => '000',
		) );
		$data['src all view'] = array(
			$image->attributes,
			'src',
			'https://via.placeholder.com/400x300/000/FFF?text=' . __FUNCTION__,
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
	 * @covers ::get_src_attribute_for_view()
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
 * @coversDefaultClass Image_Tag_Placeholder_Settings
 *
 * @todo add tests
 */
class Image_Tag_Placeholder_Settings_Test extends Image_Tag_Settings_Test {

	const DEFAULTS = array(
		'before_output' => array(),
		'after_output' => array(),
		'width' => null,
		'height' => null,
		'text' => null,
		'text_color' => null,
		'bg_color' => null,
	);

	protected function class_name() {
		return Image_Tag_Placeholder_Settings::class;
	}

}

?>