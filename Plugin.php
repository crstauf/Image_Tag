<?php
/**
 * Image tag generator.
 *
 * Plugin name: Image Tag Generator
 * Plugin URI: https://github.com/crstauf/image_tag
 * Description: WordPress drop-in to generate <code>img</code> tags.
 * Author: Caleb Stauffer
 * Author URI: https://develop.calebstauffer.com
 * Version: 2.3
 * Requires at least: 5.1
 * Requires PHP: 7.1
 *
 * @todo add CLI command: clear common colors from attachment meta data
 * @todo add CLI command: clear LQIPs from attachment meta data
 */

declare( strict_types=1 );

namespace Image_Tag;

defined( 'WPINC' ) || die();

/**
 * Class: Image_Tag\Plugin
 */
final class Plugin {

	/**
	 * @var float
	 */
	const VERSION = 2.3;

	/**
	 * Get instance.
	 *
	 * @return self
	 */
	public static function instance() : self {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self; // @codeCoverageIgnore
		}

		return $instance;
	}

	/**
	 * Construct.
	 *
	 * @codeCoverageIgnore
	 */
	protected function __construct() {
		add_action( 'template_redirect', array( $this, 'action__template_redirect' ), 100 );
		add_action( 'admin_init', array( $this, 'action__admin_init' ) );
	}

	/**
	 * Action: template_redirect
	 *
	 * - include files, hopefully after any processing and redirection
	 *
	 * @uses static::includes()
	 *
	 * @codeCoverageIgnore
	 */
	public function action__template_redirect() : void {
		static::include_files();
	}

	/**
	 * Action: admin_init
	 *
	 * - include files, except for AJAX requests
	 *
	 * @uses static::includes()
	 *
	 * @codeCoverageIgnore
	 */
	public function action__admin_init() : void {
		if ( ! wp_doing_ajax() ) {
			return;
		}

		static::include_files();
	}

	/**
	 * Get plugin directory path.
	 *
	 * @return string
	 */
	public static function dir() : string {
		return trailingslashit( __DIR__ );
	}

	/**
	 * Get includes directory path.
	 *
	 * @uses static::dir()
	 * @return string
	 */
	public static function inc() : string {
		return static::dir() . 'includes/';
	}

	/**
	 * Include all the core files.
	 *
	 * @see Image_Tag::create() for loading of image tag types.
	 * @uses static::inc()
	 *
	 * @codeCoverageIgnore
	 */
	public static function include_files() : void {
		$dir = static::inc();

		# Interfaces.
		require_once $dir . 'interfaces/Conversion.php';
		require_once $dir . 'interfaces/Data_Store.php';
		require_once $dir . 'interfaces/Dynamic_Source.php';
		require_once $dir . 'interfaces/Output.php';
		require_once $dir . 'interfaces/Validation.php';

		# Abstracts.
		require_once $dir . 'abstracts/Base.php';
		require_once $dir . 'abstracts/Data_Store.php';
		require_once $dir . 'abstracts/WordPress.php';

		# Data.
		require_once $dir . 'data_stores/Attributes.php';
		require_once $dir . 'data_stores/Settings.php';

		# Base.
		require_once $dir . 'types/Image_Tag.php';
	}

}

Plugin::instance();
