<?php

/**
 * @covers Image_Tag_Properties_Abstract
 * @group properties
 */
abstract class Image_Tag_Properties_Abstract_Test extends WP_UnitTestCase {

	/**
	 * @group constant
	 * @group defaults
	 */
	abstract function test_defaults_constant();


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
	 * @covers Image_Tag_Properties_Abstract::__construct()
	 * @group magic
	 * @group magic
	 */
	abstract function test__construct();

	/**
	 * @covers Image_Tag_Properties_Abstract::__set()
	 * @group magic
	 * @group set
	 */
	abstract function test__set();

	/**
	 * @covers Image_Tag_Properties_Abstract::__get()
	 * @group magic
	 * @group get
	 */
	abstract function test__get();

	/**
	 * @covers Image_Tag_Properties_Abstract::__isset()
	 * @group magic
	 * @group isset
	 */
	abstract function test__isset();

	/**
	 * @covers Image_Tag_Properties_Abstract::__unset()
	 * @group magic
	 * @group unset
	 */
	abstract function test__unset();


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
	 * @group instance
	 * @group add
	 */
	abstract function test_add();


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
	 * @group instance
	 * @group set
	 */
	abstract function test_set();

	/**
	 * @covers Image_Tag_Properties_Abstract::unset()
	 * @group instance
	 * @group unset
	 */
	abstract function test_unset();

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
	 * @group instance
	 * @group isset
	 */
	abstract function test_isset();

	/**
	 * @covers Image_Tag_Properties_Abstract::get()
	 * @group instance
	 * @group get
	 */
	abstract function test_get();


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
	 * @group instance
	 * @group arrayaccess
	 */
	abstract function test_arrayAccess();

}

?>