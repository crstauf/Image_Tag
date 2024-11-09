<?php declare(strict_types=1);

namespace Image_Tag\Traits;

trait Settings_Helper {

	/**
	 * Add setting to store.
	 */
	public function setting( string $key, $value ) {
		$this->attributes->update( $key, $value );

		return $this;
	}

	/**
	 * Add settings to store.
	 */
	public function settings( array $data ) {
		foreach ( $data as $key => $value ) {
			$this->attribute( $key, $value );
		}

		return $this;
	}

}
