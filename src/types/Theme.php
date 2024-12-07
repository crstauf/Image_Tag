<?php declare(strict_types=1);

namespace Image_Tag;

class Theme implements Interfaces\Core {

	use Traits\Attributes,
		Traits\Common_Colors,
		Traits\Dimensions,
		Traits\Fallbacks,
		Traits\Lazysizes,
		Traits\Local,
		Traits\LQIP,
		Traits\Output,
		Traits\Settings,
		Traits\Validation;

	/** @var string */
	protected readonly string $relpath;

	/** @var string */
	protected readonly string $url;

	/**
	 * Construct.
	 */
	public function __construct(
		string $relpath,
		Stores\Attributes $attributes = new Stores\Attributes,
		Stores\Settings $settings = new Stores\Settings
	) {
		$this->relpath    = $relpath;
		$this->attributes = $attributes;
		$this->settings   = $settings;

		$this->path = get_theme_file_path( $relpath );
		$this->url  = get_theme_file_uri( $relpath );

		$this->attribute( 'src', $this->url );
	}

	/**
	 * Validation checks.
	 */
	protected function perform_validation_checks() : \WP_Error {
		$errors = new \WP_Error;
		$checks = true;

		if ( ! file_exists( $this->path ) ) {
			$errors->add( 'does_not_exist', 'Theme image file does not exist.' );
			$checks = false;
		}

		if ( $checks && false === wp_http_validate_url( $this->url ) ) {
			$errors->add( 'invalid_url', 'Theme image URL is not valid.' );
		}

		return $errors;
	}

	protected function dimensions() : array {
		static $dimensions = null;

		if ( ! $this->is_valid() ) {
			return $dimensions = [ 0, 0 ];
		}

		if ( is_null( $dimensions ) ) {
			$dimensions = getimagesize( $this->path );
		}

		return array_slice( $dimensions, 0, 2 );
	}

	protected function determine_width() : int {
		return $this->dimensions()[0];
	}

	protected function determine_height() : int {
		return $this->dimensions()[1];
	}

	protected function lqip_as_args() : array {
		return array(
			'classname' => self::class,
			'args'      => array(
				$this->relpath,
				$this->attributes,
				$this->settings,
			),
		);
	}

}
