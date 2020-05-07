<?php

/**
 * @coversDefaultClass Image_Tag_Properties_Abstract
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

	abstract function data__construct();

	/**
	 * @covers ::__construct()
	 * @group magic
	 * @group construct
	 *
	 * @dataProvider data__construct
	 */
	function test__construct( string $class, $attributes, $defaults, $expected ) {
		$instance = new $class( $attributes, $defaults );
		$this->assertSame( $expected, $instance->get() );
	}

	/**
	 * @covers ::__set()
	 * @group magic
	 * @group set
	 */
	abstract function test__set();

	/**
	 * @covers ::__get()
	 * @group magic
	 * @group get
	 */
	abstract function test__get();

	/**
	 * @covers ::__isset()
	 * @group magic
	 * @group isset
	 */
	abstract function test__isset();

	/**
	 * @covers ::__unset()
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
	 * @covers ::add()
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
	 * @covers ::set()
	 * @group instance
	 * @group set
	 */
	abstract function test_set();

	/**
	 * @covers ::unset()
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
	 * @covers ::isset()
	 * @group instance
	 * @group isset
	 */
	abstract function test_isset();

	/**
	 * @covers ::get()
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
	 * @covers ::offsetExists()
	 * @covers ::offsetGet()
	 * @covers ::offsetSet()
	 * @covers ::offsetUnset()
	 * @group instance
	 * @group arrayaccess
	 */
	abstract function test_arrayAccess();

}

?>