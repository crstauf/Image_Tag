<?php

class Image_Tag_ArrayAccess_Test extends WP_UnitTestCase {

	function test_get() {
		$image_tag = new Image_Tag( array( 'id' => __FUNCTION__ ), array( 'test' => __FUNCTION__ ) );
		$this->assertEquals( __FUNCTION__, $image_tag['id'] );
		$this->assertEquals( __FUNCTION__, $image_tag['test'] );
		$this->assertNull( $image_tag['tester'] );
	}

	function test_isset() {
		$image_tag = new Image_Tag( array( 'id' => __FUNCTION__ ) );
		$this->assertTrue( isset( $image_tag['id'] ) );
		$this->assertFalse( isset( $image_tag['ids'] ) );
	}

	function test_set() {
		$img = new Image_Tag( array( 'id' => __FUNCTION__ ) );
		$img['id'] = uniqid();

		$this->assertNotEquals( __FUNCTION__, $img['id'] );
	}

	function test_unset() {
		$img = new Image_Tag( array( 'id' => __FUNCTION__ ) );
		unset( $img['id'] );

		$this->assertNotEquals( __FUNCTION__, $img['id'] );
		$this->assertEmpty( $img['id'] );
	}

}
