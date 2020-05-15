<?php

/**
 * @coversDefaultClass Image_Tag_Plugin
 * @group plugin
 */
class Image_Tag_Plugin_Test extends WP_UnitTestCase {

	static $directory = null;
	static $included_files = array();

	/**
	 * Test plugin info.
	 */
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

	/**
	 * Test class constants.
	 *
	 * @group constant
	 */
	function test_constants() {
		$this->assertSame( 2.0, Image_Tag_Plugin::VERSION );
	}

	/**
	 * @covers ::instance()
	 */
	function test_instance() {
		$this->assertInstanceOf( Image_Tag_Plugin::class, Image_Tag_Plugin::instance() );
	}

	/**
	 * @covers ::__construct()
	 */
	function test_construct() {
		$this->assertSame( 100, has_action( 'template_redirect', array( Image_Tag_Plugin::instance(), 'action__template_redirect' ) ) );
	}

	/**
	 * Data provider for Image_Tag_Plugin_Test::test_includes()
	 *
	 * @see self::test_includes()
	 * @return array[]
	 */
	function data_includes() {
		static::$directory = trailingslashit( dirname( __DIR__ ) ) . 'image_tags/';
		static::$included_files = get_included_files();

		return array(
			'abstract/image tag' => array( 'Image_Tag_Abstract.php', 'Image_Tag_Abstract' ),
			'abstract/properties' => array( 'properties/abstract.php', 'Image_Tag_Properties_Abstract' ),
			'properties/attributes' => array( 'properties/attributes.php', 'Image_Tag_Attributes' ),
			'properties/settings' => array( 'properties/settings.php', 'Image_Tag_Settings' ),
			'image tag' => array( null, 'Image_Tag' ),
		);
	}

	/**
	 * @param string $relative_filepath
	 * @param string $class_name
	 *
	 * @covers ::includes()
	 *
	 * @dataProvider data_includes
	 */
	function test_includes( $relative_filepath, $class_name ) {
		if ( !is_null( $relative_filepath ) )
			$this->assertContains( static::$directory . $relative_filepath, static::$included_files );

		if ( !is_null( $class_name ) )
			$this->assertTrue( class_exists( $class_name ) );
	}

}