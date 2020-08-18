<?php
/**
 * PHPUnit tests for attributes.
 */

/**
 * Class: Image_Tag_Test_Attributes
 */
class Image_Tag_Test_Attributes extends WP_UnitTestCase {

	/**
	 * Hack to support expecting notices.
	 *
	 * @throws ErrorException
	 * @return false
	 */
	function notice_handler( $errno, $errstr, $errfile, $errline ) : bool {
		if ( preg_match_all( '/Undefined index: .*/', $errstr, $output_array ) )
			throw new ErrorException( $errstr, 0, $errno, $errfile, $errline );

		return false;
	}


	/*
	########     ###    ########    ###
	##     ##   ## ##      ##      ## ##
	##     ##  ##   ##     ##     ##   ##
	##     ## ##     ##    ##    ##     ##
	##     ## #########    ##    #########
	##     ## ##     ##    ##    ##     ##
	########  ##     ##    ##    ##     ##
	*/

	/**
	 * @see $this->test__has_attribute()
	 * @return array
	 */
	function data__has_attribute__Image_Tag_Base() : array {
		$object  = Image_Tag::create( 'https://source.unsplash.com/random/800x600' );
		$object2 = Image_Tag::create( 'https://source.unsplash.com/random/800x600', array(
			'class' => array( 'foo', 'bar' ),
		) );

		return array(
			'L' . __LINE__ => array( $object,  'src',    true ),
			'L' . __LINE__ => array( $object,  'class', false ),
			'L' . __LINE__ => array( $object2, 'class',  true ),
		);
	}

	/**
	 * @see $this->test__get_attributes()
	 * @return array
	 */
	function data__get_attributes__Image_Tag_Base() : array {
		$object  = Image_Tag::create( 'https://source.unsplash.com/random/800x600' );
		$object2 = Image_Tag::create( 'https://source.unsplash.com/random/800x600', array(
			'class' => array( 'foo', 'bar' ),
		) );

		$data = array();

		$data[ 'L' . __LINE__ ] = array( $object, null, array(
			'alt' => '',
			'src' => 'https://source.unsplash.com/random/800x600',
		) );

		$data[ 'L' . __LINE__ ] = array( $object, array( 'src' ), array(
			'src' => 'https://source.unsplash.com/random/800x600',
		) );

		$data[ 'L' . __LINE__ ] = array( $object2, array(), array(
			'alt' => '',
			'class' => array( 'foo', 'bar' ),
			'src' => 'https://source.unsplash.com/random/800x600',
		), 'edit' );

		$data[ 'L' . __LINE__ ] = array( $object2, array(
			'class',
			'src',
		), array(
			'class' => array( 'foo', 'bar' ),
			'src' => 'https://source.unsplash.com/random/800x600',
		), 'edit' );

		$data[ 'L' . __LINE__ ] = array( $object, array( 'title' ), 'error' );

		return $data;
	}

	/**
	 * @see $this->test__get_attribute()
	 * @return array
	 */
	function data__get_attribute__Image_Tag_Base() : array {
		$object = Image_Tag::create( 'https://source.unsplash.com/random/800x600' );

		$data = array();

		$data[ 'L' . __LINE__ ] = array( $object, 'src', 'https://source.unsplash.com/random/800x600' );
		$data[ 'L' . __LINE__ ] = array( $object, 'alt', '' ); // Test default value.
		$data[ 'L' . __LINE__ ] = array( $object, 'class', 'error' );

		$object = Image_Tag::create( 'https://source.unsplash.com/random/800x600' );
		$object->set_attribute( 'class', array( 'foo', 'bar' ) );

		$data[ 'L' . __LINE__ ] = array( $object, 'class', 'foo bar', 'view' );
		$data[ 'L' . __LINE__ ] = array( $object, 'class', array( 'foo', 'bar' ), 'edit' );

		$object = Image_Tag::create( 'https://source.unsplash.com/random/800x600' );
		$object->set_attribute( 'style', array( 'color: #000', 'font-size: 10px' ) );

		$data[ 'L' . __LINE__ ] = array( $object, 'style', 'color: #000; font-size: 10px', 'view' );
		$data[ 'L' . __LINE__ ] = array( $object, 'style', array( 'color: #000', 'font-size: 10px' ), 'edit' );

		$object = Image_Tag::create( 'https://source.unsplash.com/random/800x600' );
		$object->set_attribute( 'sizes', array( '50vw', '100vw' ) );

		$data[ 'L' . __LINE__ ] = array( $object, 'sizes', '50vw, 100vw', 'view' );
		$data[ 'L' . __LINE__ ] = array( $object, 'sizes', array( '50vw', '100vw' ), 'edit' );

		return $data;
	}

