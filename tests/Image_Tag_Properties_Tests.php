<?php

abstract class Image_Tag_Properties_Tests extends WP_UnitTestCase {

	/**
	 * @param string $class_name
	 * @param string $property
	 * @param string $expected
	 *
	 * @covers ::function_name()
	 * @group static
	 *
	 * @dataProvider data_function_name()
	 */
	function test_function_name( string $class_name, string $property, string $expected ) {
		$this->assertSame( $expected, call_user_func( array( $class_name, 'function_name' ), $property ) );
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