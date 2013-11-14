<?php
/**
 * Prepares a minimalist framework for unit testing.
 *
 * Nawala!RDK is assumed to include the /unittest/ directory.
 * eg, /path/to/mfw/unittest/
 *
 * @package     Nawala!RDK.UnitTest
 *
 * @copyright   Copyright (C) 1997 - 2013 devXive - reseach and development. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @link        http://www.phpunit.de/manual/current/en/installation.html
 */

// set for no direct access functionality in applications (NRDK Restricted Access)
define('_NRDKRA', 1);

// Fix magic quotes.
ini_set('magic_quotes_runtime', 0);

// Maximise error reporting.
ini_set('zend.ze1_compatibility_mode', '0');
error_reporting(E_ALL & ~E_STRICT);
ini_set('display_errors', 1);

/*
 * Ensure that required path constants are defined.  These can be overridden within the phpunit.xml file
 * if you chose to create a custom version of that file.
 */
if (!defined('NRDKPATH_TESTS'))
{
	define('NRDKPATH_TESTS', realpath(__DIR__));
}
if (!defined('NRDKPATH_BASE'))
{
	define('NRDKPATH_BASE', realpath(dirname(dirname(__DIR__))));
}
if (!defined('NRDKPATH_ROOT'))
{
	define('NRDKPATH_ROOT', realpath(NRDKPATH_BASE));
}
if (!defined('NRDKPATH_LIBRARIES'))
{
	define('NRDKPATH_LIBRARIES', NRDKPATH_TESTS . '/libraries');
}

// Import the entrypoint
require_once NRDKPATH_LIBRARIES . '/lib_nawala/nawala.php';