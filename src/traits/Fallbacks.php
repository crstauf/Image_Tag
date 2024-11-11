<?php declare(strict_types=1);

namespace Image_Tag\Traits;

trait Fallbacks {

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

	public function get_fallback() : ?\Image_Tag\Interfaces\Core {
		if ( ! $this->has_fallback() ) {
			return null;
		}

		return array_shift( $this->fallbacks );
	}

}
