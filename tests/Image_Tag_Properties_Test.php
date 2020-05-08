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
	 */
	function data__construct() {
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
	 * @covers ::__construct()
	 * @group magic
	 * @group construct
	 *
	 * @dataProvider data__construct
	 */
	function test__construct( string $class, $properties, $defaults, $expected ) {
		$instance = new $class( $properties, $defaults );
		$this->assertSame( $expected, $instance->get() );
	}

	/**
	 * Data provider for test__set().
	 *
	 * @see self::test__set()
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
			'multidimensional-array' => array(
				Image_Tag_Properties::class,
				'foo',
				array_fill( 0, 5, range( 10, 15 ) ),
			),
			'array-objects' => array(
				Image_Tag_Properties::class,
				'foo',
				array_fill( 0, 5, ( object ) range( 15, 20 ) ),
			),
		);
	}

	/**
	 * @covers ::__set()
	 * @group magic
	 * @group set
	 *
	 * @dataProvider data__set
	 */
	function test__set( string $class, $property, $value ) {
		$instance = new $class( array() );
		$this->assertEmpty( $instance->$property );

		$instance->$property = $value;
		$this->assertSame( $value, $instance->$property );
	}

	/**
	 * @covers ::__get()
	 * @group magic
	 * @group get
	 *
	 * @doesNotPerformAssertions
	 */
	function test__get() {}

	/**
	 * @covers ::__isset()
	 * @group magic
	 * @group isset
	 *
	 * @doesNotPerformAssertions
	 */
	function test__isset() {}

	/**
	 * @covers ::__unset()
	 * @group magic
	 * @group unset
	 *
	 * @doesNotPerformAssertions
	 */
	function test__unset() {}


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
	 *
	 * @doesNotPerformAssertions
	 */
	function test_add() {}


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
	 *
	 * @doesNotPerformAssertions
	 */
	function test_set() {}

	/**
	 * @covers ::unset()
	 * @group instance
	 * @group unset
	 *
	 * @doesNotPerformAssertions
	 */
	function test_unset() {}

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
	 * @covers ::isset()
	 * @group instance
	 * @group isset
	 *
	 * @doesNotPerformAssertions
	 */
	function test_isset() {}

	/**
	 * @covers ::get()
	 * @group instance
	 * @group get
	 *
	 * @doesNotPerformAssertions
	 */
	function test_get() {}


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
	 *
	 * @doesNotPerformAssertions
	 */
	function test_arrayAccess() {}

}

?>