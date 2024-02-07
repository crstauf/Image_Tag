<?php
/**
 * Main abstract class for Image Tag types.
 */

declare( strict_types=1 );

namespace Image_Tag\Abstracts;

use Image_Tag;
use Image_Tag\Data_Stores\Attributes;
use Image_Tag\Data_Stores\Settings;
use Image_Tag\Data_Stores\Sources;
use Image_Tag\Interfaces\Conversion;
use Image_Tag\Interfaces\Output;
use Image_Tag\Interfaces\Validation;

defined( 'WPINC' ) || die();

/**
 * Abstract class: Image_Tag\Abstracts\Base
 */
abstract class Base implements Conversion, Output, Validation {

	/**
	 * @var string[]
	 */
	const TYPES = array();

	/**
	 * Smallest transparent data URI image.
	 * @var string
	 */
	const BLANK = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';

	/**
	 * @var Attributes $attributes
	 */
	protected $attributes;

	/**
	 * @var Settings $settings
	 */
	protected $settings;

	/**
	 * @var mixed[] $cache
	 */
	protected $cache = array();

	/**
	 * Getter.
	 *
	 * @param string $property
	 * @return mixed
	 */
	public function __get( string $property ) {
		if ( 'cache' !== $property ) {
			return $this->$property;
		}
	}

	/**
	 * Construct helper.
	 *
	 * @param null|mixed[]|Attributes $attributes
	 * @param null|mixed[]|Settings $settings
	 * @uses $this->create_Attributes()
	 * @uses $this->create_Settings()
	 * @return void
	 *
	 * @codeCoverageIgnore
	 */
	protected function construct( $attributes, $settings ) : void {
		if ( is_object( $attributes ) && is_a( $attributes, static::class ) ) {
			foreach ( get_object_vars( $attributes ) as $key => $value ) {
				$this->$key = $value;
			}
		}

		$this->create_Attributes( $attributes );
		$this->create_Settings( $settings );
	}

	/**
	 * Create Attributes object.
	 *
	 * @param null|mixed[]|Attributes $attributes
	 *
	 * @return void
	 *
	 * @codeCoverageIgnore
	 */
	protected function create_Attributes( $attributes ) : void {
		if ( is_object( $this->attributes ) && is_a( $this->attributes, Attributes::class ) ) {
			return;
		}

		$this->attributes = new Attributes( $attributes );
	}

	/**
	 * Create Settings object.
	 *
	 * @param null|mixed[]|Settings $settings
	 *
	 * @return void
	 *
	 * @codeCoverageIgnore
	 */
	protected function create_Settings( $settings ) : void {
		if ( is_object( $this->settings ) && is_a( $this->settings, Settings::class ) ) {
			return;
		}

		$this->settings = new Settings( $settings );
	}


	/*
	 #######  ##     ## ######## ########  ##     ## ########
	##     ## ##     ##    ##    ##     ## ##     ##    ##
	##     ## ##     ##    ##    ##     ## ##     ##    ##
	##     ## ##     ##    ##    ########  ##     ##    ##
	##     ## ##     ##    ##    ##        ##     ##    ##
	##     ## ##     ##    ##    ##        ##     ##    ##
	 #######   #######     ##    ##         #######     ##
	*/

	/**
	 * String output.
	 *
	 * @uses $this->output()
	 * @return string
	 */
	public function __toString() : string {
		return $this->output();
	}

	/**
	 * Tag output.
	 *
	 * @uses $this->is_valid()
	 * @uses $this->fallback()
	 * @uses $this->output_attributes()
	 * @uses Attributes::output()
	 * @return string
	 */
	public function output() : string {
		if ( ! $this->is_valid() ) {
			if ( ! $this->settings->has( 'fallback' ) ) {
				return '';
			}

			$fallback = $this->fallback();

			if ( ! $fallback->is_valid() ) {
				return '';
			}

			return $fallback->output();
		}

		$output = '';

		if ( defined( 'WP_DEBUG' ) && constant( 'WP_DEBUG' ) ) {
			$output .= PHP_EOL;
		}

		$output .= '<img ';
		$output .= $this->output_attributes()->output();
		$output .= ' />';

		if ( defined( 'WP_DEBUG' ) && constant( 'WP_DEBUG' ) ) {
			$output .= PHP_EOL;
		}

		$before = '';
		$after  = '';

		if ( $this->settings->has( 'before' ) ) {
			$before = $this->settings->get( 'before' );
		}

		if ( $this->settings->has( 'after' ) ) {
			$after = $this->settings->get( 'after' );
		}

		return $before . $output . $after;
	}

