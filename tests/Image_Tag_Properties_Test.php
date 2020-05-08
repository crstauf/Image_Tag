<?php

require_once 'Image_Tag_Properties_Tests.php';

/**
 * @coversDefaultClass Image_Tag_Properties
 * @group properties
 */
class Image_Tag_Properties_Test extends Image_Tag_Properties_Tests {

	/**
	 * @group constant
	 * @group defaults
	 */
	function test_defaults_constant() {
		$this->assertEmpty(   Image_Tag_Properties::DEFAULTS );
		$this->assertIsArray( Image_Tag_Properties::DEFAULTS );
	}

	/**
	 * Data provider for Image_Tag_Properties_Test::test_function_name().
	 *
	 * @see Image_Tag_Properties_Test::test_function_name()
	 * @return array
	 */
	function data_function_name() {
		return array(
			'no change' => array( Image_Tag_Properties::class, 'foo', 'foo' ),
			'spaces'    => array( Image_Tag_Properties::class, ' foo bar ', 'foo_bar' ),
			'space'     => array( Image_Tag_Properties::class, 'foo bar', 'foo_bar' ),
			'dash'      => array( Image_Tag_Properties::class, 'foo-bar', 'foo_bar' ),
			'tab'       => array( Image_Tag_Properties::class, 'foo	bar', 'foo_bar' ),
		);
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
	 * Data provider for Image_Tag_Properties_Tests::test__construct().
	 *
	 * @see Image_Tag_Properties_Tests::test__construct()
	 * @return array
	 *
	 * @todo add more types
	 */
	function data__construct() {
		$this->markTestIncomplete();
		return array(
			'empty' => array(
				Image_Tag_Properties::class,
				array(),
				array(),
				array(),
			),
		);
	}

	/**
	 * Data provider for Image_Tag_Properties_Tests::test__set().
	 *
	 * @see Image_Tag_Properties_Tests::test__set()
	 * @return array
	 */
	function data__set() {
		return array(

			'string' => array(
				Image_Tag_Properties::class,
				'foo',
				'bar',
			),

			'array' => array(
				Image_Tag_Properties::class,
				'foo',
				range( 1, 5 ),
			),

			'float' => array(
				Image_Tag_Properties::class,
				'foo',
				3.1415,
			),

			'integer' => array(
				Image_Tag_Properties::class,
				'foo',
				2400,
			),

			'object' => array(
				Image_Tag_Properties::class,
				'foo',
				( object ) range( 5, 10 ),
			),

			'multi-dimensional array' => array(
				Image_Tag_Properties::class,
				'foo',
				array_fill( 0, 5, range( 10, 15 ) ),
			),

			'array of objects' => array(
				Image_Tag_Properties::class,
				'foo',
				array_fill( 0, 5, ( object ) range( 15, 20 ) ),
			),

		);
	}

	/**
	 * Data provider for Image_Tag_Properties_Tests::test__get().
	 *
	 * @see Image_Tag_Properties_Tests::test__get()
	 * @return array
	 */
	function data__get() {
		return array(

			'empty' => array(
				Image_Tag_Properties::class,
				'foo',
				null,
			),

			'string' => array(
				Image_Tag_Properties::class,
				'foo',
				'bar',
			),

			'array' => array(
				Image_Tag_Properties::class,
				'foo',
				range( 1, 5 ),
			),

			'float' => array(
				Image_Tag_Properties::class,
				'foo',
				3.1415,
			),

			'integer' => array(
				Image_Tag_Properties::class,
				'foo',
				2400,
			),

			'object' => array(
				Image_Tag_Properties::class,
				'foo',
				( object ) range( 5, 10 ),
			),

			'multi-dimensional array' => array(
				Image_Tag_Properties::class,
				'foo',
				array_fill( 0, 5, range( 10, 15 ) ),
			),

			'array of objects' => array(
				Image_Tag_Properties::class,
				'foo',
				array_fill( 0, 5, ( object ) range( 15, 20 ) ),
			),

		);
	}

	/**
	 * Data provider for Image_Tag_Properties_Tests::test__isset().
	 *
	 * @see Image_Tag_Properties_Tests::test__isset()
	 * @return array
	 */
	function data__isset() {
		$data = array(

			'empty' => array(
				Image_Tag_Properties::class,
				null,
				'foo',
				false,
			),

			'null' => array(
				Image_Tag_Properties::class,
				array( 'foo' => null ),
				'foo',
				false,
			),

			'false' => array(
				Image_Tag_Properties::class,
				array( 'foo' => uniqid( __FUNCTION__ ) ),
				'bar',
				false,
			),

		);

		# Create truths.
		foreach ( array(

			'string'  => 'bar',
			'array'   => range( 1, 5 ),
			'float'   => 3.1415,
			'integer' => 2400,
			'object'  => ( object ) range( 5, 10 ),

			'multi-dimesional array' => array_fill( 0, 5, range( 10, 15 ) ),
			'array of objects'       => array_fill( 0, 5, ( object ) range( 15, 20 ) ),

		) as $name => $value )
			$data[$name] = array(
				Image_Tag_Properties::class,
				array( 'foo' => $value ),
				'foo',
				true,
			);

		return $data;
	}

}