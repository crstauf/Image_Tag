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

}

?>