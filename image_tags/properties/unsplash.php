<?php

class Image_Tag_Unsplash_Attributes extends Image_Tag_Attributes {

	const BASE_URL = 'https://source.unsplash.com/';

	/**
	 * Get "src" attribute.
	 *
	 * @uses static::get_url()
	 * @uses Image_Tag_Unsplash_Settings::get()
	 * @return string
	 */
	function get_src_attribute_for_view() {
		if ( !empty( $this->get( 'src' ) ) )
			return $this->get( 'src' );

		$src = static::BASE_URL;

		if ( is_null( $this->image_tag ) )
			return $src;

		$settings = $this->image_tag->settings->get( null, 'view' );

		# Add ID.
		if ( !empty( $settings['image_id'] ) )
			$src .= urlencode( $settings['image_id'] ) . '/';

		else if ( !empty( $settings['user'] ) )
			$src .= sprintf( 'user/%s/', urlencode( $settings['user'] ) );

		else if ( !empty( $settings['user_likes'] ) )
			$src .= sprintf( 'user/%s/likes/', urlencode( $settings['user_likes'] ) );

		else if ( !empty( $settings['collection'] ) )
			$src .= sprintf( 'collection/%d/', urlencode( $settings['collection'] ) );

		if ( ( bool ) $settings['featured'] )
			$src .= 'featured/';

		if (
			   !empty( $settings['width'] )
			&& !empty( $settings['height'] )
		)
			$src .= sprintf( '%dx%d/', $settings['width'], $settings['height'] );

		if ( !empty( $settings['update'] ) )
			$src .= $settings['update'] . '/';

		if ( !empty( $settings['search'] ) ) {
			$search = array_map( 'urlencode', $settings['search'] );
			$src .= sprintf( '?%s', implode( ',', $search ) );
		}

		return $src;
	}

}

class Image_Tag_Unsplash_Settings extends Image_Tag_Settings {

	const DEFAULTS = array(
		'before_output' => array(),
		'after_output' => array(),
		'image_id' => null,
		'user' => null,
		'user_likes' => null,
		'collection' => null,
		'update' => null,
		'featured' => false,
		'width' => null,
		'height' => null,
		'search' => array(),
	);

}

?>