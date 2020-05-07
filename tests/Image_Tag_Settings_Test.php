<?php

require_once 'Image_Tag_Properties_Abstract_Test.php';

/**
 * @coversDefaultClass Image_Tag_Settings
 * @group properties
 * @group settings
 */
class Image_Tag_Settings_Test extends Image_Tag_Properties_Abstract_Test {

	/**
	 * @group constant
	 * @group defaults
	 *
	 * @doesNotPerformAssertions
	 */
	function test_defaults_constant() {}


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
	 *     expected value,
	 *     attributes,
	 *     defaults
	 * )
	 *
	 * @see Image_Tag_Properties_Abstract_Test::test__construct()
	 */
	function data__construct() {
		return array(
			array(
				'Image_Tag_Settings',
				array(),
				array(),
				array(),
			),
		);
	}

	/**
	 * @covers Image_Tag_Properties_Abstract::__set()
	 * @covers Image_Tag_Settings::__set()
	 * @group magic
	 * @group set
	 *
	 * @doesNotPerformAssertions
	 */
	function test__set() {}

	/**
	 * @covers Image_Tag_Properties_Abstract::__get()
	 * @covers Image_Tag_Settings::__get()
	 * @group magic
	 * @group get
	 *
	 * @doesNotPerformAssertions
	 */
	function test__get() {}

	/**
	 * @covers Image_Tag_Properties_Abstract::__isset()
	 * @covers Image_Tag_Settings::__isset()
	 * @group magic
	 * @group isset
	 *
	 * @doesNotPerformAssertions
	 */
	function test__isset() {}

	/**
	 * @covers Image_Tag_Properties_Abstract::__unset()
	 * @covers Image_Tag_Settings::__unset()
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
	 * @covers Image_Tag_Settings::add()
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
	 * @covers Image_Tag_Settings::set()
	 * @group instance
	 * @group set
	 *
	 * @doesNotPerformAssertions
	 */
	function test_set() {}

	/**
	 * @covers Image_Tag_Properties_Abstract::unset()
	 * @covers Image_Tag_Settings::unset()
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
	 * @covers Image_Tag_Settings::isset()
	 * @group instance
	 * @group isset
	 *
	 * @doesNotPerformAssertions
	 */
	function test_isset() {}

	/**
	 * @covers Image_Tag_Properties_Abstract::get()
	 * @covers Image_Tag_Settings::get()
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
	 * @covers Image_Tag_Settings::offsetExists()
	 * @covers Image_Tag_Settings::offsetGet()
	 * @covers Image_Tag_Settings::offsetSet()
	 * @covers Image_Tag_Settings::offsetUnset()
	 * @group arrayaccess
	 *
	 * @doesNotPerformAssertions
	 */
	function test_arrayAccess() {}

}

?>