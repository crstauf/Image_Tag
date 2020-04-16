<?php

abstract class Image_Tag_UnitTestCase extends WP_UnitTestCase {

	/**
	 * Get name of class to test.
	 * @return string
	 */
	abstract protected function class_name();

	/**
	 * Create Image_Tag object using static method.
	 *
	 * @param null|array $attributes
	 * @param null|array $settings
	 * @param null|string|int $source
	 * @uses Image_Tag::create()
	 * @return Image_Tag
	 */
	abstract protected function create( $attributes = array(), $settings = array(), $source = null );

	/**
	 * Construct Image_Tag object.
	 *
	 * @param null|array $attributes
	 * @param null|array $settings
	 * @uses Image_Tag::__construct()
	 */
	protected function construct( $attributes = array(), $settings = array() ) {
		$classname = $this->class_name();
		return new $classname( $attributes, $settings );
	}

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
	 * @uses Image_Tag::get_attributes()
	 */
	function test_default_attributes() {
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

		$img = $this->construct();

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
	 * @uses Image_Tag::get_settings()
	 * @uses Image_Tag::get_setting()
	 */
	function test_default_settings() {
		$img = $this->construct();

		$this->assertNotEmpty( $img->get_settings() );
		$this->assertNull(     $img->get_setting( 'before_output' ) );
		$this->assertNull(     $img->get_setting( 'after_output' ) );
		$this->assertIsArray(  $img->get_setting( 'sizes' ) );
	}


	/*
	##     ##    ###     ######   ####  ######
	###   ###   ## ##   ##    ##   ##  ##    ##
	#### ####  ##   ##  ##         ##  ##
	## ### ## ##     ## ##   ####  ##  ##
	##     ## ######### ##    ##   ##  ##
	##     ## ##     ## ##    ##   ##  ##    ##
	##     ## ##     ##  ######   ####  ######
	*/

	/**
	 * Test construct.
	 *
	 * @uses Image_Tag::get_attribute()
	 * @uses Image_Tag::get_setting()
	 *
	 * @group magic
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

		$img = $this->construct( $attributes, $settings );

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
	 * @uses Image_Tag::__get()
	 *
	 * @group magic
	 * @covers Image_Tag::__get()
	 */
	function test_get() {
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

		$img = $this->construct();

		foreach ( $assertNull as $attribute )
			$this->assertNull( $img->$attribute );

		foreach ( $assertIsArray as $attribute )
			$this->assertIsArray( $img->$attribute );
	}

	/**
	 * Test returning string.
	 *
	 * @uses Image_Tag::__toString()
	 *
	 * @group magic
	 * @covers Image_Tag::__toString()
	 */
	abstract function test_toString();

	/**
	 * Test valid.
	 *
	 * @uses Image_Tag::is_valid()
	 * @covers Image_Tag::is_valid()
	 * @covers Image_Tag::__toString()
	 * @covers Image_Tag::check_valid()
	 */
	function test_valid() {
		$img = $this->create();
		$this->assertTrue( $img->is_valid() );

		$img = $this->create();
		$img->set_attribute( 'src', null );
		$this->assertFalse( $img->is_valid() );

		$this->expectException( \Exception::class );
		@$img->__toString();
	}


	/*
	 ######  ######## ########       ###    ######## ######## ########  #### ########  ##     ## ######## ########  ######
	##    ## ##          ##         ## ##      ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##    ##
	##       ##          ##        ##   ##     ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##
	 ######  ######      ##       ##     ##    ##       ##    ########   ##  ########  ##     ##    ##    ######    ######
	      ## ##          ##       #########    ##       ##    ##   ##    ##  ##     ## ##     ##    ##    ##             ##
	##    ## ##          ##       ##     ##    ##       ##    ##    ##   ##  ##     ## ##     ##    ##    ##       ##    ##
	 ######  ########    ##       ##     ##    ##       ##    ##     ## #### ########   #######     ##    ########  ######
	*/

	/**
	 * Test setting attributes.
	 *
	 * @group attributes
	 * @group set-attributes
	 * @covers Image_Tag::set_attributes()
	 */
	function test_set_attributes() {
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

		$img = $this->create();
		$img->set_attributes( $attributes );

		foreach ( $img->get_attributes( true ) as $attribute => $value ) {
			$this->assertEquals( $value, $img[$attribute], sprintf( 'Failed asserting that "%s" attribute matches expected value.', $attribute ) );
			$this->assertEquals( $value, $img->$attribute, sprintf( 'Failed asserting that "%s" attribute matches expected value.', $attribute ) );
			$this->assertEquals( $value, $img->get_attribute( $attribute, true ), sprintf( 'Failed asserting that "%s" attribute matches expected value.', $attribute ) );
		}
	}

	/**
	* Test set attribute.
	*
	* @uses Image_Tag::set_attribute()
	* @uses Image_Tag::get_attribute()
	*
	* @group attributes
	* @group set-attributes
	* @covers Image_Tag::set_attribute()
	*/
	function test_set_attribute() {
		$img = $this->create();

		$this->assertEmpty( $img->get_attribute( 'id' ) );

		$img->set_attribute( 'id' , __FUNCTION__ );
		$this->assertEquals( __FUNCTION__, $img->get_attribute( 'id' ) );
	}

	/**
	 * Test set "class" attribute.
	 *
	 * @uses Image_Tag::set_attribute()
	 *
	 * @group attributes
	 * @group set-attributes
	 * @covers Image_Tag::set_class_attribute()
	 */
	function test_set_class_attribute() {
		$img = $this->create();

		$this->assertEmpty( $img->get_attribute( 'class' ) );

		$class = ' foo  bar ';
		$_class = array_filter( array_map( 'trim', explode( ' ', $class ) ) );
		$img->set_attribute( 'class', $class );
		$this->assertEquals( $_class, $img->get_attribute( 'class', true ) );

		$class = array( 'foo ', 'bar' );
		$_class = array_filter( array_map( 'trim', $class ) );
		$img->set_attribute( 'class', $class );
		$this->assertEquals( $_class, $img->get_attribute( 'class', true ) );
	}

	/**
	 * Test set "sizes" attribute.
	 *
	 * @uses Image_Tag::set_attribute()
	 *
	 * @group attributes
	 * @group set-attributes
	 * @covers Image_Tag::set_sizes_attribute()
	 */
	function test_set_sizes_attribute() {
		$img = $this->create();

		$this->assertEmpty( $img->get_attribute( 'sizes' ) );

		$sizes = ' 50w , 100w ';
		$_sizes = array_filter( array_map( 'trim', explode( ',', $sizes ) ) );
		$img->set_attribute( 'sizes', $sizes );
		$this->assertEquals( $_sizes, $img->get_attribute( 'sizes', true ) );

		$sizes = array( ' 50w', '100w ' );
		$_sizes = array_filter( array_map( 'trim', $sizes ) );
		$img->set_attribute( 'sizes', $sizes );
		$this->assertEquals( $_sizes, $img->get_attribute( 'sizes', true ) );
	}

	/**
	 * Test set "srcset" attribute.
	 *
	 * @uses Image_Tag::set_attribute()
	 *
	 * @group attributes
	 * @group set-attributes
	 * @covers Image_Tag::set_srcset_attribute()
	 */
	function test_set_srcset_attribute() {
		$img = $this->create();

		$this->assertEmpty( $img->get_attribute( 'srcset' ) );

		$srcset = ' https://source.unsplash.com/random/500x500 50w,  https://source.unsplash.com/random/1000x1000 100w ';
		$_srcset = array_filter( array_map( 'trim', explode( ',', $srcset ) ) );
		$img->set_attribute( 'srcset', $srcset );
		$this->assertEquals( $_srcset, $img->get_attribute( 'srcset', true ) );

		$srcset = array( ' https://source.unsplash.com/random/500x500 50w ', 'https://source.unsplash.com/random/1000x1000  100w' );
		$_srcset = array_filter( array_map( 'trim', $srcset ) );
		$img->set_attribute( 'srcset', $srcset );
		$this->assertEquals( $_srcset, $img->get_attribute( 'srcset', true ) );
	}

	/**
	 * Test set "style" attribute.
	 *
	 * @uses Image_Tag::set_attribute()
	 *
	 * @group attributes
	 * @group set-attributes
	 * @covers Image_Tag::set_style_attribute()
	 */
	function test_set_style_attribute() {
		$img = $this->create();

		$this->assertEmpty( $img->get_attribute( 'style' ) );

		$style = ' color: #000;  display: none ';
		$_style = array_filter( array_map( 'trim', explode( ';', $style ) ) );
		$img->set_attribute( 'style', $style );
		$this->assertEquals( $_style, $img->get_attribute( 'style', true ) );

		$style = 'color: #000; display: none;';
		$_style = array_filter( array_map( 'trim', explode( ';', $style ) ) );
		$img->set_attribute( 'style', $style );
		$this->assertEquals( $_style, $img->get_attribute( 'style', true ) );

		$style = array( 'color: #000', 'display: none' );
		$_style = array_filter( array_map( 'trim', $style ) );
		$img->set_attribute( 'style', $style );
		$this->assertEquals( $_style, $img->get_attribute( 'style', true ) );

		$style = array( 'color: #000;', 'display: none;' );
		$_style = array_filter( array_map( 'trim', $style ) );
		$img->set_attribute( 'style', $style );
		$this->assertEquals( $_style, $img->get_attribute( 'style', true ) );
	}


	/*
	 ######   ######## ########       ###    ######## ######## ########  #### ########  ##     ## ######## ########  ######
	##    ##  ##          ##         ## ##      ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##    ##
	##        ##          ##        ##   ##     ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##
	##   #### ######      ##       ##     ##    ##       ##    ########   ##  ########  ##     ##    ##    ######    ######
	##    ##  ##          ##       #########    ##       ##    ##   ##    ##  ##     ## ##     ##    ##    ##             ##
	##    ##  ##          ##       ##     ##    ##       ##    ##    ##   ##  ##     ## ##     ##    ##    ##       ##    ##
	 ######   ########    ##       ##     ##    ##       ##    ##     ## #### ########   #######     ##    ########  ######
	*/

	/**
	 * Test get attributes.
	 *
	 * @uses Image_Tag::get_attributes()
	 * @uses Image_Tag::_get_attribute()
	 *
	 * @group attributes
	 * @group get-attributes
	 * @covers Image_Tag::get_attributes()
	 */
	function test_get_attributes() {
		$attributes = array(
			'id' => __FUNCTION__,
			'width'  => mt_rand( 5, 9995 ),
			'height' => mt_rand( 5, 9995 ),
			'class' => array( __FUNCTION__ ),
		);

		$img = @$this->create( $attributes, array(), null );

		$expected_attributes = $attributes;
		$expected_attributes['class'] = implode( ' ', $attributes['class'] );

		$this->assertEquals( $expected_attributes, $img->get_attributes() );

		# Test raw attributes.
		$expected_attributes['class'] = explode( ' ', $expected_attributes['class'] );
		$expected_attributes = wp_parse_args( $expected_attributes, array(
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
		$this->assertEquals( $expected_attributes, $img->get_attributes( true ) );
	}

	/**
	 * Test get attribute.
	 *
	 * @uses Image_Tag::get_attribute()
	 *
	 * @group attributes
	 * @group get-attributes
	 * @covers Image_Tag::get_attribute()
	 */
	function test_get_attribute() {
		$attributes = array(
			'id' => uniqid( __FUNCTION__ ),
			'alt' => uniqid( __FUNCTION__ ),
			'title' => uniqid( __FUNCTION__ ),
			'class' => array( uniqid( __FUNCTION__ ) ),
		);

		$img = $this->create( $attributes );

		foreach ( $attributes as $attribute => $value )
			$this->assertEquals( $value, $img->get_attribute( $attribute, true ) );

		$attributes['class'] = implode( ' ', $attributes['class'] );
		foreach ( $attributes as $attribute => $value )
			$this->assertEquals( $value, $img->get_attribute( $attribute ) );
	}

	/**
	 * Test getting "class" attribute.
	 *
	 * @uses Image_Tag::set_attribute()
	 * @uses Image_Tag::get_attribute()
	 *
	 * @group attributes
	 * @group get-attributes
	 * @covers Image_Tag::get_class_attribute()
	 */
	function test_get_class_attribute() {
		$img = $this->create();

		$classes = array( 'foo', ' bar', 'zulu ', ' foo ' );
		$_classes = array_filter( array_map( 'trim', $classes ) );
		$img->set_attribute( 'class', $classes );
		$this->assertEquals( $_classes, $img->get_attribute( 'class', true ) );
		$this->assertEquals( implode( ' ', array_unique( $_classes ) ), $img->get_attribute( 'class' ) );

		$classes = 'foo  bar  zulu foo ';
		$_classes = array_filter( array_map( 'trim', explode( ' ', trim( $classes ) ) ) );
		$img->set_attribute( 'class', $classes );
		$this->assertEquals( $_classes, $img->get_attribute( 'class', true ) );
		$this->assertEquals( implode( ' ', array_unique( $_classes ) ), $img->get_attribute( 'class' ) );
	}

	/**
	 * Test getting "style" attribute.
	 *
	 * @uses Image_Tag::set_attribute()
	 * @uses Image_Tag::get_attribute()
	 *
	 * @group attributes
	 * @group get-attributes
	 * @covers Image_Tag::get_style_attribute()
	 */
	function test_get_style_attribute() {
		$img = $this->create();

		$style = ' color: #000;  display: none ';
		$_style = array_filter( array_map( 'trim', explode( ';', $style ) ) );
		$img->set_attribute( 'style', $style );
		$this->assertEquals( $_style, $img->get_attribute( 'style', true ) );
		$this->assertEquals( implode( '; ', array_unique( $_style ) ), $img->get_attribute( 'style' ) );

		$style = 'color: #000; display: none;';
		$_style = array_filter( array_map( 'trim', explode( ';', $style ) ) );
		$img->set_attribute( 'style', $style );
		$this->assertEquals( $_style, $img->get_attribute( 'style', true ) );
		$this->assertEquals( implode( '; ', array_unique( $_style ) ), $img->get_attribute( 'style' ) );

		$style = array( ' color: #000', 'display: none ' );
		$_style = array_filter( array_map( 'trim', $style ) );
		$img->set_attribute( 'style', $style );
		$this->assertEquals( $_style, $img->get_attribute( 'style', true ) );
		$this->assertEquals( implode( '; ', array_unique( $_style ) ), $img->get_attribute( 'style' ) );

		$style = array( 'color: #000;', 'display: none;' );
		$_style = array_filter( array_map( 'trim', $style ) );
		$img->set_attribute( 'style', $style );
		$this->assertEquals( $_style, $img->get_attribute( 'style', true ) );
		$this->assertEquals( implode( '; ', array_unique( $_style ) ), $img->get_attribute( 'style' ) );
	}

	/**
	 * Test getting array attribute.
	 *
	 * @uses Image_Tag::set_attribute()
	 * @uses Image_Tag::get_attributes()
	 * @uses Image_Tag::get_attribute()
	 *
	 * @group attributes
	 * @group get-attributes
	 * @covers Image_Tag::get_array_attribute()
	 */
	function test_get_array_attribute() {
		$img = $this->create();

		$sizes = array( '50w', ' 100w ', '100w' );
		$_sizes = array_filter( array_map( 'trim', $sizes ) );
		$img->set_attribute( 'sizes', $sizes );
		$this->assertEquals( $_sizes, $img->get_attribute( 'sizes', true ) );
		$this->assertEquals( implode( ', ', array_unique( $_sizes ) ), $img->get_attribute( 'sizes' ) );

		$sizes = '50w , 100w ';
		$_sizes = array_filter( array_map( 'trim', explode( ',', trim( $sizes ) ) ) );
		$img->set_attribute( 'sizes', $sizes );
		$this->assertEquals( $_sizes, $img->get_attribute( 'sizes', true ) );
		$this->assertEquals( implode( ', ', array_unique( $_sizes ) ), $img->get_attribute( 'sizes' ) );
	}

	/*
	 ######  ######## ########     ######  ######## ######## ######## #### ##    ##  ######    ######
	##    ## ##          ##       ##    ## ##          ##       ##     ##  ###   ## ##    ##  ##    ##
	##       ##          ##       ##       ##          ##       ##     ##  ####  ## ##        ##
	 ######  ######      ##        ######  ######      ##       ##     ##  ## ## ## ##   ####  ######
	      ## ##          ##             ## ##          ##       ##     ##  ##  #### ##    ##        ##
	##    ## ##          ##       ##    ## ##          ##       ##     ##  ##   ### ##    ##  ##    ##
	 ######  ########    ##        ######  ########    ##       ##    #### ##    ##  ######    ######
	*/

	/**
	 * Test set settings.
	 *
	 * @covers Image_Tag::set_settings()
	 */
	function test_set_settings() {
		$settings = array(
			'foo' => uniqid( 'foo' ),
			'bar' => uniqid( 'bar' ),
		);

		$img = $this->create();
		$img->set_settings( $settings );

		foreach ( $settings as $setting => $value )
			$this->assertEquals( $value, $img->get_setting( $setting ) );
	}

	function test_set_setting() {}
	function test_get_settings() {}
	function test_get_setting() {}

	function test_add_class() {}
	function test_add_sizes_item() {}
	function test_add_srcset_item() {}
	function test_add_style() {}

	function test_set_sizes_item() {}
	function test_set_srcset_item() {}

	function test_remove_classes() {}
	function test_remove_sizes_item() {}
	function test_remove_srcset_item() {}

	function test_get_width() {}
	function test_get_height() {}
	function test_get_ratio() {}
	function test_get_orientation() {}

	function test_http() {}
	function test_lazyload() {}
	function test_noscript() {}
	function test_lqip() {}

	function test_joeschmoe() {}
	function test_picsum() {}
	function test_placeholder() {}
	function test_unsplash() {}

	function test_supports() {}
	function test_can() {}

	function test_arrayaccess_exists() {}
	function test_arrayaccess_get() {}
	function test_arrayaccess_set() {}
	function test_arrayaccess_unset() {}

}