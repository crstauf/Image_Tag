<?php

/**
 * @coversDefaultClass Image_Tag_JoeSchmoe_Attributes
 *
 * @todo add tests
 */
class Image_Tag_JoeSchmoe_Attributes_Test extends Image_Tag_Attributes_Test {

	protected function class_name() {
		return Image_Tag_JoeSchmoe_Attributes::class;
	}

	/**
	 * @group constant
	 */
	function test_constant_primary_url() {
		$this->assertSame( 'https://joeschmoe.io/api/v1/', constant( $this->class_name() . '::PRIMARY_URL' ) );
	}

	/**
	 * @group constant
	 */
	function test_constant_alt_url() {
		$this->assertSame( 'https://joeschmoe.crstauf.workers.dev/', constant( $this->class_name() . '::ALT_URL' ) );
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
			'src="https://joeschmoe.crstauf.workers.dev/" ' .
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
			'src' => 'https://joeschmoe.crstauf.workers.dev/',
			'sizes' => '100vw',
			'class' => 'foo bar',
		);

		$image_tag = Image_Tag::create( 'joeschmoe', array(), array(
			'source' => 'primary',
		) );
		$data['primary source'] = array(
			$image_tag->attributes,
			'src',
			'https://joeschmoe.io/api/v1/',
			'view',
		);

		$gender = array_rand( array( 'male' => 1, 'female' => 1 ) );
		$image_tag = Image_Tag::create( 'joeschmoe', array(), array(
			'gender' => $gender,
		) );
		$data['gender'] = array(
			$image_tag->attributes,
			'src',
			'https://joeschmoe.crstauf.workers.dev/' . $gender . '/',
			'view',
		);

		$image_tag = Image_Tag::create( 'joeschmoe', array(), array(
			'seed' => __FUNCTION__,
		) );
		$data['seed'] = array(
			$image_tag->attributes,
			'src',
			'https://joeschmoe.crstauf.workers.dev/' . __FUNCTION__ . '/',
			'view',
		);

		$image_tag = Image_Tag::create( 'joeschmoe', array( 'src' => 'https://joeschmoe.io/joe' ) );
		$data['preset'] = array(
			$image_tag->attributes,
			'src',
			'https://joeschmoe.io/joe',
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
	 * @covers ::get_class_attribute_for_view()
	 * @covers ::get_style_attribute_for_view()
	 * @covers ::get_array_attribute_for_view()
	 * @covers Image_Tag_Properties_Abstract::_get()
	 * @covers ::get_url()
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
 * @coversDefaultClass Image_Tag_JoeSchmoe_Settings
 *
 * @todo add tests
 */
class Image_Tag_JoeSchmoe_Settings_Test extends Image_Tag_Settings_Test {

	const DEFAULTS = array(
		'before_output' => array(),
		'after_output' => array(),
		'source' => 'alt',
		'gender' => null,
		'seed' => null,
	);

	protected function class_name() {
		return Image_Tag_JoeSchmoe_Settings::class;
	}

	/**
	 * Data provider for Image_Tag_Properties_Base::test_set().
	 *
	 * @see Image_Tag_Properties_Base::test_set()
	 * @uses Image_Tag_Settings_Test::data_set()
	 * @return array[]
	 *
	 * @todo add test for lgbtq
	 */
	function data_set() {
		$data = parent::data_set();

		$data['gender'] = array(
			$this->new_instance(),
			'gender',
			'male',
		);

		return $data;
	}

	/**
	 * @param Image_Tag_Properties_Abstract $instance
	 * @param string|array $set_properties
	 * @param mixed $value
	 * @param mixed $expected
	 *
	 * @covers ::set()
	 * @covers ::set_property()
	 * @covers ::set_properties()
	 * @covers ::_set()
	 * @covers ::set_gender_setting()
	 * @group instance
	 * @group set
	 *
	 * @dataProvider data_set
	 */
	function test_set( Image_Tag_Properties_Abstract $instance, $set_properties, $value = null, $expected = null ) {
		parent::test_set( $instance, $set_properties, $value, $expected );
	}

}

?>