<?php
/**
 * Image tag generator for WordPress attachment images.
 */

defined( 'ABSPATH' ) || die();

require_once '_Image_Tag_WP.php';

/**
 * Class: Image_Tag_WP_Attachment
 */
class Image_Tag_WP_Attachment extends _Image_Tag_WP {

	/**
	 * @var int $attachment_id
	 */
	protected $attachment_id;

}

?>