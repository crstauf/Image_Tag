<?php
/**
 * Placeholder image service: holderjs.com
 *
 * @link https://holderjs.com/
 */

declare( strict_types=1 );

namespace Image_Tag\Types;

use Image_Tag\Data_Stores\Attributes;
use Image_Tag\Data_Stores\Settings;

defined( 'WPINC' ) || die();

/**
 * Class: Image_Tag\Types\HolderJS
 */
class HolderJS extends \Image_Tag\Abstracts\Base implements \Image_Tag\Interfaces\Dynamic_Source {

	const BASE_URL = 'holder.js';

	/**
	 * @var string[] Image types.
	 */
	const TYPES = array(
		'holder',
		'holderjs',
		'holder.js',
		'__placeholder',
	);

	/**
	 * Construct.
	 *
	 * @param null|mixed[]|Attributes $attributes
	 * @param null|mixed[]|Settings $settings
	 * @uses $this->construct()
	 */
	public function __construct( $attributes = null, $settings = null ) {
		$this->enqueue_script();
		$this->construct( $attributes, $settings );
	}

	/**
	 * Enqueue the script.
	 *
	 * @return void
	 */
	public function enqueue_script() : void {
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
	 * Create Attributes object to use for output.
	 *
	 * @uses Base::output_attributes()
	 * @uses $this->generate_source()
	 * @return Attributes
	 */
	protected function output_attributes() : Attributes {
		$attributes = parent::output_attributes();
		$dimensions = array();

		$attributes->update( 'src', $this->generate_source() );

		# Width
		if ( $this->settings->has( 'width' ) ) {
			$dimensions[] = $this->settings->get( 'width' );
		} else if ( $this->attributes->has( 'width' ) ) {
			$dimensions[] = $this->attributes->get( 'width' );
		}

		# Height
		if ( $this->settings->has( 'height' ) ) {
			$dimensions[] = $this->settings->get( 'height' );
		} else if ( $this->attributes->has( 'height' ) ) {
			$dimensions[] = $this->attributes->get( 'height' );
		}

		if ( 1 === count( $dimensions ) ) {
			$dimensions[] = $dimensions[0];
		}

		if ( ! $attributes->has( 'width' ) && ! empty( $dimensions[0] ) ) {
			$attributes->set( 'width', $dimensions[0] );
		}

		if ( ! $attributes->has( 'height' ) && ! empty( $dimensions[1] ) ) {
			$attributes->set( 'height', $dimensions[1] );
		}

		$attributes->append( 'class', 'hide-if-no-js' );

		return $attributes;
	}

	/**
	 * Generate image source.
	 *
	 * @uses Settings::has()
	 * @uses Settings::get()
	 * @uses Attributes::has()
	 * @uses Attributes::get()
	 * @return string
	 */
	public function generate_source() : string {
		if ( array_key_exists( __FUNCTION__, $this->cache ) ) {
			return $this->cache[ __FUNCTION__ ];
		}

		$dimensions = array();
		$src        = array( static::BASE_URL );

		# Width
		if ( $this->settings->has( 'width' ) ) {
			$dimensions[] = $this->settings->get( 'width' );
		} else if ( $this->attributes->has( 'width' ) ) {
			$dimensions[] = $this->attributes->get( 'width' );
		}

		# Height
		if ( $this->settings->has( 'height' ) ) {
			$dimensions[] = $this->settings->get( 'height' );
		} else if ( $this->attributes->has( 'height' ) ) {
			$dimensions[] = $this->attributes->get( 'height' );
		}

		if ( 1 === count( $dimensions ) ) {
			$dimensions[] = $dimensions[0];
		}

		$dimensions = implode( 'x', $dimensions );

		$src[] = $dimensions;

		# Convert to string
		$src = implode( '/', $src );

		$args = array(
			'auto' => 'yes',
		);

		# Theme
		if ( $this->settings->has( 'theme' ) ) {
			if ( 'random' === $this->settings->get( 'theme' ) ) {
				$args['random'] = 'yes';
			} else {
				$args['theme'] = $this->settings->get( 'theme' );
			}
		}

		# Background
		if ( $this->settings->has( 'bg_color' ) ) {
			$args['bg'] = $this->settings->get( 'bg_color' );
		}

		# Text color
		if ( $this->settings->has( 'text_color' ) ) {
			$args['fg'] = $this->settings->get( 'text_color' );
		}

		# Font size
		if ( $this->settings->has( 'font_size' ) ) {
			$args['size'] = $this->settings->get( 'font_size' );
		}

		# Font face
		if ( $this->settings->has( 'font' ) ) {
			$args['font'] = $this->settings->get( 'font' );
		}

		# Text alignment
		if ( $this->settings->has( 'text_align' ) ) {
			$args['align'] = $this->settings->get( 'text_align' );
		}

		# Outline
		if ( $this->settings->has( 'outline' ) ) {
			$args['outline'] = 'yes';
		}

		# Auto-sized
		if ( $this->settings->has( 'auto' ) && ! $this->settings->get( 'auto' ) ) {
			unset( $args['auto'] );
		}

		$src = add_query_arg( $args, $src );

		$this->cache[ __FUNCTION__ ] = $src;

		return $src;
	}

	/**
	 * Get ratio of image dimensions: width divided by height.
	 *
	 * @return float
	 */
	public function ratio() : float {
		$width  = 0;
		$height = 0;

		if ( $this->settings->has( 'width', false ) ) {
			$width = $this->settings->get( 'width' );
		} else if ( $this->attributes->has( 'width', false ) ) {
			$width = $this->attributes->get( 'width' );
		}

		if ( $this->settings->has( 'height', false ) ) {
			$height = $this->settings->get( 'height' );
		} else if ( $this->attributes->has( 'height', false ) ) {
			$height = $this->attributes->get( 'height' );
		}

		if ( empty( $height ) ) {
			$height = $width;
		}

		if ( empty( $width ) ) {
			$width = $height;
		}

		if ( empty( $height ) ) {
			return 0;
		}

		return absint( $width ) / absint( $height );
	}

	/**
	 * Perform validation checks.
	 *
	 * @uses $this->validate_dimensions()
	 * @return \WP_Error
	 */
	protected function perform_validation_checks() : \WP_Error {
		$errors = new \WP_Error;

		try {
			$this->validate_dimensions();
		} catch ( \Exception $e ) {
			$errors->add( 'placehold', $e->getMessage() );
		}

		return $errors;
	}

	/**
	 * Check that at least one dimension is set.
	 *
	 * @uses Settings::has()
	 * @uses Attributes::has()
	 * @return void
	 */
	protected function validate_dimensions() : void {
		if (
			   $this->settings->has( 'width', false )
			|| $this->settings->has( 'height', false )
			|| $this->attributes->has( 'width', false )
			|| $this->attributes->has( 'height', false )
		) {
			return;
		}

		throw new \Exception( 'HolderJS requires at least one dimension.' );
	}

	/**
	 * Prevent conversion to same type.
	 *
	 * @param null|mixed[]|Attributes $attributes
	 * @param null|mixed[]|Settings $settings
	 * @return self
	 */
	public function holderjs( $attributes = null, $settings = null ) : self {
		trigger_error( sprintf( 'Image is already type <code>%s</code>', $this->get_type() ) );
		return $this;
	}

	/**
	 * Output.
	 *
	 * @return string
	 */
	public function output() : string {
		return parent::output() . $this->noscript();
	}

	/**
	 * Noscript.
	 *
	 * @return string
	 */
	public function noscript() : string {
		$placehold = \Image_Tag::create( 'placehold', $this->attributes, $this->settings );

		if ( ! $placehold->is_valid() ) {
			return '';
		}

		return '<noscript>' . $placehold->output() . '</noscript>';
	}

	/**
	 * No lazyloading.
	 *
	 * @return string
	 */
	public function lazyload() : string {
		return $this->output();
	}

}