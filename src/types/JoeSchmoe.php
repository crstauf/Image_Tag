<?php declare(strict_types=1);

namespace Image_Tag;

class JoeSchmoe implements Interfaces\Core {

	use Traits\Attributes,
		Traits\Dimensions,
		Traits\Constructed_URL,
		Traits\Fallbacks,
		Traits\Lazysizes,
		Traits\Output,
		Traits\Settings,
		Traits\Validation;

	public const BASE_URL = 'https://joeschmoe.crstauf.workers.dev';

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

	public function width() : int {
		return 125;
	}

	public function height() : int {
		return 125;
	}

	/**
	 * Validation checks.
	 */
	protected function perform_validation_checks() : \WP_Error {
		$errors = new \WP_Error;

		if (
			$this->settings->has( 'gender' )
			&& ! in_array( $this->settings->get( 'gender' ), array( 'male', 'female' ) )
		) {
			$errors->add( 'joeschmoe_binary_gender', 'Joe Schmoes are only available in male and female genders' );
		}

		if ( $this->settings->has( 'seed' ) && ! is_scalar( $this->settings->get( 'seed' ) ) ) {
			$errors->add( 'joeschmoe_invalid_seed', 'Seed for Joe Schmoe must be a string or number.' );
		}

		return $errors;
	}

	/**
	 * Construct the URL.
	 */
	public function construct_url() : void {
		$this->url_segment( self::BASE_URL );

		// Gender.
		$this->url_segment( $this->settings->get( 'gender' ) );

		// Seed.
		$seed = $this->settings->get( 'seed', uniqid( 'random-' ) );
		$this->url_segment( $seed );

		$this->set_constructed_url();
	}

}
