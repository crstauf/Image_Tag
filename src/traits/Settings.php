<?php declare(strict_types=1);

namespace Image_Tag\Traits;

use Image_Tag\Stores\Attributes as Attributes_Store;
use Image_Tag\Stores\Settings as Settings_Store;

trait Settings {

	/** @var \Image_Tag\Stores\Settings */
	public Settings_Store $settings;

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
