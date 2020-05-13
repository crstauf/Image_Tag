<?php

/**
 * @todo force use of add_output() helper: add(), set(), add_to()
 */
class Image_Tag_Settings extends Image_Tag_Properties {

	const NAME = 'setting';
	const DEFAULTS = array(
		'before_output' => array(),
		'after_output' => array(),
	);

	/**
	 * Getter.
	 *
	 * @param string $property
	 * @uses Image_Tag_Properties::__get()
	 * @return mixed
	 */
	function __get( string $setting ) {
		if ( 'settings' === $setting )
			return $this->properties;

		return parent::__get( $setting );
	}

}

?>