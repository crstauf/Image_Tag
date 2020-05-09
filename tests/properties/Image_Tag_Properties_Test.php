<?php

require_once 'Image_Tag_Properties_Tests.php';

/**
 * Testing of Image_Tag_Properties class.
 *
 * @coversDefaultClass Image_Tag_Properties
 * @group properties
 */
class Image_Tag_Properties_Test extends Image_Tag_Properties_Tests {

	protected function class_name() {
		return Image_Tag_Properties::class;
	}

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
			'no change' => array( 'foo', 'foo' ),
			'spaces'    => array( ' foo bar ', 'foo_bar' ),
			'space'     => array( 'foo bar', 'foo_bar' ),
			'dash'      => array( 'foo-bar', 'foo_bar' ),
			'tab'       => array( 'foo	bar', 'foo_bar' ),
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
	 */
	function data__construct() {
		$data = array(
			'empty' => array(
				array(),
				array(),
				array(),
			),
		);

		$properties = array(
			'foo' => 'bar',
			'rand' => mt_rand( 50, 200 ),
		);
		$data['properties'] = array(
			$properties,
			array(),
			$properties,
		);

		$defaults = array(
			'foo' => __FUNCTION__,
		);
		$data['defaults-overridden'] = array(
			$properties,
			$defaults,
			$properties,
		);

		$defaults['bar'] = range( 1, 5 );
		$data['defaults'] = array(
			$properties,
			$defaults,
			wp_parse_args( $properties, $defaults ),
		);


		$instance = new Image_Tag_Properties( $properties, $defaults );
		$data['object'] = array(
			$instance,
			array(),
			wp_parse_args( $properties, $defaults ),
		);

		return $data;
	}

	/**
	 * Data provider for Image_Tag_Properties_Tests::test__set().
	 *
	 * @see Image_Tag_Properties_Tests::test__set()
	 * @return array
	 *
	 * @todo add tests that override existing values
	 */
	function data__set() {
		return array(

			'string' => array(
				array(),
				'foo',
				'bar',
			),

			'array' => array(
				array(),
				'foo',
				range( 1, 5 ),
			),

			'float' => array(
				array(),
				'foo',
				3.1415,
			),

			'integer' => array(
				array(),
				'foo',
				2400,
			),

			'object' => array(
				array(),
				'foo',
				( object ) range( 5, 10 ),
			),

			'multi-dimensional array' => array(
				array(),
				'foo',
				array_fill( 0, 5, range( 10, 15 ) ),
			),

			'array of objects' => array(
				array(),
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
				'foo',
				null,
			),

			'string' => array(
				'foo',
				'bar',
			),

			'array' => array(
				'foo',
				range( 1, 5 ),
			),

			'float' => array(
				'foo',
				3.1415,
			),

			'integer' => array(
				'foo',
				2400,
			),

			'object' => array(
				'foo',
				( object ) range( 5, 10 ),
			),

			'multi-dimensional array' => array(
				'foo',
				array_fill( 0, 5, range( 10, 15 ) ),
			),

			'array of objects' => array(
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
				null,
				'foo',
				false,
			),

			'null' => array(
				array( 'foo' => null ),
				'foo',
				false,
			),

			'false' => array(
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
				array( 'foo' => $value ),
				'foo',
				true,
			);

		return $data;
	}

	/**
	 * Data provider for Image_Tag_Properties_Tests::test__unset().
	 *
	 * @see Image_Tag_Properties_Tests::test__unset()
	 * @return array
	 */
	function data__unset() {
		return array(
			'string' => array(
				array( 'id' => uniqid( __FUNCTION__ ) ),
				'id',
			),
			'array' => array(
				array( 'id' => range( 1, 5 ) ),
				'id',
			),
			'self' => array(
				new Image_Tag_Properties( array(), array( 'id' => uniqid( __FUNCTION__ ) ) ),
				'id',
			),
		);
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
	 * Data provider for Image_Tag_Properties_Test::test_add().
	 *
	 * @see Image_Tag_Properties_Test::test_add()
	 * @return array
	 */
	function data_add() {
		return array(
			array(
				array( 'id' => __FUNCTION__ ),
				'id',
				uniqid( __FUNCTION__ ),
				__FUNCTION__,
			),
			array(
				array(),
				'id',
				__FUNCTION__,
				__FUNCTION__,
			),
			array(
				array(),
				array(
					'id' => __FUNCTION__,
					'foo' => 'bar',
				),
				null,
				array(
					'id' => __FUNCTION__,
					'foo' => 'bar',
				),
			),
			array(
				array( 'id' => __FUNCTION__ ),
				array(
					'id' => uniqid( __FUNCTION__ ),
					'foo' => 'bar',
				),
				null,
				array(
					'id' => __FUNCTION__,
					'foo' => 'bar',
				),
			)
		);
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

	function data_set() {
		return array(
			'empty' => array(
				array(),
				'id',
				__FUNCTION__,
				__FUNCTION__,
			),
			'override string' => array(
				array( 'id' => uniqid( __FUNCTION__ ) ),
				'id',
				__FUNCTION__,
				__FUNCTION__,
			),
		);
	}

}