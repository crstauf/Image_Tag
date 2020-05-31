<?php

/**
 * Class: Image_Tag
 */
class Image_Tag extends Image_Tag_Abstract {

	const TYPES = array(
		'base',
	);

	/*
	 ######  ########    ###    ######## ####  ######
	##    ##    ##      ## ##      ##     ##  ##    ##
	##          ##     ##   ##     ##     ##  ##
	 ######     ##    ##     ##    ##     ##  ##
	      ##    ##    #########    ##     ##  ##
	##    ##    ##    ##     ##    ##     ##  ##    ##
	 ######     ##    ##     ##    ##    ####  ######
	*/

	/**
	 * Create Image_Tag object based on source.
	 *
	 * @param int|string $source
	 * @param null|array|Image_Tag_Attributes $attributes
	 * @param null|array|Image_Tag_Settings $settings
	 * @return Image_Tag_Abstract
	 *
	 * @todo add types
	 */
	static function create( $source, $attributes = array(), $settings = array() ) {
		$attributes = ( array ) $attributes;

		# Create JoeSchmoe image tag.
		if ( in_array( $source, Image_Tag_JoeSchmoe::TYPES ) )
			return new Image_Tag_JoeSchmoe( $attributes, $settings );

		# Create Picsum image tag.
		if ( in_array( $source, Image_Tag_Picsum::TYPES ) )
			return new Image_Tag_Picsum( $attributes, $settings );

		# If URL, create using base object.
		if ( ( bool ) wp_http_validate_url( $source ) ) {
			$attributes['src'] = $source;
			return new Image_Tag( $attributes, $settings );
		}

		# Unable to determine type.
		trigger_error( sprintf( 'Unable to determine image type from source <code>%s</code>.', $source ), E_USER_WARNING );
		return new Image_Tag( $attributes, $settings );
	}

}

?>