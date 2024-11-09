<?php declare(strict_type=1);

namespace Image_Tag\Traits;

require_once 'Noscript.php';

trait Lazysizes {

	use Noscript;

	public const BLANK = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';

	public function lazysizes() : self {
		$this->attributes->set( 'data-src', $this->attributes->src );
		$this->attributes->set( 'src', self::BLANK );
		$this->attributes->unset( 'loading' );

		$class = $this->attributes->class ?? '';

		return $this;
	}

}
