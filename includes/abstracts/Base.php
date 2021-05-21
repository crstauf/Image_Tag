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

	/**
	 * @var null|Attributes $attributes
	 * @var null|Settings $settings
	 */
	protected $attributes = null;
	protected $settings   = null;

	/**
	 * Getter.
	 *
	 * @param string $property
	 * @return mixed
	 */
	function __get( string $property ) {
		return $this->$property;
	}

	/**
	 * String output.
	 *
	 * @uses $this->output()
	 * @return string
	 */
	function __toString() : string {
		return $this->output();
	}

	/**
	 * Construct helper.
	 *
	 * @param null|array|Attributes $attributes
	 * @param null|array|Settings $settings
	 * @uses $this->create_Attributes()
	 * @uses $this->create_Settings()
	 * @return void
	 */
	protected function construct( $attributes, $settings ) : void {
		if ( is_a( $attributes, Image_Tag::class ) )
			foreach ( get_object_vars( $attributes ) as $key => $value )
				$this->$key = $value;

		$this->create_Attributes( $attributes );
		$this->create_Settings( $settings );
	}

	/**
	 * Create Attributes object.
	 *
	 * @param void
	 */
	protected function create_Attributes( $attributes ) : void {
		if ( !is_null( $this->attributes ) )
			return;

		$this->attributes = new Attributes( $attributes );
	}

	/**
	 * Create Settings object.
	 *
	 * @return void
	 */
	protected function create_Settings( $settings ) : void {
		if ( !is_null( $this->settings ) )
			return;

		$this->settings = new Settings( $settings );
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

		$output  = '<img ';
		$output .= $this->output_attributes()->output();
		$output .= ' />';

		return $output;
	}

	/**
	 * Create Attributes object to use for output.
	 *
	 * @uses Attributes::__construct()
	 * @return Attributes
	 */
	protected function output_attributes() : Attributes {
		$attributes = new Attributes( $this->attributes );

		/**
		 * Better to have an empty alt attribute than none at all.
		 *
		 * @link https://www.a11y-101.com/development/the-alt-attribute
		 */
		if ( !$this->attributes->has( 'alt' ) ) {
			$alt = '';

			if ( $this->attributes->has( 'title', false ) )
				$alt = $this->attributes->get( 'title' );

			$attributes->set( 'alt', $alt );
		}

		return $attributes;
	}

	/**
	 * Get image type.
	 *
	 * @return string
	 */
	function get_type() : string {
		return static::TYPES[0];
	}

	/**
	 * Test image type.
	 *
	 * @param string|array $test_types
	 * @return bool
	 */
	function is_type( $test_types ) : bool {
		return !empty( array_intersect( static::TYPES, ( array ) $test_types ) );
	}

	/**
	 * Test image is valid.
	 *
	 * @param null|string|array $test_types
	 * @uses $this->is_type()
	 * @uses $this->check_valid()
	 * @return bool
	 */
	function is_valid( $test_types = null ) : bool {
		if (
			!is_null( $test_types )
			&& $this->is_type( $test_types )
		)
			return false;

		return true === $this->check_valid();
	}

	/**
	 * Collect errors from validation checks.
	 *
	 * @uses $this->perform_validation_checks()
	 * @return \WP_Error|true
	 */
	private function check_valid() {
		$errors = $this->perform_validation_checks();

		if ( $errors->has_errors() )
			return $errors;

		return true;
	}

	/**
	 * Perform validation checks.
	 *
	 * @return \WP_Error
	 */
	abstract protected function perform_validation_checks() : \WP_Error;

}