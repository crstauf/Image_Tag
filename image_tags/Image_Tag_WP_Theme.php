<?php
/**
 * Image tag generator for WordPress theme images.
 */

defined( 'ABSPATH' ) || die();

require_once 'Image_Tag_WP.php';

/**
 * Class: Image_Tag_WP_Theme
 */
class Image_Tag_WP_Theme extends Image_Tag_WP {

	/**
	 * @var string $path
	 */
	protected $path;

	/**
	 * Construct.
	 *
	 * @param string $source
	 * @param array $attributes
	 * @param array $settings
	 * @uses Image_Tag::__construct()
	 */
	function __construct( string $source, array $attributes = array(), array $settings = array() ) {
		$this->path = locate_template( $source );

		parent::__construct( $attributes, $settings );

		if ( !$this->is_valid() )
			return;

		$this->set_source( $source );
		$this->set_orientation();
	}

	/**
	 * To string.
	 *
	 * @uses $this->is_valid()
	 * @uses Image_Tag->__toString()
	 * @return string
	 */
	function __toString() {
		if ( !$this->is_valid() ) {
			trigger_error( sprintf( 'Unable to find <code>%s</code> in theme.', $source ), E_USER_WARNING );
			return '';
		}

		return parent::__toString();
	}

	/**
	 * Getter.
	 *
	 * @param string $key
	 * @uses Image_Tag_WP::__get()
	 * @return mixed
	 */
	function __get( $key ) {
		if ( 'path' === $key )
			return $this->path;

		return parent::__get( $key );
	}

	/**
	 * Check if the image is valid.
	 *
	 * @return bool
	 */
	function is_valid() {
		return (
			!empty( $this->path )
			&& file_exists( $this->path )
		);
	}

	/**
	 * Set theme image source.
	 *
	 * @param string $source
	 * @uses $this->_set_attribute()
	 */
	protected function set_source( string $source ) {
		foreach ( array(
			STYLESHEETPATH => get_stylesheet_directory_uri(),
			  TEMPLATEPATH => get_template_directory_uri(),
		) as $themepath => $themeurl )
			if ( false !== strpos( $this->path, $themepath ) )
				$this->_set_attribute( 'src', trailingslashit( $themeurl ) . $source );
	}

	/**
	 * Get image width.
	 *
	 * @uses Image_Tag->get_width()
	 * @uses $this->is_valid()
	 * @return int
	 */
	function get_width() {
		if ( !empty( parent::get_width() ) )
			return parent::get_width();

		if ( !$this->is_valid() )
			return 0;

		return ( int ) getimagesize( $this->path )[0];
	}

	/**
	 * Get image height.
	 *
	 * @uses Image_Tag->get_height()
	 * @uses $this->is_valid()
	 * @return int
	 */
	function get_height() {
		if ( !empty( parent::get_height() ) )
			return parent::get_height();

		if ( !$this->is_valid() )
			return 0;

		return ( int ) getimagesize( $this->path )[1];
	}

	/**
	 * Transient key for common colors.
	 *
	 * @param string $path
	 * @return string
	 */
	static function colors_transient_key( string $path ) {
		return sprintf( 'theme_img_colors_%s', md5( $path ) );
	}

	/**
	 * Get common colors (cached to transient).
	 *
	 * @param int $count
	 * @uses static::colors_transient_key()
	 * @uses $this->_get_colors()
	 * @return array
	 */
	function get_colors( int $count = 3 ) {
		$transient_key = static::colors_transient_key( $this->path );
		$transient = get_transient( $transient_key );

		if (
			  !empty( $transient )
			&& count( $transient ) >= $count
		)
			return $transient;

		$colors = $this->_get_colors( $this->path, $count );
		set_transient( $transient_key, $colors, DAY_IN_SECONDS );

		return $colors;
	}

}

?>