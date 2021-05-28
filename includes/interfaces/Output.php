<?php

declare( strict_types=1 );

namespace Image_Tag\Interfaces;

defined( 'WPINC' ) || die();

/**
 * Interface: Image_Tag\Intefaces\Output
 */
interface Output {

	function output() : string;
	function lazyload( array $fallbacks = array() ) : string;
	function noscript( array $fallbacks = array() ) : string;

}