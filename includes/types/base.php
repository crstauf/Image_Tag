<?php
declare( strict_types=1 );

defined( 'ABSPATH' ) || die();

/**
 * Class: Image_Tag_Base
 */
class Image_Tag_Base extends Image_Tag implements Image_Tag_Attributes_Interface, Image_Tag_Settings_Interface, Image_Tag_Validation_Interface {

	/**
	 * Keywords to identify image type.
	 * @var string[]
	 */
	const TYPES = array(
		'base',
		'default',
	);

}