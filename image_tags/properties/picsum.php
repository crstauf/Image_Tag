<?php

class Image_Tag_Picsum_Attributes extends Image_Tag_Attributes {

	const BASE_URL = 'https://picsum.photos/';

	/**
	 * Get "src" attribute.
	 *
	 * @uses static::get_url()
	 * @uses Image_Tag_Picsum_Settings::get()
	 * @return string
	 */
	function get_src_attribute_for_view() {
		if ( wp_http_validate_url( $this->get( 'src' ) ) )
			return $this->get( 'src' );

		$src = static::BASE_URL;

		if ( is_null( $this->image_tag ) )
			return $src;

		$settings = $this->image_tag->settings->get( null, 'view' );

		if ( !empty( $settings['image_id'] ) )
			$src .= 'id/' . $settings['image_id'] . '/';

		if ( !empty( $settings['seed'] ) )
			$src .= 'seed/' . $settings['seed'] . '/';

		if ( !empty( $settings['width'] ) )
			$src .= ( int ) $settings['width'] . '/';

		if ( !empty( $settings['height'] ) )
			$src .= ( int ) $settings['height'] . '/';

		$src = untrailingslashit( $src );

		if ( false !== $settings['blur'] )
			$src = add_query_arg( 'blur', $settings['blur'], $src );

		if (
			  !empty( $settings['random'] )
			&& empty( $settings['image_id'] )
			&& empty( $settings['seed'] )
		)
			$src = add_query_arg( 'random', $settings['random'], $src );

		if ( !empty( $settings['grayscale'] ) )
			$src = add_query_arg( 'grayscale', 1, $src );

		return $src;
	}

}

class Image_Tag_Picsum_Settings extends Image_Tag_Settings {

	const DEFAULTS = array(
		'before_output' => array(),
		'after_output' => array(),
		'blur' => false,
		'seed' => null,
		'width' => null,
		'height' => null,
		'random' => false,
		'image_id' => null,
		'grayscale' => false,
	);

}

?>