<?php
/**
 * Placeholder image service: Unsplash Source
 *
 * @link https://source.unsplash.com/
 */

declare( strict_types=1 );

namespace Image_Tag\Types;
use Image_Tag\Data_Store\Attributes;
use Image_Tag\Data_Store\Settings;

defined( 'WPINC' ) || die();

/**
 * Class: Image_Tag\Types\Unsplash
 */
class Unsplash extends \Image_Tag\Abstracts\Base {

	const BASE_URL = 'https://source.unsplash.com';

	/**
	 * @var array Image types.
	 */
	const TYPES = array(
		'unsplash',
		'__placeholder',
	);

	/**
	 * Construct.
	 *
	 * @param null|array|Attributes $attributes
	 * @param null|array|Settings $settings
	 * @uses $this->construct()
	 */
	function __construct( $attributes = null, $settings = null ) {
		$this->construct( $attributes, $settings );
	}

	/**
	 * Tag output.
	 *
	 * @uses $this->output_attributes()
	 * @uses Attributes::output()
	 * @return string
	 */
	function output() : string {
		if ( !$this->is_valid() )
			return '';

		$this->generate_src();

		$output  = '<img ';
		$output .= $this->output_attributes()->output();
		$output .= ' />';

		return $output;
	}

	/**
	 * Generage image source.
	 *
	 * @uses Settings::has()
	 * @uses Settings::get()
	 * @uses Attributes::has()
	 * @uses Attributes::get()
	 * @return void
	 */
	protected function generate_src() : void {
		$url = array( static::BASE_URL );

		# User (likes)
		if ( $this->settings->has( 'user', false ) ) {
			$url[] = sprintf( 'user/%s', sanitize_title_with_dashes( $this->settings->get( 'user' ) ) );

			# User likes
			if ( $this->settings->has( 'user_likes', false ) )
				$url[] = 'likes';
		}

		# Daily
		if ( $this->settings->has( 'daily', false ) )
			$url[] = 'daily';

		# Weekly
		else if ( $this->settings->has( 'weekly', false ) )
			$url[] = 'weekly';

		# Collection
		else if (
			  !$this->settings->has(       'user', false )
			&& $this->settings->has( 'collection', false )
		)
			$url[] = sprintf( 'collection/%d', absint( $this->settings->get( 'collection' ) ) );

		# Photo ID
		else if (
			  !$this->settings->has(     'user', false )
			&& $this->settings->has( 'photo_id', false )
		)
			$url[] = $this->settings->get( 'photo_id' );

		# Random
		if ( $this->settings->has( 'random', false ) )
			$url = array(
				static::BASE_URL,
				'random',
			);

		# Dimensions
		if (
			   !$this->settings->has( 'daily' )
			&& !$this->settings->has( 'weekly' )
		) {
			$dimensions = array();

			     if ( $this->settings->has(   'width', false ) ) $dimensions[] = $this->settings->get(   'width' );
			else if ( $this->attributes->has( 'width', false ) ) $dimensions[] = $this->attributes->get( 'width' );

			     if ( $this->settings->has(   'height', false ) ) $dimensions[] = $this->settings->get(   'height' );
			else if ( $this->attributes->has( 'height', false ) ) $dimensions[] = $this->attributes->get( 'height' );

			if ( 1 === count( $dimensions ) )
				$dimensions[] = $dimensions[0];

			$url[] = implode( 'x', $dimensions );

		}

		# Convert to string
		$url = implode( '/', $url );

		# Add search keywords
		if ( $this->settings->has( 'search', false ) )
			$url .= '?' . ( ( string ) $this->settings->get( 'search' ) );

		$this->attributes->update( 'src', $url );
	}

	/**
	 * Perform validation checks.
	 *
	 * @uses $this->validate_dimensions()
	 * @return WP_Error
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
			   $this->settings->has(   'width' )
			|| $this->attributes->has( 'width' )
			|| $this->settings->has(   'height' )
			|| $this->attributes->has( 'height' )
		)
			return;

		throw new \Exception( 'Unsplash Source requires at least one dimension.' );
	}

	/**
	 * Prevent conversion to same type.
	 *
	 * @param null|array|Attributes $attributes
	 * @param null|array|Settings $settings
	 * @return self
	 */
	function unsplash( $attributes = null, $settings = null ) : self {
		trigger_error( sprintf( 'Image is already type <code>%s</code>', $this->get_type() ) );
		return $this;
	}

}