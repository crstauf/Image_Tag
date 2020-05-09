<?php
/**
 * Tests for Image_Tag_Properties and descendants.
 */

abstract class Image_Tag_Properties_Base extends WP_UnitTestCase {

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

	function test_chaining() {
		$instance = $this->new_instance();
		$this->assertInstanceOf( $this->class_name(), $instance->add( 'id', __FUNCTION__ ) );
		$this->assertInstanceOf( $this->class_name(), $instance->set( 'id', __FUNCTION__ ) );
	}


	/*
	 ######  ########    ###    ######## ####  ######
	##    ##    ##      ## ##      ##     ##  ##    ##
	##          ##     ##   ##     ##     ##  ##
	 ######     ##    ##     ##    ##     ##  ##
	      ##    ##    #########    ##     ##  ##
	##    ##    ##    ##     ##    ##     ##  ##    ##
	 ######     ##    ##     ##    ##    ####  ######
	*/

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
	 * @group magic
	 * @group construct
	 *
	 * @dataProvider data__construct
	 */
	function test__construct( $properties, $defaults, $expected ) {
		$instance = $this->new_instance( $properties, $defaults );
		$this->assertSame( $expected, $instance->get() );
	}

	/**
	 * @param mixed $properties
	 * @param string $property
	 * @param mixed $value
	 *
	 * @covers ::__set()
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
	 * @param array $properties
	 * @param string|array $unset_properties
	 *
	 * @covers ::unset()
	 * @group instance
	 * @group unset
	 *
	 * @dataProvider data_unset
	 */
	function test_unset( $properties, $unset_properties ) {
		$instance = $this->new_instance( $properties );

		# Check properties are set.
		foreach ( ( array ) $unset_properties as $property )
			$this->assertSame( $properties[$property], $instance->$property );

		# Unset specified properties.
		$instance->unset( $unset_properties );

		# Check specified properties are unset (null).
		foreach ( ( array ) $unset_properties as $property )
			$this->assertNull( $instance->$property );

		# Check remaining properties are still set.
		$remaining = array_diff( array_keys( $properties ), ( array ) $unset_properties );
		foreach ( $remaining as $property )
			$this->assertSame( $properties[$property], $instance->$property );
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
	 * @param array $properties
	 * @param string|array $isset_properties
	 * @param array $not_isset_properties
	 *
	 * @covers ::isset()
	 * @group instance
	 * @group exists
	 *
	 * @dataProvider data_isset
	 */
	function test_isset( $properties, $isset_properties, $not_isset_properties = null ) {
		$instance = $this->new_instance( $properties );

		if ( !is_null( $isset_properties ) )
			$this->assertTrue( $instance->isset( $isset_properties ) );

		if ( !is_null( $not_isset_properties ) )
			$this->assertFalse( $instance->isset( $not_isset_properties ) );
	}

	/**
	 * @param array $properties
	 * @param string|array $exist_properties
	 * @param array $not_exist_properties
	 * @covers ::exists()
	 * @group instance
	 * @group exists
	 *
	 * @dataProvider data_exists
	 */
	function test_exists( $properties, $exist_properties, $not_exist_properties = null ) {
		$instance = $this->new_instance( $properties );

		if ( !is_null( $exist_properties ) )
			$this->assertTrue( $instance->exists( $exist_properties ) );

		if ( !is_null( $not_exist_properties ) )
			$this->assertFalse( $instance->exists( $not_exist_properties ) );
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
	 * @param array $properties
	 * @param string|array $get_properties
	 *
	 * @covers ::get()
	 * @covers ::get_properties()
	 * @covers ::get_property()
	 * @covers ::_get()
	 * @group instance
	 * @group get
	 *
	 * @dataProvider data_get
	 */
	function test_get( $properties, $get_properties ) {
		$instance = $this->new_instance( $properties );

		if ( is_string( $get_properties ) ) {
			$this->assertSame( $properties[$get_properties], $instance->get( $get_properties ) );
			return;
		}

		if ( is_null( $get_properties ) ) {
			$this->assertSame( $properties, $instance->get( null ) );
			return;
		}

		$expected
		= $actual
		= array();

		foreach ( $get_properties as $property ) {
			$expected[$property] = $properties[$property];
			$actual[$property] = $instance->get( $property, 'edit' );
		}

		$this->assertSame( $expected, $actual );
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
	 * @param array $properties
	 * @param string|array $exist_properties
	 * @param array $not_exist_properties
	 *
	 * @covers ::offsetExists()
	 * @group instance
	 * @group arrayaccess
	 *
	 * @dataProvider data_arrayAccess_exists
	 */
	function test_arrayAccess_exists( $properties, $exist_properties, $not_exist_properties = null ) {
		$instance = $this->new_instance( $properties );

		if ( !is_null( $exist_properties ) )
			foreach ( ( array ) $exist_properties as $property )
				$this->assertTrue( isset( $instance[$property] ) );

		if ( !is_null( $not_exist_properties ) )
			foreach ( ( array ) $not_exist_properties as $property )
				$this->assertFalse( isset( $instance[$property] ) );
	}

	/**
	 * @param array $properties
	 *
	 * @covers ::offsetExists()
	 * @covers ::offsetGet()
	 * @covers ::offsetSet()
	 * @covers ::offsetUnset()
	 * @group instance
	 * @group arrayaccess
	 *
	 * @dataProvider data_arrayAccess_get
	 */
	function test_arrayAccess_get( $properties ) {
		$instance = $this->new_instance( $properties );

		foreach ( $properties as $property => $value )
			$this->assertSame( $value, $instance[$property] );
	}

	/**
	 * @param array $properties
	 * @param string $set_property
	 * @param mixed $set_value
	 *
	 * @covers ::offsetSet()
	 * @group instance
	 * @group arrayaccess
	 *
	 * @dataProvider data_arrayAccess_set
	 */
	function test_arrayAccess_set( $properties, string $set_property, $set_value ) {
		$instance = $this->new_instance( $properties );

		if ( array_key_exists( $set_property, $properties ) )
			$this->assertSame( $properties[$set_property], $instance->$set_property );

		$instance[$set_property] = $set_value;

		$this->assertSame( $set_value, $instance->$set_property );
	}

	/**
	 * @param array $properties
	 * @param string|array $unset_properties
	 *
	 * @covers ::offsetUnset()
	 * @group instance
	 * @group arrayaccess
	 *
	 * @dataProvider data_arrayAccess_unset
	 */
	function test_arrayAccess_unset( $properties, $unset_properties ) {
		$instance = $this->new_instance( $properties );

		# Check properties are set.
		foreach ( ( array ) $unset_properties as $property )
			$this->assertSame( $properties[$property], $instance->$property );

		# Unset specified properties.
		foreach ( ( array ) $unset_properties as $property )
			unset( $instance[$property] );

		# Check specified properties are unset (null).
		foreach ( ( array ) $unset_properties as $property )
			$this->assertNull( $instance->$property );

		# Check remaining properties are still set.
		$remaining = array_diff( array_keys( $properties ), ( array ) $unset_properties );
		foreach ( $remaining as $property )
			$this->assertSame( $properties[$property], $instance->$property );
	}

}

?>