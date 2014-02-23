<?php
/**
 * @package          Nawala Rapid Development Kit
 * @subPackage       Nawala - Library
 * @author           devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright        Copyright (C) 1997 - 2014 devXive - research and development. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense    devXive Proprietary Use License (http://www.devxive.com/license)
 */

// Check to ensure this file is included in Nawala!RDK environment
defined('_NRDKRA') or die();

/**
 * Nawala Framework Autoloader Class
 * The main class autoloader for N itself
 *
 * @package       Framework
 * @subpackage    Autoloader
 * 
 * @since         1.0
 */
class NAutoloaderRdk
{
	/**
	 * An instance of this autoloader
	 *
	 * @var   NAutoloaderRdk
	 */
	public static $autoloader = null;

	/**
	 * The path to the NRDK root directory
	 *
	 * @var   string
	 */
	public static $nrdkPath = null;

	/**
	 * Initialise this autoloader
	 *
	 * @return  NAutoloaderRdk
	 */
	public static function init()
	{
		if (self::$autoloader == null)
		{
			self::$autoloader = new self;
		}

		return self::$autoloader;
	}

	/**
	 * Public constructor. Registers the autoloader with PHP.
	 */
	public function __construct()
	{
		self::$nrdkPath = realpath(__DIR__ . '/../');

		spl_autoload_register(array($this,'autoload_nrdk_core'));
	}

	/**
	 * The actual autoloader
	 *
	 * @param   string  $class_name  The name of the class to load
	 *
	 * @return  void
	 */
	public function autoload_nrdk_core($class_name)
	{
		// Make sure the class has a N prefix
		if (substr($class_name, 0, 1) != 'N')
		{
			return;
		}

		// Remove the prefix
		$class = substr($class_name, 1);

		// Change from camel cased (e.g. ViewHtml) into a lowercase array (e.g. 'view','html')
		$class = preg_replace('/(\s)+/', '_', $class);
		$class = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $class));
		$class = explode('_', $class);

		/**
		 * First try finding in structured directory format (preferred)
		 */
		$path = self::$nrdkPath . '/' . implode('/', $class) . '.php';

		if (@file_exists($path))
		{
			include_once $path;
		}

		/**
		 * Then try the duplicate last name structured directory format (not recommended)
		 * 
		 * @since 1.1
		 */
		if (!class_exists($class_name, false))
		{
			reset($class);
			$lastPart = end($class);
			$path = self::$nrdkPath . '/' . implode('/', $class) . '/' . $lastPart . '.php';

			if (@file_exists($path))
			{
				include_once $path;
			}
		}

		/**
		 * If it still fails, try looking in the legacy folder (used for backwards compatibility)
		 * 
		 * @since 1.2
		 */
		if (!class_exists($class_name, false))
		{
			$path = self::$nrdkPath . '/legacy/' . implode('/', $class) . '.php';

			if (@file_exists($path))
			{
				include_once $path;
			}
		}
	}
}