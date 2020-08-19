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
 * Requires at least: 5.1
 * Requires PHP: 7.1
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || die();

/**
 * Class: Image_Tag_Plugin.
 */
final class Image_Tag_Plugin {

	/**
	 * @var float
	 */
	const VERSION = 2.0;

	/**
	 * Get instance.
	 *
	 * @return self
	 */
	static function instance() : self {
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
	function action__template_redirect() : void {
		static::include_files();
	}

	/**
	 * Get plugin directory path.
	 *
	 * @return string
	 */
	static function dir() : string {
		return trailingslashit( __DIR__ );
	}

	/**
	 * Get includes directory path.
	 *
	 * @uses static::dir()
	 * @return string
	 */
	static function inc() : string {
		return static::dir() . 'includes/';
	}

	/**
	 * Include all the core files.
	 *
	 * @see Image_Tag::create() for loading of image tag types.
	 * @uses static::inc()
	 */
	static function include_files() : void {
		$dir = static::inc();

		# Interfaces.
		require_once $dir . 'interfaces/attributes.php';
		require_once $dir . 'interfaces/settings.php';
		require_once $dir . 'interfaces/sources.php';
		require_once $dir . 'interfaces/validation.php';

		# Abstracts.
		require_once $dir . 'abstracts/helpers.php';
		require_once $dir . 'abstracts/image_tag.php';

	}

}

Image_Tag_Plugin::instance();

add_action( 'get_header', function() {
	echo Image_Tag::create( 'https://source.unsplash.com/random/800x600' );
	exit;
} );

?>