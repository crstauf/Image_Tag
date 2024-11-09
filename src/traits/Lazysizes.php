<?php declare(strict_types=1);

namespace Image_Tag\Traits;

trait Lazysizes {

	public const BLANK = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';

	/**
	 * Integrate lazysizes.
	 */
	public function lazysizes() : self {
		static $once = false;

		if ( $once ) {
			return $this;
		}

		$once = true;

		$this->noscript();
		$this->nojs->attribute( 'loading', 'lazy' );

		$class  = $this->attributes->class ?? '';
		$class .= ' lazysizes hide-if-no-js';

		$this->attributes
			->move( 'src', 'data-src' )
			->unset( 'loading' );

		$this->attribute( 'src', self::BLANK )
			->attribute( 'class', trim( $class ) );

		if ( isset( $this->attributes->srcset ) ) {
			$this->attributes
				->move( 'srcset', 'data-srcset' )
				->move( 'sizes', 'data-sizes', 'auto' )
				->unset(
					'sizes',
					'srcset'
				);
		}

		return $this;
	}

}
