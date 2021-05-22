<?php

declare( strict_types=1 );

namespace Image_Tag\Interfaces;

defined( 'WPINC' ) || die();

/**
 * Interface: Image_Tag\Interfaces\Dynamic_Source
 */
interface Dynamic_Source {

	function generate_source() : string;

}