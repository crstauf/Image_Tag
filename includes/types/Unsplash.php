<?php
/**
 * Placeholder image service: Unsplash Source
 *
 * @link https://source.unsplash.com/
 */

declare( strict_types=1 );

namespace Image_Tag\Types;

use Image_Tag\Data_Stores\Attributes;
use Image_Tag\Data_Stores\Settings;

defined( 'WPINC' ) || die();

/**
 * Class: Image_Tag\Types\Unsplash
 */
class Unsplash extends \Image_Tag\Abstracts\Base implements \Image_Tag\Interfaces\Dynamic_Source {

	const BASE_URL = 'https://source.unsplash.com';

	/**
	 * @var string[] Image types.
	 */
	const TYPES = array(
		'unsplash',
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
		$attributes->update( 'src', $this->generate_source() );

		return $attributes;
	}

	/**
	 * Generage image source.
	 *
	 * @uses Settings::has()
	 * @uses Settings::get()
	 * @uses Attributes::has()
	 * @uses Attributes::get()
	 * @return string
	 */
	public function generate_source() : string {
		static $random = 0;

		if ( array_key_exists( __FUNCTION__, $this->cache ) ) {
			return $this->cache[ __FUNCTION__ ];
		}

		$src = array( static::BASE_URL );

		# User (likes)
		if ( $this->settings->has( 'user', false ) ) {
			$src[] = sprintf( 'user/%s', sanitize_title_with_dashes( $this->settings->get( 'user' ) ) );

			# User likes
			if ( $this->settings->has( 'user_likes', false ) ) {
				$src[] = 'likes';
			}
		}

		# Daily
		if ( $this->settings->has( 'daily', false ) ) {
			$src[] = 'daily';

		# Weekly
		} else if ( $this->settings->has( 'weekly', false ) ) {
			$src[] = 'weekly';

		# Collection
		} else if (
			 ! $this->settings->has( 'user', false )
			&& $this->settings->has( 'collection', false )
		) {
			$src[] = sprintf( 'collection/%d', absint( $this->settings->get( 'collection' ) ) );

		# Photo ID
		} else if (
			 ! $this->settings->has( 'user', false )
			&& $this->settings->has( 'photo_id', false )
		) {
			$src[] = $this->settings->get( 'photo_id' );
		}

		# Random
		$random_arg = '';
		if ( $this->settings->has( 'random', false ) ) {
			$random_arg = '?random=' . ++$random;

			$src = array(
				static::BASE_URL,
				'random',
			);
		}

		# Dimensions
		if (
			   ! $this->settings->has( 'daily' )
			&& ! $this->settings->has( 'weekly' )
		) {
			$dimensions = array();

			if ( $this->settings->has( 'width', false ) ) {
				$dimensions[] = $this->settings->get( 'width' );
			} else if ( $this->attributes->has( 'width', false ) ) {
				$dimensions[] = $this->attributes->get( 'width' );
			}

			if ( $this->settings->has( 'height', false ) ) {
				$dimensions[] = $this->settings->get( 'height' );
			} else if ( $this->attributes->has( 'height', false ) ) {
				$dimensions[] = $this->attributes->get( 'height' );
			}

			if ( 1 === count( $dimensions ) ) {
				$dimensions[] = $dimensions[0];
			}

			$src[] = implode( 'x', $dimensions );

		}

		# Convert to string
		$src = implode( '/', $src );

		# Add search keywords
		if ( $this->settings->has( 'search', false ) ) {
			$src .= '?' . ( ( string ) $this->settings->get( 'search' ) );

			if ( ! empty( $random ) ) {
				$random_arg = '&random=' . $random;
			}
		}

		$src .= $random_arg;

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
			$errors->add( 'unsplash', $e->getMessage() );
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

		throw new \Exception( 'Unsplash Source requires at least one dimension.' );
	}

	/**
	 * Prevent conversion to same type.
	 *
	 * @param null|mixed[]|Attributes $attributes
	 * @param null|mixed[]|Settings $settings
	 * @return self
	 */
	public function unsplash( $attributes = null, $settings = null ) : self {
		trigger_error( sprintf( 'Image is already type <code>%s</code>', $this->get_type() ) );
		return $this;
	}

}
