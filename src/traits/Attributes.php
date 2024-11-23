<?php declare(strict_types=1);

namespace Image_Tag\Traits;

use Image_Tag\Stores\Attributes as Attributes_Store;
use Image_Tag\Stores\Settings as Settings_Store;

trait Attributes {

	/** @var \Image_Tag\Stores\Attributes */
	public readonly Attributes_Store $attributes;

	/**
	 * Add attribute to store.
	 */
	public function attribute( string $key, $value ) {
		$method_name = sprintf( 'attribute__%s', preg_replace( '/[^a-zA-Z0-9_]/', '_', $key ) );

		if ( is_callable( array( $this, $method_name ) ) ) {
			call_user_func( array( $this, $method_name ), $value );
		}

		$this->attributes->update( $key, $value );

		return $this;
	}

	/**
	 * Add attributes to store.
	 */
	public function attributes( array $data ) {
		foreach ( $data as $key => $value ) {
			$this->attribute( $key, $value );
		}

		return $this;
	}

}
