<?php
/**
 * PHPUnit tests for validation functions.
 */

/**
 * Class: Image_Tag_Test_Validation
 */
class Image_Tag_Test_Validation extends WP_UnitTestCase {


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
	 * @see $this->test__get_type()
	 * @return array
	 */
	function data__get_type() : array {
		return array(
			array( Image_Tag::create( 'https://source.unsplash.com/800x600' ), 'base' ),
		);
	}

	/**
	 * @see $this->test__is_type()
	 * @return array
	 */
	function data__is_type__Image_Tag_Base() : array {
		$data = array();

		$object = Image_Tag::create( 'https://source.unsplash.com/800x600' );
		$data[ 'L' . __LINE__ ] = array( $object, array( 'base' ), true );
		$data[ 'L' . __LINE__ ] = array( $object, array( 'simple' ), false );
		$data[ 'L' . __LINE__ ] = array( $object, array( 'base', 'default' ), true );

		return $data;
	}

	/**
	 * @see $this->test__is_valid()
	 * @return array
	 */
	function data__is_valid__Image_Tag_Base() : array {
		$data = array();

		$object = Image_Tag::create( 'https://source.unsplash.com/800x600' );
		$data[ 'L' . __LINE__ ] = array( $object, null, true );
		$data[ 'L' . __LINE__ ] = array( $object, array( 'base' ), true );
		$data[ 'L' . __LINE__ ] = array( $object, array( 'simple' ), false );
		$data[ 'L' . __LINE__ ] = array( $object, array( 'base', 'default' ), true );

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
	 * @dataProvider data__get_type
	 *
	 * @covers Image_Tag_Helpers::get_type()
	 *
	 * @param Image_Tag $object
	 * @param string $expected
	 */
	function test__get_type( Image_Tag $object, string $expected ) : void {
		$actual = $object->get_type();
		$this->assertSame( $expected, $actual );
	}

	/**
	 * @dataProvider data__is_type__Image_Tag_Base
	 *
	 * @covers Image_Tag_Helpers::is_type()
	 *
	 * @param Image_Tag $object
	 * @param string[] $test_types
	 * @param bool $expected
	 */
	function test__is_type( Image_Tag $object, array $test_types, bool $expected ) : void {
		$actual = $object->is_type( $test_types );
		$this->assertSame( $expected, $actual );
	}

	/**
	 * @dataProvider data__is_valid__Image_Tag_Base
	 *
	 * @covers Image_Tag_Helpers::is_type()
	 * @covers Image_Tag_Helpers::is_valid()
	 *
	 * @param Image_Tag $object
	 * @param null|string[] $test_types
	 * @param bool $expected
	 */
	function test__is_valid( Image_Tag $object, $test_types, bool $expected ) : void {
		$actual = $object->is_valid( $test_types );
		$this->assertSame( $expected, $actual );
	}

}