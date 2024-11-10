<?php declare(strict_types=1);

namespace Image_Tag\Traits;

trait Constructed_URL {

	/** @var string[] */
	protected array $constructed_url;

	protected function url_segment( int|string $var ) : self {
		$this->constructed_url[] = $var;

		return $this;
	}

}
