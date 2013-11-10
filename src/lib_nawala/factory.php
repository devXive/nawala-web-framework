<?php
/**
 * @package          Nawala Rapid Development Kit
 * @subPackage	Nawala - Library
 * @author           devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright        Copyright (C) 1997 - 2013 devXive - research and development. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense    devXive Proprietary Use License (http://www.devxive.com/license)
 */

// Check to ensure this file is included in Nawala!RDK environment
defined('_NRDKRA') or die;

/**
 * Nawala Framework Factory class
 *
 * @package  Framework
 * @since    1.0
 */
abstract class NFWFactory
{
//	include( JPATH_SITE . '/libraries/gantry/gantry.php' );

	/**
	 * Adds a script file to the document with platform based checks
	 *
	 * @param  $file
	 *
	 * @return void
	 */
	function nawala_addScript($file)
	{
		gantry_import('core.gantryplatform');
		$platform      = new GantryPlatform();
		$document      = JFactory::getDocument();
		$filename      = basename($file);
		$relative_path = dirname($file);

		// For local url path get the local path based on checks
		$file_path       = gantry_getFilePath($file);
		$url_file_checks = $platform->getJSChecks($file_path, true);
		foreach ($url_file_checks as $url_file) {
			$full_path = realpath($url_file);
			if ($full_path !== false && file_exists($full_path)) {
				$document->addScript($relative_path . '/' . basename($full_path) . '?ver=' . NAWALA_VERSION);
				break;
			}
		}
	}

	/**
	 * Add inline script to the document
	 *
	 * @param  $script
	 *
	 * @return void
	 */
	function nawala_addInlineScript($script)
	{
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);
	}

	/**
	 * Add a css style file to the document with browser based checks
	 *
	 * @param  $file
	 *
	 * @return void
	 */
	function nawala_addStyle($file)
	{
		gantry_import('core.gantrybrowser');
		$browser       = new GantryBrowser();
		$document      = JFactory::getDocument();
		$filename      = basename($file);
		$relative_path = dirname($file);

		// For local url path get the local path based on checks
		$file_path       = gantry_getFilePath($file);
		$url_file_checks = $browser->getChecks($file_path, true);
		foreach ($url_file_checks as $url_file) {
			$full_path = realpath($url_file);
			if ($full_path !== false && file_exists($full_path)) {
				$document->addStyleSheet($relative_path . '/' . basename($full_path) . '?ver=' . NAWALA_VERSION);
			}
		}
	}

	/**
	 * Add inline css to the document
	 *
	 * @param  $css
	 *
	 * @return void
	 */
	function nawala_addInlineStyle($css)
	{
		$document = JFactory::getDocument();
		$document->addStyleDeclaration($css);
	}
}