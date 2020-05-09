<?php

require_once 'Image_Tag_Properties_Tests.php';

/**
 * Testing of Image_Tag_Properties class.
 *
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
	 */
	function data__construct() {
		$data = array(
			'empty' => array(
				Image_Tag_Properties::class,
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
			Image_Tag_Properties::class,
			$properties,
			array(),
			$properties,
		);

		$defaults = array(
			'foo' => __FUNCTION__,
		);
		$data['defaults-overridden'] = array(
			Image_Tag_Properties::class,
			$properties,
			$defaults,
			$properties,
		);

		$defaults['bar'] = range( 1, 5 );
		$data['defaults'] = array(
			Image_Tag_Properties::class,
			$properties,
			$defaults,
			wp_parse_args( $properties, $defaults ),
		);


		$instance = new Image_Tag_Properties( $properties, $defaults );
		$data['object'] = array(
			Image_Tag_Properties::class,
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

	/**
	 * Data provider for Image_Tag_Properties_Tests::test__unset().
	 *
	 * @see Image_Tag_Properties_Tests::test__unset()
	 * @return array
	 */
	function data__unset() {
		return array(
			'string' => array(
				Image_Tag_Properties::class,
				array( 'id' => uniqid( __FUNCTION__ ) ),
				'id',
			),
			'array' => array(
				Image_Tag_Properties::class,
				array( 'id' => range( 1, 5 ) ),
				'id',
			),
			'self' => array(
				Image_Tag_Properties::class,
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
				Image_Tag_Properties::class,
				array( 'id' => __FUNCTION__ ),
				'id',
				uniqid( __FUNCTION__ ),
				__FUNCTION__,
			),
			array(
				Image_Tag_Properties::class,
				array(),
				'id',
				__FUNCTION__,
				__FUNCTION__,
			),
			array(
				Image_Tag_Properties::class,
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
				Image_Tag_Properties::class,
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
				Image_Tag_Properties::class,
				array(),
				'id',
				__FUNCTION__,
				__FUNCTION__,
			),
			'override string' => array(
				Image_Tag_Properties::class,
				array( 'id' => uniqid( __FUNCTION__ ) ),
				'id',
				__FUNCTION__,
				__FUNCTION__,
			),
		);
	}

}