<?php declare(strict_types=1);

namespace Image_Tag\Traits;

trait Output {

	protected \Image_Tag\Interfaces\Image_Tag $nojs;

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
	 * Return image tag.
	 */
	public function output() : string {
		$string  = '<picture>';

		$string .= '<img ';
		$string .= (string) $this->attributes;
		$string .= ' />';

		if ( isset( $this->nojs ) ) {
			$string .= '<noscript>';
			$string .= '<img ';
			$string .= (string) $this->nojs->attributes;
			$string .= ' />';
			$string .= '</noscript>';
		}

		$string .= '</picture>';

		return $string;
	}

	/**
	 * Print image tag.
	 */
	public function print() : void {
		echo $this->output();
	}

}
