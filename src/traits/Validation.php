<?php declare(strict_types=1);

namespace Image_Tag\Traits;

trait Validation {

	public function is_valid() : bool {
		return true === $this->check_valid();
	}

	protected function check_valid() {
		$errors = $this->perform_validation_checks();

		if ( $errors->has_errors() ) {
			return $errors;
		}

		return true;
	}

	abstract protected function perform_validation_checks() : \WP_Error;

}
