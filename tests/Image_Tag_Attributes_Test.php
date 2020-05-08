<?php

require_once 'Image_Tag_Properties_Test.php';

/**
 * @coversDefaultClass Image_Tag_Attributes
 * @group properties
 * @group attributes
 */
class Image_Tag_Attributes_Test extends Image_Tag_Properties_Test {

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
	 * @see Image_Tag_Properties_Abstract_Test::test__construct()
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
	 * Data for __set() test.
	 *
	 * @see Image_Tag_Properties_Abstract_Test::test__set()
	 */
	function data__set() {
		return array(
			'string' => array( Image_Tag_Attributes::class, 'id', uniqid( __FUNCTION__ ) ),
			'array'  => array( Image_Tag_Attributes::class, 'class', array( uniqid( __FUNCTION__ ) ) ),
		);
	}

	function data__get() {
		$this->markTestIncomplete();
	}

}

?>