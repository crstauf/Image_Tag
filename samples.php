<?php
/**
 * Tests for `img` class use.
 */

add_action( 'wp_footer', function() {

	echo $img = Image_Tag::create( 'https://i.picsum.photos/id/866/536/354.jpg', array(
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

	if ( !empty( $attachment->posts ) ) {
		echo $img = Image_Tag::create( $attachment->posts[0], array(), array(
			'image_sizes' => array( 'thumbnail', 'medium', 'medium_large', 'large', 'full' ),
		) );
		echo $img->picsum();
		echo $img->placeholder();
		echo $img->get_mode_color();
	}

	echo $img = Image_Tag::create( 'picsum', array(), array(
		'width' => 500,
		'height' => 200,
		'random' => true,
		'grayscale' => true,
		// 'seed' => 237,
	) );

	echo $img = Image_Tag::create( 'placeholder', array(
		'width' => 1600,
		'height' => 800,
		'srcset' => array(
			'https://via.placeholder.com/1600x800 1600w',
			'https://via.placeholder.com/1200x600 1200w',
			'https://via.placeholder.com/1000x500 1000w',
			'https://via.placeholder.com/800x400   800w',
			'https://via.placeholder.com/600x300   600w',
			'https://via.placeholder.com/400x200   400w',
			'https://via.placeholder.com/200x100   200w',
		),
	) );

	echo $img = Image_Tag::create( 'assets/images/2020-landscape-1.png' );
	echo $img->noscript();

} );

?>
