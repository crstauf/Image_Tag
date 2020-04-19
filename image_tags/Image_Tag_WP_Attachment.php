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

	/**
	 * @todo add test
	 */
	function get_type() {
		return 'attachment';
	}

	/**
	 * @todo add test
	 */
	function is_type( $compare_types ) {
		if ( parent::is_type( $compare_types ) )
			return true;

		$actual_types = array(
			'upload',
			'wp-attachment',
			$this->get_type(),
			'wordpress-attachment',
		);

		foreach ( ( array ) $compare_types as $type )
			if ( in_array( strtolower( $type ), $actual_types ) )
				return true;

		return false;
	}

}

?>