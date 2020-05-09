<?php
/**
 * Tests for Image_Tag_Properties and descendants.
 */

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
	 * @covers ::get_properties()
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
	 * @covers ::set_property()
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
	 * @param string $class_name
	 * @param string $property
	 * @param mixed $value
	 *
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
	 * @param array|Image_Tag_Properties $properties
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
	function test__isset( string $class_name, $properties, string $property, $expected ) {
		$instance = new $class_name( $properties );

		$expected
			? $this->assertTrue(  isset( $instance->$property ) )
			: $this->assertFalse( isset( $instance->$property ) );
	}

	/**
	 * @param string $class_name
	 * @param
	 * @covers ::__unset()
	 * @covers ::unset()
	 * @group magic
	 * @group unset
	 *
	 * @dataProvider data__unset
	 */
	function test__unset( string $class_name, $properties, string $property ) {
		$instance = new $class_name( $properties );
		$this->assertSame( $properties[$property], $instance->$property );

		unset( $instance->$property );
		$this->assertFalse( isset( $instance->$property ) );
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
	 * @param string $class_name
	 * @param array|Image_Tag_Properties $properties
	 * @param string|array $add_properties
	 * @param mixed $value
	 * @param mixed $expected Expected value of property.
	 *
	 * @covers ::add()
	 * @covers ::add_property()
	 * @covers ::add_properties()
	 * @group instance
	 * @group add
	 *
	 * @dataProvider data_add
	 */
	function test_add( string $class_name, $properties, $add_properties, $value, $expected ) {
		$instance = new $class_name( $properties );
		$instance->add( $add_properties, $value );

		if ( is_string( $add_properties ) ) {
			$this->assertSame( $expected, $instance->$add_properties );
			return;
		}

		foreach ( $expected as $property => $expected_value )
			$this->assertSame( $expected_value, $instance->$property );
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
	 * @param string $class_name
	 * @param mixed $properties
	 *
	 * @covers ::set()
	 * @covers ::set_property()
	 * @covers ::set_properties()
	 * @covers ::_set()
	 * @group instance
	 * @group set
	 *
	 * @dataProvider data_set
	 */
	function test_set( string $class_name, $properties, $set_properties, $value, $expected ) {
		$this->markTestIncomplete();

		$instance = new $class_name( $properties );

		if ( is_string( $set_properties ) ) {
			if ( array_key_exists( $set_properties, $properties ) )
				$this->assertSame( $properties[$set_properties], $instance->$set_properties );

			$instance->set( $set_properties, $value );

			$this->assertSame( $value, $instance->$set_properties );
			return;
		}

		$override_properties = array_key_intersect( $properties, $set_properties );
		$override_properties = array_keys( $override_properties );

		foreach ( $override_properties as $property )
			$this->assertSame( $properties[$property], $instance->$property );

		$instance->set( $set_properties );

		foreach ( $set_properties as $property => $value )
			$this->assertSame( $value, $instance->$property );

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