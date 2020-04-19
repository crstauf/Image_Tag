<?php

require_once '_unit-test-case.php';

class Image_Tag_JoeSchmoe_Test extends Image_Tag_UnitTestCase {

	protected function class_name() {
		return 'Image_Tag_JoeSchmoe';
	}

	protected function create() {
	
	}

	function test_base_url() {
		$this->assertSame( 'https://joeschmoe.io/api/v1/', constant( $this->class_name() . '::BASE_URL' ) );
	}

	function test_type() {
		$type = 'joeschmoe';
		$img = $this->create( $type, array() );

		$this->assertSame( $type, $img->get_type() );
		
		$types = array(
			'remote',
			'external',
			'__placeholder',
			'avatar',
			'person',
			'profile',
			'joe schmoe',
			$type,
		);

		foreach ( $types as $type )
			$this->assertTrue( $img->is_type( $type ) );
	}

}

?>