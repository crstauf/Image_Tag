<?php declare(strict_types=1);

namespace Image_Tag\Traits;

use Image_Tag\Stores\Attributes as Attributes_Store;
use Image_Tag\Stores\Settings as Settings_Store;

trait Attributes {

	/** @var \Image_Tag\Stores\Attributes */
	public Attributes_Store $attributes;

	/**
	 * Add attribute to store.
	 */
	public function attribute( string $key, $value ) {
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
