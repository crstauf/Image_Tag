<?php declare(strict_types=1);

namespace Image_Tag\Traits;

trait Noscript {

	protected $noscript = false;

	public function noscript() : self {
		if ( $this->noscript ) {
			return;
		}

		$this->noscript = true;

		return $this;
	}

}
