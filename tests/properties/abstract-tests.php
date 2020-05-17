<?php

require_once 'abstract-base.php';

/**
 * Tests for descendants of Image_Tag_Properties.
 */
abstract class Image_Tag_Properties_Tests extends Image_Tag_Properties_Base {

	const DEFAULTS = array();

	/**
	 * Get the class name to run tests against.
	 *
	 * @return string
	 */
	abstract protected function class_name();

	/**
	 * Test class is child of Image_Tag_Properties_Abstract.
	 *
	 * @group plugin
	 */
	function test_ancestry() {
		$this->assertTrue( is_subclass_of( $this->get_instance(), Image_Tag_Properties_Abstract::class ) );
	}

	/**
	 * Test Image_Tag_Properties::NAME constant value.
	 *
	 * @group constant
	 */
	abstract function test_name_constant();

	/**
	 * Test Image_Tag_Properties::DEFAULTS constant.
	 *
	 * @group constant
	 */
	function test_defaults_constant() {
		$this->assertSame( static::DEFAULTS, constant( $this->class_name() . '::DEFAULTS' ) );
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
	 * Data provider for Image_Tag_Properties_Base::test_function_name().
	 *
	 * @see Image_Tag_Properties_Base::test_function_name()
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
				static::DEFAULTS,
				static::DEFAULTS,
			),
		);

		$properties = array(
			'foo' => 'bar',
			'rand' => mt_rand( 50, 200 ),
		);
		$data['properties'] = array(
			$properties,
			static::DEFAULTS,
			wp_parse_args( $properties, static::DEFAULTS ),
		);

		$defaults = wp_parse_args( array(
			'foo' => __FUNCTION__,
		), static::DEFAULTS );
		$data['defaults-overridden'] = array(
			$properties,
			$defaults,
			wp_parse_args( $properties, static::DEFAULTS ),
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
	 */
	function data__set() {
		return array(

			'string' => array(
				$this->get_instance(),
				'foo',
				'bar',
			),

			'array' => array(
				$this->get_instance(),
				'foo',
				range( 1, 5 ),
			),

			'float' => array(
				$this->get_instance(),
				'foo',
				3.1415,
			),

			'integer' => array(
				$this->get_instance(),
				'foo',
				2400,
			),

			'object' => array(
				$this->get_instance(),
				'foo',
				( object ) range( 5, 10 ),
			),

			'multi-dimensional array' => array(
				$this->get_instance(),
				'foo',
				array_fill( 0, 5, range( 10, 15 ) ),
			),

			'array of objects' => array(
				$this->get_instance(),
				'foo',
				array_fill( 0, 5, ( object ) range( 15, 20 ) ),
			),

			'override string' => array(
				$this->get_instance( array( 'foo' => uniqid() ) ),
				'foo',
				__FUNCTION__,
			),

			'override array' => array(
				$this->get_instance( array( 'foo' => range( 1, 5 ) ) ),
				'foo',
				range( 6, 10 ),
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

			'properties' => array(
				'properties',
				null,
			),

			'defaults' => array(
				'defaults',
				null,
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
				$this->new_instance( array() ),
				'foo',
				false,
			),

			'null' => array(
				$this->new_instance( array( 'foo' => null ) ),
				'foo',
				false,
			),

			'false' => array(
				$this->new_instance( array( 'foo' => uniqid( __FUNCTION__ ) ) ),
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
				$this->new_instance( array( 'foo' => $value ) ),
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
				$this->new_instance( array( 'foo' => uniqid( __FUNCTION__ ) ) ),
				'foo',
			),

			'string default' => array(
				$this->new_instance( array(), array( 'foo' => __FUNCTION__ ) ),
				'foo',
				__FUNCTION__,
			),

			'array' => array(
				$this->new_instance( array( 'foo' => range( 1, 5 ) ) ),
				'foo',
			),

			'array default' => array(
				$this->new_instance( array( 'foo' => range( 1, 5 ) ), array( 'foo' => array() ) ),
				'foo',
				array(),
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
	 * Data provider for Image_Tag_Properties_Base::test_add().
	 *
	 * @see Image_Tag_Properties_Base::test_add()
	 * @return array[]
	 */
	function data_add() {
		return array(

			array(
				$this->new_instance( array( 'id' => __FUNCTION__ ) ),
				'id',
				uniqid( __FUNCTION__ ),
				__FUNCTION__,
			),

			array(
				$this->new_instance( array() ),
				'id',
				__FUNCTION__,
				__FUNCTION__,
			),

			array(
				$this->new_instance(),
				'foo',
				range( 1, 5 ),
			),

			array(
				$this->new_instance( array() ),
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
				$this->new_instance( array( 'id' => __FUNCTION__ ) ),
				array(
					'id' => uniqid( __FUNCTION__ ),
					'foo' => 'bar',
				),
				null,
				array(
					'id' => __FUNCTION__,
					'foo' => 'bar',
				),
			),

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
	 * Data provider for Image_Tag_Properties_Base::test_set().
	 *
	 * @see Image_Tag_Properties_Base::test_set()
	 * @return array[]
	 */
	function data_set() {
		return array(

			'empty' => array(
				$this->new_instance(),
				'foo',
				array(),
			),

			'string' => array(
				$this->new_instance(),
				'foo',
				__FUNCTION__,
			),

			'override string' => array(
				$this->new_instance( array( 'foo' => uniqid( __FUNCTION__ ) ) ),
				'foo',
				__FUNCTION__,
			),

			'multiple' => array(
				$this->new_instance( array( 'foo' => uniqid( __FUNCTION__ ) ) ),
				array(
					'foo' => __FUNCTION__,
					'bar' => __METHOD__,
				),
			),

		);
	}

	/**
	 * Data provider for Image_Tag_Properties_Base::test_unset().
	 *
	 * @see Image_Tag_Properties_Base::test_unset()
	 * @return array[]
	 */
	function data_unset() {
		return array(

			'string' => array(
				$this->new_instance( array( 'id' => __FUNCTION__ ) ),
				'id',
			),

			'array' => array(
				$this->new_instance( array( 'id' => __FUNCTION__ ) ),
				array( 'id' ),
			),

			'multiple strings' => array(
				$this->new_instance( array(
					'foo' => uniqid(),
					'bar' => uniqid(),
					'zoo' => uniqid(),
				) ),
				array( 'foo', 'bar' ),
			),

			'multiple arrays' => array(
				$this->new_instance( array(
					'foo' => range(  1, 10, 2 ),
					'bar' => range( 10, 20, 2 ),
					'zoo' => uniqid(),
				) ),
				array( 'foo', 'bar' ),
			),

			'multiple objects' => array(
				$this->new_instance( array(
					'foo' => ( object ) range(  1, 10, 2 ),
					'bar' => ( object ) range( 10, 20, 2 ),
					'zoo' => uniqid(),
				) ),
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
	 * Data provider for Image_Tag_Properties_Base::test_isset().
	 *
	 * @see Image_Tag_Properties_Base::test_isset()
	 * @return array[]
	 */
	function data_isset() {
		return array(

			'string' => array(
				$this->get_instance( array( 'foo' => uniqid() ) ),
				'foo',
			),

			'false string' => array(
				$this->get_instance(),
				null,
				'bar',
			),

			'array' => array(
				$this->new_instance( array( 'foo' => range( 1, 5 ) ) ),
				'foo',
			),

			'null' => array(
				$this->new_instance( array( 'foo' => null ) ),
				null,
				'foo',
			),

			'object' => array(
				$this->new_instance( array( 'foo' => ( object ) range( 1, 5 ) ) ),
				'foo',
			),

			'multiple strings' => array(
				$this->new_instance( array(
					'foo' => uniqid(),
					'bar' => uniqid(),
				) ),
				array( 'foo', 'bar' ),
				'zoo',
			),

			'false multiple string' => array(
				$this->new_instance( array( 'foo' => uniqid() ) ),
				null,
				array( 'foo', 'bar' ),
			),

			'multiple arrays' => array(
				$this->new_instance( array(
					'foo' => range(  1, 10 ),
					'bar' => range( 10, 20 ),
				) ),
				array( 'foo', 'bar' ),
			),

			'false multiple arrays' => array(
				$this->new_instance( array( 'bar' => uniqid() ) ),
				null,
				array( 'foo', 'bar' ),
			),

			'multiple_objects' => array(
				$this->new_instance( array(
					'foo' => ( object ) range(  1, 10 ),
					'bar' => ( object ) range( 10, 20 ),
				) ),
				array( 'foo', 'bar' ),
			),

			'false multiple_objects' => array(
				$this->new_instance( array(
					'bar' => ( object ) range( 10, 20 ),
				) ),
				null,
				array( 'foo', 'bar' ),
			),

		);
	}

	/**
	 * Data provider for Image_Tag_Properties_Base::test_exists().
	 *
	 * @see Image_Tag_Properties_Base::test_exists()
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
	   ###    ########  ########     ########  #######
	  ## ##   ##     ## ##     ##       ##    ##     ##
	 ##   ##  ##     ## ##     ##       ##    ##     ##
	##     ## ##     ## ##     ##       ##    ##     ##
	######### ##     ## ##     ##       ##    ##     ##
	##     ## ##     ## ##     ##       ##    ##     ##
	##     ## ########  ########        ##     #######
	*/

	/**
	 * Data provider for Image_Tag_Properties_Base::test_add_to().
	 *
	 * @see Image_Tag_Properties_Base::test_add_to()
	 * @return array[]
	 */
	function data_add_to() {
		return array(
			'empty' => array(
				$this->new_instance(),
				'foo',
				'bar',
				'bar',
			),
			'string' => array(
				$this->new_instance( array( 'foo' => __FUNCTION__ ) ),
				'foo',
				'bar',
				__FUNCTION__ . 'bar',
			),
			'integer' => array(
				$this->new_instance( array( 'foo' => 72 ) ),
				'foo',
				28,
				100,
			),
			'double/float' => array(
				$this->new_instance( array( 'foo' => 3.1415 ) ),
				'foo',
				7.3585,
				10.5
			),
			'array string' => array(
				$this->new_instance( array( 'foo' => array( __FUNCTION__ ) ) ),
				'foo',
				'foobar',
				array(
					__FUNCTION__,
					'foobar',
				),
			),
			'multiple strings' => array(
				$this->new_instance( array(
					'foo' => __FUNCTION__,
					'bar' => __FUNCTION__,
				) ),
				array(
					'foo' => 'foo',
					'bar' => 'bar',
				),
				null,
				array(
					'foo' => __FUNCTION__ . 'foo',
					'bar' => __FUNCTION__ . 'bar',
				),
			),
			'multiple integers' => array(
				$this->new_instance( array(
					'two' => 2,
					'four' => 4,
					'six' => 6,
				) ),
				array(
					'two' => 8,
					'four' => 6,
					'six' => 4,
				),
				null,
				array(
					'two'  => 10,
					'four' => 10,
					'six'  => 10,
				),
			),
			'multiple dobules/floats' => array(
				$this->new_instance( array(
					'one' => 1.0,
					'pi' => 3.1415,
				) ),
				array(
					'one' => 2.1415,
					'pi' => 1,
				),
				null,
				array(
					'one' => 3.1415,
					'pi'  => 4.1415
				),
			),
			'multiple arrays' => array(
				$this->new_instance( array(
					'foo' => array(),
					'bar' => array( 'key1' => __FUNCTION__ ),
					'zoo' => array( 'keyA' => __FUNCTION__ ),
				) ),
				array(
					'foo' => array( __FUNCTION__, 'bar' ),
					'bar' => array( 'key2' => 'foo' ),
					'zoo' => array( 'keyB' => array(
						'food' => 'bar',
						'drink' => 'foo',
					) ),
				),
				null,
				array(
					'foo' => array( __FUNCTION__, 'bar' ),
					'bar' => array(
						'key1' => __FUNCTION__,
						'key2' => 'foo',
					),
					'zoo' => array(
						'keyA' => __FUNCTION__,
						'keyB' => array(
							'food' => 'bar',
							'drink' => 'foo',
						),
					),
				),
			),
		);
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
	 * Data provider for Image_Tag_Properties_Base::test_get().
	 *
	 * @see Image_Tag_Properties_Base::test_get()
	 * @return array[]
	 */
	function data_get() {
		return array(

			'empty' => array(
				$this->new_instance(),
				'does-not-exist',
				null,
			),

			'single' => array(
				$this->new_instance( array(
					'foo' => __FUNCTION__,
					'bar' => uniqid(),
				) ),
				'foo',
				__FUNCTION__,
			),

			'multiple' => array(
				$this->new_instance( array(
					'foo' => 'foo',
					'bar' => 'bar',
				) ),
				array( 'foo', 'bar' ),
				array(
					'foo' => 'foo',
					'bar' => 'bar',
				),
			),

			'arrays' => array(
				$this->new_instance( array(
					'foo' => range( 1, 10 ),
					'bar' => range( 1, 20 ),
				) ),
				array( 'foo' ),
				array( 'foo' => range( 1, 10 ) ),
			),

			'null' => array(
				$this->get_instance( array(
					'foo' => uniqid(),
					'bar' => uniqid(),
				) ),
				null,
				$this->get_instance()->properties,
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
	 * Data provider for Image_Tag_Properties_Base::test_arrayAccess_exists().
	 *
	 * @see Image_Tag_Properties_Base::test_arrayAccess_exists()
	 * @return array[]
	 */
	function data_arrayAccess_exists() {
		return array(

			'null' => array(
				$this->new_instance( array() ),
				null,
				'id',
			),

			'string' => array(
				$this->new_instance( array( 'foo' => uniqid() ) ),
				'foo',
				'bar',
			),

			'array' => array(
				$this->new_instance( array( 'foo' => range( 1, 5 ) ) ),
				'foo',
			),

			'object' => array(
				$this->new_instance( array( 'foo' => ( object ) range( 1, 5 ) ) ),
				'foo',
				'zoo',
			),

		);
	}

	/**
	 * Data provider for Image_Tag_Properties_Base::test_arrayAccess_get().
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
				$this->new_instance( array() ),
				'foo',
				uniqid(),
			),

			'string override' => array(
				$this->new_instance( array( 'foo' => __FUNCTION__ ) ),
				'foo',
				uniqid(),
			),

			'array' => array(
				$this->new_instance( array() ),
				'foo',
				range( 1, 5 ),
			),

			'array override' => array(
				$this->new_instance( array( 'foo' => __FUNCTION__ ) ),
				'foo',
				range( 5, 10 ),
			),

			'object' => array(
				$this->new_instance( array() ),
				'foo',
				( object ) range( 5, 10 ),
			),

			'object override' => array(
				$this->new_instance( array( 'foo' => range( 1, 5 ) ) ),
				'foo',
				( object ) range( 10, 20 ),
			),

		);
	}

	/**
	 * Data provider for Image_Tag_Properties_Base::test_arrayAccess_unset().
	 *
	 * @see Image_Tag_Properties_Base::test_arrayAccess_unset()
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

?>