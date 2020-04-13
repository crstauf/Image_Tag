<?php

/**
 * @group features
 */
class Image_Tag_Features_Test extends WP_UnitTestCase {

	function test_noscript() {
		$img = Image_Tag::create( 'picsum', array( 'width' => 200 ) );

		$nojs = $img->noscript();
		$nojs_string = $nojs->__toString();

		$this->assertEquals( $img->get_attribute( 'src' ), $nojs->get_attribute( 'src' ) );

		$this->assertEquals(    '<noscript>', $nojs->get_setting( 'before_output' ) );
		$this->assertEquals(   '</noscript>', $nojs->get_setting( 'after_output' ) );
		$this->assertContains(  '<noscript>', $nojs_string );
		$this->assertContains( '</noscript>', $nojs_string );

		$this->assertContains( 'no-js', $nojs->get_attribute( 'class' ) );
		$this->assertContains( 'no-js', $nojs_string );
	}

}
