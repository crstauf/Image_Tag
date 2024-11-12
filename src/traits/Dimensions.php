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

	public function ratio() : float {
		if ( empty( $this->width() ) || empty( $this->height() ) ) {
			return (float) 0;
		}

		return $this->width() / $this->height();
	}

	public function oriented() : string {
		return match ( true ) {
			$this->ratio() > 1 => 'landscape',
			$this->ratio() < 1 => 'portrait',
			default            => 'square',
		};
	}

}
