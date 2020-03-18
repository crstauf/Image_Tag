<?php

class Image_Tag_Test_Attributes extends WP_UnitTestCase {

	function test_set_attributes() {
		$styles = 'width: 200px; height: 200px;';

		$image_tag = new Image_Tag( array(
			'id' => __FUNCTION__,
			'class' => __FUNCTION__,
			'srcset' => 'https://picsum.photos/400/300 400w',
			'sizes' => '100vw',
			'style' => $styles,
		) );

		$this->assertEquals( __FUNCTION__, $image_tag['id'] );
		$this->assertEquals( __FUNCTION__, $image_tag->get_attribute( 'class' ) );
		$this->assertEquals( $styles, $image_tag->get_attribute( 'style' ) );
	}

	function test_get_attributes() {
		$attributes = array( 'id' => __FUNCTION__ );
		$image_tag = new Image_Tag( $attributes );
		$this->assertEquals( $attributes, $image_tag->get_attributes() );
		$this->assertEquals( __FUNCTION__, $image_tag->id );
	}

	function test_add_to_attribute_array() {
		$attributes = array( 'id' => __FUNCTION__ );
		$image_tag = new Image_Tag( $attributes );

		$image_tag->add_class( __FUNCTION__ );
		$attributes['class'] = implode( ' ', array( __FUNCTION__ ) );
		$this->assertEquals( $attributes, $image_tag->get_attributes() );

		$image_tag->add_size( '100vw' );
		$attributes['sizes'] = implode( ', ', array( '100vw' ) );
		$this->assertEquals( $attributes, $image_tag->get_attributes() );

		$image_tag->add_srcset( 'https://picsum.photos/400/300 400w' );
		$attributes['srcset'] = implode( ', ', array( 'https://picsum.photos/400/300 400w' ) );
		$this->assertEquals( $attributes, $image_tag->get_attributes() );

		$image_tag->add_style( 'width: auto' );
		$attributes['style'] = implode( '; ', array( 'width: auto' ) );
		$this->assertEquals( $attributes, $image_tag->get_attributes() );
	}

}
