<?php declare(strict_types=1);

namespace Image_Tag;

final class Manager {

	public const string AS_LQIP_GENERATE = 'image-tag/lqip/generate';

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

		add_action( 'init', array( $instance, 'include_action_scheduler' ) );
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
		require_once 'interfaces/Core.php';

		require_once 'traits/Attributes.php';
		require_once 'traits/Constructed_URL.php';
		require_once 'traits/Dimensions.php';
		require_once 'traits/Fallbacks.php';
		require_once 'traits/Lazysizes.php';
		require_once 'traits/Local.php';
		require_once 'traits/LQIP.php';
		require_once 'traits/Output.php';
		require_once 'traits/Settings.php';
		require_once 'traits/Validation.php';

		require_once 'stores/Attributes.php';
		require_once 'stores/Settings.php';

		require_once 'types/Attachment.php';
		require_once 'types/HolderJS.php';
		require_once 'types/JoeSchmoe.php';
		require_once 'types/Picsum.php';
		require_once 'types/Placehold.php';
		require_once 'types/Remote.php';
		require_once 'types/Theme.php';
	}

	public function has_action_scheduler() : bool {
		return function_exists( 'as_enqueue_async_action' );
	}

	public function include_action_scheduler() : void {
		if ( 'init' !== current_action() ) {
			return;
		}

		include_once dirname( __DIR__ ) . '/vendor/woocommerce/action-scheduler/action-scheduler.php';

		if ( ! $this->has_action_scheduler() ) {
			return;
		}

		add_action( self::AS_LQIP_GENERATE, static function ( string $classname, array $args = array() ) {
			Manager::instance()->include_files();

			$classname = '\\' . $classname;

			if ( isset( $args[1] ) && is_array( $args[1] ) ) {
				$args[1] = new Stores\Attributes( $args[1] );
			}

			if ( isset( $args[2] ) && is_array( $args[2] ) ) {
				$args[2] = new Stores\Settings( $args[2] );
			}

			$that = new $classname( ...$args );

			if ( ! is_object( $that ) || ! is_callable( array( $that, 'lqip' ) ) ) {
				return;
			}

			$that->lqip();
		}, 10, 2 );
	}

}
