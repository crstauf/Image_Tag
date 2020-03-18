<?php

/**
 * @group wp
 * @group wp_theme
 */
class Image_Tag_WP_Theme_Test extends WP_UnitTestCase {

	static $img;
	const SRC = 'assets/images/2020-landscape-1.png';

	static function setUpBeforeClass() {
		static::$img = Image_Tag::create( static::SRC );
	}

	function test_source() {
		$this->assertEquals( trailingslashit( get_stylesheet_directory_uri() ) . static::SRC, static::$img->get_attribute( 'src' ) );
	}

	function test_width() {
		$this->assertEquals( 1200, static::$img->get_width() );
	}

	function test_height() {
		$this->assertEquals( 769, static::$img->get_height() );
	}

	function test_ratio() {
		$this->assertEquals( 0.640833333333333, static::$img->get_ratio() );
	}

}
