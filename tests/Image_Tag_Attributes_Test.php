<?php

require_once 'Image_Tag_Properties_Test.php';

/**
 * @coversDefaultClass Image_Tag_Attributes
 * @group properties
 * @group attributes
 */
class Image_Tag_Attributes_Test extends Image_Tag_Properties_Tests {

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

	/**
	 * Data provider for Image_Tag_Properties_Test::test_function_name().
	 *
	 * @see Image_Tag_Properties_Test::test_function_name()
	 * @return array
	 */
	function data_function_name() {
		$this->markTestIncomplete();
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
	 * Data provider for Image_Tag_Properties_Tests::__construct().
	 *
	 * @see Image_Tag_Properties_Tests::test__construct()
	 * @return array
	 */
	function data__construct() {
		$data = array();

		$data['empty'] = array(
			Image_Tag_Attributes::class,
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
			Image_Tag_Attributes::class,
			$attributes,
			$defaults,
			wp_parse_args( $attributes, $defaults ),
		);

		$attributes = array( 'alt' => uniqid( __FUNCTION__ ) );
		$defaults   = array(  'id' => uniqid( __FUNCTION__ ) );
		$instance   = new Image_Tag_Attributes( $attributes, $defaults );
		$data['pre_instance'] = array(
			Image_Tag_Attributes::class,
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
	 * Data provider for Image_Tag_Properties_Tests::__set().
	 *
	 * @see Image_Tag_Properties_Tests::test__set()
	 * @return array
	 */
	function data__set() {
		return array(
			'string' => array( Image_Tag_Attributes::class, 'id', uniqid( __FUNCTION__ ) ),
			'array'  => array( Image_Tag_Attributes::class, 'class', array( uniqid( __FUNCTION__ ) ) ),
		);
	}

	/**
	 * Data provider for Image_Tag_Properties_Tests::__get().
	 *
	 * @see Image_Tag_Properties_Tests::__get()
	 * @return array
	 */
	function data__get() {
		$this->markTestIncomplete();
	}

	/**
	 * Data provider for Image_Tag_Properties_Tests::__isset().
	 *
	 * @see Image_Tag_Properties_Tests::__isset()
	 * @return array
	 */
	function data__isset() {
		$this->markTestIncomplete();
	}

}

?>