<?php declare(strict_types=1);

namespace Image_Tag;

final class Manager {

	/**
	 * Get singleton instance.
	 */
	public static function instance() : self {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
		}

		return $instance;
	}

	/**
	 * Initialize singleton.
	 */
	public static function init() : void {
		static $init = false;

		if ( false !== $init ) {
			trigger_warning( 'Singleton already initialized.', E_USER_NOTICE );
			return;
		}

		$instance = static::instance();

		add_action( 'template_redirect', array( $instance, 'include_files' ) );

		$init = true;
	}

	/**
	 * Construct.
	 */
	protected function __construct() {

	}

	/**
	 * Include the files.
	 */
	public function include_files() : void {
		require_once 'traits/Attributes_Helper.php';
		require_once 'traits/Lazysizes.php';
		require_once 'traits/Noscript.php';
		require_once 'traits/Settings_Helper.php';

		require_once 'stores/Attributes.php';
		require_once 'stores/Settings.php';

		require_once 'types/External.php';
	}

}
