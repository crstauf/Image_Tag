<?php

/**
 * @covers Image_Tag_Plugin
 * @group plugin
 */
class Image_Tag_Plugin_Test extends WP_UnitTestCase {

	function test_info() {
		$data = get_plugin_data( dirname( __DIR__ ) . '/Image_Tag_Plugin.php', false, false );

		$this->assertEquals( 'Image Tag Generator', $data['Name'] );
		$this->assertEquals( 'https://github.com/crstauf/image_tag', $data['PluginURI'] );
		$this->assertEquals( '2.0', $data['Version'] );
		$this->assertEquals( 'WordPress drop-in to generate <code>img</code> tags.', $data['Description'] );
		$this->assertEquals( 'Caleb Stauffer', $data['Author'] );
		$this->assertEquals( 'https://develop.calebstauffer.com', $data['AuthorURI'] );
		$this->assertEquals( 'Image Tag Generator', $data['Title'] );
		$this->assertEquals( 'Caleb Stauffer', $data['AuthorName'] );

		$this->assertEmpty( $data['TextDomain'] );
		$this->assertEmpty( $data['DomainPath'] );
		$this->assertEmpty( $data['Network'] );
	}

	/**
	 * "RequiresWP" and "RequiresPHP" indexes were added in 5.3.0.
	 *
	 * @link https://developer.wordpress.org/reference/functions/get_plugin_data/
	 */
	function test_requires_info() {
		require ABSPATH . WPINC . '/version.php';

		if ( version_compare( $wp_version, '5.3.0', '<=' ) )
			$this->markTestSkipped( 'get_plugin_data() did not return "RequiresWP" and "RequiresPHP" prior to version 5.3.0' );

		$data = get_plugin_data( dirname( __DIR__ ) . '/Image_Tag_Plugin.php', false, false );

		$this->assertEquals( '5.1', $data['RequiresWP']  );
		$this->assertEquals( '7.1', $data['RequiresPHP'] );
	}

	function test_constants() {
		$this->assertSame( 2.0, Image_Tag_Plugin::VERSION );
	}

	/**
	 * @covers Image_Tag_Plugin::instance()
	 */
	function test_instance() {
		$this->assertInstanceOf( Image_Tag_Plugin::class, Image_Tag_Plugin::instance() );
	}

	/**
	 * @covers Image_Tag_Plugin::__construct()
	 */
	function test_construct() {
		$this->assertSame( 100, has_action( 'template_redirect', array( Image_Tag_Plugin::instance(), 'action__template_redirect' ) ) );
	}

	/**
	 * @covers Image_Tag_Plugin::includes()
	 */
	function test_includes() {
		$includes_dir = trailingslashit( dirname( __DIR__ ) ) . 'image_tags/';

		# Abstracts.
		$this->assertTrue( file_exists( $includes_dir . 'Image_Tag_Abstract.php'   ) );
		$this->assertTrue( class_exists( 'Image_Tag_Abstract' ) );

		# Properties.
		$this->assertTrue( file_exists( $includes_dir . 'Image_Tag_Properties.php' ) );
		$this->assertTrue( file_exists( $includes_dir . 'Image_Tag_Attributes.php' ) );
		$this->assertTrue( file_exists( $includes_dir . 'Image_Tag_Settings.php' ) );
		$this->assertTrue( class_exists( 'Image_Tag_Properties' ) );
		$this->assertTrue( class_exists( 'Image_Tag_Attributes' ) );
		$this->assertTrue( class_exists( 'Image_Tag_Settings' ) );

		# Types.
		$this->assertTrue( file_exists( $includes_dir . 'Image_Tag.php' ) );
		$this->assertTrue( class_exists( 'Image_Tag' ) );
	}

}