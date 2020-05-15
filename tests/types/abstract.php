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

}

?>