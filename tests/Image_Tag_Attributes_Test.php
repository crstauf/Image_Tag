<?php

require_once 'Image_Tag_Properties_Abstract_Test.php';

/**
 * @coversDefaultClass Image_Tag_Attributes
 * @group properties
 * @group attributes
 */
class Image_Tag_Attributes_Test extends Image_Tag_Properties_Abstract_Test {

	/**
	 * @group constant
	 * @group defaults
	 */
	function test_defaults_constant() {
		$this->assertSame( array(
			'id' => null,
			'alt' => null,
			'src' => null,
			'title' => null,
			'width' => null,
			'height' => null,
			'data-src' => null,
			'data-srcset' => array(),
			'data-sizes' => array(),
			'srcset' => array(),
			'style' => array(),
			'sizes' => array(),
			'class' => array(),
		), Image_Tag_Attributes::DEFAULTS );
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
	 * Data for __construct() test.
	 *
	 * array(
	 *     class name,
	 *     attributes,
	 *     defaults,
	 *     expected value
	 * )
	 *
	 * @see Image_Tag_Properties_Abstract_Test::test__construct()
	 */
	function data__construct() {
		$data = array();

		$data['empty'] = array(
			'Image_Tag_Attributes',
			array(),
			array(),
			array(),
		);

		$attributes = array( 'id' => uniqid( __FUNCTION__ ) );
		$data['attributes'] = array(
			'Image_Tag_Attributes',
			$attributes,
			array(),
			$attributes,
		);

		$attributes = array( 'alt' => uniqid( __FUNCTION__ ) );
		$defaults   = array(  'id' => uniqid( __FUNCTION__ ) );
		$data['defaults'] = array(
			'Image_Tag_Attributes',
			$attributes,
			$defaults,
			wp_parse_args( $attributes, $defaults ),
		);

		$attributes = array( 'alt' => uniqid( __FUNCTION__ ) );
		$defaults   = array(  'id' => uniqid( __FUNCTION__ ) );
		$instance   = new Image_Tag_Attributes( $attributes, $defaults );
		$data['pre_instance'] = array(
			'Image_Tag_Attributes',
			$attributes,
			$defaults,
			$instance->get(),
		);

		$data['instance'] = array(
			'Image_Tag_Attributes',
			$instance,
			array(),
			wp_parse_args( $attributes, $defaults ),
		);

		return $data;
	}

	/**
	 * @covers Image_Tag_Properties_Abstract::__set()
	 * @group magic
	 * @group set
	 */
	function test__set() {
		$attributes = array(
			'id' => uniqid( __FUNCTION__ ),
		);

		$instance = new Image_Tag_Attributes( $attributes );
		$this->assertSame( $attributes['id'], $instance->get( 'id' ) );

		$instance->id = $attributes['id'] = uniqid( __FUNCTION__ );
		$this->assertSame( $attributes['id'], $instance->get( 'id' ) );
	}

	/**
	 * @covers Image_Tag_Properties_Abstract::__get()
	 * @group magic
	 * @group get
	 */
	function test__get() {
		$attributes = array(
			'id' => uniqid( __FUNCTION__ ),
			'class' => ' as df  jk ',
		);

		$instance = new Image_Tag_Attributes( $attributes );
		$this->assertSame( $attributes['id'], $instance->id );

		$this->assertSame( array( 'as', 'df', 'jk' ), $instance->class );
	}

	/**
	 * @covers Image_Tag_Properties_Abstract::__isset()
	 * @group magic
	 * @group isset
	 *
	 * @doesNotPerformAssertions
	 */
	function test__isset() {}

	/**
	 * @covers Image_Tag_Properties_Abstract::__unset()
	 * @covers Image_Tag_Attributes::__unset()
	 * @group magic
	 * @group unset
	 *
	 * @doesNotPerformAssertions
	 */
	function test__unset() {}


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
	 * @covers Image_Tag_Properties_Abstract::add()
	 * @covers Image_Tag_Properties_Abstract::add_property()
	 * @covers Image_Tag_Properties_Abstract::add_properties()
	 * @covers Image_Tag_Attributes::add()
	 * @group instance
	 * @group add
	 *
	 * @doesNotPerformAssertions
	 */
	function test_add() {}


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
	 * @covers Image_Tag_Properties_Abstract::set()
	 * @covers Image_Tag_Attributes::set()
	 * @group instance
	 * @group set
	 *
	 * @doesNotPerformAssertions
	 */
	function test_set() {}

	/**
	 * @covers Image_Tag_Properties_Abstract::unset()
	 * @covers Image_Tag_Attributes::unset()
	 * @group instance
	 * @group unset
	 *
	 * @doesNotPerformAssertions
	 */
	function test_unset() {}


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
	 * @covers Image_Tag_Properties_Abstract::isset()
	 * @covers Image_Tag_Attributes::isset()
	 * @group instance
	 * @group isset
	 *
	 * @doesNotPerformAssertions
	 */
	function test_isset() {}

	/**
	 * @covers Image_Tag_Properties_Abstract::get()
	 * @covers Image_Tag_Attributes::get()
	 * @group instance
	 * @group get
	 *
	 * @doesNotPerformAssertions
	 */
	function test_get() {}


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
	 * @covers Image_Tag_Properties_Abstract::offsetExists()
	 * @covers Image_Tag_Properties_Abstract::offsetGet()
	 * @covers Image_Tag_Properties_Abstract::offsetSet()
	 * @covers Image_Tag_Properties_Abstract::offsetUnset()
	 * @covers Image_Tag_Attributes::offsetExists()
	 * @covers Image_Tag_Attributes::offsetGet()
	 * @covers Image_Tag_Attributes::offsetSet()
	 * @covers Image_Tag_Attributes::offsetUnset()
	 * @group arrayaccess
	 *
	 * @doesNotPerformAssertions
	 */
	function test_arrayAccess() {}

}

?>