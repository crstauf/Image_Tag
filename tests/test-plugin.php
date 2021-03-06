<?php

class Image_Tag_Plugin_Test extends WP_UnitTestCase {

	function test_info() {
		$file = trailingslashit( dirname( __DIR__ ) ) . 'Image_Tag.php';
		$info = get_plugin_data( $file );

		$this->assertEquals( $info['Name'], 'Image Tag Generator' );
		$this->assertEquals( $info['Version'], '1.0' );
	}

	function test_constants() {
		$this->assertEquals( 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7', Image_Tag::BLANK );
	}

	function test_wp_version() {
		global $wp_version;

		$this->assertEquals(  1, version_compare( $wp_version, '5.1', '>=' ), sprintf( 'WordPress version %s has not been tested.', $wp_version ) );
		$this->assertEquals( -1, version_compare( $wp_version, '5.5', '<'  ), sprintf( 'WordPress version %s has not been tested.', $wp_version ) );
	}

	function test_php_version() {
		$this->assertTrue( version_compare( phpversion(), '7.4', '<'  ), sprintf( 'PHP version %s has not been tested.', phpversion() ) );
		$this->assertTrue( version_compare( phpversion(), '7.1', '>=' ), sprintf( 'PHP version %s has not been tested.', phpversion() ) );
	}

}
