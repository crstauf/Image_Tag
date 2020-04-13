<?php
/**
 * Image tag generator for Unsplash Source.
 *
 * @link https://source.unsplash.com/
 */

defined( 'ABSPATH' ) || die();

require_once '_Image_Tag_Placeholder.php';

/**
 * Class: Image_Tag_Unsplash
 */
class Image_Tag_Unsplash extends Image_Tag_Placeholder {

	/**
	 * @var string Base URL.
	 */
	const BASE_URL = 'https://source.unsplash.com/';

}

?>