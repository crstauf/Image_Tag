<?php

/**
 * @group wp
 * @group wp_theme
 */
class Image_Tag_WP_Theme_Test extends WP_UnitTestCase {

	const SRC = 'screenshot.png';

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
		$this->assertEquals( 900, $img->get_height() );
	}

	function test_ratio() {
		$img = Image_Tag::create( static::SRC );
		$this->assertEquals( 0.75, $img->get_ratio() );
	}

	/**
	 * @group _placeholder
	 * @group picsum
	 */
	function test_picsum() {
		$img = Image_Tag::create( static::SRC );
		$this->assertInstanceOf( 'Image_Tag_Picsum', $img->picsum() );
	}

	/**
	 * @group _placeholder
	 * @group picsum
	 * @group http
	 */
	function test_picsum_from_invalid() {
		$img = Image_Tag::create( 'image-does-not-exist.jpg' );
		$this->assertInstanceOf( 'Image_Tag_Picsum', $img->picsum() );

		$picsum = $img->picsum( array(
			'width' => 160,
			'height' => 90,
		) );
		$this->assertEquals( 160, $picsum->get_attribute( 'width' ) );
		$this->assertEquals(  90, $picsum->get_attribute( 'height' ) );

		$this->assertEquals( 'image/jpeg', wp_remote_retrieve_header( $picsum->http(), 'content-type' ) );

		$img = Image_Tag::create( 0, null, array(
			'image-sizes' => array( 'full' ),
			'width' => 1600,
			'height' => 900,
		) );
		$this->assertEquals( 1600, $img->get_setting(  'width' ) );
		$this->assertEquals(  900, $img->get_setting( 'height' ) );

		$picsum = $img->picsum();
		$this->assertEquals( 1600, $picsum->get_setting(  'width' ) );
		$this->assertEquals(  900, $picsum->get_setting( 'height' ) );
	}

	/**
	 * @group _placeholder
	 * @group placeholder
	 */
	function test_placeholder() {
		$img = Image_Tag::create( static::SRC );
		$this->assertInstanceOf( 'Image_Tag_Placeholder', $img->placeholder() );
	}

	/**
	 * @group _placeholder
	 * @group placeholder
	 * @group http
	 */
	function test_placeholder_from_invalid() {
		$img = Image_Tag::create( 'image-does-not-exist.jpg' );
		$this->assertInstanceOf( 'Image_Tag_Placeholder', $img->placeholder() );

		$placeholder = $img->placeholder( array(
			'width' => 160,
			'height' => 90,
		) );
		$this->assertEquals( 160, $placeholder->get_attribute( 'width' ) );
		$this->assertEquals(  90, $placeholder->get_attribute( 'height' ) );

		$this->assertEquals( 'image/png', wp_remote_retrieve_header( $placeholder->http(), 'content-type' ) );

		$img = Image_Tag::create( 0, null, array(
			'image-sizes' => array( 'full' ),
			'width' => 1600,
			'height' => 900,
		) );
		$this->assertEquals( 1600, $img->get_setting(  'width' ) );
		$this->assertEquals(  900, $img->get_setting( 'height' ) );

		$placeholder = $img->placeholder();
		$this->assertEquals( 1600, $placeholder->get_setting(  'width' ) );
		$this->assertEquals(  900, $placeholder->get_setting( 'height' ) );
	}

	/**
	 * @group _placeholder
	 * @group joeschmoe
	 */
	function test_joeschmoe() {
		$img = Image_Tag::create( static::SRC );
		$this->assertInstanceOf( 'Image_Tag_JoeSchmoe', $img->joeschmoe() );
	}

	/**
	 * @group _placeholder
	 * @group joeschmoe
	 * @group http
	 */
	function test_joeschmoe_from_invalid() {
		$img = Image_Tag::create( 'image-does-not-exist.jpg' );

		$this->assertInstanceOf( 'Image_Tag_JoeSchmoe', $img->joeschmoe() );
		$this->assertEquals( 'image/svg+xml; charset=utf-8', wp_remote_retrieve_header( $img->joeschmoe()->http(), 'content-type' ) );
	}

	function test_colors() {
		$img = Image_Tag::create( static::SRC );
		$colors = array(
			'#ffffff' => 0.41791666666666666,
			'#606060' => 0.38815476190476189,
			'#000000' => 0.17232142857142857,
		);

		$this->assertEquals( $colors, $img->get_colors() );
		$this->assertEquals( array_keys( $colors )[0], $img->get_mode_color() );

		$transient_key = Image_Tag_WP_Theme::colors_transient_key( $img->path );
		$this->assertEquals( $colors, get_transient( $transient_key ) );

		$transient_timeout = get_option( '_transient_timeout_' . $transient_key );
		$life = $transient_timeout - time();
		$this->assertGreaterThanOrEqual( DAY_IN_SECONDS,     $life );
		$this->assertLessThanOrEqual(    DAY_IN_SECONDS + 1, $life );

		# Test retrieval from transient.
		$this->assertEquals( $colors, $img->get_colors() );
	}

	function test_type() {
		$img = Image_Tag::create( static::SRC );

		$this->assertTrue( $img->is_type(              'wp' ) );
		$this->assertTrue( $img->is_type(           'local' ) );
		$this->assertTrue( $img->is_type(           'theme' ) );
		$this->assertTrue( $img->is_type(        'internal' ) );
		$this->assertTrue( $img->is_type(        'wp-theme' ) );
		$this->assertTrue( $img->is_type(       'wordpress' ) );
		$this->assertTrue( $img->is_type( 'wordpress-theme' ) );

		$this->assertFalse( $img->is_type(           'attachment' ) );
		$this->assertFalse( $img->is_type(        'wp-attachment' ) );
		$this->assertFalse( $img->is_type( 'wordpress-attachment' ) );

		$this->assertFalse( $img->is_type( 'external' ) );
	}

	function test_valid() {
		$img = Image_Tag::create( static::SRC );
		$this->assertTrue( $img->is_valid() );
	}

	function test_invalid() {
		$img = Image_Tag::create( 'does-not-exist.jpg' );
		$this->assertFalse( $img->is_valid() );
	}

	/**
	 * @group _placeholder
	 * @group unsplash
	 */
	function test_unsplash() {
		$img = Image_Tag::create( static::SRC );
		$this->assertInstanceOf( 'Image_Tag_Unsplash', $img->unsplash() );
	}

	/**
	 * @group _placeholder
	 * @group unsplash
	 * @group http
	 */
	function test_unsplash_from_invalid() {
		$img = Image_Tag::create( 'image-does-not-exist.jpg' );
		$this->assertInstanceOf( 'Image_Tag_Unsplash', $img->unsplash() );

		$unsplash = $img->unsplash( array(
			'width' => 160,
			'height' => 90,
		) );
		$this->assertEquals( 160, $unsplash->get_attribute( 'width' ) );
		$this->assertEquals(  90, $unsplash->get_attribute( 'height' ) );

		$this->assertEquals( 'image/jpeg', wp_remote_retrieve_header( $unsplash->http(), 'content-type' ) );

		$img = Image_Tag::create( 0, null, array(
			'image-sizes' => array( 'full' ),
			'width' => 1600,
			'height' => 900,
		) );
		$this->assertEquals( 1600, $img->get_setting(  'width' ) );
		$this->assertEquals(  900, $img->get_setting( 'height' ) );

		$unsplash = $img->unsplash();
		$this->assertEquals( 1600, $unsplash->get_setting(  'width' ) );
		$this->assertEquals(  900, $unsplash->get_setting( 'height' ) );
	}

}
