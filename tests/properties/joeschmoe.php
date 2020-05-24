<?php

/**
 * @coversDefaultClass Image_Tag_JoeSchmoe_Attributes
 *
 * @todo add tests
 */
class Image_Tag_JoeSchmoe_Attribute_Test extends Image_Tag_Attributes_Test {

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

		return $data;
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

}

?>