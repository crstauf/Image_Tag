<?php
/**
 * Image tag generator.
 *
 * Plugin name: Image Tag Generator
 * Plugin URI: https://github.com/crstauf/image_tag
 * Description: WordPress drop-in to generate <code>img</code> tags.
 * Author: Caleb Stauffer
 * Author URI: https://develop.calebstauffer.com
 * Version: 2.0
 * Requires at least: 5.4
 * Requires PHP: 7.3
 */

defined( 'ABSPATH' ) || die();

/**
 * Class: Image_Tag_Plugin
 *
 * @todo add tests
 */
class Image_Tag_Plugin {

	/**
	 * @var float
	 */
	const VERSION = 2.0;

	/**
	 * Get instance.
	 *
	 * @return self
	 */
	static function instance() {
		static $instance = null;

		if ( is_null( $instance ) )
			$instance = new self;

		return $instance;
	}

	/**
	 * Construct.
	 */
	protected function __construct() {

		add_action( 'template_redirect', array( $this, 'action__template_redirect' ), 100 );

	}

	/**
	 * Action: template_redirect
	 *
	 * - include files, hopefully after any processing and redirection
	 *
	 * @uses static::includes()
	 */
	function action__template_redirect() {
		static::includes();
	}

	/**
	 * Include all the files.
	 */
	static function includes() {

		# Abstracts.
		require_once 'image_tags/Image_Tag_Abstract.php';
		require_once 'image_tags/Image_Tag_Properties_Abstract.php';

		# Properties.
		require_once 'image_tags/Image_Tag_Attributes.php';
		require_once 'image_tags/Image_Tag_Settings.php';

		# Types.
		require_once 'image_tags/Image_Tag.php';

	}

}

Image_Tag_Plugin::instance();

?>