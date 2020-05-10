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

	function get_instance( ...$params ) {
		static $instance = null;

		if ( !empty( $params ) )
			$instance = null;

		if ( is_null( $instance ) )
			$instance = $this->new_instance( ...$params );

		return $instance;
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
	function test__construct( Image_Tag_Properties $instance, array $expected ) {
		$this->assertSame( $expected, $instance->get( null, 'edit' ) );
	}

	/**
	 * @param Image_Tag_Properties $instance
	 * @param string $property
	 * @param mixed $value
	 *
	 * @covers ::__set()
	 * @group magic
	 * @group set
	 *
	 * @dataProvider data__set
	 */
	function test__set( Image_Tag_Properties $instance, string $property, $value ) {
		$this->assertNotEquals( $value, $instance->$property );

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
	 * @param Image_Tag_Properties $instance
	 * @param string $property
	 * @param bool $expected
	 *
	 * @covers ::__isset()
	 * @group magic
	 * @group isset
	 *
	 * @dataProvider data__isset
	 */
	function test__isset( Image_Tag_Properties $instance, string $property, $expected ) {
		$expected
			? $this->assertTrue(  isset( $instance->$property ) )
			: $this->assertFalse( isset( $instance->$property ) );
	}

	/**
	 * @param Image_Tag_Properties $instance
	 * @param string $property
	 *
	 * @covers ::__unset()
	 * @group magic
	 * @group unset
	 *
	 * @dataProvider data__unset
	 */
	function test__unset( Image_Tag_Properties $instance, string $property ) {
		$this->assertTrue( isset( $instance->$property ) );

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
	 * @param Image_Tag_Properties $instance
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
	function test_add( Image_Tag_Properties $instance, $add_properties, $value, $expected ) {
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
	 * @param Image_Tag_Properties $instance
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
	function test_set( Image_Tag_Properties $instance, $set_properties, $value, $expected ) {
		if ( is_string( $set_properties ) ) {
			$this->assertNotEquals( $expected, $instance->$set_properties );

			$instance->set( $set_properties, $value );

			$this->assertSame( $value, $instance->$set_properties );
			return;
		}

		foreach ( $set_properties as $set_property => $set_value )
			$this->assertNotEquals( $set_value, $instance->$set_property );

		$instance->set( $set_properties );

		foreach ( $set_properties as $property => $value )
			$this->assertSame( $value, $instance->$property );

	}

	/**
	 * @param Image_Tag_Properties $instance
	 * @param string|array $unset_properties
	 *
	 * @covers ::unset()
	 * @group instance
	 * @group unset
	 *
	 * @dataProvider data_unset
	 */
	function test_unset( Image_Tag_Properties $instance, $unset_properties ) {
		$properties = $instance->get( null, 'edit' );

		# Check properties are set.
		foreach ( ( array ) $unset_properties as $property )
			$this->assertNotNull( $instance->$property );

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
	 * @param Image_Tag_Properties $instance
	 * @param string|array $isset_properties
	 * @param array $not_isset_properties
	 *
	 * @covers ::isset()
	 * @group instance
	 * @group exists
	 *
	 * @dataProvider data_isset
	 */
	function test_isset( Image_Tag_Properties $instance, $isset_properties, $not_isset_properties = null ) {
		if ( !is_null( $isset_properties ) )
			$this->assertTrue( $instance->isset( $isset_properties ) );

		if ( !is_null( $not_isset_properties ) )
			$this->assertFalse( $instance->isset( $not_isset_properties ) );
	}

	/**
	 * @param Image_Tag_Properties $instance
	 * @param string|array $exist_properties
	 * @param array $not_exist_properties
	 * @covers ::exists()
	 * @group instance
	 * @group exists
	 *
	 * @dataProvider data_exists
	 */
	function test_exists( Image_Tag_Properties $instance, $exist_properties, $not_exist_properties = null ) {
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
	 * @param Image_Tag_Properties $instance
	 * @param string|array $get_properties
	 * @param mixed $expected
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
	function test_get( Image_Tag_Properties $instance, $get_properties, $expected ) {
		if ( is_string( $get_properties ) ) {
			$this->assertSame( $expected, $instance->get( $get_properties, 'edit' ) );
			return;
		}

		if ( is_null( $get_properties ) ) {
			$this->assertSame( $expected, $instance->get( null, 'edit' ) );
			return;
		}

		$actual = array();

		foreach ( $get_properties as $property )
			$actual[$property] = $instance->get( $property, 'edit' );

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
	 * @param Image_Tag_Properties $instance
	 * @param string|array $exist_properties
	 * @param array $not_exist_properties
	 *
	 * @covers ::offsetExists()
	 * @group instance
	 * @group arrayaccess
	 *
	 * @dataProvider data_arrayAccess_exists
	 */
	function test_arrayAccess_exists( Image_Tag_Properties $instance, $exist_properties, $not_exist_properties = null ) {
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
	 * @covers ::offsetGet()
	 * @group instance
	 * @group arrayaccess
	 *
	 * @dataProvider data_arrayAccess_get
	 */
	function test_arrayAccess_get( array $properties ) {
		$instance = $this->new_instance( $properties );

		foreach ( $properties as $property => $value )
			$this->assertSame( $value, $instance[$property] );
	}

	/**
	 * @param Image_Tag_Properties $instance
	 * @param string $set_property
	 * @param mixed $set_value
	 *
	 * @covers ::offsetSet()
	 * @group instance
	 * @group arrayaccess
	 *
	 * @dataProvider data_arrayAccess_set
	 */
	function test_arrayAccess_set( Image_Tag_Properties $instance, string $set_property, $set_value ) {
		$this->assertNotEquals( $set_value, $instance->$set_property );

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