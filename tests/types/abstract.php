<?php

/**
 * @coversDefaultClass Image_Tag_Abstract
 */
abstract class Image_Tag_Test_Base extends WP_UnitTestCase {


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
	 * @covers Image_Tag_Abstract::__construct()
	 */
	function test__construct() {
		$img = new Image_Tag;

		$this->assertInstanceOf( Image_Tag_Attributes::class, $img->attributes );
		$this->assertInstanceOf( Image_Tag_Settings::class,   $img->settings   );
	}

	/**
	 * @todo define
	 */
	function test__get() {

	}


	/*
	   ###    ######## ######## ########  #### ########  ##     ## ######## ########  ######
	  ## ##      ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##    ##
	 ##   ##     ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##
	##     ##    ##       ##    ########   ##  ########  ##     ##    ##    ######    ######
	#########    ##       ##    ##   ##    ##  ##     ## ##     ##    ##    ##             ##
	##     ##    ##       ##    ##    ##   ##  ##     ## ##     ##    ##    ##       ##    ##
	##     ##    ##       ##    ##     ## #### ########   #######     ##    ########  ######
	*/

	/**
	 * @covers ::add_attributes()
	 *
	 * @todo define
	 */
	function test_add_attributes() {

	}

	/**
	 * @covers ::add_attribute()
	 *
	 * @todo define
	 */
	function test_add_attribute() {

	}

	/**
	 * @covers ::set_attributes()
	 *
	 * @todo define
	 */
	function test_set_attributes() {

	}

	/**
	 * @covers ::set_attribute()
	 *
	 * @todo define
	 */
	function test_set_attribute() {

	}

	/**
	 * @covers ::attributes_are_set()
	 *
	 * @todo define
	 */
	function test_attributes_are_set() {

	}

	/**
	 * @covers ::attribute_isset()
	 *
	 * @todo define
	 */
	function test_attribute_isset() {

	}

	/**
	 * @covers ::attributes_exist()
	 *
	 * @todo define
	 */
	function test_attributes_exist() {

	}

	/**
	 * @covers ::attribute_exists()
	 *
	 * @todo define
	 */
	function test_attribute_exists() {

	}

	/**
	 * @covers ::add_to_attributes()
	 *
	 * @todo define
	 */
	function test_add_to_attributes() {

	}

	/**
	 * @covers ::add_to_attribute()
	 *
	 * @todo define
	 */
	function test_add_to_attribute() {

	}

	/**
	 * @covers ::get_attributes()
	 *
	 * @todo define
	 */
	function test_get_attributes() {

	}

	/**
	 * @covers ::get_attribute()
	 *
	 * @todo define
	 */
	function test_get_attribute() {

	}


	/*
	##     ##    ###    ##       #### ########     ###    ######## ####  #######  ##    ##
	##     ##   ## ##   ##        ##  ##     ##   ## ##      ##     ##  ##     ## ###   ##
	##     ##  ##   ##  ##        ##  ##     ##  ##   ##     ##     ##  ##     ## ####  ##
	##     ## ##     ## ##        ##  ##     ## ##     ##    ##     ##  ##     ## ## ## ##
	 ##   ##  ######### ##        ##  ##     ## #########    ##     ##  ##     ## ##  ####
	  ## ##   ##     ## ##        ##  ##     ## ##     ##    ##     ##  ##     ## ##   ###
	   ###    ##     ## ######## #### ########  ##     ##    ##    ####  #######  ##    ##
	*/

	/**
	 * @covers ::get_type()
	 */
	abstract function test_get_type();

	/**
	 * @covers ::add_attributes()
	 */
	abstract function test_is_type();

	/**
	 * @covers ::check_valid()
	 */
	abstract function test_check_valid();

	/**
	 * @covers ::is_valid()
	 *
	 * @todo define
	 */
	function test_is_valid() {

	}

}

?>