<?php

require_once '_unit-test-case.php';

class Image_Tag_Unsplash_Test extends Image_Tag_UnitTestCase {

	protected function class_name() {
		return 'Image_Tag_Unsplash';
	}

	protected function create() {
	
	}

	function test_base_url() {
		$this->assertSame( 'https://source.unsplash.com/', constant( $this->class_name() . '::BASE_URL' ) );
	}

	function test_type() {
		$type = 'unsplash';
		$img = $this->create( $type, array() );

		$this->assertSame( $type, $img->get_type() );
		
		$types = array(
			'remote',
			'external',
			'__placeholder',
			'source-unsplash',
			'Unsplash Source',
			'source.unsplash.com',
			$type,
		);

		foreach ( $types as $type )
			$this->assertTrue( $img->is_type( $type ) );
	}

}

?>