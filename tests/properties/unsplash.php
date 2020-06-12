<?php

/**
 * @coversDefaultClass Image_Tag_Unsplash_Attributes
 *
 * @todo add tests
 */
class Image_Tag_Unsplash_Attributes_Test extends Image_Tag_Attributes_Test {

	protected function class_name() {
		return Image_Tag_Unsplash_Attributes::class;
	}

	/**
	 * @group constant
	 */
	function test_constant_base_url() {
		$this->assertSame( 'https://source.unsplash.com/', constant( $this->class_name() . '::BASE_URL' ) );
	}

	/**
	 * Test Image_Tag_Attributes::__toString().
	 *
	 * @covers ::__toString()
	 * @group instance
	 * @group magic
	 * @group output
	 */
	function test__toString() {
		$attributes = array(
			'class' => array( 'foo', 'bar' ),
			'id' => __FUNCTION__,
			'sizes' => '( max-width: 800px ) 50vw, 100vw',
			'data-preloaded' => 1, // test attribute not in DEFAULTS
		);

		$defaults = array(
			'width' => 1600,
			'height' => 900,
		);

		$instance = $this->new_instance( $attributes, $defaults );

		$expected = 'id="' . esc_attr( __FUNCTION__ ) . '" ' .
			'src="https://source.unsplash.com/" ' .
			'sizes="( max-width: 800px ) 50vw, 100vw" ' .
			'class="foo bar" ' .
			'width="1600" ' .
			'height="900" ' .
			'alt="" ' .
			'data-preloaded="1"';

		$this->assertSame( $expected, $instance->__toString() );
	}

	/**
	 * Data provider for Image_Tag_Attributes_Test::test_get().
	 *
	 * Add attribute specific tests.
	 *
	 * @see Image_Tag_Properties_Test::test_get()
	 * @uses Image_Tag_Attributes_Test::data_get()
	 * @return array[]
	 */
	function data_get() {
		$data = parent::data_get();

		$data['null view'][2] = array(
			'id' => __FUNCTION__,
			'alt' => '',
			'src' => 'https://source.unsplash.com/',
			'sizes' => '100vw',
			'class' => 'foo bar',
		);

		$image = Image_Tag::create( 'unsplash', array(), array(
			'width' => 400,
			'height' => 300,
		) );
		$data['src view'] = array(
			$image->attributes,
			'src',
			'https://source.unsplash.com/400x300/',
			'view',
		);

		$image = Image_Tag::create( 'unsplash', array(), array(
			'image_id' => 'WLUHO9A_xik',
			'width' => 400,
			'height' => 300,
		) );
		$data['src image_id'] = array(
			$image->attributes,
			'src',
			'https://source.unsplash.com/WLUHO9A_xik/400x300/',
			'view',
		);

		$image = Image_Tag::create( 'unsplash', array(), array(
			'user' => 'erondu',
			'width' => 400,
			'height' => 300,
		) );
		$data['src user'] = array(
			$image->attributes,
			'src',
			'https://source.unsplash.com/user/erondu/400x300/',
			'view',
		);

		$image = Image_Tag::create( 'unsplash', array(), array(
			'user_likes' => 'jackie',
			'width' => 400,
			'height' => 300,
		) );
		$data['src user_likes'] = array(
			$image->attributes,
			'src',
			'https://source.unsplash.com/user/jackie/likes/400x300/',
			'view',
		);

		$image = Image_Tag::create( 'unsplash', array(), array(
			'collection' => '190727',
			'width' => 400,
			'height' => 300,
		) );
		$data['src collection'] = array(
			$image->attributes,
			'src',
			'https://source.unsplash.com/collection/190727/400x300/',
			'view',
		);

		$image = Image_Tag::create( 'unsplash', array(), array(
			'user' => 'erondu',
			'update' => 'daily',
			'width' => 400,
			'height' => 300,
		) );
		$data['src user daily'] = array(
			$image->attributes,
			'src',
			'https://source.unsplash.com/user/erondu/400x300/daily/',
			'view',
		);

		$image = Image_Tag::create( 'unsplash', array(), array(
			'user' => 'erondu',
			'featured' => 'true',
			'width' => 400,
			'height' => 300,
		) );
		$data['src featured'] = array(
			$image->attributes,
			'src',
			'https://source.unsplash.com/user/erondu/featured/400x300/',
			'view',
		);

		$image = Image_Tag::create( 'unsplash', array(), array(
			'search' => array( 'test', 'example' ),
			'width' => 400,
			'height' => 300,
		) );
		$data['src search'] = array(
			$image->attributes,
			'src',
			'https://source.unsplash.com/400x300/?test,example',
			'view',
		);

		return $data;
	}

	/**
	 * @param Image_Tag_Properties_Abstract $instance
	 * @param string|array $get_properties
	 * @param mixed $expected
	 * @param string $context
	 * @see static::test_get()
	 *
	 * @covers ::get()
	 * @covers ::trim()
	 * @covers ::get_properties()
	 * @covers ::get_property()
	 * @covers ::get_src_attribute_for_view()
	 * @covers Image_Tag_Properties_Abstract::_get()
	 * @covers ::get_src_attribute_for_view()
	 * @group instance
	 * @group get
	 *
	 * @dataProvider data_get
	 */
	function test_get( Image_Tag_Properties_Abstract $instance, $get_properties, $expected, $context = 'edit' ) {
		parent::test_get( $instance, $get_properties, $expected, $context );
	}

}

/**
 * @coversDefaultClass Image_Tag_Unsplash_Settings
 *
 * @todo add tests
 */
class Image_Tag_Unsplash_Settings_Test extends Image_Tag_Settings_Test {

	const DEFAULTS = array(
		'before_output' => array(),
		'after_output' => array(),
		'image_id' => null,
		'user' => null,
		'user_likes' => null,
		'collection' => null,
		'update' => null,
		'featured' => false,
		'width' => null,
		'height' => null,
		'search' => array(),
	);

	protected function class_name() {
		return Image_Tag_Unsplash_Settings::class;
	}

}

?>