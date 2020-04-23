<?php
/**
 * Abstract class for Image Tags.
 */

defined( 'ABSPATH' ) || die();

/**
 * Abstract class: Image_Tag
 */
abstract class _Image_Tag implements ArrayAccess {

	/**
	 * @var string Base64 encoded transparent gif.
	 */
	const BLANK = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

	/**
	 * @var array $attributes Internal attributes of img tag.
	 */
	protected $attributes = array(

		# Strings.
		'id' => null,
		'alt' => null,
		'src' => null,
		'width' => null,
		'height' => null,

		# Arrays.
		'class' => array(),
		'style' => array(),
		'sizes' => array(),
		'srcset' => array(),

	);

	/**
	 * @var array $settings Settings for object.
	 */
	protected $settings = array();

	/**
	 * @var array $default_settings Default settings for object.
	 */
	protected $default_settings = array(
		'before_output' => null,
		'after_output' => null,
		'sizes' => array(),
	);

	/**
	 * @var array $supports Capabilities that the Image_Tag supports.
	 */
	protected $supports = array(
		'lazyload',
		'noscript',
		'joeschmoe',
		'picsum',
		'placeholder',
		'unsplash',
	);

	/**
	 * @var array Extra data.
	 */
	protected $extra = array();


	/*
	##     ##    ###     ######   ####  ######
	###   ###   ## ##   ##    ##   ##  ##    ##
	#### ####  ##   ##  ##         ##  ##
	## ### ## ##     ## ##   ####  ##  ##
	##     ## ######### ##    ##   ##  ##
	##     ## ##     ## ##    ##   ##  ##    ##
	##     ## ##     ##  ######   ####  ######
	*/

	/**
	 * Construct.
	 *
	 * @param array $attributes
	 * @param array $settings
	 * @uses $this->set_attributes()
	 * @uses $this->set_settings()
	 */
	function __construct( array $attributes, array $settings = array() ) {
		$settings = wp_parse_args( $settings, $this->default_settings );

		$this->set_attributes( $attributes );
		$this->set_settings( $settings );
	}

	/**
	 * Getter.
	 *
	 * @param string $key
	 * @uses $this->_get_attribute()
	 * @return mixed
	 */
	function __get( $key ) {
		return $this->_get_attribute( $key );
	}

