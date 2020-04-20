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
	 *
	 * @group arrayaccess
	 */
	function test_implements() {
		$this->assertContains( 'ArrayAccess', class_implements( $this->class_name() ) );
	}

	/**
	 * Test base64 encoded blank image.
	 *
	 * @group constants
	 */
	function test_blank() {
		$this->assertSame( 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7', constant( $this->class_name() . '::BLANK' ) );
	}

	/**
	 * Test default attributes.
	 *
	 * @uses Image_Tag::get_attributes()
	 *
	 * @group attributes
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

		$this->assertEmpty( array_filter( $img->get_attributes( true ) ) );
	}

	/**
	 * Test default settings.
	 *
	 * @uses Image_Tag::get_settings()
	 * @uses Image_Tag::get_setting()
	 *
	 * @group settings
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
			$this->assertSame( $value, $img[$attribute], sprintf( 'Failed asserting that attribute "%s" matches expected "%s".', $attribute, $value ) );
			$this->assertSame( $value, $img->$attribute, sprintf( 'Failed asserting that attribute "%s" matches expected "%s".', $attribute, $value ) );
			$this->assertSame( $value, $img->get_attribute( $attribute ), sprintf( 'Failed asserting that attribute "%s" matches expected "%s".', $attribute, $value ) );
		}

		foreach ( $settings as $setting => $value )
			$this->assertSame( $value, $img->get_setting( $setting ), sprintf( 'Failed asserting that setting "%s" matches expected "%s".', $setting, $value ) );

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
	 * @group output
	 * @covers Image_Tag::__toString()
	 */
	abstract function test_toString();

	/**
	 * Test type.
	 *
	 * @group type
	 * @covers Image_Tag::is_type()
	 * @covers Image_Tag::get_type()
	 */
	abstract function test_type();

	/**
	 * Test valid.
	 *
	 * @uses Image_Tag::is_valid()
	 *
	 * @group valid
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
			$this->assertSame( $value, $img[$attribute], sprintf( 'Failed asserting that "%s" attribute matches expected value.', $attribute ) );
			$this->assertSame( $value, $img->$attribute, sprintf( 'Failed asserting that "%s" attribute matches expected value.', $attribute ) );
			$this->assertSame( $value, $img->get_attribute( $attribute, true ), sprintf( 'Failed asserting that "%s" attribute matches expected value.', $attribute ) );
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
		$this->assertSame( __FUNCTION__, $img->get_attribute( 'id' ) );
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
		$this->assertSame( $_class, $img->get_attribute( 'class', true ) );

		$class = array( 'foo ', 'bar' );
		$_class = array_filter( array_map( 'trim', $class ) );
		$img->set_attribute( 'class', $class );
		$this->assertSame( $_class, $img->get_attribute( 'class', true ) );
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
		$this->assertSame( $_sizes, $img->get_attribute( 'sizes', true ) );

		$sizes = array( ' 50w', '100w ' );
		$_sizes = array_filter( array_map( 'trim', $sizes ) );
		$img->set_attribute( 'sizes', $sizes );
		$this->assertSame( $_sizes, $img->get_attribute( 'sizes', true ) );
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
		$this->assertSame( $_srcset, $img->get_attribute( 'srcset', true ) );

		$srcset = array( ' https://source.unsplash.com/random/500x500 50w ', 'https://source.unsplash.com/random/1000x1000  100w' );
		$_srcset = array_filter( array_map( 'trim', $srcset ) );
		$img->set_attribute( 'srcset', $srcset );
		$this->assertSame( $_srcset, $img->get_attribute( 'srcset', true ) );
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
		$this->assertSame( $_style, $img->get_attribute( 'style', true ) );

		$style = 'color: #000; display: none;';
		$_style = array_filter( array_map( 'trim', explode( ';', $style ) ) );
		$img->set_attribute( 'style', $style );
		$this->assertSame( $_style, $img->get_attribute( 'style', true ) );

		$style = array( 'color: #000', 'display: none' );
		$_style = array_filter( array_map( 'trim', $style ) );
		$img->set_attribute( 'style', $style );
		$this->assertSame( $_style, $img->get_attribute( 'style', true ) );

		$style = array( 'color: #000;', 'display: none;' );
		array_walk( $style, function( &$item, $key ) {
			$item = trim( $item, " ;\t\n\r\0\x0B" );
		} );
		$_style = array_filter( $style );
		$img->set_attribute( 'style', $style );
		$this->assertSame( $_style, $img->get_attribute( 'style', true ) );
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

		$this->assertSame( $expected_attributes, $img->get_attributes() );

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
		$this->assertSame( $expected_attributes, $img->get_attributes( true ) );
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
			$this->assertSame( $value, $img->get_attribute( $attribute, true ) );

		$attributes['class'] = implode( ' ', $attributes['class'] );
		foreach ( $attributes as $attribute => $value )
			$this->assertSame( $value, $img->get_attribute( $attribute ) );
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
		$this->assertSame( $_classes, $img->get_attribute( 'class', true ) );
		$this->assertSame( implode( ' ', array_unique( $_classes ) ), $img->get_attribute( 'class' ) );

		$classes = 'foo  bar  zulu foo ';
		$_classes = array_filter( array_map( 'trim', explode( ' ', trim( $classes ) ) ) );
		$img->set_attribute( 'class', $classes );
		$this->assertSame( $_classes, $img->get_attribute( 'class', true ) );
		$this->assertSame( implode( ' ', array_unique( $_classes ) ), $img->get_attribute( 'class' ) );
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
		$this->assertSame( $_style, $img->get_attribute( 'style', true ) );
		$this->assertSame( implode( '; ', array_unique( $_style ) ), $img->get_attribute( 'style' ) );

		$style = 'color: #000; display: none;';
		$_style = array_filter( array_map( 'trim', explode( ';', $style ) ) );
		$img->set_attribute( 'style', $style );
		$this->assertSame( $_style, $img->get_attribute( 'style', true ) );
		$this->assertSame( implode( '; ', array_unique( $_style ) ), $img->get_attribute( 'style' ) );

		$style = array( ' color: #000', 'display: none ' );
		$_style = array_filter( array_map( 'trim', $style ) );
		$img->set_attribute( 'style', $style );
		$this->assertSame( $_style, $img->get_attribute( 'style', true ) );
		$this->assertSame( implode( '; ', array_unique( $_style ) ), $img->get_attribute( 'style' ) );

		$style = array( 'color: #000;', 'display: none;' );
		array_walk( $style, function( &$item, $key ) {
			$item = trim( $item, " ;\t\n\r\0\x0B" );
		} );
		$_style = array_filter( $style );
		$img->set_attribute( 'style', $style );
		$this->assertSame( $_style, $img->get_attribute( 'style', true ) );
		$this->assertSame( implode( '; ', array_unique( $_style ) ), $img->get_attribute( 'style' ) );
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
		$this->assertSame( $_sizes, $img->get_attribute( 'sizes', true ) );
		$this->assertSame( implode( ', ', array_unique( $_sizes ) ), $img->get_attribute( 'sizes' ) );

		$sizes = '50w , 100w ';
		$_sizes = array_filter( array_map( 'trim', explode( ',', trim( $sizes ) ) ) );
		$img->set_attribute( 'sizes', $sizes );
		$this->assertSame( $_sizes, $img->get_attribute( 'sizes', true ) );
		$this->assertSame( implode( ', ', array_unique( $_sizes ) ), $img->get_attribute( 'sizes' ) );
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
	 * @group settings
	 * @group set-settings
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
			$this->assertSame( $value, $img->get_setting( $setting ) );
	}

	/**
	 * Test set setting.
	 *
	 * @group settings
	 * @group set-settings
	 * @covers Image_Tag::set_setting()
	 */
	function test_set_setting() {
		$img = $this->create();

		$img->set_setting( 'foo', 'foobar' );
		$this->assertSame( 'foobar', $img->get_setting( 'foo' ) );

		$img->set_setting( 'array', array( 'foobar' ) );
		$this->assertSame( array( 'foobar' ), $img->get_setting( 'array' ) );
	}


	/*
	 ######   ######## ########     ######  ######## ######## ######## #### ##    ##  ######    ######
	##    ##  ##          ##       ##    ## ##          ##       ##     ##  ###   ## ##    ##  ##    ##
	##        ##          ##       ##       ##          ##       ##     ##  ####  ## ##        ##
	##   #### ######      ##        ######  ######      ##       ##     ##  ## ## ## ##   ####  ######
	##    ##  ##          ##             ## ##          ##       ##     ##  ##  #### ##    ##        ##
	##    ##  ##          ##       ##    ## ##          ##       ##     ##  ##   ### ##    ##  ##    ##
	 ######   ########    ##        ######  ########    ##       ##    #### ##    ##  ######    ######
	*/

	/**
	 * Test get settings.
	 *
	 * @group settings
	 * @group get-settings
	 * @covers Image_Tag::get_settings()
	 */
	abstract function test_get_settings();

	/**
	 * Test get setting.
	 *
	 * @group settings
	 * @group get-settings
	 * @covers Image_Tag::get_setting()
	 */
	function test_get_setting() {
		$settings = array(
			'foo' => 'foobar',
			'array' => array( 'foobar' ),
			'int' => mt_rand( 1, 999 ),
		);

		$img = $this->create( null, $settings );

		foreach ( $settings as $setting => $value ) {
			$this->assertSame( $value, $img->get_setting( $setting ) );
			$this->assertNotEquals( $setting, $img->get_setting( $setting ) );
		}
	}


	/*
	   ###    ########  ########     ##     ## ######## ##       ########  ######## ########   ######
	  ## ##   ##     ## ##     ##    ##     ## ##       ##       ##     ## ##       ##     ## ##    ##
	 ##   ##  ##     ## ##     ##    ##     ## ##       ##       ##     ## ##       ##     ## ##
	##     ## ##     ## ##     ##    ######### ######   ##       ########  ######   ########   ######
	######### ##     ## ##     ##    ##     ## ##       ##       ##        ##       ##   ##         ##
	##     ## ##     ## ##     ##    ##     ## ##       ##       ##        ##       ##    ##  ##    ##
	##     ## ########  ########     ##     ## ######## ######## ##        ######## ##     ##  ######
	*/

	/**
	 * Test adding to attribute.
	 *
	 * @uses Image_Tag::get_attribute()
	 *
	 * @group attributes
	 * @group helpers
	 * @covers Image_Tag::add_to_attribute()
	 * @covers Image_Tag::trim()
	 */
	function test_add_to_attribute() {
		$img = $this->create();

		$this->assertEmpty( $img->get_attribute( 'style' ) );
		$this->assertEmpty( $img->get_attribute( 'style', true ) );

		$style = 'color: #FFF;';
		$img->add_to_attribute( 'style', $style );
		$this->assertSame( array( 'color: #FFF' ), $img->get_attribute( 'style', true ) );
		$this->assertSame( 'color: #FFF', $img->get_attribute( 'style' ) );

		$style = array( 'color: #FFF;' );
		$img->set_attribute( 'style', null );
		$this->expectException( \TypeError::class );
		$img->add_to_attribute( 'style', $style );
	}

	/**
	 * Test adding class.
	 *
	 * @uses Image_Tag::get_attribute()
	 * @uses Image_Tag::add_to_attribute()
	 *
	 * @group attributes
	 * @group helpers
	 * @covers Image_Tag::add_to_attribute()
	 * @covers Image_Tag::add_to_class_attribute()
	 */
	function test_add_to_class_attribute() {
		$img = $this->create();

		$this->assertEmpty( $img->get_attribute( 'class' ) );
		$this->assertEmpty( $img->get_attribute( 'class', true ) );

		$classes = 'foo bar';
		$img->add_to_attribute( 'class', $classes );
		$this->assertSame( array( 'foo', 'bar' ), $img->get_attribute( 'class', true ) );
		$this->assertSame( $classes, $img->get_attribute( 'class' ) );

		$classes = array( 'foo', 'bar' );
		$this->expectException( \TypeError::class );
		$img->add_to_attribute( 'class', $classes );
	}


	/*
	########  #### ##     ## ######## ##    ##  ######  ####  #######  ##    ##  ######
	##     ##  ##  ###   ### ##       ###   ## ##    ##  ##  ##     ## ###   ## ##    ##
	##     ##  ##  #### #### ##       ####  ## ##        ##  ##     ## ####  ## ##
	##     ##  ##  ## ### ## ######   ## ## ##  ######   ##  ##     ## ## ## ##  ######
	##     ##  ##  ##     ## ##       ##  ####       ##  ##  ##     ## ##  ####       ##
	##     ##  ##  ##     ## ##       ##   ### ##    ##  ##  ##     ## ##   ### ##    ##
	########  #### ##     ## ######## ##    ##  ######  ####  #######  ##    ##  ######
	*/

	/**
	 * Test getting width.
	 *
	 * @uses Image_Tag::get_width()
	 *
	 * @group dimensions
	 * @covers Image_Tag::get_width()
	 */
	function test_get_width() {
		$img = $this->create();
		$this->assertSame( 0, $img->get_width() );

		$img->set_attribute( 'width', '400' );
		$this->assertSame( 400, $img->get_width() );

		$img->set_attribute( 'width', 1600 );
		$this->assertSame( 1600, $img->get_width() );
	}

	/**
	 * Test getting height.
	 *
	 * @uses Image_Tag::get_height()
	 *
	 * @group dimensions
	 * @covers Image_Tag::get_height()
	 */
	function test_get_height() {
		$img = $this->create();
		$this->assertSame( 0, $img->get_height() );

		$img->set_attribute( 'height', '300' );
		$this->assertSame( 300, $img->get_height() );

		$img->set_attribute( 'height', 900 );
		$this->assertSame( 900, $img->get_height() );
	}

	/**
	 * Test calculating ratio.
	 *
	 * @uses Image_Tag::get_width()
	 * @uses Image_Tag::get_height()
	 *
	 * @group dimensions
	 * @covers Image_Tag::get_ratio()
	 */
	function test_get_ratio() {
		$dimensions = array(
			500 => 500,
			400 => 300,
			1600 => 900,
			2400 => 1350,
		);

		foreach ( $dimensions as $width => $height ) {
			$img = $this->create( array(
				'width' => $width,
				'height' => $height,
			) );
			$this->assertSame( $height / $width, $img->get_ratio() );
		}
	}

	/**
	 * Test orientation.
	 *
	 * @uses Image_Tag::set_attribute()
	 * @uses Image_Tag::get_orientation()
	 *
	 * @group dimensions
	 * @covers Image_Tag::get_orientation()
	 */
	function test_get_orientation() {
		$tests = array(
			'square' => array( 1600, 1600 ),
			'portrait' => array( 900, 1600 ),
			'landscape' => array( 1600, 900 ),
		);

		$img = $this->create();

		foreach ( $tests as $orientation => $dimensions ) {
			$img->set_attribute(  'width', $dimensions[0] );
			$img->set_attribute( 'height', $dimensions[1] );
			$this->assertSame( $orientation, $img->get_orientation() );
		}
	}


	/*
	######## ########    ###    ######## ##     ## ########  ########  ######
	##       ##         ## ##      ##    ##     ## ##     ## ##       ##    ##
	##       ##        ##   ##     ##    ##     ## ##     ## ##       ##
	######   ######   ##     ##    ##    ##     ## ########  ######    ######
	##       ##       #########    ##    ##     ## ##   ##   ##             ##
	##       ##       ##     ##    ##    ##     ## ##    ##  ##       ##    ##
	##       ######## ##     ##    ##     #######  ##     ## ########  ######
	*/

	/**
	 * Test HTTP request.
	 *
	 * @uses Image_Tag::http()
	 *
	 * @group http
	 * @group features
	 * @group feature-http
	 * @covers Image_Tag::http()
	 */
	function test_http() {
		$img = $this->create();
		$http = $img->http();

		$this->assertFalse( is_wp_error( $http ) );
		$this->assertNotEmpty( wp_remote_retrieve_body( $http ) );
		$this->assertContains( 'image/', wp_remote_retrieve_header( $http, 'content-type' ) );
	}

	/**
	 * Test adjustments to lazyload.
	 *
	 * @uses Image_Tag::lazyload()
	 *
	 * @group http
	 * @group features
	 * @group feature-lazylaod
	 * @covers Image_Tag::lazyload()
	 */
	function test_lazyload() {
		$img = $this->create();
		$this->assertNotEmpty( $img->get_attribute( 'src' ) );
		$this->assertTrue( $img->can( 'lazyload' ) );

		$lazyload = $img->lazyload();
		$this->assertSame( $img->get_attribute( 'src' ), $lazyload->get_attribute( 'data-src' ) );
		$this->assertEmpty( $lazyload->get_attribute( 'data-sizes' ) );
		$this->assertEmpty( $lazyload->get_attribute( 'data-srcset' ) );
		$this->assertSame( Image_Tag::BLANK, $lazyload->get_attribute( 'src' ) );
		$this->assertSame( 'lazyload hide-if-no-js', $lazyload->get_attribute( 'class' ) );
		$this->assertContains( '<noscript>', $lazyload->get_setting( 'after_output' ) );
	}

	/**
	 * Test adjustments for noscript.
	 *
	 * @uses Image_Tag::noscript()
	 *
	 * @group http
	 * @group features
	 * @group feature-noscript
	 * @covers Image_Tag::noscript()
	 */
	function test_noscript() {
		$img = $this->create();
		$noscript = $img->noscript();

		$this->assertTrue( $img->can( 'noscript' ) );
		$this->assertSame( 'no-js', $noscript->get_attribute( 'class' ) );
		$this->assertSame(  '<noscript>', $noscript->get_setting( 'before_output' ) );
		$this->assertSame( '</noscript>', $noscript->get_setting(  'after_output' ) );
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
	function test_lqip() {}

	/**
	 * Test common colors.
	 *
	 * @uses Image_Tag::common_colors()
	 *
	 * @group features
	 * @group feature-colors
	 * @covers Image_Tag::common_colors()
	 */
	function test_common_colors() {}

	/**
	 * Test mode color.
	 *
	 * @uses Image_Tag::mode_color()
	 *
	 * @group features
	 * @group feature-colors
	 * @covers Image_Tag::mode_color()
	 */
	function test_mode_color() {}


	/*
	########  ##          ###     ######  ######## ##     ##  #######  ##       ########  ######## ########   ######
	##     ## ##         ## ##   ##    ## ##       ##     ## ##     ## ##       ##     ## ##       ##     ## ##    ##
	##     ## ##        ##   ##  ##       ##       ##     ## ##     ## ##       ##     ## ##       ##     ## ##
	########  ##       ##     ## ##       ######   ######### ##     ## ##       ##     ## ######   ########   ######
	##        ##       ######### ##       ##       ##     ## ##     ## ##       ##     ## ##       ##   ##         ##
	##        ##       ##     ## ##    ## ##       ##     ## ##     ## ##       ##     ## ##       ##    ##  ##    ##
	##        ######## ##     ##  ######  ######## ##     ##  #######  ######## ########  ######## ##     ##  ######
	*/

	/**
	 * Test changing into.
	 *
	 * @uses Image_Tag::into()
	 *
	 * @group into
	 * @covers Image_Tag::into()
	 */
	function test_into() {}

	/**
	 * Test changing into JoeSchmoe.
	 *
	 * @uses Image_Tag::into()
	 *
	 * @group into
	 * @group joeschmoe
	 * @covers Image_Tag::into()
	 */
	function test_into_joeschmoe() {}

	/**
	 * Test changing into Picsum.
	 *
	 * @uses Image_Tag::into()
	 *
	 * @group into
	 * @group picsum
	 * @covers Image_Tag::into()
	 */
	function test_into_picsum() {}

	/**
	 * Test changing into Placeholder.
	 *
	 * @uses Image_Tag::into()
	 *
	 * @group into
	 * @group placeholder
	 * @covers Image_Tag::into()
	 */
	function test_into_placeholder() {}

	/**
	 * Test changing into Unsplash.
	 *
	 * @uses Image_Tag::into()
	 *
	 * @group into
	 * @group unsplash
	 * @covers Image_Tag::into()
	 */
	function test_into_unsplash() {}


	/*
	 ######     ###    ########     ###    ########  #### ##       #### ######## #### ########  ######
	##    ##   ## ##   ##     ##   ## ##   ##     ##  ##  ##        ##     ##     ##  ##       ##    ##
	##        ##   ##  ##     ##  ##   ##  ##     ##  ##  ##        ##     ##     ##  ##       ##
	##       ##     ## ########  ##     ## ########   ##  ##        ##     ##     ##  ######    ######
	##       ######### ##        ######### ##     ##  ##  ##        ##     ##     ##  ##             ##
	##    ## ##     ## ##        ##     ## ##     ##  ##  ##        ##     ##     ##  ##       ##    ##
	 ######  ##     ## ##        ##     ## ########  #### ######## ####    ##    #### ########  ######
	*/

	function test_supports() {}
	function test_can() {}


	/*
	   ###    ########  ########     ###    ##    ##    ###     ######   ######  ########  ######   ######
	  ## ##   ##     ## ##     ##   ## ##    ##  ##    ## ##   ##    ## ##    ## ##       ##    ## ##    ##
	 ##   ##  ##     ## ##     ##  ##   ##    ####    ##   ##  ##       ##       ##       ##       ##
	##     ## ########  ########  ##     ##    ##    ##     ## ##       ##       ######    ######   ######
	######### ##   ##   ##   ##   #########    ##    ######### ##       ##       ##             ##       ##
	##     ## ##    ##  ##    ##  ##     ##    ##    ##     ## ##    ## ##    ## ##       ##    ## ##    ##
	##     ## ##     ## ##     ## ##     ##    ##    ##     ##  ######   ######  ########  ######   ######
	*/

	function test_arrayaccess_exists() {}
	function test_arrayaccess_get() {}
	function test_arrayaccess_set() {}
	function test_arrayaccess_unset() {}

}