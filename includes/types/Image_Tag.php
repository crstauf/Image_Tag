<?php

declare( strict_types=1 );

use Image_Tag\Plugin;
use Image_Tag\Abstracts\Base;

defined( 'WPINC' ) || die();

/**
 * Class: Image_Tag
 */
class Image_Tag extends Base {

	/**
	 * @var array Image types.
	 */
	const TYPES = array(
		'base',
		'default',
	);

	/**
	 * Create image tag.
	 *
	 * @param int|string $source
	 * @param null|array|Attributes $attributes
	 * @param null|array|Settings $settings
	 * @return Image_Tag
	 */
	static function create( $source, $attributes = null, $settings = null ) : Base {
		$source = strtolower( $source );

		if ( 'picsum' === $source ) {
			require_once Plugin::inc() . 'types/Picsum.php';
			return new Image_Tag\Types\Picsum( $attributes, $settings );
		}

		if ( 'unsplash' === $source ) {
			require_once Plugin::inc() . 'types/Unsplash.php';
			return new Image_Tag\Types\Unsplash( $attributes, $settings );
		}

		if ( ( bool ) wp_http_validate_url( $source ) )
			return new Image_Tag( $source, $attributes, $settings );

		return new Image_Tag( $attributes, $settings );
	}

	/**
	 * Construct.
	 *
	 * @param string $source
	 * @param null|array|Attributes $attributes
	 * @param null|array|Settings $settings
	 * @uses $this->construct()
	 * @uses Attributes->set()
	 */
	function __construct( string $source, $attributes = null, $settings = null ) {
		$this->construct( $attributes, $settings );
		$this->attributes->set( 'src', $source );
	}

	/**
	 * Perform validation checks.
	 *
	 * @uses Attributes::has()
	 * @return WP_Error
	 */
	protected function perform_validation_checks() : WP_Error {
		$errors = new \WP_Error;

		if ( !$this->attributes->has( 'src' ) )
			$errors->add( 'empty_source', 'Image tag requires at least one source.' );

		return $errors;
	}

}