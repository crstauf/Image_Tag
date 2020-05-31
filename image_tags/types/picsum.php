<?php

class Image_Tag_Picsum extends Image_Tag_Abstract {

	const TYPES = array(
		'picsum', // primary type
		'placeholder',
		'external',
		'remote',
	);

	/**
	 * Construct.
	 *
	 * @param null|array|Image_Tag_Attributes $attributes
	 * @param null|array|Image_Tag_Settings $settings
	 */
	function __construct( $attributes = null, $settings = null ) {
		$this->attributes = new Image_Tag_Picsum_Attributes( $attributes, null, $this );
		$this->settings   = new Image_Tag_Picsum_Settings( $settings, null, $this );
	}

}

?>