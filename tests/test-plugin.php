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
		$this->assertEquals( 0, version_compare( '5.3.2', $wp_version ) );
	}

	function test_php_version() {
		$this->assertEquals( 0, version_compare( '7.3.2', phpversion() ) );
	}

}
