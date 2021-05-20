<?php

declare( strict_types=1 );

defined( 'WPINC' ) || die();

class Image_Tag extends \Image_Tag\Abstracts\Base {

	const TYPES = array(
		'base',
		'default',
	);

	static function create( $source, $attributes = null, $settings = null ) : Image_Tag {

		if ( esc_url_raw( $source ) === $source ) {
			$object = new Image_Tag( $source, $attributes, $settings );
			return $object;
		}

		return new Image_Tag( $attributes, $settings );
	}

	function __construct( string $source, $attributes = null, $settings = null ) {
		$this->construct( $attributes, $settings );
		$this->attributes->set( 'src', $source );
	}

	protected function perform_validation_checks() : WP_Error {
		$errors = new WP_Error;

		if ( !$this->attributes->has( 'src' ) )
			$errors->add( 'empty_source', 'Image tag requires at least one source.' );

		return $errors;
	}

}