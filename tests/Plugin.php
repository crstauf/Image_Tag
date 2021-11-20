<?php

declare( strict_types=1 );

namespace Image_Tag\Tests;
use Image_Tag\Plugin;

/**
 * @coversDefaultClass Image_Tag\Plugin
 */
class _Plugin extends \WP_UnitTestCase {

	/**
	 * Return directory path.
	 *
	 * @return string
	 */
	static function dir() : string {
		return trailingslashit( __DIR__ );
	}

	/**
	 * Test plugin info.
	 */
	function test_info() : void {
		$data = get_plugin_data( dirname( __DIR__ ) . '/Plugin.php', false, false );

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
	function test_requires_info() : void {
		require ABSPATH . WPINC . '/version.php';

		if ( version_compare( $wp_version, '5.3.0', '<=' ) )
			$this->markTestSkipped( 'get_plugin_data() did not return "RequiresWP" and "RequiresPHP" prior to WordPress v5.3.0' );

		$data = get_plugin_data( dirname( __DIR__ ) . '/Plugin.php', false, false );

		$this->assertEquals( '5.1', $data['RequiresWP']  );
		$this->assertEquals( '7.1', $data['RequiresPHP'] );
	}

	/**
	 * Test class constants.
	 *
	 * @group constant
	 */
	function test_constants() : void {
		$this->assertSame( 2.0, Plugin::VERSION );
	}

	/**
	 * @covers ::instance()
	 */
	function test_instance() : void {
		$this->assertInstanceOf( Plugin::class, Plugin::instance() );
	}

	/**
	 * @covers ::dir()
	 * @covers ::inc()
	 */
	function test_static() : void {
		$this->assertEquals( trailingslashit( dirname( __DIR__ ) ), Plugin::dir() );
		$this->assertEquals( trailingslashit( dirname( __DIR__ ) ) . 'includes/', Plugin::inc() );
	}

	/**
	 * @covers ::include_files()
	 */
	function test_includes() : void {
		$files = array_filter( get_included_files(), function( string $path ) : bool {
			return (
				   0 === stripos( $path, Plugin::dir() )
				&& 0 !== stripos( $path, Plugin::dir() . 'tests/' )
			);
		} );

		$files = array_map( function( string $path ) : string {
			return str_replace( Plugin::dir(), '', $path );
		}, $files );

		$expected = array (
			'Plugin.php',
			'includes/interfaces/Conversion.php',
			'includes/interfaces/Data_Store.php',
			'includes/interfaces/Dynamic_Source.php',
			'includes/interfaces/Output.php',
			'includes/interfaces/Validation.php',
			'includes/abstracts/Base.php',
			'includes/abstracts/Data_Store.php',
			'includes/abstracts/WordPress.php',
			'includes/data_stores/Attributes.php',
			'includes/data_stores/Settings.php',
			'includes/types/Image_Tag.php',
			// 'includes/types/Placeholder.php',
		);

		$this->assertSame( $expected, array_values( $files ) );
	}

}
