<?php declare(strict_types=1);

namespace Image_Tag\Interfaces;

use Image_Tag\Stores\Attributes as Attributes_Store;
use Image_Tag\Stores\Settings as Settings_Store;

interface Image_Tag {

	public function print() : void;
	public function output() : string;
	public function is_valid() : bool;

}