	/**
	 * Get fallback image.
	 *
	 * @uses Image_Tag::create()
	 * @uses $this->is_valid()
	 * @return \Image_Tag\Abstracts\Base
	 *
	 * @todo support attributes and settings in array:
	 * array(
	 *   'placeholder' => true,
	 *   'joeschmoe' => array(
	 *     'conditional' => true,
	 *     'attributes' => array( ... ),
	 *     'settings' => array( ... ),
	 *   ),
	 * )
	 */
	protected function fallback() : Base {
		if ( ! $this->settings->has( 'fallback' ) ) {
			return new Image_Tag;
		}

		$fallbacks = $this->settings->get( 'fallback' );

		if ( empty( $fallbacks ) ) {
			return new Image_Tag;
		}

		$fallbacks = ( array ) $fallbacks;

		# Handle non-associative array of fallbacks.
		$has_string_keys = ( 0 < count( array_filter( array_keys( $fallbacks ), 'is_string' ) ) );
		if ( ! $has_string_keys ) {

			# Change image type names to array's keys.
			$fallbacks = array_flip( $fallbacks );

			# Set each array item to true.
			array_walk( $fallbacks, function ( &$item ) {
				$item = true;
			} );
		}

		foreach ( $fallbacks as $fallback => $conditional ) {
			if ( ! $conditional ) {
				continue;
			}

			$img = Image_Tag::create( $fallback, $this->attributes, $this->settings );

			if ( ! $img->is_valid() ) {
				continue;
			}

			return $img;
		}

		return new Image_Tag;
	}

	/**
	 * Create Attributes object to use for output.
	 *
	 * @uses Attributes::__construct()
	 * @return Attributes
	 */
	protected function output_attributes() : Attributes {
		$attributes = new Attributes( $this->attributes );

		/**
		 * Better to have an empty alt attribute than none at all.
		 *
		 * @link https://www.a11y-101.com/development/the-alt-attribute
		 */
		if ( ! $this->attributes->has( 'alt' ) ) {
			$alt = '';

			if ( $this->attributes->has( 'title', false ) ) {
				$alt = $this->attributes->get( 'title' );
			}

			$attributes->set( 'alt', $alt );
		}

		return $attributes;
	}

	/**
	 * Output lazyloaded image.
	 *
	 * @uses $this->is_valid()
	 * @uses $this->fallback()
	 * @uses $this->output_attributes()
	 * @uses Attributes::append()
	 * @uses Attributes::has()
	 * @uses Attributes::update()
	 * @uses Attributes::remove()
	 * @uses $this->noscript()
	 * @return string
	 */
	public function lazyload() : string {
		if ( ! $this->is_valid() ) {
			$fallback = $this->fallback();

			if ( ! $fallback->is_valid() ) {
				return '';
			}

			return $fallback->lazyload();
		}

		$no_js = $this->output_attributes();
		   $js = clone $no_js;

		$lazyload_class = 'lazyload';
		if ( $this->settings->has( 'lazyload-class' ) ) {
			$lazyload_class = $this->settings->get( 'lazyload-class' );
		}

		$js->append( 'class', $lazyload_class . ' hide-if-no-js' );

		if ( $js->has( 'src' ) ) {
			$js->update( 'data-src', $js->get( 'src' ) );
			$js->update( 'src', static::BLANK );
		}

		if ( $js->has( 'srcset' ) ) {
			$js->update( 'data-srcset', $js->get( 'srcset' ) );
			$js->update( 'data-sizes', 'auto' );
			$js->remove( 'data-src' );
			$js->remove( 'srcset' );
		}

		if ( $js->has( 'sizes' ) ) {
			$sizes = $js->get( 'sizes' );

			if ( '100vw' === $sizes ) {
				$sizes = 'auto';
			}

			$js->update( 'data-sizes', $sizes );
			$js->remove( 'sizes' );
		}

		$no_js->append( 'class', 'no-js' );
		$no_js->update( 'loading', 'lazy' );

		$no_js = new Image_Tag( $no_js, $this->settings );
		   $js = new Image_Tag( $js, $this->settings );

		$output = $js;

		if ( defined( 'WP_DEBUG' ) && constant( 'WP_DEBUG' ) ) {
			$output .= PHP_EOL;
		}

		$output .= $no_js->noscript();

		return $output;
	}

