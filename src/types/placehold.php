<?php declare(strict_types=1);

namespace Image_Tag;

class Placehold implements Interfaces\Core {

	use Traits\Attributes,
		Traits\Dimensions,
		Traits\Constructed_URL,
		Traits\Fallbacks,
		Traits\Lazysizes,
		Traits\Output,
		Traits\Settings,
		Traits\Validation;

	public const BASE_URL = 'https://placehold.co';

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
		$this->url_segment( self::BASE_URL );

		// Width.
		$dimensions = $this->width();

		// Height.
		if ( ! empty( $this->height() ) ) {
			$dimensions .= 'x' . $this->height();
		}

		// Retina.
		if ( $this->settings->has( 'retina' ) ) {
			$retina      = absint( $this->settings->get( 'retina' ) );
			$dimensions .= sprintf( '@%dx', $retina );
		}

		$this->url_segment( $dimensions );

		// Colors.
		if ( $this->settings->has( 'bg_color' ) && $this->settings->has( 'fg_color' ) ) {
			$this->url_segment( $this->settings->get( 'bg_color' ) )
				->url_segment( $this->settings->get( 'fg_color' ) );
		}

		// Format.
		if ( $this->settings->has( 'format' ) ) {
			$this->url_segment( $this->settings->get( 'format' ) );
		}

		$url = implode( '/', $this->constructed_url );

		// Custom text.
		if ( $this->settings->has( 'text' ) ) {
			$text = urlencode( $this->settings->get( 'text' ) );
			$url  = add_query_arg( 'text', $text, $url );
		}

		$this->attribute( 'src', $url );
	}

}
