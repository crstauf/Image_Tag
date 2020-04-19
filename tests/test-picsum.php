<?php

require_once '_unit-test-case.php';

class Image_Tag_Picsum_Test extends Image_Tag_UnitTestCase {

	protected function class_name() {
		return 'Image_Tag_Picsum';
	}

	protected function create() {
	
	}

	function test_base_url() {
		$this->assertSame( 'https://picsum.photos/', constant( $this->class_name() . '::BASE_URL' ) );
	}

	function test_type() {
		$type = 'picsum';
		$img = $this->create( $type, array() );

		$this->assertSame( $type, $img->get_type() );
		
		$types = array(
			'remote',
			'external',
			'__placeholder',
			'lorem-picsum',
			'Lorem Picsum',
			'picsum.photos',
			$type,
		);

		foreach ( $types as $type )
			$this->assertTrue( $img->is_type( $type ) );
	}

}

?>