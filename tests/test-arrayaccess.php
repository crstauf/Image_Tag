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

}
