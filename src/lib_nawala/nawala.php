<?php
/**
 * @package          Nawala Rapid Development Kit
 * @subPackage	Nawala - Library
 * @author           devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright        Copyright (C) 1997 - 2013 devXive - research and development. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense    devXive Proprietary Use License (http://www.devxive.com/license)
 */

// Restrict access
defined('_JEXEC') or die;

// Define Nawala Rapid Development Kit Restricted Access (NRDKRA) as entrypoint and further check to ensure this file is included in Nawala!RDK environment
define('_NRDKRA', 1);

if (!defined('NAWALA_VERSION')) {
	// Register the library.
	JLoader::registerPrefix('N', JPATH_LIBRARIES . '/nawala');

	/**
	 * @name NAWALA_VERSION
	 */
	$nawala_updates = NCoreUpdates::getInstance();
	$nawalaCurrent = $nawala_updates->getCurrentVersion();

	define('NAWALA_VERSION', $nawalaCurrent);

	if (!defined('DS')) {
		define('DS', DIRECTORY_SEPARATOR);
	}

	define( 'NAWALA_LIBRARY', JPATH_LIBRARIES . '/nawala' );

	// Init the factory if necessary.
	if (!class_exists('NFactory'))
	{
		require_once( NAWALA_LIBRARY . '/factory.php' );
	}
}