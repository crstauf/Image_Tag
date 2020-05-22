<?php

/**
 * Class: Image_Tag_Abstract
 */
abstract class Image_Tag_Abstract {

	/**
	 * @var string[] Image tag types, ex: array( 'base' ).
	 */
	const TYPES = array();

	/**
	 * @var string Encoded transparent gif.
	 */
	const BLANK = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';

	/**
	 * @var null|Image_Tag_Attributes $attributes
	 * @var null|Image_Tag_Settings $settings
	 */
	protected $attributes = null;
	protected $settings   = null;


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
	 * @param null|array|Image_Tag_Attributes $attributes
	 * @param null|array|Image_Tag_Settings $settings
	 */
	function __construct( $attributes = null, $settings = null ) {
		$this->attributes = new Image_Tag_Attributes( $attributes, null, $this );
		$this->settings   = new Image_Tag_Settings(     $settings, null, $this );
	}

	/**
	 * Setter.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	function __set( string $key, $value ) {
		if ( in_array( $key, array_keys( get_object_vars( $this ) ) ) ) {
			if ( is_subclass_of( $value, Image_Tag_Properties_Abstract::class ) )
				$this->$key = $value;

			return;
		}

		$this->attributes->$key = $value;
	}

	/**
	 * Getter.
	 *
	 * @param string $key
	 * @return mixed
	 */
	function __get( string $key ) {
		if ( in_array( $key, array_keys( get_object_vars( $this ) ) ) )
			return $this->$key;

		return $this->attributes->$key;
	}

	/**
	 * Check if isset.
	 *
	 * @param string $key
	 * @return bool
	 */
	function __isset( string $key ) {
		if ( in_array( $key, array_keys( get_object_vars( $this ) ) ) )
			return isset( $this->$key );

		return isset( $this->attributes->$key );
	}

	/**
	 * Unsetter.
	 *
	 * @param string $key
	 */
	function __unset( string $key ) {
		if ( in_array( $key, array_keys( get_object_vars( $this ) ) ) )
			$this->$key = null;

		unset( $this->attributes->$key );
	}

	/**
	 * To string.
	 *
	 * @uses static::check_valid()
	 * @uses Image_Tag_Attributes::__toString()
	 * @uses Image_Tag_Settings::get()
	 * @return string
	 */
	function __toString() {
		$errors = $this->check_valid();

		if ( is_wp_error( $errors ) ) {
			foreach ( $errors->get_error_messages() as $message )
				trigger_error( $message, E_USER_WARNING );

			return null;
		}

		$array = array(
			'<img',
			$this->attributes->__toString(),
			'/>',
		);

		$string =
			$this->settings->get( 'before_output', 'view' ) .
			trim( implode( ' ', array_filter( $array ) ) ) .
			$this->settings->get( 'after_output', 'view' );

		return $string;
	}

