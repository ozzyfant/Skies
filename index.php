<?php
/**
 * @author    Janek Ostendorf (ozzy) <ozzy2345de@gmail.com>
 * @copyright Copyright (c) Janek Ostendorf
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Root directory
define('ROOT_DIR', dirname(__FILE__).'/');

// Start Skies up!
require ROOT_DIR.'lib/system/Skies.class.php';

new \Skies(false);
