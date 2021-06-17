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

	protected $path = null;

	/**
	 * @var array Image types.
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
	 * @param null|array|Attributes $attributes
	 * @param null|array|Settings $settings
	 */
	function __construct( string $source, $attributes = null, $settings = null ) {
		$stylesheet = trailingslashit( get_stylesheet_directory() );
		$template   = trailingslashit( get_template_directory() );

		if ( file_exists( $stylesheet . $source ) ) {
			$this->path = $stylesheet . $source;
			$url = trailingslashit( get_stylesheet_directory_uri() ) . $source;
		} else if ( file_exists( $template . $source ) ) {
			$this->path = $template . $source;
			$url = trailingslashit( get_template_directory_uri() ) . $source;
		}

		$this->construct( $attributes, $settings );

		if ( empty( $url ) )
			return;

		$this->attributes->set( 'src', $url );
	}

	/**
	 * Get most common colors.
	 *
	 * @param int $count
	 * @uses static::identify_colors()
	 * @return array
	 */
	function colors( int $count = 3 ) : array {
		static $colors = null;

		if ( !is_null( $colors ) )
			return $colors;

		$_colors = static::identify_colors( $this->path, $count );

		if ( empty( $_colors ) )
			return array();

		$colors = $_colors;
		return $colors;
	}

	/**
	 * Perform validation checks.
	 *
	 * @return \WP_Error
	 */
	protected function perform_validation_checks() : \WP_Error {
		$errors = new \WP_Error;

		if ( empty( $this->path ) )
			$errors->add( 'not_exists', 'Unable to find theme image.' );

		return $errors;
	}

	function ratio() : float {
		if ( !$this->is_valid() )
			return 0;

		$dimensions = getimagesize( $this->path );

		return absint( $dimensions[0] ) / absint( $dimensions[1] );
	}

}