<?php
/**
 * Tests for Image_Tag_Properties and descendants.
 */

abstract class Image_Tag_Properties_Tests extends WP_UnitTestCase {

	/**
	 * Get the class name to run tests against.
	 *
	 * @return string
	 */
	abstract protected function class_name();

	/**
	 * Create a new instance of the tested class.
	 *
	 * @param array $params
	 * @uses self::class_name()
	 * @return Image_Tag_Properties
	 */
	function new_instance( ...$params ) {
		$class_name = $this->class_name();
		return new $class_name( ...$params );
	}

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
	function test_function_name( string $property, string $expected ) {
		$this->assertSame( $expected, call_user_func( array( $this->class_name(), 'function_name' ), $property ) );
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
	function test__construct( $properties, $defaults, $expected ) {
		echo $this->class_name();
		$instance = $this->new_instance( $properties, $defaults );
		$this->assertSame( $expected, $instance->get() );
	}

	/**
	 * @param mixed $properties
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
	function test__set( $properties, string $property, $value ) {
		$instance = $this->new_instance( $properties );
		$this->assertEmpty( $instance->$property );

		$instance->$property = $value;
		$this->assertSame( $value, $instance->$property );
	}

	/**
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
	function test__get( string $property, $value ) {
		if ( is_null( $value ) ) {
			$instance = $this->new_instance();
			$this->assertNull( $instance->$property );
			return;
		}

		$properties = array( $property => $value );
		$instance = $this->new_instance( $properties );
		$this->assertSame( $value, $instance->$property );
	}

	/**
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
	function test__isset( $properties, string $property, $expected ) {
		$instance = $this->new_instance( $properties );

		$expected
			? $this->assertTrue(  isset( $instance->$property ) )
			: $this->assertFalse( isset( $instance->$property ) );
	}

	/**
	 * @param string $class_name
	 * @param array|Image_Tag_Properties $properties
	 * @param string $property
	 *
	 * @covers ::__unset()
	 * @covers ::unset()
	 * @group magic
	 * @group unset
	 *
	 * @dataProvider data__unset
	 */
	function test__unset( $properties, string $property ) {
		$instance = $instance = $this->new_instance( $properties );
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
	function test_add( $properties, $add_properties, $value, $expected ) {
		$instance = $this->new_instance( $properties );

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
	 * @param mixed $properties
	 * @param string|array $set_properties
	 * @param mixed $value
	 * @param mixed $expected
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
	function test_set( $properties, $set_properties, $value, $expected ) {
		$this->markTestIncomplete();

		$instance = $this->new_instance( $properties );

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