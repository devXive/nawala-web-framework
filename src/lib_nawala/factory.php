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
class NFactory
{
	/**
	 * Marked for TODO
	 * @var    array  Array containing information for loaded files
	 * @since  1.0
	 */
	protected static $loaded = array();


	/**
	 * Initializer
	 */
	static function init()
	{
		$session = JFactory::getSession();
		$sessionHelper = new NObject;

		$sessionHelper->set(
			'library', array(
				'name' => 'nawala',
				'version' => NAWALA_VERSION
			)
		);

		$sessionHelper->set(
			'template', array(
				'name' => JFactory::getApplication()->getTemplate(),
				'version' => '1.0.0',
				'stylePaths' => array(
					'templates/' . NCore::get('template')->name . '/less',
					'templates/' . NCore::get('template')->name . '/css',
					'templates/' . NCore::get('template')->name . '/css-compiled',
					'libraries/nawala/assets/less',
					'libraries/nawala/assets/css'
				),
				'css' => array(),
				'js' => array()
			)
		);

		// Set the initial session object
		$session->set('nawala', $sessionHelper);
	}


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


	/**
	 * Method to add a style file the Nawala Template Header
	 *
	 * @return void
	 */
	public function addStyle($file, $priority = '0') {
		// Get the session object
		$nawala = JFactory::getSession()->get('nawala');

		$cssArray = array();

		if ( isset($nawala->template['css'][$priority]) ) {
			$cssArray = $nawala->template['css'][$priority];
			array_push($cssArray, $file);
		} else {
			array_push($cssArray, $file);
		}

		array_unique( $cssArray );

		$nawala->template['css'][$priority] = $cssArray;

		array_unique( $nawala->template['css'] );
		asort( $nawala->template['css'] );

		return;
	}


	/**
	 * Method to get the Nawala Template Header
	 *
	 * @return html
	 */
	public function getTemplateOptions( $inBuild = true ) {
		$doc = JFactory::getDocument();

		$templateoptions = new NObject;
		$html = '';

		// Get the stylesheet files
		$cssArray = NCore::get('template')->css;

		foreach ( $cssArray as $array ) {
			foreach ( $array as $file ) {
				if ( $inBuild ) {
					$doc->addStyleSheet($file);
				} else {
					$html .= '<link rel="stylesheet" href="' . $file . '" type="text/css">' . "\n";
				}
			}
		}

		if ( $inBuild ) {
			$templateoptions->displayHead = null;
		} else {
			$templateoptions->displayHead = $html;
		}

		return $templateoptions;
	}
}