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
	 * @var array Image types.
	 */
	const TYPES = array(
		'placeholder',
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
	function generate_source() : string {
		$url = array( static::BASE_URL );

		$dimensions = array();

		# Width
		     if ( $this->settings->has(   'width', false ) ) $dimensions[] = $this->settings->get(   'width' );
		else if ( $this->attributes->has( 'width', false ) ) $dimensions[] = $this->attributes->get( 'width' );

		# Height
		     if ( $this->settings->has(   'height', false ) ) $dimensions[] = $this->settings->get(   'height' );
		else if ( $this->attributes->has( 'height', false ) ) $dimensions[] = $this->attributes->get( 'height' );

		if ( 1 === count( $dimensions ) )
			$dimensions[] = $dimensions[0];

		$url[] = implode( 'x', $dimensions );

		# Colors
		if ( $this->settings->has( 'bg_color' ) ) {
			$url[] = $this->settings->get( 'bg_color' );

			if ( $this->settings->has( 'text_color' ) )
				$url[] = $this->settings->get( 'text_color' );
		}

		# Convert to string
		$url = implode( '/', $url );

		# Custom text
		if ( $this->settings->has( 'text' ) )
			$url = add_query_arg( 'text', $this->settings->get( 'text' ), $url );

		return $url;
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
			   $this->settings->has(   'width' )
			|| $this->attributes->has( 'width' )
			|| $this->settings->has(   'height' )
			|| $this->attributes->has( 'height' )
		)
			return;

		throw new \Exception( 'Placeholder requires at least one dimension.' );
	}

	/**
	 * Prevent conversion to same type.
	 *
	 * @param null|array|Attributes $attributes
	 * @param null|array|Settings $settings
	 * @return self
	 */
	function placeholder( $attributes = null, $settings = null ) : self {
		trigger_error( sprintf( 'Image is already type <code>%s</code>', $this->get_type() ) );
		return $this;
	}

}