<?php

declare( strict_types=1 );

namespace Image_Tag\Interfaces;

defined( 'WPINC' ) || die();

/**
 * Interface: Image_Tag\Interfaces\Conversion
 */
interface Conversion {

	/**
	 * Convert to Joe Schmoe.
	 *
	 * @param null|array|Attributes $attributes
	 * @param null|array|Settings $settings
	 * @return \Image_Tag\Types\JoeSchmoe
	 */
	public function joeschmoe( $attributes = null, $settings = null ) : \Image_Tag\Types\JoeSchmoe;

	/**
	 * Convert to Picsum photo.
	 *
	 * @param null|array|Attributes $attributes
	 * @param null|array|Settings $settings
	 * @return \Image_Tag\Types\Picsum
	 */
	public function picsum( $attributes = null, $settings = null ) : \Image_Tag\Types\Picsum;

	/**
	 * Convert to Placeholder.com image.
	 *
	 * @param null|array|Attributes $attributes
	 * @param null|array|Settings $settings
	 * @return \Image_Tag\Types\Placeholder
	 */
	public function placeholder( $attributes = null, $settings = null ) : \Image_Tag\Types\Placeholder;

	/**
	 * Convert to Unsplash Source photo.
	 *
	 * @param null|array|Attributes $attributes
	 * @param null|array|Settings $settings
	 * @return \Image_Tag\Types\Unsplash
	 */
	public function unsplash( $attributes = null, $settings = null ) : \Image_Tag\Types\Unsplash;

}