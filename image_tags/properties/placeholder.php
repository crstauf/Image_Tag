<?php

class Image_Tag_Placeholder_Attributes extends Image_Tag_Attributes {

	const BASE_URL = 'https://via.placeholder.com/';

	/**
	 * Get "src" attribute.
	 *
	 * @uses static::get_url()
	 * @uses Image_Tag_Placeholder_Settings::get()
	 * @return string
	 */
	function get_src_attribute_for_view() {
		if ( !empty( $this->get( 'src' ) ) )
			return $this->get( 'src' );

		$src = static::BASE_URL;

		if ( is_null( $this->image_tag ) )
			return $src;

		$settings = $this->image_tag->settings->get( null, 'view' );

		if ( !empty( $settings['width'] ) )
			$src .= ( int ) $settings['width'];

		if (
			   !empty( $settings['width'] )
			&& !empty( $settings['height'] )
		)
			$src .= 'x';

		if ( !empty( $settings['height'] ) )
			$src .= ( int ) $settings['height'];

		$src .= '/';

		if ( !empty( $settings['bg_color'] ) )
			$src .= $settings['bg_color'] . '/';

		if ( !empty( $settings['text_color'] ) )
			$src .= $settings['text_color'] . '/';

		$src = untrailingslashit( $src );

		if ( !empty( $settings['text'] ) )
			$src = add_query_arg( 'text', urlencode( $settings['text'] ), $src );

		return $src;
	}

}

class Image_Tag_Placeholder_Settings extends Image_Tag_Settings {

	const DEFAULTS = array(
		'before_output' => array(),
		'after_output' => array(),
		'width' => null,
		'height' => null,
		'text' => null,
		'text_color' => null,
		'bg_color' => null,
	);

}

?>