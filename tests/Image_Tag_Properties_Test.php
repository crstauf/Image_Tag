<?php

/**
 * @coversDefaultClass Image_Tag_Properties
 * @group properties
 */
class Image_Tag_Properties_Test extends WP_UnitTestCase {

	/**
	 * @group constant
	 * @group defaults
	 */
	function test_defaults_constant() {
		$this->assertEmpty( Image_Tag_Properties::DEFAULTS );
		$this->assertIsArray( Image_Tag_Properties::DEFAULTS );
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
	 * Data provider for test__construct().
	 *
	 * @see self::test__construct()
	 * @return array
	 */
	function data__construct() {
		$this->markTestIncomplete();
		return array(
			'empty' => array(
				Image_Tag_Properties::class,
				array(),
				array(),
				array(),
			),
		);
	}

	/**
	 * @param string $class_name
	 * @param array|Image_Tag_Properties $properties
	 * @param array $defaults
	 * @param mixed $expected
	 *
	 * @covers ::__construct()
	 * @covers ::get()
	 * @group magic
	 * @group construct
	 *
	 * @dataProvider data__construct
	 */
	function test__construct( string $class_name, $properties, $defaults, $expected ) {
		$instance = new $class_name( $properties, $defaults );
		$this->assertSame( $expected, $instance->get() );
	}

	/**
	 * Data provider for test__set().
	 *
	 * @see self::test__set()
	 * @return array
	 */
	function data__set() {
		return array(

			'string' => array(
				Image_Tag_Properties::class,
				'foo',
				'bar',
			),

			'array' => array(
				Image_Tag_Properties::class,
				'foo',
				range( 1, 5 ),
			),

			'float' => array(
				Image_Tag_Properties::class,
				'foo',
				3.1415,
			),

			'integer' => array(
				Image_Tag_Properties::class,
				'foo',
				2400,
			),

			'object' => array(
				Image_Tag_Properties::class,
				'foo',
				( object ) range( 5, 10 ),
			),

			'multi-dimensional array' => array(
				Image_Tag_Properties::class,
				'foo',
				array_fill( 0, 5, range( 10, 15 ) ),
			),

			'array of objects' => array(
				Image_Tag_Properties::class,
				'foo',
				array_fill( 0, 5, ( object ) range( 15, 20 ) ),
			),

		);
	}

	/**
	 * @param string $class_name
	 * @param string $property
	 * @param mixed $value
	 *
	 * @covers ::__set()
	 * @covers ::set()
	 * @covers ::_set()
	 * @group magic
	 * @group set
	 *
	 * @dataProvider data__set
	 */
	function test__set( string $class_name, string $property, $value ) {
		$instance = new $class_name();
		$this->assertEmpty( $instance->$property );

		$instance->$property = $value;
		$this->assertSame( $value, $instance->$property );
	}

	/**
	 * Data provider for test__get().
	 *
	 * @see self::test__get()
	 * @return array
	 */
	function data__get() {
		return array(

			'empty' => array(
				Image_Tag_Properties::class,
				'foo',
				null,
			),

			'string' => array(
				Image_Tag_Properties::class,
				'foo',
				'bar',
			),

			'array' => array(
				Image_Tag_Properties::class,
				'foo',
				range( 1, 5 ),
			),

			'float' => array(
				Image_Tag_Properties::class,
				'foo',
				3.1415,
			),

			'integer' => array(
				Image_Tag_Properties::class,
				'foo',
				2400,
			),

			'object' => array(
				Image_Tag_Properties::class,
				'foo',
				( object ) range( 5, 10 ),
			),

			'multi-dimensional array' => array(
				Image_Tag_Properties::class,
				'foo',
				array_fill( 0, 5, range( 10, 15 ) ),
			),

			'array of objects' => array(
				Image_Tag_Properties::class,
				'foo',
				array_fill( 0, 5, ( object ) range( 15, 20 ) ),
			),

		);
	}

	/**
	 * @covers ::__get()
	 * @covers::get()
	 * @covers::get_property()
	 * @group magic
	 * @group get
	 *
	 * @dataProvider data__get
	 */
	function test__get( string $class_name, string $property, $value ) {
		if ( is_null( $value ) ) {
			$instance = new $class_name();
			$this->assertNull( $instance->$property );
			return;
		}

		$properties = array( $property => $value );
		$instance = new $class_name( $properties );
		$this->assertSame( $value, $instance->$property );
	}

	function data__isset() {
		$data = array(

			'empty' => array(
				Image_Tag_Properties::class,
				null,
				'foo',
				false,
			),

			'null' => array(
				Image_Tag_Properties::class,
				array( 'foo' => null ),
				'foo',
				false,
			),

			'false' => array(
				Image_Tag_Properties::class,
				array( 'foo' => uniqid( __FUNCTION__ ) ),
				'bar',
				false,
			),

		);

		# Create truths.
		foreach ( array(

			'string'  => 'bar',
			'array'   => range( 1, 5 ),
			'float'   => 3.1415,
			'integer' => 2400,
			'object'  => ( object ) range( 5, 10 ),

			'multi-dimesional array' => array_fill( 0, 5, range( 10, 15 ) ),
			'array of objects'       => array_fill( 0, 5, ( object ) range( 15, 20 ) ),

		) as $name => $value )
			$data[$name] = array(
				Image_Tag_Properties::class,
				array( 'foo' => $value ),
				'foo',
				true,
			);

		return $data;
	}

	/**
	 * @param string $class_name
	 * @param array $attributes
	 * @param string $property
	 * @param bool $expected
	 *
	 * @covers ::__isset()
	 * @covers ::isset()
	 * @group magic
	 * @group isset
	 *
	 * @dataProvider data__isset
	 */
	function test__isset( string $class_name, $attributes, string $property, $expected ) {
		$instance = new $class_name( $attributes );

		$expected
			? $this->assertTrue(  isset( $instance->$property ) )
			: $this->assertFalse( isset( $instance->$property ) );
	}

	/**
	 * @covers ::__unset()
	 * @group magic
	 * @group unset
	 */
	function test__unset() {
		$this->markTestIncomplete();
	}


	/*
	   ###    ########  ########
	  ## ##   ##     ## ##     ##
	 ##   ##  ##     ## ##     ##
	##     ## ##     ## ##     ##
	######### ##     ## ##     ##
	##     ## ##     ## ##     ##
	##     ## ########  ########
	*/

	/**
	 * @covers ::add()
	 * @group instance
	 * @group add
	 */
	function test_add() {
		$this->markTestIncomplete();
	}


	/*
	 ######  ######## ########
	##    ## ##          ##
	##       ##          ##
	 ######  ######      ##
	      ## ##          ##
	##    ## ##          ##
	 ######  ########    ##
	*/

	/**
	 * @covers ::set()
	 * @group instance
	 * @group set
	 */
	function test_set() {
		$this->markTestIncomplete();
	}

	/**
	 * @covers ::unset()
	 * @group instance
	 * @group unset
	 */
	function test_unset() {
		$this->markTestIncomplete();
	}


	/*
	######## ##     ## ####  ######  ########  ######
	##        ##   ##   ##  ##    ##    ##    ##    ##
	##         ## ##    ##  ##          ##    ##
	######      ###     ##   ######     ##     ######
	##         ## ##    ##        ##    ##          ##
	##        ##   ##   ##  ##    ##    ##    ##    ##
	######## ##     ## ####  ######     ##     ######
	*/

	/**
	 * @covers ::isset()
	 * @group instance
	 * @group exists
	 */
	function test_isset() {
		$this->markTestIncomplete();
	}

	/**
	 * @covers ::exists()
	 * @group instance
	 * @group exists
	 */
	function test_exists() {
		$this->markTestIncomplete();
	}

	/*
	 ######   ######## ########
	##    ##  ##          ##
	##        ##          ##
	##   #### ######      ##
	##    ##  ##          ##
	##    ##  ##          ##
	 ######   ########    ##
	*/

	/**
	 * @covers ::get()
	 * @group instance
	 * @group get
	 */
	function test_get() {
		$this->markTestIncomplete();
	}


	/*
	   ###    ########  ########     ###    ##    ##    ###     ######   ######  ########  ######   ######
	  ## ##   ##     ## ##     ##   ## ##    ##  ##    ## ##   ##    ## ##    ## ##       ##    ## ##    ##
	 ##   ##  ##     ## ##     ##  ##   ##    ####    ##   ##  ##       ##       ##       ##       ##
	##     ## ########  ########  ##     ##    ##    ##     ## ##       ##       ######    ######   ######
	######### ##   ##   ##   ##   #########    ##    ######### ##       ##       ##             ##       ##
	##     ## ##    ##  ##    ##  ##     ##    ##    ##     ## ##    ## ##    ## ##       ##    ## ##    ##
	##     ## ##     ## ##     ## ##     ##    ##    ##     ##  ######   ######  ########  ######   ######
	*/

	/**
	 * @covers ::offsetExists()
	 * @covers ::offsetGet()
	 * @covers ::offsetSet()
	 * @covers ::offsetUnset()
	 * @group instance
	 * @group arrayaccess
	 */
	function test_arrayAccess() {
		$this->markTestIncomplete();
	}

}

?>