	/**
	 * Output of noscript image.
	 *
	 * @uses $this->is_valid()
	 * @uses $this->fallback()
	 * @uses $this->output()
	 * @return string
	 */
	public function noscript() : string {
		if ( ! $this->is_valid() ) {
			$fallback = $this->fallback();

			if ( ! $fallback->is_valid() ) {
				return '';
			}

			return $fallback->noscript();
		}

		return '<noscript>' . $this->output() . '</noscript>';
	}


	/*
	##     ##    ###    ##       #### ########     ###    ######## ####  #######  ##    ##
	##     ##   ## ##   ##        ##  ##     ##   ## ##      ##     ##  ##     ## ###   ##
	##     ##  ##   ##  ##        ##  ##     ##  ##   ##     ##     ##  ##     ## ####  ##
	##     ## ##     ## ##        ##  ##     ## ##     ##    ##     ##  ##     ## ## ## ##
	 ##   ##  ######### ##        ##  ##     ## #########    ##     ##  ##     ## ##  ####
	  ## ##   ##     ## ##        ##  ##     ## ##     ##    ##     ##  ##     ## ##   ###
	   ###    ##     ## ######## #### ########  ##     ##    ##    ####  #######  ##    ##
	*/

	/**
	 * Get valid object, either this or fallback.
	 *
	 * @param null|string|string[] $test_types
	 * @uses $this->is_valid()
	 * @uses $this->fallback()
	 * @return self
	 */
	public function get_valid( $test_types = null ) : self {
		if ( $this->is_valid( $test_types ) ) {
			return $this;
		}

		if ( $this->fallback()->is_valid( $test_types ) ) {
			return $this->fallback();
		}

		return new Image_Tag;
	}

	/**
	 * Get image type.
	 *
	 * @return string
	 */
	public function get_type() : string {
		return static::TYPES[0];
	}

	/**
	 * Test image type.
	 *
	 * @param null|string|string[] $test_types
	 * @return bool
	 */
	public function is_type( $test_types ) : bool {
		if ( empty( $test_types ) ) {
			return true;
		}

		return ! empty( array_intersect( static::TYPES, ( array ) $test_types ) );
	}

	/**
	 * Check image is valid.
	 *
	 * @param null|string|string[] $test_types
	 * @param bool $check_fallback
	 * @uses $this->check_valid()
	 * @uses $this->is_type()
	 * @return bool
	 */
	public function is_valid( $test_types = null, bool $check_fallback = false ) : bool {
		if ( true === $this->check_valid() ) {
			return $this->is_type( $test_types );
		}

		if ( ! $check_fallback ) {
			return false;
		}

		return $this->fallback()->is_valid( $test_types );
	}

	/**
	 * Collect errors from validation checks.
	 *
	 * @uses $this->perform_validation_checks()
	 * @return \WP_Error|true
	 */
	protected function check_valid() {
		$errors = $this->perform_validation_checks();

		if ( $errors->has_errors() ) {
			return $errors;
		}

		return true;
	}

	/**
	 * Perform validation checks.
	 *
	 * @return \WP_Error
	 */
	abstract protected function perform_validation_checks() : \WP_Error;


	/*
	 ######   #######  ##    ## ##     ## ######## ########   ######  ####  #######  ##    ##
	##    ## ##     ## ###   ## ##     ## ##       ##     ## ##    ##  ##  ##     ## ###   ##
	##       ##     ## ####  ## ##     ## ##       ##     ## ##        ##  ##     ## ####  ##
	##       ##     ## ## ## ## ##     ## ######   ########   ######   ##  ##     ## ## ## ##
	##       ##     ## ##  ####  ##   ##  ##       ##   ##         ##  ##  ##     ## ##  ####
	##    ## ##     ## ##   ###   ## ##   ##       ##    ##  ##    ##  ##  ##     ## ##   ###
	 ######   #######  ##    ##    ###    ######## ##     ##  ######  ####  #######  ##    ##
	*/

	/**
	 * Convert to JoeSchmoe.
	 *
	 * @param null|mixed[]|Attributes $attributes
	 * @param null|mixed[]|Settings $settings
	 * @uses $this->is_type()
	 * @return \Image_Tag\Types\JoeSchmoe
	 */
	public function joeschmoe( $attributes = null, $settings = null ) : \Image_Tag\Types\JoeSchmoe {
		if ( is_a( $this, \Image_Tag\Types\JoeSchmoe::class ) ) {
			trigger_error( sprintf( 'Image is already type <code>%s</code>', $this->get_type() ) );
			return $this;
		}

		$attributes = wp_parse_args( ( array ) $attributes, $this->attributes->store );
		$settings   = wp_parse_args( ( array ) $settings, $this->settings->store );

		$created = Image_Tag::create( 'joeschmoe', $attributes, $settings );

		if ( ! is_a( $created, \Image_Tag\Types\JoeSchmoe::class ) ) {
			return new \Image_Tag\Types\JoeSchmoe;
		}

		return $created;
	}

