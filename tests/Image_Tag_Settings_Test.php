<?php

require_once 'Image_Tag_Properties_Test.php';

/**
 * @coversDefaultClass Image_Tag_Settings
 * @group properties
 * @group settings
 */
class Image_Tag_Settings_Test extends Image_Tag_Properties_Test {

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

}

?>