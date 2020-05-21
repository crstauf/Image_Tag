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
				'source.unsplash.com',
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
	 * @covers ::http()
	 * @group instance
	 * @group external-http
	 */
	function test_http() {
		$instance = $this->new_instance( array( 'src' => 'https://source.unsplash.com/WLUHO9A_xik/1x1' ) );

		$count = ( int ) did_action( 'http_api_debug' );
		$response = $instance->http();

		if ( is_wp_error( $response ) )
			$this->fail( $response->get_error_message() );

		$this->assertEquals( ++$count, did_action( 'http_api_debug' ) );
		$this->assertSame( '1054000', wp_remote_retrieve_header( $response, 'content-length' ) );
		$this->assertSame( 'image/jpeg', wp_remote_retrieve_header( $response, 'content-type' ) );
		$this->assertSame( '3b5a685596c9e862dba90ae80ec369769dd1afe4', wp_remote_retrieve_header( $response, 'x-imgix-id' ) );

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
	 * @doesNotPerformAssertions Conversion into base type is not expected.
	 */
	function data_into() {}

	/**
	 * @doesNotPerformAssertions Conversion into base type is not expected.
	 */
	function test_into( Image_Tag_Abstract $instance, $into_params, Image_Tag_Abstract $expected ) {}

}

?>