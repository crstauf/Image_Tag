<?php
/**
 * Placeholder image service: Lorem Picsum
 *
 * @link https://picsum.photos/
 */

declare( strict_types=1 );

namespace Image_Tag\Types;
use Image_Tag\Data_Store\Attributes;
use Image_Tag\Data_Store\Settings;

defined( 'WPINC' ) || die();

/**
 * Class: Image_Tag\Types\Picsum
 */
class Picsum extends \Image_Tag\Abstracts\Base {

	const BASE_URL = 'https://picsum.photos';

	/**
	 * @var array Image types.
	 */
	const TYPES = array(
		'picsum',
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
	 * Generate image source.
	 *
	 * @uses Settings::has()
	 * @uses Settings::get()
	 * @uses Attributes::has()
	 * @uses Attributes::get()
	 * @uses Attributes::update()
	 * @return void
	 */
	protected function generate_src() : void {
		static $random = 1;

		$src = array( static::BASE_URL );

		# Image ID
		if ( $this->settings->has( 'image_id', false ) )
			$src[] = sprintf( 'id/%d', absint( $this->settings->get( 'image_id' ) ) );

		# Seed
		if ( $this->settings->has( 'seed', false ) )
			$src[] = sprintf( 'seed/%s', sanitize_title_with_dashes( $this->settings->get( 'seed' ) ) );

		# Width
		     if ( $this->settings->has(   'width', false ) ) $src[] = $this->settings->get(   'width' );
		else if ( $this->attributes->has( 'width', false ) ) $src[] = $this->attributes->get( 'width' );

		# Height
		     if ( $this->settings->has(   'height', false ) ) $src[] = $this->settings->get(   'height' );
		else if ( $this->attributes->has( 'height', false ) ) $src[] = $this->attributes->get( 'height' );

		# Convert to string
		$src = implode( '/', $src );

		# Grayscale
		if ( $this->settings->has( 'grayscale', false ) )
			$src = add_query_arg( 'grayscale', 1, $src );

		# Blur
		if ( $this->settings->has( 'blur', false ) )
			$src = add_query_arg( 'blur', absint( $this->settings->get( 'blur' ) ), $src );

		if ( $this->settings->has( 'random', false ) )
			$src = add_query_arg( 'random', $random++, $src );

		$this->attributes->update( 'src', $src );
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
			$errors->add( $e->getCode(), $e->getMessage() );
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

		throw new \Exception( 'Picsum requires at least one dimension.' );
	}

}