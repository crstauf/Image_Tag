<?php
/**
 * Image tag generator for Picsum.photos.
 *
 * @link https://picsum.photos
 */

defined( 'ABSPATH' ) || die();

require_once '_Image_Tag_Placeholder.php';

/**
 * Class: Image_Tag_Picsum
 */
class Image_Tag_Picsum extends _Image_Tag_Placeholder {

	/**
	 * @var string Base URL.
	 */
	const BASE_URL = 'https://picsum.photos/';

}

?>