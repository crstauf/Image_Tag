<?php declare(strict_types=1);

namespace Image_Tag\Traits;

trait Validation {

	public function is_valid() : bool {
		return true === $this->check_valid();
	}

	protected function check_valid() : bool {
		static $is_valid = null;

		if ( ! is_null( $is_valid ) ) {
			return $is_valid;
		}

		$errors = $this->perform_validation_checks();

		if ( ! $errors->has_errors() ) {
			return $is_valid = true;
		}

		foreach ( $errors->get_error_messages() as $message ) {
			trigger_error( $message, E_USER_NOTICE );
		}

		return $is_valid = false;
	}

	abstract protected function perform_validation_checks() : \WP_Error;

}
