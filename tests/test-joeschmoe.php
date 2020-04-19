<?php

require_once '_unit-test-case.php';

/**
 * @group __placeholder
 * @group joeschmoe
 */
class Image_Tag_JoeSchmoe_Test extends Image_Tag_UnitTestCase {

	protected function class_name() {
		return 'Image_Tag_JoeSchmoe';
	}

	protected function create( $attributes = array(), $settings = array(), $source = 'joeschmoe' ) {
		return Image_Tag::create( $source, $attributes, $settings );
	}

	/**
	 * Test base URL.
	 */
	function test_base_url() {
		$this->assertSame( 'https://joeschmoe.io/api/v1/', constant( $this->class_name() . '::BASE_URL' ) );
	}

	/**
	 * Test default settings.
	 *
	 * @uses Image_Tag_UnitTestCase::test_default_settings()
	 * @uses Image_Tag::get_setting()
	 */
	function test_default_settings() {
		parent::test_default_settings();

		$img = $this->construct();
		$this->assertNull( $img->get_setting( 'gender' ) );
		$this->assertNull( $img->get_setting( 'seed' ) );
	}

	/**
	 * Test to string.
	 *
	 * @uses Image_Tag::set_setting()
	 * @covers Image_Tag_JoeSchmoe::__tostring()
	 */
	function test_toString() {
		$src = Image_Tag_JoeSchmoe::BASE_URL;

		$img = $this->create();
		$this->assertSame( '<img src="' . esc_attr( esc_url( $src . 'random/' ) ) . '" />', $img->__toString() );

		$genders = array( 'male', 'female' );
		$gender = $genders[ array_rand( $genders ) ];
		$img->set_setting( 'gender', $gender );
		$this->assertSame( '<img src="' . esc_attr( esc_url( $src . $gender . '/random/' ) ) . '" />', $img->__toString() );

		$src .= $gender . '/';
		$seed = uniqid( __FUNCTION__ );
		$img->set_setting( 'seed', $seed );
		$this->assertSame( '<img src="' . esc_attr( esc_url( $src . urlencode( $seed ) ) ) . '/" />', $img->__toString() );
	}

	/**
	 * Test type.
	 *
	 * @uses Image_Tag_JoeSchmoe::get_type()
	 * @uses Image_Tag_JoeSchmoe::is_type()
	 * @covers Image_Tag_JoeSchmoe::get_type()
	 * @covers Image_Tag_JoeSchmoe::is_type()
	 */
	function test_type() {
		$type = 'joeschmoe';
		$img = $this->create( $type );

		$this->assertSame( $type, $img->get_type() );

		$types = array(
			'remote',
			'external',
			'__placeholder',
			'avatar',
			'person',
			'profile',
			'joe schmoe',
			$type,
		);

		foreach ( $types as $type )
			$this->assertTrue( $img->is_type( $type ) );
	}

	/**
	 * Test valid.
	 *
	 * @uses Image_Tag_JoeSchmoe::is_valid()
	 * @covers Image_Tag_JoeSchmoe::is_valid()
	 * @covers Image_Tag_JoeSchmoe::__toString()
	 * @covers Image_Tag_JoeSchmoe::check_valid()
	 */
	function test_valid() {
		$img = $this->create();
		$this->assertTrue( $img->is_valid() );

		$img = $this->create();
		$img->set_attribute( 'src', null );
		$this->assertTrue( $img->is_valid() );

		$img->__toString();
		$this->assertNull( null );
	}

	/**
	 * Test get settings.
	 *
	 * @covers Image_Tag_JoeSchmoe::get_settings()
	 */
	function test_get_settings() {
		$settings = array(
			'gender' => null,
			'seed' => null,
			'before_output' => null,
			'after_output' => null,
			'sizes' => array(),
			'foo' => 'foobar',
			'array' => array( 'foobar' ),
			'int' => mt_rand( 1, 999 ),
		);

		$img = $this->create( null, $settings );

		$this->assertSame( $settings, $img->get_settings() );

		$settings['int'] = ( string ) $settings['int'];
		$this->assertNotSame( $settings, $img->get_settings() );
	}

	/**
	 * Test getting width.
	 *
	 * If "width" attribute not set, returns SVG's "viewbox" width (last check: 125).
	 *
	 * @uses Image_Tag_JoeSchmoe::get_width()
	 * @uses Image_Tag_JoeSchmoe::set_attribute()
	 * @covers Image_Tag_JoeSchmoe::get_width()
	 */
	function test_get_width() {
		$img = $this->create();
		$this->assertSame( 125, $img->get_width() );

		$img->set_attribute( 'width', 500 );
		$this->assertSame( 500, $img->get_width() );
	}

	/**
	 * Test getting height.
	 *
	 * If "height" attribute not set, returns SVG's "viewbox" height (last check: 125).
	 *
	 * @uses Image_Tag_JoeSchmoe::get_height()
	 * @uses Image_Tag_JoeSchmoe::set_attribute()
	 * @covers Image_Tag_JoeSchmoe::get_height()
	 */
	function test_get_height() {
		$img = $this->create();
		$this->assertSame( 125, $img->get_height() );

		$img->set_attribute( 'height', 500 );
		$this->assertSame( 500, $img->get_height() );
	}

}

?>