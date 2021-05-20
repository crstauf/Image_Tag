<?php
/**
 * Main abstract class for Image Tag types.
 */

declare( strict_types=1 );

namespace Image_Tag\Abstracts;
use Image_Tag\Data_Stores\Attributes;
use Image_Tag\Data_Stores\Settings;
use Image_Tag\Data_Stores\Sources;
use Image_Tag\Interfaces\Validation;

defined( 'ABSPATH' ) || die();

/**
 * Abstract class: Image_Tag\Abstracts\Base
 */
abstract class Base implements Validation {

	/**
	 * Smallest transparent data URI image.
	 * @var string
	 */
	const BLANK = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';

	protected $attributes = null;
	protected $settings   = null;
	protected $sources    = null;

	function __get( string $property ) {
		return $this->$property;
	}

	function __toString() : string {
		return $this->output();
	}

	protected function construct( $attributes, $settings ) : void {
		if ( is_a( $attributes, Image_Tag::class ) )
			foreach ( get_object_vars( $attributes ) as $key => $value )
				$this->$key = $value;

		$this->create_Attributes( $attributes );
		$this->create_Settings( $settings );
	}

	protected function create_Attributes( $attributes ) : void {
		if ( !is_null( $this->attributes ) )
			return;

		$this->attributes = new Attributes( $attributes );
	}

	protected function create_Settings( $settings ) : void {
		if ( !is_null( $this->settings ) )
			return;

		$this->settings = new Settings( $settings );
	}

	protected function create_Sources( $sources = array() ) : void {
		if ( !is_null( $this->sources ) )
			return;

		$this->sources = new Sources( $sources );
	}

	function output() : string {
		if ( !$this->is_valid() )
			return '';

		$output  = '<img ';
		$output .= $this->output_attributes()->output();
		$output .= ' />';

		return $output;
	}

	protected function output_attributes() : Attributes {
		$attributes = new Attributes( $this->attributes );

		/**
		 * Better to have an empty alt attribute than none at all.
		 *
		 * @link https://www.a11y-101.com/development/the-alt-attribute
		 */
		if ( !array_key_exists( 'alt', $this->attributes->store ) )
			$attributes->set( 'alt', '' );

		return $attributes;
	}

	function get_type() : string {
		return static::TYPES[0];
	}

	function is_type( $test_types ) : bool {
		return !empty( array_intersect( static::TYPES, ( array ) $test_types ) );
	}

	function is_valid( $test_types = null ) : bool {
		if (
			!is_null( $test_types )
			&& $this->is_type( $test_types )
		)
			return false;

		return true === $this->check_valid();
	}

	private function check_valid() {
		$errors = $this->perform_validation_checks();

		if ( $errors->has_errors() )
			return $errors;

		return true;
	}

	abstract protected function perform_validation_checks() : \WP_Error;

}