	/**
	 * To string.
	 *
	 * @uses $this->is_valid()
	 * @uses $this->check_valid()
	 * @uses $this->get_attributes()
	 * @uses $this->get_setting()
	 * @return null|string
	 */
	function __toString() {
		if ( !$this->is_valid() ) {
			foreach ( $this->check_valid()->get_error_messages() as $error ) {
				error_log( $error );
				throw new Exception( $error );
			}

			return null;
		}

		$array = array( '<img' );

		foreach ( $this->get_attributes() as $key => $value )
			$array[$key] = $key . '="' . esc_attr( $value ) . '"';

		$array[] = '/>';

		$array = apply_filters( 'image_tag/output/array', $array, $this );

		$string  = $this->get_setting( 'before_output' );
		$string .= apply_filters( 'image_tag/output/string', implode( ' ', $array ), $this );
		$string .= $this->get_setting( 'after_output' );

		return apply_filters( 'image_tag/output', $string, $this );
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
	 * Check properties are sufficient to create tag.
	 *
	 * @uses $this->check_valid()
	 * @return bool
	 */
	function is_valid() {
		return true === $this->check_valid();
	}

	/**
	 * Check properties are sufficient to create tag.
	 *
	 * @uses $this->get_attribute()
	 * @return true|WP_Error
	 */
	protected function check_valid() {
		$errors = new WP_Error;

		if ( empty( $this->get_attribute( 'src' ) ) )
			$errors->add( 'required_attribute', 'Image requires <code>src</code> attribute.' );

		if ( $errors->has_errors() )
			return $errors;

		return true;
	}

	/**
	 * Get image type.
	 *
	 * @return string
	 *
	 * @todo add test
	 */
	abstract function get_type();

	/**
	 * Check if image is specified type(s).
	 *
	 * @param string|array $types
	 * @return bool
	 *
	 * @todo add test
	 */
	abstract function is_type( $types );


	/*
	 ######  ######## ########       ###    ######## ######## ########  #### ########  ##     ## ######## ########  ######
	##    ## ##          ##         ## ##      ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##    ##
	##       ##          ##        ##   ##     ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##
	 ######  ######      ##       ##     ##    ##       ##    ########   ##  ########  ##     ##    ##    ######    ######
	      ## ##          ##       #########    ##       ##    ##   ##    ##  ##     ## ##     ##    ##    ##             ##
	##    ## ##          ##       ##     ##    ##       ##    ##    ##   ##  ##     ## ##     ##    ##    ##       ##    ##
	 ######  ########    ##       ##     ##    ##       ##    ##     ## #### ########   #######     ##    ########  ######
	*/

	/**
	 * Set attributes.
	 *
	 * @param array $attributes
	 * @uses $this->set_attribute()
	 */
	function set_attributes( array $attributes ) {
		foreach ( $attributes as $attribute => $value )
			$this->set_attribute( $attribute, $value );
	}

	/**
	 * Add attribute.
	 *
	 * Set attribute only if not already set.
	 *
	 * @uses $this->get_attribute()
	 * @uses $this->set_attribute()
	 */
	function add_attribute( string $key, $value ) {
		if ( !empty( $this->get_attribute( $key ) ) )
			return;

		$this->set_attribute( $key, $value );
	}

	/**
	 * Set attribute.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @uses $this->_set_attribute()
	 */
	function set_attribute( string $key, $value ) {
		$method_name = preg_replace( '/[^A-z0-9_]/', '_', 'set_' . $key . '_attribute' );

		# Allow overriding attribute retrieval by name.
		if ( is_callable( array( $this, $method_name ) ) )
			return $this->$method_name( $value );

		# Allow overriding attribute retrieval by variable type.
		$type = gettype( $value );
		$method_name = 'set_' . $type . '_attribute';

		if ( is_callable( array( $this, $method_name ) ) )
			return $this->$method_name( $value );

		return $this->_set_attribute( $key, $value );
	}

	/**
	 * Set "class" attribute.
	 *
	 * @param array|string $classes
	 * @uses $this->_set_attribute()
	 */
	protected function set_class_attribute( $classes ) {
		if ( is_string( $classes ) )
			$classes = explode( ' ', $classes );

		if ( empty( $classes ) )
			$classes = array();

		if ( !is_array( $classes ) ) {
			trigger_error( sprintf( 'Value of type <code>%s</code> is not valid for <code>%s</code> attribute.', gettype( $classes ), 'class' ) );
			return;
		}

		$this->_set_attribute( 'class', array_filter( Image_Tag::trim( $classes ) ) );
	}

	/**
	 * Set "sizes" attribute.
	 *
	 * @param array|string $sizes
	 * @uses $this->_set_attribute()
	 */
	protected function set_sizes_attribute( $sizes ) {
		if ( is_string( $sizes ) )
			$sizes = explode( ',', $sizes );

		else if ( empty( $sizes ) )
			$sizes = array();

		if ( !is_array( $sizes ) ) {
			trigger_error( sprintf( 'Value of type <code>%s</code> is not valid for <code>%s</code> attribute.', gettype( $sizes ), 'sizes' ) );
			return;
		}

		$this->_set_attribute( 'sizes', array_filter( Image_Tag::trim( $sizes ) ) );
	}

	/**
	 * Set "srcset" attribute.
	 *
	 * @param array|string $srcset
	 * @uses $this->_set_attribute()
	 */
	protected function set_srcset_attribute( $srcset ) {
		if ( is_string( $srcset ) )
			$srcset = explode( ',', $srcset );

		if ( empty( $srcset ) )
			$srcset = array();

		if ( !is_array( $srcset ) ) {
			trigger_error( sprintf( 'Value of type <code>%s</code> is not valid for <code>%s</code> attribute.', gettype( $srcset ), 'srcset' ) );
			return;
		}

		$this->_set_attribute( 'srcset', array_filter( Image_Tag::trim( $srcset ) ) );
	}

	/**
	 * Set "style" attribute.
	 *
	 * @param array|string $style
	 * @uses $this->_set_attribute()
	 */
	protected function set_style_attribute( $style ) {
		if ( is_string( $style ) )
			$style = explode( ';', $style );

		if ( empty( $style ) )
			$style = array();

		if ( !is_array( $style ) ) {
			trigger_error( sprintf( 'Value of type <code>%s</code> is not valid for <code>%s</code> attribute.', gettype( $style ), 'style' ) );
			return;
		}

		$this->_set_attribute( 'style', array_filter( Image_Tag::trim( $style ) ) );
	}

	/**
	 * Set attribute of array type.
	 *
	 * @param string $key
	 * @param array $value
	 * @uses $this->_set_attribute()
	 */
	protected function set_array_attribute( string $key, array $value ) {
		$this->_set_attribute( $key, array_filter( Image_Tag::trim( $value ) ) );
	}

	/**
	 * Set raw attribute.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	protected function _set_attribute( string $key, $value ) {
		$this->attributes[$key] = $value;
	}


	/*
	 ######   ######## ########       ###    ######## ######## ########  #### ########  ##     ## ######## ########  ######
	##    ##  ##          ##         ## ##      ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##    ##
	##        ##          ##        ##   ##     ##       ##    ##     ##  ##  ##     ## ##     ##    ##    ##       ##
	##   #### ######      ##       ##     ##    ##       ##    ########   ##  ########  ##     ##    ##    ######    ######
	##    ##  ##          ##       #########    ##       ##    ##   ##    ##  ##     ## ##     ##    ##    ##             ##
	##    ##  ##          ##       ##     ##    ##       ##    ##    ##   ##  ##     ## ##     ##    ##    ##       ##    ##
	 ######   ########    ##       ##     ##    ##       ##    ##     ## #### ########   #######     ##    ########  ######
	*/

	/**
	 * Get attributes.
	 *
	 * @param bool $raw
	 * @uses $this->_get_attributes()
	 * @uses $this->get_attribute()
	 * @return array
	 */
	function get_attributes( bool $raw = false ) {
		$attributes = array();

		foreach ( array_keys( $this->attributes ) as $attribute )
			$attributes[$attribute] = $this->get_attribute( $attribute, $raw );

		if ( $raw )
			return $attributes;

		return array_filter( $attributes );
	}

	/**
	 * Get attribute.
	 *
	 * @param string $key
	 * @uses $this->_get_attribute()
	 * @return mixed
	 */
	function get_attribute( string $key, bool $raw = false ) {
		if ( $raw )
			return $this->_get_attribute( $key );

		$method_name = preg_replace( '/[^A-z0-9_]/', '_', 'get_' . $key . '_attribute' );

		# Allow overriding attribute retrieval by name.
		if ( is_callable( array( $this, $method_name ) ) )
			return $this->$method_name();

		$type = gettype( $this->_get_attribute( $key ) );
		$method_name = 'get_' . $type . '_attribute';

		# Allow overriding attribute retrieval by variable type.
		if ( is_callable( array( $this, $method_name ) ) )
			return $this->$method_name( $key );

		return $this->_get_attribute( $key );
	}

	/**
	 * Get "class" attribute.
	 *
	 * @uses $this->_get_attribute()
	 * @return string
	 */
	protected function get_class_attribute() {
		return implode( ' ', array_unique( array_filter( $this->_get_attribute( 'class' ) ) ) );
	}

	/**
	 * Get "style" attribute.
	 *
	 * @uses $this->_get_attribute()
	 * @return string
	 */
	protected function get_style_attribute() {
		return implode( '; ', $this->_get_attribute( 'style' ) );
	}

	/**
	 * Get array attribute.
	 *
	 * @param string $key
	 * @uses $this->_get_attribute()
	 * @return string
	 */
	function get_array_attribute( string $key ) {
		return implode( ', ', array_unique( array_filter( $this->_get_attribute( $key ) ) ) );
	}

	/**
	 * Get raw attribute.
	 *
	 * @param string $key
	 * @return mixed
	 */
	protected function _get_attribute( string $key ) {
		if ( !isset( $this->attributes[$key] ) )
			return null;

		return $this->attributes[$key];
	}


	/*
	 ######  ######## ########     ######  ######## ######## ######## #### ##    ##  ######    ######
	##    ## ##          ##       ##    ## ##          ##       ##     ##  ###   ## ##    ##  ##    ##
	##       ##          ##       ##       ##          ##       ##     ##  ####  ## ##        ##
	 ######  ######      ##        ######  ######      ##       ##     ##  ## ## ## ##   ####  ######
	      ## ##          ##             ## ##          ##       ##     ##  ##  #### ##    ##        ##
	##    ## ##          ##       ##    ## ##          ##       ##     ##  ##   ### ##    ##  ##    ##
	 ######  ########    ##        ######  ########    ##       ##    #### ##    ##  ######    ######
	*/

	/**
	 * Set settings.
	 *
	 * @param array $settings
	 * @uses $this->set_setting()
	 */
	function set_settings( array $settings ) {
		foreach ( $settings as $setting => $value )
			$this->set_setting( $setting, $value );
	}

	/**
	 * Set setting.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @uses $this->_set_setting()
	 */
	function set_setting( string $key, $value ) {
		$method_name = preg_replace( '/[^A-z0-9_]/', '_', 'set_' . $key . '_setting' );

		if ( is_callable( array( $this, $method_name ) ) )
			return $this->$method_name();

		return $this->_set_setting( $key, $value );
	}

	/**
	 * Set raw setting.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	protected function _set_setting( string $key, $value ) {
		$this->settings[$key] = $value;
	}


	/*
	 ######   ######## ########     ######  ######## ######## ######## #### ##    ##  ######    ######
	##    ##  ##          ##       ##    ## ##          ##       ##     ##  ###   ## ##    ##  ##    ##
	##        ##          ##       ##       ##          ##       ##     ##  ####  ## ##        ##
	##   #### ######      ##        ######  ######      ##       ##     ##  ## ## ## ##   ####  ######
	##    ##  ##          ##             ## ##          ##       ##     ##  ##  #### ##    ##        ##
	##    ##  ##          ##       ##    ## ##          ##       ##     ##  ##   ### ##    ##  ##    ##
	 ######   ########    ##        ######  ########    ##       ##    #### ##    ##  ######    ######
	*/

	/**
	 * Get settings.
	 *
	 * @param bool $raw
	 * @uses $this->get_setting()
	 * @return array
	 */
	function get_settings( bool $raw = false ) {
		$settings = array();

		foreach ( array_keys( $this->settings ) as $setting )
			$settings[$setting] = $this->get_setting( $setting, $raw );

		return $settings;
	}

	/**
	 * Get setting.
	 *
	 * @param string $key
	 * @param bool $raw
	 * @uses $this->_get_setting()
	 * @return mixed
	 */
	function get_setting( string $key, bool $raw = false ) {
		if ( $raw )
			return $this->_get_setting( $key );

		$method_name = preg_replace( '/[^A-z0-9_]/', '_', 'get_' . $key . '_setting' );

		if ( is_callable( array( $this, $method_name ) ) )
			return $this->$method_name();

		return $this->_get_setting( $key );
	}

	/**
	 * Get raw setting.
	 *
	 * @param string $key
	 * @return mixed
	 */
	function _get_setting( string $key ) {
		return $this->settings[$key];
	}


	/*
	   ###    ########  ########     ##     ## ######## ##       ########  ######## ########   ######
	  ## ##   ##     ## ##     ##    ##     ## ##       ##       ##     ## ##       ##     ## ##    ##
	 ##   ##  ##     ## ##     ##    ##     ## ##       ##       ##     ## ##       ##     ## ##
	##     ## ##     ## ##     ##    ######### ######   ##       ########  ######   ########   ######
	######### ##     ## ##     ##    ##     ## ##       ##       ##        ##       ##   ##         ##
	##     ## ##     ## ##     ##    ##     ## ##       ##       ##        ##       ##    ##  ##    ##
	##     ## ########  ########     ##     ## ######## ######## ##        ######## ##     ##  ######
	*/

	/**
	 * Add value to an attribute.
	 *
	 * @param string $attribute
	 * @param string $value
	 * @uses $this->_get_attribute()
	 * @uses $this->set_attribute()
	 *
	 * @todo add test
	 */
	function add_to_attribute( string $attribute, string $value ) {
		static $allowed_attributes = null;

		$method_name = preg_replace( '/[^A-z0-9_]/', '_', 'add_to_' . $attribute . '_attribute' );
		if ( is_callable( array( $this, $method_name ) ) ) {
			$this->$method_name( $value );
			return;
		}

		if ( is_null( $allowed_attributes ) )
			$allowed_attributes = apply_filters( 'image_tag/add_to_attribute/allowed', array(
				'class',
				'sizes',
				'srcset',
				'style',
			) );

		if (
			   !is_string( $value )
			|| !in_array( $attribute, $allowed_attributes )
		) {
			trigger_error( sprintf( 'Values can be added only to the following attributes: <code>%s</code>.', implode( '</code>, <code>', $allowed_attributes ) ), E_USER_NOTICE );
			return;
		}

		$new_value = ( array ) $this->_get_attribute( $attribute );
		$new_value[] = $value;

		$this->set_attribute( $attribute, $new_value );
		return $this;
	}

	/**
	 * Add class(es).
	 *
	 * @param string|array
	 * @uses $this->_get_attribute()
	 * @uses $this->set_attribute()
	 */
	protected function add_to_class_attribute( string $classes ) {
		if ( is_string( $classes ) )
			$classes = array_filter( array_map( 'Image_Tag::trim', explode( ' ', $classes ) ) );

		if ( !is_array( $classes ) ) {
			trigger_error( sprintf( 'Value of type <code>%s</code> is not valid for <code>class</code> attribute.', gettype( $classes ) ), E_USER_NOTICE );
			return;
		}

		$classes = array_merge( ( array ) $this->_get_attribute( 'class' ), $classes );
		$this->set_attribute( 'class', $classes );
	}


	/*
	########  #### ##     ## ######## ##    ##  ######  ####  #######  ##    ##  ######
	##     ##  ##  ###   ### ##       ###   ## ##    ##  ##  ##     ## ###   ## ##    ##
	##     ##  ##  #### #### ##       ####  ## ##        ##  ##     ## ####  ## ##
	##     ##  ##  ## ### ## ######   ## ## ##  ######   ##  ##     ## ## ## ##  ######
	##     ##  ##  ##     ## ##       ##  ####       ##  ##  ##     ## ##  ####       ##
	##     ##  ##  ##     ## ##       ##   ### ##    ##  ##  ##     ## ##   ### ##    ##
	########  #### ##     ## ######## ##    ##  ######  ####  #######  ##    ##  ######
	*/

	/**
	 * Get image's primary width.
	 *
	 * @uses $this->get_attribute()
	 * @return int
	 */
	function get_width() {
		return ( int ) $this->get_attribute( 'width' );
	}

	/**
	 * Get image's primary height.
	 *
	 * @uses $this->get_attribute()
	 * @return int
	 */
	function get_height() {
		return ( int ) $this->get_attribute( 'height' );
	}

	/**
	 * Get image's primary ratio.
	 *
	 * @uses $this->get_height()
	 * @uses $this->get_width()
	 * @return float
	 */
	function get_ratio() {
		if (
			   empty( $this->get_height() )
			|| empty( $this->get_width() )
		)
			return 0;

		return $this->get_height() / $this->get_width();
	}

	/**
	 * Get image's primary orientation.
	 *
	 * @return string
	 */
	function get_orientation() {
		$ratio = $this->get_ratio();

		if ( empty( $ratio ) )
			return 'unknown';

		if ( $ratio > 1 )
			return 'portrait';

		if ( $ratio < 1 )
			return 'landscape';

		if ( 1 === $ratio )
			return 'square';

		return 'unknown';
	}


	/*
	######## ########    ###    ######## ##     ## ########  ########  ######
	##       ##         ## ##      ##    ##     ## ##     ## ##       ##    ##
	##       ##        ##   ##     ##    ##     ## ##     ## ##       ##
	######   ######   ##     ##    ##    ##     ## ########  ######    ######
	##       ##       #########    ##    ##     ## ##   ##   ##             ##
	##       ##       ##     ##    ##    ##     ## ##    ##  ##       ##    ##
	##       ######## ##     ##    ##     #######  ##     ## ########  ######
	*/

	/**
	 * Make HTTP GET request to image's primary URL.
	 *
	 * @uses wp_remote_get()
	 * @return array|WP_Error
	 */
	function http( bool $force = false ) {
		$src = $this->get_attribute( 'src' );

		if (
			!$force
			&& isset( $this->extra[__FUNCTION__] )
		)
			return $this->extra[__FUNCTION__];

		$response = wp_remote_get( $src );

		if ( is_wp_error( $response ) )
			return $response;

		$this->extra[__FUNCTION__] = $response;

		return $response;
	}

	/**
	 * Adjust to lazyload the image.
	 *
	 * @param array $attributes
	 * @param array $settings
	 * @return static
	 */
	function lazyload( $attributes = array(), array $settings = array() ) {
		if ( !$this->can( __FUNCTION__ ) )
			return new static( $attributes, $settings );

		$pre = apply_filters( 'image_tag/lazyload/pre', null, $this, $attributes, $settings );
		if ( !is_null( $pre ) )
			return $pre;

		$attributes = wp_parse_args( ( array ) $attributes, $this->get_attributes( true ) );
		$settings = wp_parse_args( $settings, $this->get_settings( true ) );

		$lazyload = clone $this;
		$lazyload->set_attributes( $attributes );
		$lazyload->set_settings( $settings );

		$lazyload->add_attribute( 'data-src',    $lazyload->get_attribute( 'src' ) );
		$lazyload->add_attribute( 'data-sizes',  $lazyload->get_attribute( 'sizes' ) );
		$lazyload->add_attribute( 'data-srcset', $lazyload->get_attribute( 'srcset' ) );

		$lazyload->set_attribute( 'src', static::BLANK );
		$lazyload->add_to_attribute( 'class', 'lazyload hide-if-no-js' );

		if (
			    empty( $lazyload->get_attribute( 'data-sizes' ) )
			&& !empty( $lazyload->get_attribute( 'data-srcset' ) )
		)
			$lazyload->set_attribute( 'data-sizes', 'auto' );

		$lazyload->set_setting( 'after_output', $this->noscript( array(
			'loading' => 'lazy',
		) )->__toString() );

		return $lazyload;
	}

	/**
	 * Adjust to a noscript image.
	 *
	 * @param array $attributes
	 * @param array $settings
	 * @return static
	 */
	function noscript( $attributes = array(), array $settings = array() ) {
		if ( !$this->can( __FUNCTION__ ) )
			return new static( $attributes, $settings );

		$attributes = wp_parse_args( ( array ) $attributes, $this->get_attributes( true ) );
		$settings = wp_parse_args( $settings, $this->get_settings( true ) );

		$noscript = clone $this;
		$noscript->set_attributes( $attributes );
		$noscript->set_settings( $settings );

		$noscript->add_to_attribute( 'class', 'no-js' );
		$noscript->set_setting( 'before_output', '<noscript>' . $this->get_setting( 'before_output' ) );
		$noscript->set_setting( 'after_output', '</noscript>' . $this->get_setting( 'after_output' ) );

		return $noscript;
	}

	/**
	 * Adjust to or create a low-quality image placeholder.
	 *
	 * @param array $attributes
	 * @param array $settings
	 * @return static
	 *
	 * @todo figure out
	 */
	function lqip( $attributes = array(), array $settings = array() ) {
		$attributes = wp_parse_args( $attributes, array(
			'class' => 'lqip',
		) );

		if ( !$this->can( __FUNCTION__ ) )
			return new static( $attributes, $settings );

		$attributes = wp_parse_args( ( array ) $attributes, $this->get_attributes( true ) );
		$settings = wp_parse_args( $settings, $this->get_settings( true ) );
	}

	/**
	 * Get image's common colors.
	 *
	 * @param int $count
	 * @uses $this-find_common_colors()
	 * @return array
	 *
	 * @todo define
	 */
	function common_colors( int $count = 1 ) {
		if (
			!$this->can( 'common-colors' )
			|| !is_callable( array( $this, 'find_common_colors' ) )
		)
			return array();

		return $this->find_common_colors( $count );
	}

	/**
	 * Find common colors.
	 *
	 * @return array
	 *
	 * @todo define
	 */
	protected function find_common_colors( int $count = 1 ) {}

	/**
	 * Get image's mode color.
	 *
	 * @uses $this->common_colors()
	 * @return string
	 */
	function mode_color() {
		$common_colors = $this->common_colors( 1 );
		return array_pop( $common_colors );
	}


	/*
	########  ##          ###     ######  ######## ##     ##  #######  ##       ########  ######## ########   ######
	##     ## ##         ## ##   ##    ## ##       ##     ## ##     ## ##       ##     ## ##       ##     ## ##    ##
	##     ## ##        ##   ##  ##       ##       ##     ## ##     ## ##       ##     ## ##       ##     ## ##
	########  ##       ##     ## ##       ######   ######### ##     ## ##       ##     ## ######   ########   ######
	##        ##       ######### ##       ##       ##     ## ##     ## ##       ##     ## ##       ##   ##         ##
	##        ##       ##     ## ##    ## ##       ##     ## ##     ## ##       ##     ## ##       ##    ##  ##    ##
	##        ######## ##     ##  ######  ######## ##     ##  #######  ######## ########  ######## ##     ##  ######
	*/

	/**
	 * Duplicate into specified image type.
	 *
	 * @param string $type
	 * @param array $settings
	 * @param array $attributes
	 * @uses $this->supports()
	 * @uses $this->get_attributes()
	 * @uses $this->get_settings()
	 * @uses Image_Tag::create()
	 * @return Image_Tag
	 */
	function into( string $type, $settings = array(), array $attributes = array() ) {
		if ( !$this->can( $type ) )
			return new static( $attributes, $settings );

		$attributes = wp_parse_args( $attributes, $this->get_attributes( true ) );
		$settings = wp_parse_args( ( array ) $settings, $this->get_settings( true ) );

		if ( is_callable( array( $this, 'into_' . $type ) ) )
			return call_user_func( array( $this, 'into_' . $type ), $settings, $attributes );

		return Image_Tag::create( $type, $attributes, $settings );
	}


	/*
	 ######     ###    ########     ###    ########  #### ##       #### ######## #### ########  ######
	##    ##   ## ##   ##     ##   ## ##   ##     ##  ##  ##        ##     ##     ##  ##       ##    ##
	##        ##   ##  ##     ##  ##   ##  ##     ##  ##  ##        ##     ##     ##  ##       ##
	##       ##     ## ########  ##     ## ########   ##  ##        ##     ##     ##  ######    ######
	##       ######### ##        ######### ##     ##  ##  ##        ##     ##     ##  ##             ##
	##    ## ##     ## ##        ##     ## ##     ##  ##  ##        ##     ##     ##  ##       ##    ##
	 ######  ##     ## ##        ##     ## ########  #### ######## ####    ##    #### ########  ######
	*/

	/**
	 * Check if capability is supported.
	 *
	 * @param string $capability
	 * @return bool
	 */
	function supports( string $capability ) {
		return in_array( $capability, $this->supports );
	}

	/**
	 * Check if capability can be performed.
	 *
	 * @param string $capability
	 * @uses $this->supports()
	 * @return bool
	 */
	function can( string $capability ) {
		if ( !$this->supports( $capability ) )
			return false;

		$pre = apply_filters( 'image_tag/can/pre', null, $capability, $this );
		if ( !is_null( $pre ) )
			return ( bool ) $pre;

		$can = true;

		$method_name = preg_replace( '/[^A-z0-9_]/', '_', 'can_' . $capability );
		if ( is_callable( array( $this, $method_name ) ) )
			$can = ( bool ) $this->$method_name();

		return ( bool ) apply_filters( 'image_tag/can', $can, $capability, $this );
	}


	/*
	   ###    ########  ########     ###    ##    ##    ###     ######   ######  ########  ######   ######
	  ## ##   ##     ## ##     ##   ## ##    ##  ##    ## ##   ##    ## ##    ## ##       ##    ## ##    ##
	 ##   ##  ##     ## ##     ##  ##   ##    ####    ##   ##  ##       ##       ##       ##       ##
	##     ## ########  ########  ##     ##    ##    ##     ## ##       ##       ######    ######   ######
	######### ##   ##   ##   ##   #########    ##    ######### ##       ##       ##             ##       ##
	##     ## ##    ##  ##    ##  ##     ##    ##    ##     ## ##    ## ##    ## ##       ##    ## ##    ##
	##     ## ##     ## ##     ## ##     ##    ##    ##     ##  ######   ######  ########  ######   ######
	*/

	/**
	 * ArrayAccess: exists
	 *
	 * @param $offset
	 * @return bool
	 */
	function offsetExists( $offset ) {
		return isset( $this->attributes[$offset] );
	}

	/**
	 * ArrayAccess: get
	 *
	 * @param $offset
	 * @return mixed
	 */
	function offsetGet( $offset ) {
		return $this->attributes[$offset];
	}

	/**
	 * ArrayAccess: set
	 *
	 * @param $offset
	 * @param $value
	 * @uses $this->set_attribute()
	 */
	function offsetSet( $offset, $value ) {
		$this->set_attribute( $offset, $value );
	}

	/**
	 * ArrayAccess: unset
	 *
	 * @param $offset
	 * @uses $this->set_attribute()
	 */
	function offsetUnset( $offset ) {
		$this->set_attribute( $offset, null );
	}

}

?>
