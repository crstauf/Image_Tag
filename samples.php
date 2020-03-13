<?php
/**
 * Tests for `img` class use.
 */

add_action( 'wp_footer', function() {

	echo Image_Tag::create( site_url( 'wp-content/uploads/2019/11/arnold-exconde-544483.jpg' ), array(
		'class' => array(
			'rosalie',
		),
		'style' => array(
			'width: 300px',
			'height: auto',
		),
	) );

	echo Image_Tag::create( 22, array(), array(
		'image_sizes' => array( 'small', 'medium' ),
	) );

	echo $img = Image_Tag::create( 'picsum', array(), array(
		'width' => 500,
		'height' => 200,
		// 'random' => true,
		'grayscale' => true,
		'seed' => 237,
	) );

	echo $img = Image_Tag::create( 'placeholder', array(
		'width' => 500,
		'height' => 300,
	) );

} );

?>
