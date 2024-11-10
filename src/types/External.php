<?php declare(strict_types=1);

namespace Image_Tag;

class External implements Interfaces\Image_Tag {

	use Traits\Attributes,
		Traits\Fallbacks,
		Traits\Lazysizes,
		Traits\Output,
		Traits\Settings,
		Traits\Validation;

	/** @var string */
	protected readonly string $url;

	/**
	 * Construct.
	 */
	public function __construct(
		string $url,
		Stores\Attributes $attributes = new Stores\Attributes,
		Stores\Settings $settings = new Stores\Settings
	) {
		$this->url        = $url;
		$this->attributes = $attributes;
		$this->settings   = $settings;

		$this->attributes->update( 'src', $url );
	}

	/**
	 * Validation checks.
	 */
	protected function perform_validation_checks() : \WP_Error {
		$errors = new \WP_Error;

		if ( false === wp_http_validate_url( $this->url ) ) {
			$errors->add( 'invalid_url', 'External URL is not valid.' );
		}

		return $errors;
	}

}
