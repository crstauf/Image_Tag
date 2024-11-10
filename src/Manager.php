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
		require_once 'interfaces/Image_Tag.php';

		require_once 'traits/Attributes.php';
		require_once 'traits/Constructed_URL.php';
		require_once 'traits/Fallbacks.php';
		require_once 'traits/Lazysizes.php';
		require_once 'traits/Output.php';
		require_once 'traits/Settings.php';
		require_once 'traits/Validation.php';

		require_once 'stores/Attributes.php';
		require_once 'stores/Settings.php';

		require_once 'types/External.php';
		require_once 'types/Picsum.php';
	}

}
