<?php

declare( strict_types=1 );

namespace Image_Tag\Interfaces;

defined( 'WPINC' ) || die();

/**
 * Interface: Image_Tag\Interfaces\Output
 */
interface Output {

	public function output() : string;
	public function lazyload() : string;
	public function noscript() : string;

}