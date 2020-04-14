<?php

abstract class Image_Tag_UnitTestCase extends WP_UnitTestCase {

	abstract protected function class_name();

	/**
	 * Test Image_Tag implements ArrayAccess.
	 */
	function test_implements() {
		$this->assertContains( 'ArrayAccess', class_implements( $this->class_name() ) );
	}

	/**
	 * Test base64 encoded blank image.
	 */
	function test_blank() {
		$this->assertEquals( 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7', constant( $this->class_name() . '::BLANK' ) );
	}

	/**
	 * Test default attributes.
	 *
	 * @uses Image_Tag::__construct()
	 * @uses Image_Tag::get_attributes()
	 */
	function test_default_attributes() {
		$classname = $this->class_name();
		$img = new $classname( array() );

		$attributes = array();

		$assertNull = array(
			'id',
			'alt',
			'src',
			'width',
			'height',
		);

		$assertIsArray = array(
			'class',
			'style',
			'sizes',
			'srcset',
		);

		foreach ( $assertNull as $attribute ) {
			$attributes[$attribute] = null;
			$this->assertNull( $img[$attribute], sprintf( 'Failed asserting that "%s" attribute is null.', $attribute ) );
			$this->assertNull( $img->$attribute, sprintf( 'Failed asserting that "%s" attribute is null.', $attribute ) );
		}

		foreach ( $assertIsArray as $attribute ) {
			$attributes[$attribute] = array();
			$this->assertIsArray( $img[$attribute], sprintf( 'Failed asserting that "%s" attribute is of type "array".', $attribute ) );
			$this->assertIsArray( $img->$attribute, sprintf( 'Failed asserting that "%s" attribute is of type "array".', $attribute ) );
			$this->assertEmpty(   $img[$attribute], sprintf( 'Failed asserting that "%s" attribute is empty.', $attribute ) );
			$this->assertEmpty(   $img->$attribute, sprintf( 'Failed asserting that "%s" attribute is empty.', $attribute ) );
		}

		$this->assertEmpty( $img->get_attributes() );
	}

	/**
	 * Test default settings.
	 *
	 * @uses Image_Tag::__construct()
	 * @uses Image_Tag::get_settings()
	 * @uses Image_Tag::get_setting()
	 */
	function test_default_settings() {
		$classname = $this->class_name();
		$img = new $classname( array() );

		$this->assertNotEmpty( $img->get_settings() );
		$this->assertNull(     $img->get_setting( 'before_output' ) );
		$this->assertNull(     $img->get_setting( 'after_output' ) );
		$this->assertIsArray(  $img->get_setting( 'sizes' ) );
	}

	/**
	 * Test construct.
	 *
	 * @uses Image_Tag::__construct()
	 * @uses Image_Tag::get_attribute()
	 * @uses Image_Tag::get_setting()
	 * @covers Image_Tag::__construct()
	 */
	function test_construct() {
		$attributes = array(
			'id' => uniqid( __FUNCTION__ ),
			'alt' => uniqid( __FUNCTION__ ),
			'width'  => mt_rand( 5, 9995 ),
			'height' => mt_rand( 5, 9995 ),
		);

		$settings = array(
			'before_output' => uniqid( __FUNCTION__ ),
			 'after_output' => uniqid( __FUNCTION__ ),
		);

		$classname = $this->class_name();
		$img = new $classname( $attributes, $settings );

		foreach ( $attributes as $attribute => $value ) {
			$this->assertEquals( $value, $img[$attribute], sprintf( 'Failed asserting that attribute "%s" matches expected "%s".', $attribute, $value ) );
			$this->assertEquals( $value, $img->$attribute, sprintf( 'Failed asserting that attribute "%s" matches expected "%s".', $attribute, $value ) );
			$this->assertEquals( $value, $img->get_attribute( $attribute ), sprintf( 'Failed asserting that attribute "%s" matches expected "%s".', $attribute, $value ) );
		}

		foreach ( $settings as $setting => $value )
			$this->assertEquals( $value, $img->get_setting( $setting ), sprintf( 'Failed asserting that setting "%s" matches expected "%s".', $setting, $value ) );

	}

	/**
	 * Test getter.
	 *
	 * @uses Image_Tag::__construct()
	 * @uses Image_Tag::__get()
	 * @covers Image_Tag::__get()
	 */
	function test_get() {
		$classname = $this->class_name();
		$img = new $classname( array() );

		$assertNull = array(
			'id',
			'alt',
			'src',
			'width',
			'height',
		);

		$assertIsArray = array(
			'class',
			'style',
			'sizes',
			'srcset',
		);

		foreach ( $assertNull as $attribute )
			$this->assertNull( $img->$attribute );

		foreach ( $assertIsArray as $attribute )
			$this->assertIsArray( $img->$attribute );
	}

	/**
	 * Test string.
	 *
	 * @uses Image_Tag::__construct()
	 * @uses Image_Tag::__toString()
	 * @covers Image_Tag::__toString()
	 */
	function test_toString() {
		$src = 'https://source.unsplash.com/random';

		$classname = $this->class_name();
		$img = new $classname( array(
			'src' => $src,
		) );

		$this->assertEquals( '<img src="' . esc_attr( esc_url( $src ) ) . '" />', $img->__toString() );
	}

	/**
	 * Test valid.
	 *
	 * @uses Image_Tag::__construct()
	 * @uses Image_Tag::is_valid()
	 * @covers Image_Tag::is_valid()
	 */
	function test_valid() {
		$src = 'https://source.unsplash.com/random';

		$classname = $this->class_name();
		$img = new $classname( array() );

		$this->assertFalse( $img->is_valid() );

		$img->set_attribute( 'src', $src );
		$this->assertTrue( $img->is_valid() );
	}

	/**
	 * Test setting attributes.
	 *
	 * @uses Image_Tag::create()
	 * @uses Image_Tag::set_attributes()
	 * @uses Image_Tag::_get_attribute()
	 * @covers Image_Tag::set_attributes()
	 */
	function test_set_attributes() {
		$source = 'https://source.unsplash.com/random';
		$attributes = array(
			'id' => __FUNCTION__,
			'width'  => mt_rand( 5, 9995 ),
			'height' => mt_rand( 5, 9995 ),
			'class' => array( __FUNCTION__ ),
		);

		$classname = $this->class_name();
		$img = Image_Tag::create( $source, $attributes );

		$attributes['src'] = $source;

		foreach ( array_keys( $img->get_attributes() ) as $attribute ) {
			$this->assertEquals( $attributes[$attribute], $img[$attribute] );
			$this->assertEquals( $attributes[$attribute], $img->$attribute );
			$this->assertEquals( $attributes[$attribute], $img->_get_attribute( $attribute ) );
		}
	}

	/**
	* Test set attribute.
	*
	* @uses Image_Tag::create()
	* @uses Image_Tag::set_attribute()
	* @uses Image_Tag::get_attribute()
	* @covers Image_Tag::set_attribute()
	*/
	function test_set_attribute() {
		$source = 'https://source.unsplash.com/random';

		$classname = $this->class_name();
		$img = Image_Tag::create( $source );

		$this->assertEmpty( $img->id );
		$this->assertEmpty( $img['id'] );
		$this->assertEmpty( $img->get_attribute( 'id' ) );

		$img->set_attribute( 'id' , __FUNCTION__ );
		$this->assertEquals( __FUNCTION__, $img->id );
		$this->assertEquals( __FUNCTION__, $img['id'] );
		$this->assertEquals( __FUNCTION__, $img->get_attribute( 'id' ) );
	}

	/**
	 * Test get attributes.
	 *
	 * @uses Image_Tag::create()
	 * @uses Image_Tag::get_attributes()
	 * @uses Image_Tag::_get_attribute()
	 * @covers Image_Tag::get_attributes()
	 */
	function test_get_attributes() {
		$source = 'https://source.unsplash.com/random';
		$attributes = array(
			'id' => __FUNCTION__,
			'width'  => mt_rand( 5, 9995 ),
			'height' => mt_rand( 5, 9995 ),
			'class' => array( __FUNCTION__ ),
		);

		$classname = $this->class_name();
		$img = Image_Tag::create( $source, $attributes );

		$attributes['src'] = $source;
		$attributes['class'] = implode( ' ', $attributes['class'] );

		$this->assertEquals( $attributes, $img->get_attributes() );
	}

	/**
	 * Test get raw attributes.
	 *
	 * @uses Image_Tag::create()
	 * @uses Image_Tag::_get_attributes()
	 * @covers Image_Tag::_get_attributes()
	 */
	function test_get_raw_attributes() {
		$source = 'https://source.unsplash.com/random';

		$attributes = wp_parse_args( array(
			'id' => __FUNCTION__,
			'width'  => mt_rand( 5, 9995 ),
			'height' => mt_rand( 5, 9995 ),
			'class' => array( __FUNCTION__ ),
		), array(
			'id' => null,
			'alt' => null,
			'src' => null,
			'width' => null,
			'height' => null,
			'class' => array(),
			'style' => array(),
			'sizes' => array(),
			'srcset' => array(),
		) );

		$classname = $this->class_name();
		$img = Image_Tag::create( $source, $attributes );

		$attributes['src'] = $source;

		$this->assertEquals( $attributes, $img->_get_attributes() );
	}

}