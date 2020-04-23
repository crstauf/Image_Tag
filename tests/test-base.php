<?php

require_once '_unit-test-case.php';

/**
 * @group base
 * @group external
 * @covers Image_Tag
 */
class Image_Tag_Base_Test extends Image_Tag_UnitTestCase {

	const SOURCE = 'https://source.unsplash.com/random';

	protected function class_name() {
		return Image_Tag::class;
	}

	protected function create( $attributes = array(), $settings = array(), $source = 'https://source.unsplash.com/random' ) {
		return Image_Tag::create( $source, $attributes, $settings );
	}

	function test_toString() {
		$img = $this->construct( array( 'src' => static::SOURCE ) );
		$this->assertEquals( '<img src="' . esc_attr( esc_url( static::SOURCE ) ) . '" />', $img->__toString() );

		$img = $this->create();
		$this->assertEquals( '<img src="' . esc_attr( esc_url( static::SOURCE ) ) . '" />', $img->__toString() );
	}

	/**
	 * Test type.
	 *
	 * @uses Image_Tag::get_type()
	 * @uses Image_Tag::is_type()
	 * @covers Image_Tag::get_type()
	 * @covers Image_Tag::is_type()
	 */
	function test_type() {
		$type = 'base';
		$img = $this->create();

		$this->assertSame( $type, $img->get_type() );

		$types = array(
			'remote',
			'external',
			$type,
		);

		$this->assertTrue( $img->is_type( $types ) );

		foreach ( $types as $type )
			$this->assertTrue( $img->is_type( $type ) );

		$falseTypes = array(
			'local',
			'theme',
			'internal',
			'wordpress',
			'attachment',
		);

		$this->assertFalse( $img->is_type( $falseTypes ) );

		foreach ( $falseTypes as $type )
			$this->assertFalse( $img->is_type( $type ) );
	}

	/**
	 * Test default attributes.
	 *
	 * @uses Image_Tag::get_attributes()
	 *
	 * @group attributes
	 */
	function test_default_attributes() {
		parent::test_default_attributes();

		$img = $this->construct();
		$this->assertEmpty( $img->get_attributes() );
	}

	/**
	 * Test get settings.
	 *
	 * @uses Image_Tag::get_settings()
	 *
	 * @group settings
	 * @group get-settings
	 * @covers Image_Tag::get_settings()
	 */
	function test_get_settings() {
		$settings = array(
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
	 * Test low-quality image placeholder.
	 *
	 * @uses Image_Tag::lqip()
	 *
	 * @group features
	 * @group feature-lqip
	 * @covers Image_Tag::lqip()
	 */
	function test_lqip() {
		$img = $this->create();
		$this->assertInstanceOf( $this->class_name(), $img->lqip() );
	}

	/**
	 * Test common colors.
	 *
	 * @uses Image_Tag::common_colors()
	 *
	 * @group features
	 * @group feature-colors
	 * @covers Image_Tag::common_colors()
	 */
	function test_common_colors() {
		$img = $this->create();
		$this->assertIsArray( $img->common_colors() );
		$this->assertEmpty( $img->common_colors() );
	}

	/**
	 * Test mode color.
	 *
	 * @uses Image_Tag::mode_color()
	 *
	 * @group features
	 * @group feature-colors
	 * @covers Image_Tag::mode_color()
	 */
	function test_mode_color() {
		$img = $this->create();
		$this->assertEmpty( $img->mode_color() );
	}

}

?>