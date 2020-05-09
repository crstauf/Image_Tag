<?php

require_once 'abstract-properties-base.php';

/**
 * Tests for descendants of Image_Tag_Properties.
 */
abstract class Image_Tag_Properties_Tests extends Image_Tag_Properties_Base {

	/**
	 * Get the class name to run tests against.
	 *
	 * @return string
	 */
	abstract protected function class_name();

	/**
	 * Test Image_Tag_Properties::NAME constant value.
	 *
	 * @group constant
	 */
	abstract function test_name_constant();

	/**
	 * Test Image_Tag_Properties::DEFAULTS constant value.
	 *
	 * @group constant
	 */
	abstract function test_defaults_constant();

	/**
	 * Data provider for Image_Tag_Properties_Test::test_function_name().
	 *
	 * @see Image_Tag_Properties_Test::test_function_name()
	 * @return array[]
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
	 * Data provider for Image_Tag_Properties_Base::test__construct().
	 *
	 * @see Image_Tag_Properties_Base::test__construct()
	 * @return array[]
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


		$instance = $this->new_instance( $properties, $defaults );
		$data['self'] = array(
			$instance,
			array(),
			wp_parse_args( $properties, $defaults ),
		);

		return $data;
	}

	/**
	 * Data provider for Image_Tag_Properties_Base::test__set().
	 *
	 * @see Image_Tag_Properties_Base::test__set()
	 * @return array[]
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
	 * Data provider for Image_Tag_Properties_Base::test__get().
	 *
	 * @see Image_Tag_Properties_Base::test__get()
	 * @return array[]
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
	 * Data provider for Image_Tag_Properties_Base::test__isset().
	 *
	 * @see Image_Tag_Properties_Base::test__isset()
	 * @return array[]
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
	 * Data provider for Image_Tag_Properties_Base::test__unset().
	 *
	 * @see Image_Tag_Properties_Base::test__unset()
	 * @return array[]
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
				$this->new_instance( array(), array( 'id' => uniqid( __FUNCTION__ ) ) ),
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
	 * @return array[]
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

	/**
	 * Data provider for Image_Tag_Properties_Test::test_set().
	 *
	 * @see Image_Tag_Properties_Test::test_set()
	 * @return array[]
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

	/**
	 * Data provider for Image_Tag_Properties_Test::test_unset().
	 *
	 * @see Image_Tag_Properties_Test::test_unset()
	 * @return array[]
	 */
	function data_unset() {
		return array(
			'string' => array(
				array( 'id' => __FUNCTION__ ),
				'id',
			),
			'array' => array(
				array( 'id' => __FUNCTION__ ),
				array( 'id' ),
			),
			'multiple strings' => array(
				array(
					'foo' => uniqid(),
					'bar' => uniqid(),
					'zoo' => uniqid(),
				),
				array( 'foo', 'bar' ),
			),
			'multiple arrays' => array(
				array(
					'foo' => range(  1, 10, 2 ),
					'bar' => range( 10, 20, 2 ),
					'zoo' => uniqid(),
				),
				array( 'foo', 'bar' ),
			),
			'multiple objects' => array(
				array(
					'foo' => ( object ) range(  1, 10, 2 ),
					'bar' => ( object ) range( 10, 20, 2 ),
					'zoo' => uniqid(),
				),
				array( 'foo', 'bar' ),
			),
		);
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
	 * Data provider for Image_Tag_Properties_Test::test_isset().
	 *
	 * @see Image_Tag_Properties_Test::test_isset()
	 * @return array[]
	 */
	function data_isset() {
		return array(
			'string' => array(
				array( 'foo' => uniqid() ),
				'foo',
			),
			'false string' => array(
				array( 'foo' => uniqid() ),
				null,
				'bar',
			),
			'array' => array(
				array( 'foo' => range( 1, 5 ) ),
				'foo',
			),
			'null' => array(
				array( 'foo' => null ),
				null,
				'foo',
			),
			'object' => array(
				array( 'foo' => ( object ) range( 1, 5 ) ),
				'foo',
			),
			'multiple strings' => array(
				array(
					'foo' => uniqid(),
					'bar' => uniqid(),
				),
				array( 'foo', 'bar' ),
				'zoo',
			),
			'false multiple string' => array(
				array( 'foo' => uniqid() ),
				null,
				array( 'foo', 'bar' ),
			),
			'multiple arrays' => array(
				array(
					'foo' => range(  1, 10 ),
					'bar' => range( 10, 20 ),
				),
				array( 'foo', 'bar' ),
			),
			'false multiple arrays' => array(
				array( 'bar' => uniqid() ),
				null,
				array( 'foo', 'bar' ),
			),
			'multiple_objects' => array(
				array(
					'foo' => ( object ) range(  1, 10 ),
					'bar' => ( object ) range( 10, 20 ),
				),
				array( 'foo', 'bar' ),
			),
			'false multiple_objects' => array(
				array(
					'bar' => ( object ) range( 10, 20 ),
				),
				null,
				array( 'foo', 'bar' ),
			)
		);
	}

	/**
	 * Data provider for Image_Tag_Properties_Test::test_exists().
	 *
	 * @see Image_Tag_Properties_Test::test_exists()
	 * @uses static::data_isset()
	 * @return array[]
	 */
	function data_exists() {

		# Reuse isset test data.
		$data = $this->data_isset();

		# Adjust test to expect property with null value exists.
		$data['null'][1] = 'foo';
		$data['null'][2] = null;

		return $data;
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
	 * Data provider for Image_Tag_Properties_Test::test_exists().
	 *
	 * @see Image_Tag_Properties_Test::test_exists()
	 * @uses static::data_isset()
	 * @return array[]
	 */
	function data_get() {
		return array(
			'single' => array(
				array(
					'foo' => uniqid(),
					'bar' => uniqid(),
				),
				'foo',
			),
			'multiple' => array(
				array(
					'foo' => uniqid(),
					'bar' => uniqid(),
				),
				array( 'foo', 'bar' ),
			),
			'arrays' => array(
				array(
					'foo' => range( 1, 10 ),
					'bar' => range( 1, 20 ),
				),
				array( 'foo' ),
			),
			'null' => array(
				array(
					'foo' => uniqid(),
					'bar' => uniqid(),
				),
				null,
			),
		);
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
	 * Data provider for Image_Tag_Properties::offsetExists().
	 *
	 * @see Image_Tag_Properties::offsetExists()
	 * @return array[]
	 */
	function data_arrayAccess_exists() {
		return array(
			'null' => array(
				array(),
				null,
				'id',
			),
			'string' => array(
				array( 'foo' => uniqid() ),
				'foo',
				'bar',
			),
			'array' => array(
				array( 'foo' => range( 1, 5 ) ),
				'foo',
			),
			'object' => array(
				array( 'foo' => ( object ) range( 1, 5 ) ),
				'foo',
				'zoo',
			),
		);
	}

	/**
	 * Data provider for Image_Tag_Properties::offsetGet().
	 *
	 * @see Image_Tag_Properties::offsetGet()
	 * @return array[]
	 */
	function data_arrayAccess_get() {
		return array(
			'string' => array( array( 'foo' => __FUNCTION__ ) ),
			'array'  => array( array( 'foo' => range( 10, 20 ) ) ),
			'object' => array( array( 'foo' => ( object ) range( 20, 30 ) ) ),
			'int'    => array( array( 'foo' => mt_rand( 10, 30 ) ) ),
			'float'  => array( array( 'foo' => 3.1415 ) ),
		);
	}

	/**
	 * Data provider for Image_Tag_Properties::offsetSet().
	 *
	 * @see Image_Tag_Properties::offsetSet()
	 * @return array[]
	 */
	function data_arrayAccess_set() {
		return array(
			'string' => array(
				array(),
				'foo',
				uniqid(),
			),
			'string override' => array(
				array( 'foo' => __FUNCTION__ ),
				'foo',
				uniqid(),
			),
			'array' => array(
				array(),
				'foo',
				range( 1, 5 ),
			),
			'array override' => array(
				array( 'foo' => __FUNCTION__ ),
				'foo',
				range( 5, 10 ),
			),
			'object' => array(
				array(),
				'foo',
				( object ) range( 5, 10 ),
			),
			'object override' => array(
				array( 'foo' => range( 1, 5 ) ),
				'foo',
				( object ) range( 10, 20 ),
			),
		);
	}

	/**
	 * Data provider for Image_Tag_Properties::offsetUnset().
	 *
	 * @see Image_Tag_Properties::offsetUnset()
	 * @return array[]
	 */
	function data_arrayAccess_unset() {
		return array(
			'string' => array(
				array( 'foo' => uniqid() ),
				'foo',
			),
			'multiple' => array(
				array(
					'foo' => uniqid(),
					'bar' => uniqid(),
				),
				'foo',
			),
			'array' => array(
				array( 'foo' => range( 1, 5 ) ),
				'foo',
			),
			'object' => array(
				array(
					'foo' => ( object ) range( 10, 20 ),
					'bar' => uniqid(),
				),
				'foo',
			),
		);
	}

}