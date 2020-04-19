<?php

require_once '_unit-test-case.php';

class Image_Tag_WP_Attachment_Test extends Image_Tag_UnitTestCase {

	protected function class_name() {
		return 'Image_Tag_WP_Attachment';
	}

	protected function create() {
	
	}

	function test_type() {
		$type = 'attachment';
		$img = $this->create();

		$this->assertSame( $type, $img->get_type() );
		
		$types = array(
			'upload',
			'wp-attachment',
			'wordpress-attachment',
			$type,
		);

		foreach ( $types as $type )
			$this->assertTrue( $img->is_type( $type ) );
	}

}

?>