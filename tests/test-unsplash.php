<?php

/**
 * @link https://source.unsplash.com/
 * @group _placeholder
 * @group unsplash
 */
class Image_Tag_Unsplash_Test extends WP_UnitTestCase {

	function test_image_id() {
		$img = Image_Tag::create( 'unsplash', null, array(
			'image_id' => 'WLUHO9A_xik',
		) );

		$this->assertEquals( Image_Tag_Unsplash::BASE_URL . 'WLUHO9A_xik/', $img->get_attribute( 'src' ) );
	}

	function test_user() {
		$img = Image_Tag::create( 'unsplash', null, array(
			'user' => 'erondu',
		) );

		$this->assertEquals( Image_Tag_Unsplash::BASE_URL . 'user/erondu/', $img->get_attribute( 'src' ) );
	}

	function test_user_likes() {
		$img = Image_Tag::create( 'unsplash', null, array(
			'user_likes' => 'erondu',
		) );

		$this->assertEquals( Image_Tag_Unsplash::BASE_URL . 'user/erondu/likes/', $img->get_attribute( 'src' ) );
	}

	function test_collection() {
		$img = Image_Tag::create( 'unsplash', null, array(
			'collection' => '190727',
		) );

		$this->assertEquals( Image_Tag_Unsplash::BASE_URL . 'collection/190727/', $img->get_attribute( 'src' ) );
	}

	function test_update() {
		$img = Image_Tag::create( 'unsplash', null, array(
			'user' => 'erondu',
			'update' => 'daily',
		) );

		$this->assertEquals( Image_Tag_Unsplash::BASE_URL . 'user/erondu/daily/', $img->get_attribute( 'src' ) );
	}

	function test_featured() {
		$img = Image_Tag::create( 'unsplash', null, array(
			'user' => 'erondu',
			'featured' => true,
		) );

		$this->assertEquals( Image_Tag_Unsplash::BASE_URL . 'user/erondu/featured/', $img->get_attribute( 'src' ) );
	}

	function test_width_height() {
		$img = Image_Tag::create( 'unsplash', null, array(
			'width' => 1600,
			'height' => 900,
		) );

		$this->assertEquals( Image_Tag_Unsplash::BASE_URL . '1600x900/', $img->get_attribute( 'src' ) );

		$img = Image_Tag::create( 'unsplash', array(
			'width' => 1600,
			'height' => 900,
		) );

		$this->assertEquals( Image_Tag_Unsplash::BASE_URL . '1600x900/', $img->get_attribute( 'src' ) );
	}

	function test_search() {
		$img = Image_Tag::create( 'unsplash', null, array(
			'search' => 'forest',
		) );

		$this->assertEquals( Image_Tag_Unsplash::BASE_URL . '?forest', $img->get_attribute( 'src' ) );
		$this->assertEquals( array( 'forest' ), $img->get_setting( 'search' ) );

		$img = Image_Tag::create( 'unsplash', null, array(
			'search' => array( 'forest', 'river' ),
		) );

		$this->assertEquals( Image_Tag_Unsplash::BASE_URL . '?forest,river', $img->get_attribute( 'src' ) );
	}

	function test_multiple() {
		$img = Image_Tag::create( 'unsplash', array(
			'width' => 1600,
			'height' => 900,
		), array(
			'user' => 'erondu',
			'search' => 'water',
		) );

		$this->assertEquals( Image_Tag_Unsplash::BASE_URL . 'user/erondu/1600x900/?water', $img->get_attribute( 'src' ) );
	}

	function test_valid() {
		$img = Image_Tag::create( 'unsplash', array(
			'width' => 1600,
			'height' => 900,
		) );

		$this->assertTrue( $img->is_valid() );

		$img = Image_Tag::create( 'unsplash', null, array(
			'width' => 1600,
			'height' => 900,
		) );

		$this->assertTrue( $img->is_valid() );
	}

	function test_invalid() {
		$img = Image_Tag::create( 'unsplash', array(
			'width' => 1600,
		) );

		$this->assertFalse( $img->is_valid() );

		$img = Image_Tag::create( 'unsplash', null, array(
			'width' => 1600,
		) );

		$this->assertFalse( $img->is_valid() );

		$img = Image_Tag::create( 'unsplash', array(
			'height' => 1600,
		) );

		$this->assertFalse( $img->is_valid() );

		$img = Image_Tag::create( 'unsplash', null, array(
			'height' => 1600,
		) );

		$this->assertFalse( $img->is_valid() );
	}

}

?>