	/**
	 * Cloner.
	 *
	 * @uses static::__construct()
	 * @return Image_Tag_Abstract
	 *
	 * @todo add tests
	 */
	function __clone() {
		return static::__construct( $this->attributes, $this->settings );
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
	 * Get primary image tag type.
	 *
	 * @return string
	 */
	function get_type() {
		return static::TYPES[0];
	}

	/**
	 * Check if tag is one of specified types.
	 *
	 * @param string|array $test_types
	 * @return bool
	 */
	function is_type( $test_types ) {
		return !empty( array_intersect( static::TYPES, ( array ) $test_types ) );
	}

	/**
	 * Perform validation checks.
	 *
	 *
 	 * @uses Image_Tag_Attributes::get()
 	 * @return WP_Error|true
 	 */
 	protected function check_valid() {
 		$errors = new WP_Error;

 		if ( empty( $this->attributes->get( 'src', 'view' ) ) )
 			$errors->add( 'required_src', 'The <code>src</code> attribute is required.' );

 		if ( $errors->has_errors() )
 			return $errors;

 		return true;
 	}

	/**
	 * Check if image tag is valid.
	 *
	 * @param null|string|array $types
	 * @uses static::is_type()
	 * @uses static::check_valid()
	 * @return bool
	 */
	function is_valid( $types = null ) {
		if (
			   !is_null( $types )
			&& !$this->is_type( $types )
		)
			return false;

		return true === $this->check_valid();
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
	 * Request image via HTTP GET.
	 *
	 * @param bool $fresh Flag to refresh cached value.
	 * @uses wp_safe_remote_get()
	 * @return WP_Error|array
	 */
	function http( bool $fresh = false ) {
		static $responses = array();

		$src = $this->attributes->get( 'src' );

		if (
			empty( $src )
			|| !wp_http_validate_url( $src )
		)
			return new WP_Error( 'required_src', 'Image URL is required to perform HTTP GET request.' );

		if (
			!$fresh
			&& isset( $responses[$src] )
		)
			return $responses[$src];

		$responses[$src] = wp_safe_remote_get( $src );

		return $responses[$src];
	}

	/**
	 * Adjust atributes and settings to lazyload.
	 *
	 * @param array $set_attributes
	 * @param array $set_settings
	 * @return Image_Tag_Abstract
	 *
	 * @todo figure out how to store noscript image tag object directly (without __toString())
	 */
	function lazyload( array $set_attributes = array(), array $set_settings = array() ) {
		$lazyload = clone $this;

		$set_attributes = wp_parse_args( $set_attributes, array(
			'src' => Image_Tag::BLANK,
			'data-src' => null,
			'data-sizes' => array(),
			'data-srcset' => array(),
		) );

		$set_attributes['sizes']  = array();
		$set_attributes['srcset'] = array();

		if ( !array_key_exists( 'lazyload', $set_settings ) )
			$set_settings['lazyload'] = array();

		if ( !empty( $this->settings->get( 'lazyload' ) ) )
			$set_settings['lazyload'] = wp_parse_args( $set_settings['lazyload'], $this->settings->get( 'lazyload' ) );

		$set_settings['lazyload'] = wp_parse_args( $set_settings['lazyload'], array(
			'noscript' => true,
			'noscript_priority' => -10,
			'sizes_auto' => true,
		) );

		if (
			$this->sizes === $this->attributes::DEFAULTS['sizes']
			&& $set_settings['lazyload']['sizes_auto']
		)
			$set_attributes['data-sizes'] = array( 'auto' );

		$lazyload->attributes->set( $set_attributes );
		$lazyload->settings->set(   $set_settings   );

		$lazyload->attributes->add( 'data-src',    $this->src );
		$lazyload->attributes->add( 'data-srcset', $this->srcset );
		$lazyload->attributes->add_to( 'class', 'lazyload hide-if-no-js' );

		$lazyload_settings = $lazyload->settings->get( 'lazyload' );

		if (
			!empty( $lazyload_settings )
			&& $lazyload_settings['noscript']
		)
			$lazyload->settings->add_output(
				'after',
				$this->noscript( array( 'loading' => 'lazy' ) )->__toString(),
				$lazyload_settings['noscript_priority']
			);

		return $lazyload;
	}

	/**
	 * Adjust atributes and settings to add noscript version.
	 *
	 * @param array $attributes
	 * @param array $settings
	 * @return Image_Tag_Abstract
	 */
	function noscript( array $set_attributes = array(), array $set_settings = array() ) {
		$noscript = clone $this;

		$set_settings = wp_parse_args( $set_settings, array(
			'noscript' => array(
				'before_position' => 20,
				'after_position' => 0,
			),
		) );

		$noscript->attributes->set( $set_attributes );
		$noscript->settings->set( $set_settings );

		$classes = $noscript->class;
		$classes[] = 'no-js';
		$classes = array_filter( $classes, function( $class ) {
			return !in_array( $class, array(
				'lazyload',
				'hide-if-no-js',
			) );
		} );

		$noscript->attributes->set( 'class', $classes );

		$noscript->settings->add_output( 'before',  '<noscript>', $noscript->settings->noscript['before_position'] );
		$noscript->settings->add_output(  'after', '</noscript>', $noscript->settings->noscript[ 'after_position'] );

		return $noscript;
	}

	/**
	 * Convert into another image tag type.
	 *
	 * @param string $type Type to convert into.
	 * @param null|array|Image_Tag_Attributes $attributes
	 * @param null|array|Image_Tag_Settings $settings
	 * @return Image_Tag_Abstract
	 */
	function into( string $type, $attributes = null, $settings = null ) {
		if ( in_array( $type, static::TYPES ) ) {
			trigger_error( sprintf( 'Cannot convert <code>%s</code> image tag into itself.', $this->get_type() ) );
			return $this;
		}

		if ( is_array( $attributes ) )
			$attributes = wp_parse_args( $attributes, $this->attributes->get() );

		if ( is_array( $settings ) )
			$settings = wp_parse_args( $settings, $this->settings->get() );

		$into = Image_Tag::create( $type, $attributes, $settings );
		$into->attributes->set( 'src', null );

		return $into;
	}

}