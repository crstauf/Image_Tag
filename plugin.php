<?php declare(strict_types=1);
/**
 * Image tag generator.
 *
 * Plugin name: Image Tag Generator
 * Plugin URI: https://github.com/crstauf/image_tag
 * Description: WordPress drop-in to generate <code>img</code> tags.
 * Author: Caleb Stauffer
 * Author URI: https://develop.calebstauffer.com
 * Version: 3.0
 * Requires at least: 5.1
 * Requires PHP: 8.3
 *
 * @todo add CLI command: clear common colors from attachment meta data
 * @todo add CLI command: clear LQIPs from attachment meta data
 */

require_once 'src/Manager.php';

Image_Tag\Manager::init();
