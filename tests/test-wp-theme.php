<?php

require_once '_unit-test-case.php';

class Image_Tag_WP_Theme_Test extends Image_Tag_UnitTestCase {

	protected function class_name() {
		return 'Image_Tag_WP_Theme';
	}

	protected function create() {
	
	}

	function test_type() {
		$type = 'theme';
		$img = $this->create();

		$this->assertSame( $type, $img->get_type() );
		
		$types = array(
			'wp-theme',
			'wordpress-theme',
			$type,
		);

		foreach ( $types as $type )
			$this->assertTrue( $img->is_type( $type ) );
	}

}

?>