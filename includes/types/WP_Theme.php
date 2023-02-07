<?php
/**
 * WordPress theme image.
 */

declare( strict_types=1 );

namespace Image_Tag\Types;

use Image_Tag\Data_Stores\Attributes;
use Image_Tag\Data_Stores\Settings;

defined( 'WPINC' ) || die();

/**
 * Class: Image_Tag\Types\WP_Theme
 */
class WP_Theme extends \Image_Tag\Abstracts\WordPress {

	/**
	 * @var string
	 */
	protected $path = null;

	/**
	 * @var string[] Image types.
	 */
	const TYPES = array(
		'theme',
		'local',
		'internal',
		'wordpress',
	);

	/**
	 * Construct.
	 *
	 * @param string $source
	 * @param null|mixed[]|Attributes $attributes
	 * @param null|mixed[]|Settings $settings
	 */
	public function __construct( string $source, $attributes = null, $settings = null ) {
		$stylesheet = trailingslashit( get_stylesheet_directory() );
		$template   = trailingslashit( get_template_directory() );

		if ( file_exists( $stylesheet . $source ) ) {
			$this->path = $stylesheet . $source;
			$url        = trailingslashit( get_stylesheet_directory_uri() ) . $source;
		} else if ( file_exists( $template . $source ) ) {
			$this->path = $template . $source;
			$url        = trailingslashit( get_template_directory_uri() ) . $source;
		}

		$this->construct( $attributes, $settings );

		if ( empty( $url ) ) {
			return;
		}

		$this->attributes->set( 'src', $url );
	}

	/**
	 * Get most common colors.
	 *
	 * @param int $count
	 * @uses static::identify_colors()
	 * @return string[]
	 */
	public function colors( int $count = 3 ) : array {
		$cache_key = sprintf( '%s-%d', __FUNCTION__, $count );

		if ( array_key_exists( $cache_key, $this->cache ) ) {
			return $this->cache[ $cache_key ];
		}

		$colors = static::identify_colors( $this->path, $count );

		if ( empty( $colors ) ) {
			return array();
		}

		$this->cache[ $cache_key ] = $colors;

		return $colors;
	}

	/**
	 * Perform validation checks.
	 *
	 * @return \WP_Error
	 */
	protected function perform_validation_checks() : \WP_Error {
		$errors = new \WP_Error;

		if ( empty( $this->path ) ) {
			$errors->add( 'not_exists', 'Unable to find theme image.' );
		}

		return $errors;
	}

	/**
	 * Get image ratio.
	 *
	 * @uses $this->is_valid()
	 * @uses getimagesize()
	 * @return float
	 */
	public function ratio() : float {
		if ( ! $this->is_valid() ) {
			return 0;
		}

		$dimensions = getimagesize( $this->path );

		if ( empty( $dimensions ) ) {
			return 0;
		}

		return absint( $dimensions[0] ) / absint( $dimensions[1] );
	}

	/**
	 * No LQIP support for theme images.
	 *
	 * @return string
	 */
	public function lqip() : string {
		return '';
	}

}
