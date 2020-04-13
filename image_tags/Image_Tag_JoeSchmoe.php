<?php
/**
 * Image tag generator for JoeSchmoe.
 *
 * @link https://joeschmoe.io/
 */

defined( 'ABSPATH' ) || die();

require_once '_Image_Tag_Placeholder.php';

/**
 * Class: Image_Tag_JoeSchmoe
 */
class Image_Tag_JoeSchmoe extends _Image_Tag_Placeholder {

	/**
	 * @var string Base URL.
	 */
	const BASE_URL = 'https://joeschmoe.io/api/v1/';

}

?>