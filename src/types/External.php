<?php declare(strict_types=1);

namespace Image_Tag;

class External implements Interfaces\Image_Tag {

	use Traits\Attributes_Helper,
		Traits\Lazysizes,
		Traits\Output,
		Traits\Settings_Helper;

	/** @var string */
	protected string $url;

	/** @var Stores\Attributes */
	public Stores\Attributes $attributes;

	/** @var Stores\Settings */
	public Stores\Settings $settings;

	/**
	 * Construct.
	 */
	public function __construct( string $url ) {
		$this->url        = $url;
		$this->attributes = new Stores\Attributes();
		$this->settings   = new Stores\Settings();

		$this->attributes->update( 'src', $url );
	}

}
