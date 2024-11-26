<?php declare(strict_types=1);

namespace Image_Tag\Traits;

trait Dimensions {

	protected function find_width() : int {
		return match ( true ) {
			$this->settings->has( 'width' )   => absint( $this->settings->get( 'width' ) ),
			$this->attributes->has( 'width' ) => absint( $this->attributes->get( 'width' ) ),
			default                           => 0,
		};
	}

	protected function find_height() : int {
		return match ( true ) {
			$this->settings->has( 'height' )   => absint( $this->settings->get( 'height' ) ),
			$this->attributes->has( 'height' ) => absint( $this->attributes->get( 'height' ) ),
			default                            => 0,
		};
	}

	public function width() : int {
		return $this->find_width();
	}

	public function height() : int {
		return $this->find_height();
	}

	public function ratio( string $format = 'float' ) : string|float {
		return match ( $format ) {
			'float' => $this->ratio_as_float(),
			default => $this->ratio_as_string(),
		};
	}

	protected function ratio_as_float() : float {
		if ( empty( $this->width() ) || empty( $this->height() ) ) {
			return (float) 0;
		}

		return $this->width() / $this->height();
	}

	protected function ratio_as_string() : string {
		$w = $this->width();
		$h = $this->height();
		$g = $this->gcd();

		$w = $w / $g;
		$h = $h / $g;

		return sprintf( '%d:%d', $w, $h );
	}

	protected function gcd() {
		$a = $this->width();
		$b = $this->height();

		if ( $a < $b ) {
			list( $b, $a ) = array( $a, $b );
		}

		if ( 0 === $b ) {
			return $a;
		}

		$r = $a % $b;

		while ( $r > 0 ) {
			$a = $b;
			$b = $r;
			$r = $a % $b;
		}

		return $b;
	}

	public function oriented() : string {
		return match ( true ) {
			$this->ratio() > 1 => 'landscape',
			$this->ratio() < 1 => 'portrait',
			default            => 'square',
		};
	}

}