	/**
	 * Convert to Picsum photo.
	 *
	 * @param null|mixed[]|Attributes $attributes
	 * @param null|mixed[]|Settings $settings
	 * @uses $this->is_type()
	 * @return \Image_Tag\Types\Picsum
	 */
	public function picsum( $attributes = null, $settings = null ) : \Image_Tag\Types\Picsum {
		if ( is_a( $this, \Image_Tag\Types\Picsum::class ) ) {
			trigger_error( sprintf( 'Image is already type <code>%s</code>', $this->get_type() ) );
			return $this;
		}

		$attributes = wp_parse_args( ( array ) $attributes, $this->attributes->store );
		$settings   = wp_parse_args( ( array ) $settings, $this->settings->store );

		$created = Image_Tag::create( 'picsum', $attributes, $settings );

		if ( ! is_a( $created, \Image_Tag\Types\Picsum::class ) ) {
			return new \Image_Tag\Types\Picsum;
		}

		return $created;
	}

	/**
	 * Convert to Placeholder.com image.
	 *
	 * @param null|mixed[]|Attributes $attributes
	 * @param null|mixed[]|Settings $settings
	 * @uses $this->is_type()
	 * @return \Image_Tag\Types\Placeholder
	 */
	public function placeholder( $attributes = null, $settings = null ) : \Image_Tag\Types\Placeholder {
		if ( is_a( $this, \Image_Tag\Types\Placeholder::class ) ) {
			trigger_error( sprintf( 'Image is already type <code>%s</code>', $this->get_type() ) );
			return $this;
		}

		$attributes = wp_parse_args( ( array ) $attributes, $this->attributes->store );
		$settings   = wp_parse_args( ( array ) $settings, $this->settings->store );

		$created = Image_Tag::create( 'placeholder', $attributes, $settings );

		if ( ! is_a( $created, \Image_Tag\Types\Placeholder::class ) ) {
			return new \Image_Tag\Types\Placeholder;
		}

		return $created;
	}

	/**
	 * Convert to Placehold.co image.
	 *
	 * @param null|mixed[]|Attributes $attributes
	 * @param null|mixed[]|Settings $settings
	 * @uses $this->is_type()
	 * @return \Image_Tag\Types\Placehold
	 */
	public function placehold( $attributes = null, $settings = null ) : \Image_Tag\Types\Placehold {
		if ( is_a( $this, \Image_Tag\Types\Placehold::class ) ) {
			trigger_error( sprintf( 'Image is already type <code>%s</code>', $this->get_type() ) );
			return $this;
		}

		$attributes = wp_parse_args( ( array ) $attributes, $this->attributes->store );
		$settings   = wp_parse_args( ( array ) $settings, $this->settings->store );

		$created = Image_Tag::create( 'placehold', $attributes, $settings );

		if ( ! is_a( $created, \Image_Tag\Types\Placehold::class ) ) {
			return new \Image_Tag\Types\Placehold;
		}

		return $created;
	}

	/**
	 * Convert to Unsplash Source photo.
	 *
	 * @param null|mixed[]|Attributes $attributes
	 * @param null|mixed[]|Settings $settings
	 * @uses $this->is_type()
	 * @return \Image_Tag\Types\Unsplash
	 */
	public function unsplash( $attributes = null, $settings = null ) : \Image_Tag\Types\Unsplash {
		if ( is_a( $this, \Image_Tag\Types\Unsplash::class ) ) {
			trigger_error( sprintf( 'Image is already type <code>%s</code>', $this->get_type() ) );
			return $this;
		}

		$attributes = wp_parse_args( ( array ) $attributes, $this->attributes->store );
		$settings   = wp_parse_args( ( array ) $settings, $this->settings->store );

		$created = Image_Tag::create( 'unsplash', $attributes, $settings );

		if ( ! is_a( $created, \Image_Tag\Types\Unsplash::class ) ) {
			return new \Image_Tag\Types\Unsplash;
		}

		return $created;
	}

}
