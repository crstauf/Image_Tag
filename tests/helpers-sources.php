<?php
/**
 * PHPUnit tests for sources.
 */

/**
 * Class: Image_Tag_Test_Sources
 */
class Image_Tag_Test_Sources extends WP_UnitTestCase {


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
	 * @see $this->test__has_source()
	 * @return array
	 */
	function data__has_source() : array {
		$object = Image_Tag::create( 'https://source.unsplash.com/800x600' );
		$object->add_source( 'https://source.unsplash.com/1600x1200', '800w' );

		$data = array();

		$data[ 'L' . __LINE__ ] = array(
			$object,
			'https://source.unsplash.com/800x600',
			true,
		);

		$data[ 'L' . __LINE__ ] = array(
			$object,
			'https://source.unsplash.com/1600x1200',
			true,
		);

		$data[ 'L' . __LINE__ ] = array(
			$object,
			'800w',
			true,
		);

		$data[ 'L' . __LINE__ ] = array(
			$object,
			'https://source.unsplash.com/400x300',
			false,
		);

		return $data;
	}

	/**
	 * @see $this->test__add_source()
	 * @return array
	 */
	function data__add_source() : array {
		$object = Image_Tag::create( 'https://source.unsplash.com/800x600' );

		$data = array();

		$data[ 'L' . __LINE__ ] = array(
			$object,
			'https://source.unsplash.com/400x300',
			'',
			false,
		);

		$data[ 'L' . __LINE__ ] = array(
			$object,
			'https://source.unsplash.com/400x300',
			'200w',
			true,
		);

		return $data;
	}

	/**
	 * @see $this->test__set_source()
	 * @return array
	 */
	function data__set_source() : array {
		$object = Image_Tag::create( 'https://source.unsplash.com/800x600' );

		$data = array();

		$data[ 'L' . __LINE__ ] = array(
			$object,
			'https://source.unsplash.com/400x300',
			'',
		);

		$data[ 'L' . __LINE__ ] = array(
			$object,
			'https://source.unsplash.com/200x150',
			'100w',
		);

		return $data;
	}

	/**
	 * @see $this->test__delete_source()
	 * @return array
	 */
	function data__delete_source() : array {
		$object = Image_Tag::create( 'https://source.unsplash.com/800x600' );

		$data = array();

		$data[ 'L' . __LINE__ ] = array(
			$object,
			'',
		);

		$object->add_source( 'https://source.unsplash.com/400x300', '800w' );
		$data[ 'L' . __LINE__ ] = array(
			$object,
			'https://source.unsplash.com/400x300',
		);

		$object->add_source( 'https://source.unsplash.com/200x150', '400w' );
		$data[ 'L' . __LINE__ ] = array(
			$object,
			'400w',
		);

		return $data;
	}

	/**
	 * @see $this->test__get_sources()
	 * @return array
	 */
	function data__get_sources() : array {
		$data = array();

		$object = Image_Tag::create( 'https://source.unsplash.com/800x600' );
		$data[ 'L' . __LINE__ ] = array(
			$object,
			array(
				'' => 'https://source.unsplash.com/800x600',
			),
		);

		$object = Image_Tag::create( 'https://source.unsplash.com/800x600' );
		$object->add_source( 'https://source.unsplash.com/400x300', '800w' );
		$data[ 'L' . __LINE__ ] = array(
			$object,
			array(
				'' => 'https://source.unsplash.com/800x600',
				'800w' => 'https://source.unsplash.com/400x300',
			),
		);

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
	 * @dataProvider data__has_source()
	 *
	 * @covers Image_Tag_Helpers::has_source
	 *
	 * @param Image_Tag $object
	 * @param string $source
	 * @param bool $expected
	 */
	function test__has_source( Image_Tag $object, string $source, bool $expected ) : void {
		$actual = $object->has_source( $source );
		$this->assertSame( $expected, $actual );
	}

	/**
	 * @dataProvider data__add_source()
	 *
	 * @covers Image_Tag_Helpers::add_source
	 *
	 * @param Image_Tag $object
	 * @param string $source
	 * @param string $descriptor
	 */
	function test__add_source( Image_Tag $object, string $source, string $descriptor = '', bool $expected = true ) : void {
		$returned = $object->add_source( $source, $descriptor );

		$this->assertSame( $expected, $object->has_source( $source ) && $object->has_source( $descriptor ) );
		$this->assertSame( $object, $returned );
	}

	/**
	 * @dataProvider data__set_source()
	 *
	 * @covers Image_Tag_Helpers::set_source
	 *
	 * @param Image_Tag $object
	 * @param string $source
	 * @param string $descriptor
	 */
	function test__set_source( Image_Tag $object, string $source, string $descriptor ) : void {
		$returned = $object->set_source( $source, $descriptor );

		$this->assertTrue( $object->has_source( $source ) );
		$this->assertTrue( $object->has_source( $descriptor ) );

		$this->assertSame( $object, $returned );
	}

	/**
	 * @dataProvider data__delete_source()
	 *
	 * @covers Image_Tag_Helpers::delete_source
	 *
	 * @param Image_Tag $object
	 * @param string $source
	 */
	function test__delete_source( Image_Tag $object, string $source ) : void {
		$this->assertTrue( $object->has_source( $source ) );

		$returned = $object->delete_source( $source );

		$this->assertSame( $object, $returned );

		if ( '' === $source ) {
			$this->assertTrue( $object->has_source( $source ) );
			return;
		}

		$this->assertFalse( $object->has_source( $source ) );
	}

	/**
	 * @dataProvider data__get_sources()
	 *
	 * @covers Image_Tag_Helpers::get_sources
	 *
	 * @param Image_Tag $object
	 * @param array $expected
	 */
	function test__get_sources( Image_Tag $object, array $expected ) : void {
		$actual = $object->get_sources();
		$this->assertSame( $expected, $actual );
	}

}