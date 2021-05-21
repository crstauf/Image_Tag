<?php

declare( strict_types=1 );

namespace Image_Tag\Interfaces;

defined( 'ABSPATH' ) || die();

/**
 * Interface: Image_Tag\Interfaces\Conversion
 */
interface Conversion {

	/**
	 * Convert to Picsum photo.
	 *
	 * @param null|array|Attributes $attributes
	 * @param null|array|Settings $settings
	 * @return \Image_Tag\Types\Picsum
	 */
	function picsum( $attributes = null, $settings = null ) : \Image_Tag\Types\Picsum;

}