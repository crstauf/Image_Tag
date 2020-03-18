<?php

/**
 * @group wp
 * @group wp_theme
 */
class Image_Tag_WP_Theme_Test extends WP_UnitTestCase {

	const SRC = 'assets/images/2020-landscape-1.png';

	function test_source() {
		$img = Image_Tag::create( static::SRC );
		$this->assertEquals( trailingslashit( get_stylesheet_directory_uri() ) . static::SRC, $img->get_attribute( 'src' ) );
	}

	function test_width() {
		$img = Image_Tag::create( static::SRC );
		$this->assertEquals( 1200, $img->get_width() );
	}

	function test_height() {
		$img = Image_Tag::create( static::SRC );
		$this->assertEquals( 769, $img->get_height() );
	}

	function test_ratio() {
		$img = Image_Tag::create( static::SRC );
		$this->assertEquals( 0.640833333333333, $img->get_ratio() );
	}

}
