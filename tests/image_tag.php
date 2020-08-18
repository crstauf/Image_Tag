<?php
/**
 * PHPUnit tests for Image_Tag abstract class.
 */

/**
 * Class: Image_Tag_Test_Abstract
 * @see Image_Tag
 */
class Image_Tag_Test_Abstract extends WP_UnitTestCase {


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
	 * @see $this->test__create()
	 * @return array
	 */
	function data__create__unknown() : array {
		return array(
			'L' . __LINE__ => array(
				'fjklfew',
				null,
				null,
				'error'
			),
		);
	}

	/**
	 * @see $this->test__create()
	 * @return array
	 */
	function data__create__Image_Tag_Base() : array {
		$data = array();

		$object = new Image_Tag_Base( array(
			'src' => 'https://source.unsplash.com/800x600'
		) );

		$data[ 'L' . __LINE__ ] = array(
			'https://source.unsplash.com/800x600',
			null,
			null,
			$object,
		);

		return $data;
	}

	/**
	 * @see $this->test__toString()
	 * @return array
	 */
	function data__toString__unknown() : array {
		return array(
			array(
				new Image_Tag_Base(),
				'',
				"/\nWarning: The \<code\>src\<\/code\> attribute is required\..*/",
			),
		);
	}

	/**
	* @see $this->test__toString()
	* @return array
	*/
	function data__toString__Image_Tag_Base() : array {
		return array(
			array(
				Image_Tag::create( 'https://source.unsplash.com/800x600' ),
				'<img alt="" src="https://source.unsplash.com/800x600" />',
			),
		);
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
	 * Test Image_Tag::BLANK value.
	 */
	function test__constant_blank() : void {
		$this->assertSame( 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==', Image_Tag::BLANK );
	}

	/**
	 * @dataProvider data__create__unknown
	 * @dataProvider data__create__Image_Tag_Base
	 *
	 * @covers Image_Tag::create()
	 * @covers Image_Tag::__construct()
	 *
	 * @param string $source
	 * @param null|array $attributes
	 * @param null|array $settings
	 * @param Image_Tag|string $expected_class
	 */
	function test__create( string $source, $attributes, $settings, $expected ) : void {
		if ( 'error' === $expected ) {
			$this->expectOutputRegex( "/\nWarning: Unable to determine image type from source.*/" );
			$expected = new Image_Tag_Base( null, null );
		}

		$actual = Image_Tag::create( $source, $attributes, $settings );
		$this->assertEquals( $expected, $actual );
		$this->assertNotSame( $expected, $actual );
	}

	/**
	 * @dataProvider data__toString__unknown
	 * @dataProvider data__toString__Image_Tag_Base
	 *
	 * @covers Image_Tag::__toString()
	 *
	 * @param Image_Tag $object
	 * @param string $expected
	 * @param string $expected_output_regex
	 */
	function test__toString( Image_Tag $object, string $expected, string $expected_output_regex = '' ) : void {
		if ( '' === $expected )
			$this->expectOutputRegex( $expected_output_regex );

		$actual = $object->__toString();
		$this->assertEquals( $expected, $actual );
	}
}