<?php declare(strict_types=1);

namespace Image_Tag\Traits;

trait Validation {

	protected $valid = null;

	public function is_valid() : bool {
		return true === $this->check_valid();
	}

	protected function check_valid() : bool {
		if ( ! is_null( $this->valid ) ) {
			return $this->valid;
		}

		$errors = $this->perform_validation_checks();

		if ( ! $errors->has_errors() ) {
			return $this->valid = true;
		}

		foreach ( $errors->get_error_messages() as $message ) {
			trigger_error( $message, E_USER_NOTICE );
		}

		return $this->valid = false;
	}

	abstract protected function perform_validation_checks() : \WP_Error;

}
