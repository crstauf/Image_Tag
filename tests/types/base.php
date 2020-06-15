<?php

require_once 'abstract.php';

/**
 * @coversDefaultClass Image_Tag
 */
class Image_Tag_Test extends Image_Tag_Test_Base {

	protected function class_name() {
		return Image_Tag::class;
	}

	/**
	 * @group constant
	 */
	function test_constant_blank() {
		$this->assertSame( 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==', constant( $this->class_name() . '::BLANK' ) );
	}

	/**
	 * Data provider for Image_Tag_Test_Base::test_constant_types().
	 *
	 * @see Image_Tag_Test_Base::test_constant_types()
	 * @return array[]
	 */
	function data_constant_types() {
		return array(
			array(
				array( 'base' ),
			),
		);
	}


	/*
	 ######  ########    ###    ######## ####  ######
	##    ##    ##      ## ##      ##     ##  ##    ##
	##          ##     ##   ##     ##     ##  ##
	 ######     ##    ##     ##    ##     ##  ##
	      ##    ##    #########    ##     ##  ##
	##    ##    ##    ##     ##    ##     ##  ##    ##
	 ######     ##    ##     ##    ##    ####  ######
	*/

	/**
	 * Data provider for Image_Tag_Test_Base::test_create().
	 *
	 * @see Image_Tag_Test_Base::test_create()
	 * @return array[]
	 */
	function data_create() {
		return array(

			'fail' => array(
				'warning',
				'nothing.unsplash.com',
			),

			'base' => array(
				$this->new_instance( array( 'src' => 'https://source.unsplash.com/1000x1000' ) ),
				'https://source.unsplash.com/1000x1000',
			),

		);
	}


	/*
	##     ##    ###     ######   ####  ######
	###   ###   ## ##   ##    ##   ##  ##    ##
	#### ####  ##   ##  ##         ##  ##
	## ### ## ##     ## ##   ####  ##  ##
	##     ## ######### ##    ##   ##  ##
	##     ## ##     ## ##    ##   ##  ##    ##
	##     ## ##     ##  ######   ####  ######
	*/

	/**
	 * Data provider for Image_Tag_Test_Base::test__toString().
	 *
	 * @see static::test__toString()
	 * @return array[]
	 */
	function data__toString() {
		return array(

			'id' => array(
				$this->new_instance( array( 'id' => __FUNCTION__ ) ),
				'warning',
			),

			'class' => array(
				$this->new_instance( array( 'class' => 'foo bar' ) ),
				'warning',
			),

			'src' => array(
				$this->new_instance( array(
					'id' => __FUNCTION__,
					'class' => 'bar foo',
					'src' => 'https://source.unsplash.com/1000x1000',
				) ),
				'<img ' .
					'id="' . esc_attr( __FUNCTION__ ) . '" ' .
					'src="' . esc_attr( 'https://source.unsplash.com/1000x1000' ) . '" ' .
					'sizes="100vw" ' .
					'class="bar foo" ' .
					'alt="" ' .
				'/>',
			),

		);
	}


	/*
	##     ##    ###    ##       #### ########     ###    ######## ####  #######  ##    ##
	##     ##   ## ##   ##        ##  ##     ##   ## ##      ##     ##  ##     ## ###   ##
	##     ##  ##   ##  ##        ##  ##     ##  ##   ##     ##     ##  ##     ## ####  ##
	##     ## ##     ## ##        ##  ##     ## ##     ##    ##     ##  ##     ## ## ## ##
	 ##   ##  ######### ##        ##  ##     ## #########    ##     ##  ##     ## ##  ####
	  ## ##   ##     ## ##        ##  ##     ## ##     ##    ##     ##  ##     ## ##   ###
	   ###    ##     ## ######## #### ########  ##     ##    ##    ####  #######  ##    ##
	*/

	/**
	 * @covers ::get_type()
	 */
	function test_get_type() {
		$this->assertSame( 'base', $this->new_instance()->get_type() );
	}

	/**
	 * @covers ::is_type()
	 */
	function test_is_type() {
		$instance = $this->new_instance();

		$this->assertTrue( $instance->is_type( 'base' ) );
		$this->assertTrue( $instance->is_type( array( 'external', 'base' ) ) );

		$this->assertFalse( $instance->is_type( 'external' ) );
		$this->assertFalse( $instance->is_type( 'local' ) );
	}

	/**
	 * @covers ::is_valid()
	 * @covers ::check_valid()
	 */
	function test_is_valid() {
		$instance = $this->new_instance();

		$this->assertFalse( $instance->is_valid( 'base' ) );
		$this->assertFalse( $instance->is_valid() );

		$instance->attributes->set( 'src', 'https://source.unsplash.com/1000x1000' );

		$this->assertTrue( $instance->is_valid( array( 'foobar', 'base' ) ) );
		$this->assertTrue( $instance->is_valid( 'base' ) );
		$this->assertTrue( $instance->is_valid() );

		$this->assertFalse( $instance->is_valid( array( 'foo', 'bar' ) ) );
		$this->assertFalse( $instance->is_valid( 'foobar' ) );
	}


	/*
	######## ########    ###    ######## ##     ## ########  ########  ######
	##       ##         ## ##      ##    ##     ## ##     ## ##       ##    ##
	##       ##        ##   ##     ##    ##     ## ##     ## ##       ##
	######   ######   ##     ##    ##    ##     ## ########  ######    ######
	##       ##       #########    ##    ##     ## ##   ##   ##             ##
	##       ##       ##     ##    ##    ##     ## ##    ##  ##       ##    ##
	##       ######## ##     ##    ##     #######  ##     ## ########  ######
	*/

	/**
	 * Data provider for Image_Tag_Test::test_http().
	 *
	 * @see static::test_http()
	 * @return array[]
	 */
	function data_http() {
		return array(
			array(
				$this->new_instance( array( 'src' => 'https://source.unsplash.com/WLUHO9A_xik/1x1' ) ),
			),
		);
	}

	/**
	 * @param Image_Tag $instance
	 *
 	 * @covers ::http()
 	 * @group instance
	 * @group feature
 	 * @group external-http
 	 *
 	 * @dataProvider data_http
 	 */
 	function test_http( Image_Tag_Abstract $instance ) {
 		$count = ( int ) did_action( 'http_api_debug' );
 		$response = $instance->http();

 		if ( is_wp_error( $response ) )
 			$this->fail( $response->get_error_message() );

 		$this->assertEquals( ++$count, did_action( 'http_api_debug' ) );
 		$this->assertSame( '1054000', wp_remote_retrieve_header( $response, 'content-length' ) );
 		$this->assertSame( 'image/jpeg', wp_remote_retrieve_header( $response, 'content-type' ) );
 		$this->assertSame( '81fa2e45b06ed579f48a80af08225aa57fd5de7f', wp_remote_retrieve_header( $response, 'x-imgix-id' ) );

 		# Call again to confirm pulled from cache.
 		$instance->http();
 		$this->assertEquals( $count, did_action( 'http_api_debug' ) );

 		# And call again to test flag skips cache.
 		$instance->http( true );
 		$this->assertEquals( ++$count, did_action( 'http_api_debug' ) );

 		# And call again (one more time) to confirm pulled from cache.
 		$instance->http();
 		$this->assertEquals( $count, did_action( 'http_api_debug' ) );
 	}

	/**
	 * Data provider for Image_Tag_Test_Base::test_lazyload().
	 *
	 * @see static::test_lazyload()
	 * @return array[]
	 */
	function data_lazyload() {
		$data = array();

		$data['src'] = array();
		$data['src'][0] = $this->get_instance( array(
			'src' => 'https://source.unsplash.com/1000x1000',
		), array(
			'lazyload' => array( 'noscript' => false ),
		) );
		$data['src'][1] = $this->new_instance( array(
			'src' => Image_Tag::BLANK,
			'class' => array( 'lazyload', 'hide-if-no-js' ),
			'sizes' => array(),
			'data-src' => 'https://source.unsplash.com/1000x1000',
			'data-sizes' => array( 'auto' ),
		), array(
			'lazyload' => array(
				'noscript' => false,
				'noscript_priority' => -10,
				'sizes_auto' => true,
			),
		) );

		$data['srcset'] = array();
		$data['srcset'][0] = $this->get_instance( array(
			'src' => 'https://source.unsplash.com/300x300',
			'srcset' => array(
				'https://source.unsplash.com/300x300 300w',
				'https://source.unsplash.com/600x600 600w',
				'https://source.unsplash.com/1000x1000 1000w',
			),
		), array(
			'lazyload' => array( 'noscript' => false ),
		) );
		$data['srcset'][1] = $this->new_instance( array(
			'src' => Image_Tag::BLANK,
			'class' => array( 'lazyload', 'hide-if-no-js' ),
			'sizes' => array(),
			'data-src' => 'https://source.unsplash.com/300x300',
			'data-sizes' => array( 'auto' ),
			'data-srcset' => array(
				'https://source.unsplash.com/300x300 300w',
				'https://source.unsplash.com/600x600 600w',
				'https://source.unsplash.com/1000x1000 1000w',
			),
		), array(
			'lazyload' => array(
				'noscript' => false,
				'noscript_priority' => -10,
				'sizes_auto' => true,
			),
		) );

		$data['noscript'] = array();
		$data['noscript'][0] = $this->get_instance( array( 'src' => 'https://source.unsplash.com/1000x1000' ) );
		$data['noscript'][1] = $this->new_instance( array(
			'src' => Image_Tag::BLANK,
			'class' => array( 'lazyload', 'hide-if-no-js' ),
			'sizes' => array(),
			'data-src' => 'https://source.unsplash.com/1000x1000',
			'data-sizes' => array( 'auto' ),
		), array(
			'after_output' => array(
				-10 => $this->get_instance()->noscript( array( 'loading' => 'lazy' ) )->__toString(),
			),
			'lazyload' => array(
				'noscript' => true,
				'noscript_priority' => -10,
				'sizes_auto' => true,
			),
		) );

		return $data;
	}

	/**
	 * Data provider for Image_Tag_Test::test_into().
	 *
	 * @see static::test_into()
	 * @doesNotPerformAssertions
	 */
	function data_into() {}

	/**
	 * @covers ::into()
	 * @group instance
	 * @group into
	 *
	 * @dataProvider data_into()
	 * @doesNotPerformAssertions
	 */
	function test_into( Image_Tag_Abstract $instance, string $into_type, $into_params, Image_Tag_Abstract $expected ) {}

}

?>