<?php declare(strict_types=1);

namespace Image_Tag\Traits;

trait Output {

	use Fallbacks;

	protected \Image_Tag\Interfaces\Core $nojs;

	/**
	 * Handle cloning.
	 */
	public function __clone() : void {
		$this->attributes = clone $this->attributes;
		$this->settings   = clone $this->settings;
	}

	/**
	 * To string.
	 */
	public function __toString() : string {
		return $this->output();
	}

	/**
	 * Create noscript instance.
	 */
	public function noscript() : self {
		if ( isset( $this->nojs ) ) {
			return $this;
		}

		$this->nojs = clone $this;

		return $this;
	}

	/**
	 * Print image tag.
	 */
	public function print() : void {
		echo $this->output();
	}

	/**
	 * Output.
	 */
	public function output() : string {
		return $this->markup();
	}

	/**
	 * Return image tag markup.
	 */
	protected function markup() : string {
		$fallback = false;

		if ( ! $this->is_valid() && $this->has_fallback() ) {
			$fallback = $this->get_fallback();
		}

		if ( ! empty( $fallback ) && is_a( $fallback, \Image_Tag\Interfaces\Core::class ) ) {
			return $fallback->output();
		}

		if ( ! $this->is_valid() ) {
			return '';
		}

		$string  = '<picture>';
		$string .= sprintf( '<img %s />', (string) $this->attributes );

		if ( isset( $this->nojs ) ) {
			$string .= sprintf( '<noscript><img %s /></noscript>', (string) $this->nojs->attributes );
		}

		$string .= '</picture>';

		return $string;
	}

}
