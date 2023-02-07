<?php
/**
 * Placeholder image service: joeschmoe.io
 *
 * @link https://joeschmoe.io/
 */

declare( strict_types=1 );

namespace Image_Tag\Types;

use Image_Tag\Data_Stores\Attributes;
use Image_Tag\Data_Stores\Settings;

defined( 'WPINC' ) || die();

/**
 * Class: Image_Tag\Types\JoeSchmoe
 */
class JoeSchmoe extends \Image_Tag\Abstracts\Base implements \Image_Tag\Interfaces\Dynamic_Source {

	const BASE_URL = 'https://joeschmoe.io/api/v1';

	/**
	 * @var string[] Image types.
	 */
	const TYPES = array(
		'joeschmoe',
		'schmoe',
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
	 * @return string
	 */
	public function generate_source() : string {
		if ( array_key_exists( __FUNCTION__, $this->cache ) ) {
			return $this->cache[ __FUNCTION__ ];
		}

		$src = array( static::BASE_URL );

		# Gender
		if ( $this->settings->has( 'gender' ) ) {
			$src[] = $this->settings->get( 'gender' );
		}

		# Seed
		if ( $this->settings->has( 'seed' ) ) {
			$src[] = $this->settings->get( 'seed' );
		} else {
			$src[] = uniqid( 'random-' );
		}

		# Convert to string
		$src = implode( '/', $src );

		$this->cache[ __FUNCTION__ ] = $src;

		return $src;
	}

	/**
	 * Perform validation checks.
	 *
	 * @uses Settings::has()
	 * @uses Settings::get()
	 * @return \WP_Error
	 */
	protected function perform_validation_checks() : \WP_Error {
		$errors = new \WP_Error;

		if (
			$this->settings->has( 'gender' )
			&& ! in_array( $this->settings->get( 'gender' ), array( 'male', 'female' ) )
		) {
			$errors->add( 'joeschmoe_binary_gender', 'Joe Schmoes are only available in male and female genders' );
		}

		return $errors;
	}

}