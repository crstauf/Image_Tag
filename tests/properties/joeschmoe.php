<?php

/**
 * @coversDefaultClass Image_Tag_JoeSchmoe_Attributes
 */
class Image_Tag_JoeSchmoe_Attribute_Test extends Image_Tag_Attributes {

	/**
	 * @group constant
	 */
	function test_constant_primary_url() {
		$this->assertSame( 'https://joeschmoe.io/api/v1/', constant( $this->class_name() . '::PRIMARY_URL' ) );
	}

	/**
	 * @group constant
	 */
	function test_constant_alt_url() {
		$this->assertSame( 'https://joeschmoe.crstauf.workers.dev/', constant( $this->class_name() . '::ALT_URL' ) );
	}

}

/**
 * @coversDefaultClass Image_Tag_JoeSchmoe_Settings
 */
class Image_Tag_JoeSchmoe_Settings_Test extends Image_Tag_Settings {

}

?>