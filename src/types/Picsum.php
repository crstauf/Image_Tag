<?php declare(strict_types=1);

namespace Image_Tag;

class Picsum implements Interfaces\Core {

	use Traits\Attributes,
		Traits\Dimensions,
		Traits\Constructed_URL,
		Traits\Fallbacks,
		Traits\Lazysizes,
		Traits\Output,
		Traits\Settings,
		Traits\Validation;

	public const BASE_URL = 'https://picsum.photos';

	/**
	 * Construct.
	 */
	public function __construct(
		object $attributes = new Stores\Attributes,
		object $settings = new Stores\Settings
	) {
		$this->attributes = new Stores\Attributes( $attributes );
		$this->settings   = new Stores\Settings( $settings );
	}

	public function height() : int {
		$height = $this->find_height();

		if ( empty( $height ) ) {
			return $this->width();
		}

		return $height;
	}

	/**
	 * Validation checks.
	 */
	protected function perform_validation_checks() : \WP_Error {
		$errors = new \WP_Error;

		if ( empty( $this->width() ) ) {
			$errors->add( 'required_width', 'Width is required.' );
		}

		return $errors;
	}

	/**
	 * Construct the URL.
	 */
	public function construct_url() : void {
		static $random = 1;

		$this->url_segment( self::BASE_URL );

		// Image ID.
		if ( $this->settings->has( 'image-id' ) ) {
			$this->url_segment( sprintf( 'id/%d', absint( $this->settings->get( 'image-id' ) ) ) );
		}

		// Seed.
		if ( $this->settings->has( 'seed' ) ) {
			$this->url_segment( sprintf( 'seed/%s', sanitize_title_with_dashes( $this->settings->get( 'seed' ) ) ) );
		}

		$this->url_segment( $this->width() );

		if ( ! empty( $this->height() ) ) {
			$this->url_segment( $this->height() );
		}

		$url  = implode( '/', $this->constructed_url );
		$url .= '.webp';

		// Grayscale.
		if ( $this->settings->has( 'grayscale' ) ) {
			$url = add_query_arg( 'grayscale', 1, $url );
		}

		// Blur.
		if ( $this->settings->has( 'blur' ) ) {
			$url = add_query_arg( 'blur', absint( $this->settings->get( 'blur' ) ), $url );
		}

		// Random.
		$url = add_query_arg( 'random', $random++, $url );

		$this->attribute( 'src', $url );
	}

}
