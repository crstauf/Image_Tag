<?php

/**
 * @link https://joeschmoe.io/
 * @group joeschmoe
 */
class Image_Tag_JoeSchmoe_Test extends WP_UnitTestCase {

	function test_base_source() {
		$img = Image_Tag::create( 'joeschmoe' );

		$this->assertEquals( 'https://joeschmoe.io/api/v1/', Image_Tag_JoeSchmoe::BASE_URL );
		$this->assertContains( Image_Tag_JoeSchmoe::BASE_URL, $img->get_attribute( 'src' ) );
	}

	function test_gender_setting() {
		$img = Image_Tag::create( 'joeschmoe' );
		$this->assertNull( $img->get_setting( 'gender' ) );

		$img->set_setting( 'gender', 'non-binary' );
		$this->assertNull( $img->get_setting( 'gender' ) );

		$possible = array(
			'male',
			'female',
		);
		$test = $possible[ array_rand( $possible ) ];

		$img->set_setting( 'gender', $test );

		$this->assertEquals( $test, $img->get_setting( 'gender' ) );
		$this->assertContains( '/' . $test . '/', $img->get_attribute( 'src' ) );
	}

	function test_seed_setting() {
		$img = Image_Tag::create( 'joeschmoe', array(), array( 'seed' => __FUNCTION__ ) );

		$this->assertEquals( __FUNCTION__, $img->get_setting( 'seed' ) );
		$this->assertContains( __FUNCTION__, $img->get_attribute( 'src' ) );
	}

	function test_type() {
		$img = Image_Tag::create( 'joeschmoe' );

		$this->assertTrue( $img->is_type( 'joeschmoe' ) );
	}

	function test_valid() {
		$img = Image_Tag::create( 'joeschmoe' );

		$this->assertTrue( $img->is_valid() );
	}

}