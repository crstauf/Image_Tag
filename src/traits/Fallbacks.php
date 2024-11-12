<?php declare(strict_types=1);

namespace Image_Tag\Traits;

require_once 'Validation.php';

trait Fallbacks {

	use Validation;

	/** @var string[] */
	protected $fallbacks = array();

	public function fallback( \Image_Tag\Interfaces\Core $object, bool $conditional = true ) {
		if ( ! $conditional || ! $object->is_valid() ) {
			return $this;
		}

		$this->fallbacks[] = $object;

		return $this;
	}

	public function has_fallback() : bool {
		return ! empty( $this->fallbacks );
	}

	public function get_fallback() : \Image_Tag\Interfaces\Core {
		if ( ! $this->has_fallback() ) {
			return new \Image_Tag\External( '' );
		}

		return $this->fallbacks[0];
	}

	public function get_valid() : \Image_Tag\Interfaces\Core {
		if ( $this->is_valid() ) {
			return $this;
		}

		if ( method_exists( $this, 'get_fallback' ) ) {
			return $this->get_fallback();
		}

		return new Image_Tag\External( '' );
	}

}
