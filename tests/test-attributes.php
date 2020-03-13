<?php

class Image_Tag_Test_Attributes extends WP_UnitTestCase {

	function test_set_attributes() {
		$image_tag = new Image_Tag( array( 'id' => __FUNCTION__ ) );
		$this->assertEquals( __FUNCTION__, $image_tag['id'] );
	}

	function test_get_attributes() {
		$attributes = array( 'id' => __FUNCTION__ );
		$image_tag = new Image_Tag( $attributes );
		$this->assertEquals( $attributes, $image_tag->get_attributes() );
	}

	function test_add_to_attribute_array() {
		$attributes = array( 'id' => __FUNCTION__ );
		$image_tag = new Image_Tag( $attributes );

		$image_tag->add_class( __FUNCTION__ );
		$attributes['class'] = implode( ' ', array( __FUNCTION__ ) );
		$this->assertEquals( $attributes, $image_tag->get_attributes() );

		$image_tag->add_style( 'width: auto' );
		$attributes['style'] = implode( '; ', array( 'width: auto' ) );
		$this->assertEquals( $attributes, $image_tag->get_attributes() );
	}

}
