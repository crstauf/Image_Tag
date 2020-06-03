<?php

require_once 'abstract.php';

/**
 * @coversDefaultClass Image_Tag_Picsum
 */
class Image_Tag_Picsum_Test extends Image_Tag_Test_Base {

	protected function class_name() {
		return Image_Tag_Picsum::class;
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
				array(
					'picsum',
					'picsum.photos',
					'placeholder',
					'external',
					'remote',
				),
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
	 * @covers Imagee_Tag::create()
	 */
	function data_create() {
		return array(
			'picsum' => array(
				$this->new_instance(),
				'picsum',
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
	 * Data provider for Image_Tag_Picsum_Test::test__toString().
	 *
	 * @see static::test__toString()
	 * @return array[]
	 */
	function data__toString() {
		return array(
			'warning' => array(
				$this->new_instance(),
				'warning',
			),
			'image id' => array(
				$this->new_instance( array(), array(
					'image_id' => 13,
					'width' => 300,
				) ),
				'<img src="https://picsum.photos/id/13/300" sizes="100vw" alt="" />',
			),
			'width' => array(
				$this->new_instance( array(), array( 'width' => 400 ) ),
				'<img src="https://picsum.photos/400" sizes="100vw" alt="" />',
			),
			'height' => array(
				$this->new_instance( array(), array( 'height' => 300 ) ),
				'<img src="https://picsum.photos/300" sizes="100vw" alt="" />',
			),
			'width and height' => array(
				$this->new_instance( array(), array( 'width' => 400, 'height' => 300 ) ),
				'<img src="https://picsum.photos/400/300" sizes="100vw" alt="" />',
			),
			'seed' => array(
				$this->new_instance( array(), array( 'width' => 400, 'seed' => __FUNCTION__ ) ),
				'<img src="https://picsum.photos/seed/' . urlencode( __FUNCTION__ ) . '/400" sizes="100vw" alt="" />',
			),
			'grayscale' => array(
				$this->new_instance( array(), array( 'width' => 400, 'grayscale' => true ) ),
				'<img src="https://picsum.photos/400?grayscale=1" sizes="100vw" alt="" />',
			),
			'blur' => array(
				$this->new_instance( array(), array( 'width' => 400, 'blur' => 2 ) ),
				'<img src="https://picsum.photos/400?blur=2" sizes="100vw" alt="" />',
			),
			'random' => array(
				$this->new_instance( array(), array( 'width' => 400, 'random' => true ) ),
				'<img src="https://picsum.photos/400?random=2" sizes="100vw" alt="" />',
			),
			'second random' => array(
				$this->new_instance( array(), array( 'width' => 400, 'random' => true ) ),
				'<img src="https://picsum.photos/400?random=4" sizes="100vw" alt="" />',
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
	 *
	 * @group instance
	 * @group validation
	 */
	function test_get_type() {
		$this->assertSame( 'picsum', $this->new_instance()->get_type() );
	}

	/**
	 * @covers ::is_type()
	 *
	 * @group instance
	 * @group validation
	 */
	function test_is_type() {
		$instance = $this->new_instance();

		$this->assertTrue( $instance->is_type( 'picsum' ) );
		$this->assertTrue( $instance->is_type( 'external' ) );
		$this->assertTrue( $instance->is_type( 'remote' ) );

		$this->assertFalse( $instance->is_type( 'local' ) );
		$this->assertFalse( $instance->is_type( 'internal' ) );
	}

	/**
	 * @covers ::is_valid()
	 * @covers ::check_valid()
	 * @covers Image_Tag_Abstract::is_valid()
	 * @covers Image_Tag_Abstract::check_valid()
	 *
	 * @group instance
	 * @group validation
	 */
	function test_is_valid() {
		$instance = $this->new_instance();
		$this->assertFalse( $instance->is_valid() );

		$instance = $this->new_instance( array(), array(
			'width' => 400,
		) );
		$this->assertTrue( $instance->is_valid() );

		$instance = $this->new_instance( array(), array(
			'height' => 300,
		) );
		$this->assertTrue( $instance->is_valid() );

		$this->assertTrue( $instance->is_valid( 'picsum' ) );
		$this->assertFalse( $instance->is_valid( 'local' ) );
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
	 * @covers ::http()
	 * @group instance
	 * @group external-http
	 */
	function test_http() {
		$instance = $this->new_instance( array(), array( 'width' => 400 ) );
		$response = $instance->http();
		$image_id = ( int ) wp_remote_retrieve_header( $response, 'picsum-id' );

		$this->assertFalse( is_wp_error( $response ) );
		$this->assertContains( 'picsum-id', array_keys( wp_remote_retrieve_headers( $response )->getAll() ) );
		$this->assertNotEmpty( $image_id );
		$this->assertSame( $image_id, $instance->settings->get( 'image_id' ) );
	}

	/**
	 * @covers ::http()
	 * @covers ::details()
	 * @group instance
	 * @group external-http
	 */
	function test_details() {
		$instance = $this->new_instance( array(), array( 'width' => 400 ) );

		$count = did_action( 'http_api_debug' );
		$details = $instance->details();
		$count += 2; // Increment twice for calls in http() and details().

		$this->assertSame( array(
			'id',
			'author',
			'width',
			'height',
			'url',
			'download_url',
		), array_keys( ( array ) $details ) );

		$this->assertSame( ( int ) $details->id, $instance->settings->get( 'image_id' ) );
		$this->assertSame( $count, did_action( 'http_api_debug' ) );

		$this->assertSame( $details, $instance->details() );
		$this->assertSame( $count, did_action( 'http_api_debug' ) ); // Test retrieved from cache.

		$this->assertEquals( $details, $instance->details( true ) );
		$count++; // Only once, because the 'image_id' setting is set.
		$this->assertSame( $count, did_action( 'http_api_debug' ) );

	}

	function data_noscript() {
		$this->markTestIncomplete();
	}

	function data_lazyload() {
		$this->markTestIncomplete();
	}

	function data_into() {
		$this->markTestIncomplete();
	}

}

?>