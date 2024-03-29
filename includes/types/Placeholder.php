<?php
/**
 * Placeholder image service: Placeholder.com
 *
 * @link https://placeholder.com/
 */

declare( strict_types=1 );

namespace Image_Tag\Types;

use Image_Tag\Data_Stores\Attributes;
use Image_Tag\Data_Stores\Settings;

defined( 'WPINC' ) || die();

/**
 * Class: Image_Tag\Types\Placeholder
 */
class Placeholder extends \Image_Tag\Abstracts\Base implements \Image_Tag\Interfaces\Dynamic_Source {

	const BASE_URL = 'https://via.placeholder.com';

	/**
	 * @var string[] Image types.
	 */
	const TYPES = array(
		'placeholder',
		'__placeholder',
	);

	/**
	 * Construct.
	 *
	 * @param null|mixed[]|Attributes $attributes
	 * @param null|mixed[]|Settings $settings
	 * @uses $this->construct()
	 */
	public function __construct( $attributes = null, $settings = null ) {
		$this->construct( $attributes, $settings );
	}

	/**
	 * Create Attributes object to use for output.
	 *
	 * @uses Base::output_attributes()
	 * @uses $this->generate_source()
	 * @return Attributes
	 */
	protected function output_attributes() : Attributes {
		$attributes = parent::output_attributes();
		$dimensions = array();

		$attributes->update( 'src', $this->generate_source() );

		# Width
		if ( $this->settings->has( 'width' ) ) {
			$dimensions[] = $this->settings->get( 'width' );
		} else if ( $this->attributes->has( 'width' ) ) {
			$dimensions[] = $this->attributes->get( 'width' );
		}

		# Height
		if ( $this->settings->has( 'height' ) ) {
			$dimensions[] = $this->settings->get( 'height' );
		} else if ( $this->attributes->has( 'height' ) ) {
			$dimensions[] = $this->attributes->get( 'height' );
		}

		if ( 1 === count( $dimensions ) ) {
			$dimensions[] = $dimensions[0];
		}

		if ( ! $attributes->has( 'width' ) && ! empty( $dimensions[0] ) ) {
			$attributes->set( 'width', $dimensions[0] );
		}

		if ( ! $attributes->has( 'height' ) && ! empty( $dimensions[1] ) ) {
			$attributes->set( 'height', $dimensions[1] );
		}

		return $attributes;
	}

	/**
	 * Generate image source.
	 *
	 * @uses Settings::has()
	 * @uses Settings::get()
	 * @uses Attributes::has()
	 * @uses Attributes::get()
	 * @return string
	 */
	public function generate_source() : string {
		if ( array_key_exists( __FUNCTION__, $this->cache ) ) {
			return $this->cache[ __FUNCTION__ ];
		}

		$dimensions = array();
		$src        = array( static::BASE_URL );

		# Width
		if ( $this->settings->has( 'width' ) ) {
			$dimensions[] = $this->settings->get( 'width' );
		} else if ( $this->attributes->has( 'width' ) ) {
			$dimensions[] = $this->attributes->get( 'width' );
		}

		# Height
		if ( $this->settings->has( 'height' ) ) {
			$dimensions[] = $this->settings->get( 'height' );
		} else if ( $this->attributes->has( 'height' ) ) {
			$dimensions[] = $this->attributes->get( 'height' );
		}

		if ( 1 === count( $dimensions ) ) {
			$dimensions[] = $dimensions[0];
		}

		$src[] = implode( 'x', $dimensions );

		# Colors
		if ( $this->settings->has( 'bg_color' ) ) {
			$src[] = $this->settings->get( 'bg_color' );

			if ( $this->settings->has( 'text_color' ) ) {
				$src[] = $this->settings->get( 'text_color' );
			}
		}

		# Convert to string
		$src = implode( '/', $src );

		# Custom text
		if ( $this->settings->has( 'text' ) ) {
			$src = add_query_arg( 'text', $this->settings->get( 'text' ), $src );
		}

		$this->cache[ __FUNCTION__ ] = $src;

		return $src;
	}

	/**
	 * Get ratio of image dimensions: width divided by height.
	 *
	 * @return float
	 */
	public function ratio() : float {
		$width  = 0;
		$height = 0;

		if ( $this->settings->has( 'width', false ) ) {
			$width = $this->settings->get( 'width' );
		} else if ( $this->attributes->has( 'width', false ) ) {
			$width = $this->attributes->get( 'width' );
		}

		if ( $this->settings->has( 'height', false ) ) {
			$height = $this->settings->get( 'height' );
		} else if ( $this->attributes->has( 'height', false ) ) {
			$height = $this->attributes->get( 'height' );
		}

		if ( empty( $height ) ) {
			$height = $width;
		}

		if ( empty( $width ) ) {
			$width = $height;
		}

		if ( empty( $height ) ) {
			return 0;
		}

		return absint( $width ) / absint( $height );
	}

	/**
	 * Perform validation checks.
	 *
	 * @uses $this->validate_dimensions()
	 * @return \WP_Error
	 */
	protected function perform_validation_checks() : \WP_Error {
		$errors = new \WP_Error;

		try {
			$this->validate_dimensions();
		} catch ( \Exception $e ) {
			$errors->add( 'placeholder', $e->getMessage() );
		}

		return $errors;
	}

	/**
	 * Check that at least one dimension is set.
	 *
	 * @uses Settings::has()
	 * @uses Attributes::has()
	 * @return void
	 */
	protected function validate_dimensions() : void {
		if (
			   $this->settings->has( 'width', false )
			|| $this->settings->has( 'height', false )
			|| $this->attributes->has( 'width', false )
			|| $this->attributes->has( 'height', false )
		) {
			return;
		}

		throw new \Exception( 'Placeholder requires at least one dimension.' );
	}

	/**
	 * Prevent conversion to same type.
	 *
	 * @param null|mixed[]|Attributes $attributes
	 * @param null|mixed[]|Settings $settings
	 * @return self
	 */
	public function placeholder( $attributes = null, $settings = null ) : self {
		trigger_error( sprintf( 'Image is already type <code>%s</code>', $this->get_type() ) );
		return $this;
	}

}
