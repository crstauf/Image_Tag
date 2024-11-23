<?php declare(strict_types=1);

namespace Image_Tag\Traits;

use Image_Tag\Stores\Attributes as Attributes_Store;
use Image_Tag\Stores\Settings as Settings_Store;

trait Settings {

	/** @var \Image_Tag\Stores\Settings */
	public readonly Settings_Store $settings;

	/**
	 * Add setting to store.
	 */
	public function setting( string $key, $value ) {
		$method_name = sprintf( 'setting__%s', preg_replace( '/[^a-zA-Z0-9_]/', '_', $key ) );

		if ( is_callable( array( $this, $method_name ) ) ) {
			call_user_func( array( $this, $method_name ), $value );
			return $this;
		}

		$this->settings->update( $key, $value );

		return $this;
	}

	/**
	 * Add settings to store.
	 */
	public function settings( array $data ) {
		foreach ( $data as $key => $value ) {
			$this->setting( $key, $value );
		}

		return $this;
	}

}
