<?php declare(strict_types=1);

namespace Image_Tag;

class HolderJS implements Interfaces\Core {

	use Traits\Attributes,
		Traits\Dimensions,
		Traits\Constructed_URL,
		Traits\Fallbacks,
		Traits\Lazysizes,
		Traits\Output,
		Traits\Settings,
		Traits\Validation;

	public const BASE_URL = 'holder.js';

	/**
	 * Construct.
	 */
	public function __construct(
		object $attributes = new Stores\Attributes,
		object $settings = new Stores\Settings
	) {
		$this->attributes = new Stores\Attributes( $attributes );
		$this->settings   = new Stores\Settings( $settings );
	}

	/**
	 * Output.
	 */
	public function output() : string {
		$this->enqueue_script();
		return $this->markup();
	}

	/**
	 * Noscript.
	 *
	 * @todo create Placehold object.
	 */
	public function noscript() : self {
		if ( isset( $this->nojs ) ) {
			return $this;
		}

		return $this;
	}

	/**
	 * Enqueue script.
	 */
	protected function enqueue_script() : void {
		static $once = false;

		if ( $once ) {
			return;
		}

		$compress = defined( 'COMPRESS_SCRIPTS' ) && constant( 'COMPRESS_SCRIPTS' );
		$suffix   = $compress ? '.min' : '';

		wp_enqueue_script( 'holderjs', 'https://cdnjs.cloudflare.com/ajax/libs/holder/2.9.8/holder' . $suffix . '.js', array(), '2.9.8', array(
			'strategy'  => 'async',
			'in_footer' => true,
		) );

		add_filter( 'script_loader_tag', array( $this, 'filter__script_loader_tag' ), 10, 2 );

		$once = true;
	}

	/**
	 * Filter: script_loader_tag
	 *
	 * Add subresource integrity hash.
	 *
	 * @param string $tag
	 * @param string $handle
	 * @return string
	 */
	public function filter__script_loader_tag( string $tag, string $handle ) : string {
		if ( 'script_loader_tag' !== current_filter() ) {
			return $tag;
		}

		if ( 'holderjs' !== $handle ) {
			return $tag;
		}

		if ( defined( 'COMPRESS_SCRIPTS' ) && constant( 'COMPRESS_SCRIPTS' ) ) {
			return $tag;
		}

		$search  = ' />';
		$replace = sprintf( ' integrity=\'%s\' crossorigin=\'anonymous\' />', 'sha512-O6R6IBONpEcZVYJAmSC+20vdsM07uFuGjFf0n/Zthm8sOFW+lAq/OK1WOL8vk93GBDxtMIy6ocbj6lduyeLuqQ==' );

		return str_replace( $search, $replace, $tag );
	}

	/**
	 * Validation checks.
	 */
	protected function perform_validation_checks() : \WP_Error {
		$errors = new \WP_Error;

		if ( empty( $this->width() ) ) {
			$errors->add( 'required_width', 'Width is required.' );
		}

		return $errors;
	}

	/**
	 * Construct the URL.
	 */
	public function construct_url() : void {
		$this->url_segment( self::BASE_URL );

		$dimensions = array(
			$this->width(),
			$this->height(),
		);

		$dimensions = array_filter( $dimensions );

		if ( 1 === count( $dimensions ) ) {
			$dimensions[1] = $dimensions[0];
		}

		$this->url_segment( implode( 'x', $dimensions ) );

		$args = array( 'auto' => 'yes' );

		// Theme.
		if ( $this->settings->has( 'theme' ) ) {
			$args['theme'] = $this->settings->get( 'theme' );
		}

		// Random.
		if ( $this->settings->has( 'random' ) ) {
			$args['random'] = $this->settings->get( 'random' );
		} else if ( isset( $args['theme'] ) && 'random' === $args['theme'] ) {
			$args['random'] = 'yes';
			unset( $args['theme'] );
		}

		// Background.
		if ( $this->settings->has( 'bg_color' ) ) {
			$args['bg'] = $this->settings->get( 'bg_color' );
		}

		// Text color.
		if ( $this->settings->has( 'text_color' ) ) {
			$args['fg'] = $this->settings->get( 'fg_color' );
		}

		// Font size.
		if ( $this->settings->has( 'font_size' ) ) {
			$args['size'] = $this->settings->get( 'font_size' );
		}

		// Font family.
		if ( $this->settings->has( 'font' ) ) {
			$args['font'] = $this->settings->get( 'font' );
		}

		// Text alignment.
		if ( $this->settings->has( 'align' ) ) {
			$args['align'] = $this->settings->get( 'align' );
		}

		// Outline.
		if ( $this->settings->has( 'outline' ) ) {
			$args['outline'] = 'yes';
		}

		// Auto-sized.
		if ( $this->settings->has( 'auto' ) && ! $this->settings->get( 'auto' ) ) {
			unset( $args['auto'] );
		}

		$this->set_constructed_url( $args );
	}

}
