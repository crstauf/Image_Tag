<?php declare(strict_types=1);

namespace Image_Tag\Traits;

trait Attributes_Helper {

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
