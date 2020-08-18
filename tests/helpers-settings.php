<?php
/**
 * PHPUnit tests for settings.
 */

/**
 * Class: Image_Tag_Test_Settings
 */
class Image_Tag_Test_Settings extends WP_UnitTestCase {

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
	 * @see $this->test__has_setting()
	 * @return array
	 */
	function data__has_setting__Image_Tag_Base() : array {
		$object  = Image_Tag::create( 'https://source.unsplash.com/random/800x600' );
		$object2 = Image_Tag::create( 'https://source.unsplash.com/random/800x600', null, array(
			'foo' => 73,
			'bar' => range( 1, 5 ),
		) );

		return array(
			'L' . __LINE__ => array( $object,  'foo', false ),
			'L' . __LINE__ => array( $object,  'bar', false ),
			'L' . __LINE__ => array( $object2, 'foo', true ),
			'L' . __LINE__ => array( $object2, 'bar', true ),
		);
	}

	/**
	 * @see $this->test__get_settings()
	 * @return array
	 */
	function data__get_settings__Image_Tag_Base() : array {
		$object  = Image_Tag::create( 'https://source.unsplash.com/random/800x600' );
		$object2 = Image_Tag::create( 'https://source.unsplash.com/random/800x600', null, array(
			'foo' => 73,
			'bar' => range( 1, 5 ),
		) );

		$data = array();

		$data[ 'L' . __LINE__ ] = array( $object, null, array() );
		$data[ 'L' . __LINE__ ] = array( $object2, array( 'foo' ), array(
			'foo' => 73,
		) );

		$data[ 'L' . __LINE__ ] = array( $object2, array(), array(
			'foo' => 73,
			'bar' => range( 1, 5 ),
		), 'edit' );

		$data[ 'L' . __LINE__ ] = array( $object, array( 'foo' ), 'error' );

		return $data;
	}

	/**
	 * @see $this->test__get_setting()
	 * @return array
	 */
	function data__get_setting__Image_Tag_Base() : array {
		$object  = Image_Tag::create( 'https://source.unsplash.com/random/800x600' );
		$object2 = Image_Tag::create( 'https://source.unsplash.com/random/800x600', null, array(
			'foo' => 73,
		) );

		$data = array();

		$data[ 'L' . __LINE__ ] = array( $object,  'foo', 'error' );
		$data[ 'L' . __LINE__ ] = array( $object2, 'foo', 73 );

		return $data;
	}

	/**
	 * @see $this->test__set_settings()
	 * @return array
	 */
	function data__set_settings__Image_Tag_Base() : array {
		$object = Image_Tag::create( 'https://source.unsplash.com/random/800x600' );

		$data = array();

		$data[ 'L' . __LINE__ ] = array( $object, array(
			'foo' => 73,
			'bar' => range( 0, 5 ),
		) );

		return $data;
	}

	/**
	 * @see $this->test__set_setting()
	 * @return array
	 */
	function data__set_setting() : array {
		$object = Image_Tag::create( 'https://source.unsplash.com/random/800x600' );

		$data = array();

		$data[ 'L' . __LINE__ ] = array( $object, 'foo', 73 );

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
	 * @dataProvider data__has_setting__Image_Tag_Base
	 *
	 * @covers Image_Tag_Helpers::has_setting()
	 *
	 * @param Image_Tag $object
	 * @param string $setting_key
	 * @param bool $expected
	 */
	function test__has_setting( Image_Tag $object, string $setting_key, bool $expected ) : void {
		$actual = $object->has_setting( $setting_key );
		$this->assertSame( $expected, $actual );
	}

	/**
	 * @dataProvider data__get_settings__Image_Tag_Base
	 *
	 * @covers Image_Tag_Helpers::get_settings()
	 *
	 * @param Image_Tag $object
	 * @param string[]|null $setting_keys
	 * @param array|string $expected
	 */
	function test__get_settings( Image_Tag $object, $setting_keys, $expected ) : void {

		# Hack to catch undefined index.
		if ( 'error' === $expected ) {
			set_error_handler( array( $this, 'notice_handler' ), E_NOTICE );
			$this->expectException( ErrorException::class );
		}

		$actual = $object->get_settings( $setting_keys );
		$this->assertSame( $expected, $actual );

		if ( 'error' === $expected )
			restore_error_handler();
	}

	/**
	 * @dataProvider data__get_setting__Image_Tag_Base
	 *
	 * @covers Image_Tag_Helpers::get_setting()
	 *
	 * @param Image_Tag $object
	 * @param string $key
	 * @param mixed $expected
	 */
	function test__get_setting( Image_Tag $object, $key, $expected ) : void {

		# Hack to catch undefined index.
		if ( 'error' === $expected ) {
			set_error_handler( array( $this, 'notice_handler' ), E_NOTICE );
			$this->expectException( ErrorException::class );
		}

		$actual = $object->get_setting( $key );
		$this->assertSame( $expected, $actual );

		if ( 'error' === $expected )
			restore_error_handler();
	}

	/**
	 * @dataProvider data__set_settings__Image_Tag_Base
	 *
	 * @covers Image_Tag_Helpers::set_settings()
	 *
	 * @param Image_Tag $object
	 * @param array $settings
	 * @param null|array $expected
	 */
	function test__set_settings( Image_Tag $object, array $settings, array $expected = null ) : void {
		if ( is_null( $expected ) )
			$expected = $settings;

		$return = $object->set_settings( $settings );
		$this->assertSame( $expected, $object->get_settings( null ) );

		$this->assertSame( $object, $return );
	}

	/**
	 * @dataProvider data__set_setting
	 *
	 * @covers Image_Tag_Helpers::set_setting()
	 *
	 * @param Image_Tag $object
	 * @param string $key
	 * @param mixed $value
	 * @param mixed $expected
	 */
	function test__set_setting( Image_Tag $object, string $key, $value, $expected = null ) : void {
		if ( is_null( $expected ) )
			$expected = $value;

		$return = $object->set_setting( $key, $value );
		$this->assertSame( $expected, $object->get_setting( $key ) );

		$this->assertSame( $object, $return );
	}

}