<?php declare(strict_types=1);

namespace Image_Tag\Traits;

trait Local {

	/** @var string */
	protected readonly string $path;

	public function path() : string {
		return $this->path;
	}

}
