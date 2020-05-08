<?php

require_once 'Image_Tag_Properties_Test.php';

/**
 * @coversDefaultClass Image_Tag_Settings
 * @group properties
 * @group settings
 */
class Image_Tag_Settings_Test extends Image_Tag_Properties_Tests {

	/**
	 * @group constant
	 * @group defaults
	 */
	function test_defaults_constant() {
		$this->markTestIncomplete();
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
	 * Data for __construct() test.
	 *
	 * @see Image_Tag_Properties_Test::test__construct()
	 */
	function data__construct() {
		return array(
			'empty' => array(
				Image_Tag_Settings::class,
				array(),
				array(),
				Image_Tag_Settings::DEFAULTS,
			),
		);
	}

	/**
	 * Data provider for Image_Tag_Properties_Tests::__set().
	 *
	 * @see Image_Tag_Properties_Tests::test__set()
	 * @return array
	 */
	function data__set() {
		$this->markTestIncomplete();
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