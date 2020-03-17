<?php

/**
 * @group wp
 * @group wp_theme
 */
class Image_Tag_WP_Theme_Test extends WP_UnitTestCase {

	function test_theme() {
		$src = 'assets/images/2020-landscape-1.png';
		$img = Image_Tag::create( $src );

		$this->assertEquals( trailingslashit( get_stylesheet_directory_uri() ) . $src, $img->get_attribute( 'src' ) );
	}

}