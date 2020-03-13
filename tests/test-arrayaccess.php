<?php

class Image_Tag_ArrayAccess_Test extends WP_UnitTestCase {

	function test_external() {
		$image_tag = new Image_Tag( array( 'id' => __FUNCTION__ ) );
		$this->assertEquals( __FUNCTION__, $image_tag['id'] );
	}

}
