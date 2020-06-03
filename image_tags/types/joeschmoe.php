<?php

class Image_Tag_JoeSchmoe extends Image_Tag_Abstract {

	/**
	 * @var string[]
	 */
	const TYPES = array(
		'joeschmoe', // primary type
		'avatar',
		'person',
		'placeholder',
		'external',
		'remote',
	);


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
	 * Construct.
	 *
	 * @param null|array|Image_Tag_Attributes $attributes
	 * @param null|array|Image_Tag_Settings $settings
	 */
	function __construct( $attributes = null, $settings = null ) {
		$this->attributes = new Image_Tag_JoeSchmoe_Attributes( $attributes, null, $this );
		$this->settings   = new Image_Tag_JoeSchmoe_Settings( $settings, null, $this );
	}

}

?>