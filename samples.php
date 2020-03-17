<?php
/**
 * Tests for `img` class use.
 */

add_action( 'wp_footer', function() {

	echo Image_Tag::create( 'https://i.picsum.photos/id/866/536/354.jpg', array(
		'class' => array(
			'rosalie',
		),
		'style' => array(
			'width: 300px',
			'height: auto',
		),
	) );

	$attachment = new WP_Query( array(
		'post_type' => 'attachment',
		'post_status' => 'inherit',
		'posts_per_page' => 1,
		'fields' => 'ids',
	) );

	if ( !empty( $attachment->posts ) )
		echo Image_Tag::create( $attachment->posts[0], array(), array(
			'image_sizes' => array( 'thumbnail', 'medium', 'medium_large', 'large', 'full' ),
		) );

	echo $img = Image_Tag::create( 'picsum', array(), array(
		'width' => 500,
		'height' => 200,
		'random' => true,
		'grayscale' => true,
		// 'seed' => 237,
	) );

	echo $img = Image_Tag::create( 'placeholder', array(
		'width' => 400,
		'height' => 300,
	) );

	echo $img = Image_Tag::create( 'assets/images/2020-landscape-1.png' );

} );

?>