	/**
	 * @see $this->test__set_attributes()
	 * @return array
	 */
	function data__set_attributes() : array {
		$data = array();

		$data[ 'L' . __LINE__ ] = array( Image_Tag::create( 'https://source.unsplash.com/random/800x600' ), array(
			'class' => array( 'foo;', ',bar' ),
			'title' => __METHOD__,
		), array(
			'alt' => '',
			'src' => 'https://source.unsplash.com/random/800x600',
			'class' => array( 'foo', 'bar' ),
			'title' => __METHOD__,
		) );

		$data[ 'L' . __LINE__ ] = array( Image_Tag::create( 'https://source.unsplash.com/random/800x600' ), array(
			'alt' => __FUNCTION__,
			'title' => __METHOD__,
		), array(
			'alt' => __FUNCTION__,
			'src' => 'https://source.unsplash.com/random/800x600',
			'title' => __METHOD__,
		) );

		return $data;
	}

	/**
	 * @see $this->test__set_attribute()
	 * @return array
	 */
	function data__set_attribute() : array {
		$object = Image_Tag::create( 'https://source.unsplash.com/random/800x600' );
		$data = array();

		$data[ 'L' . __LINE__ ] = array( $object, 'alt', __FUNCTION__ );
		$data[ 'L' . __LINE__ ] = array( $object, 'alt', __METHOD__ );
		$data[ 'L' . __LINE__ ] = array( $object, 'class', array( 'foo, ', ' bar' ), array( 'foo', 'bar' ) );
		$data[ 'L' . __LINE__ ] = array( $object, 'sizes', '50vw, 100vw', array( '50vw', '100vw' ) );
		$data[ 'L' . __LINE__ ] = array( $object, 'class', null, array() );

		return $data;
	}


	/*
	######## ########  ######  ########  ######
	   ##    ##       ##    ##    ##    ##    ##
	   ##    ##       ##          ##    ##
	   ##    ######    ######     ##     ######
	   ##    ##             ##    ##          ##
	   ##    ##       ##    ##    ##    ##    ##
	   ##    ########  ######     ##     ######
	*/

	/**
	 * @dataProvider data__has_attribute__Image_Tag_Base
	 *
	 * @covers Image_Tag_Helpers::has_attribute()
	 *
	 * @param Image_Tag $object
	 * @param string $attribute_name
	 * @param bool $expected
	 */
	function test__has_attribute( Image_Tag $object, string $attribute_name, bool $expected ) : void {
		$actual = $object->has_attribute( $attribute_name );
		$this->assertSame( $expected, $actual );
	}

	/**
	 * @dataProvider data__get_attributes__Image_Tag_Base
	 *
	 * @covers Image_Tag_Helpers::get_attributes()
	 *
	 * @param Image_Tag $object
	 * @param string[]|null $attribute_names
	 * @param array|string $expected
	 * @param string $context
	 */
	function test__get_attributes( Image_Tag $object, $attribute_names, $expected, string $context = 'view' ) : void {

		# Hack to catch undefined index.
		if ( 'error' === $expected ) {
			set_error_handler( array( $this, 'notice_handler' ), E_NOTICE );
			$this->expectException( ErrorException::class );
		}

		$actual = $object->get_attributes( $attribute_names, $context );
		$this->assertSame( $expected, $actual );

		if ( 'error' === $expected )
			restore_error_handler();
	}

	/**
	 * @dataProvider data__get_attribute__Image_Tag_Base
	 *
	 * @covers Image_Tag_Helpers::get_attribute()
	 *
	 * @param Image_Tag $object
	 * @param string $attribute_name
	 * @param mixed $expected
	 * @param string $context
	 */
	function test__get_attribute( Image_Tag $object, string $attribute_name, $expected, string $context = 'view' ) : void {

		# Hack to catch undefined index.
		if ( 'error' === $expected ) {
			set_error_handler( array( $this, 'notice_handler' ), E_NOTICE );
			$this->expectException( ErrorException::class );
		}

		$actual = $object->get_attribute( $attribute_name, $context );
		$this->assertSame( $expected, $actual );

		if ( 'error' === $expected )
			restore_error_handler();
	}

	/**
	 * @dataProvider data__set_attributes
	 *
	 * @covers Image_Tag_Helpers::set_attributes()
	 *
	 * @param Image_Tag $object
	 * @param array $attributes
	 */
	function test__set_attributes( Image_Tag $object, array $attributes, array $expected = null ) : void {
		if ( is_null( $expected ) )
			$expected = $attributes;

		$return = $object->set_attributes( $attributes );
		$this->assertSame( $expected, $object->get_attributes( null, 'edit' ) );

		$this->assertSame( $object, $return );
	}

	/**
	 * @dataProvider data__set_attribute
	 *
	 * @covers Image_Tag_Helpers::set_attribute()
	 *
	 * @param Image_Tag $object
	 * @param string $attribute_name
	 * @param mixed $attribute_value
	 * @param mixed $expected
	 * @param string $context
	 */
	function test__set_attribute( Image_Tag $object, string $attribute_name, $attribute_value, $expected = null, string $context = 'edit' ) : void {
		if ( is_null( $expected ) )
			$expected = $attribute_value;

		$return = $object->set_attribute( $attribute_name, $attribute_value );
		$this->assertSame( $expected, $object->get_attribute( $attribute_name, $context ) );

		$this->assertSame( $object, $return );
	}

}