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
defined('_NRDKRA') or die;

/**
 * Nawala Framework Factory Class
 *
 * @package  Framework
 * @since    1.0
 * 
 * @deprecated in 2.0
 */
abstract class NFactory
{
	/**
	 * Global configuraiton object
	 *
	 * @var    NCoreConfig
	 * @since  1.0
	 */
	public static $config = null;


	/**
	 * Global application object
	 *
	 * @var    NApplication
	 * @since  1.0
	 */
	public static $application = null;


	/**
	 * Global document object
	 *
	 * @var    NDocument
	 * @since  1.0
	 */
	public static $document = null;


	/**
	 * Global cache object
	 *
	 * @var    NCache
	 * @since  1.0
	 */
	public static $cache = null;


	/**
	 * Global ajax Object
	 * @var    NAjax
	 * @since  1.1
	 */
	public static $ajax;


	/**
	 * Get a application object.
	 *
	 * Returns the global {@link NApplication} object, only creating it if it doesn't already exist.
	 *
	 * @param   mixed   $id      A client identifier or name.
	 * @param   array   $config  An optional associative array of configuration settings.
	 * @param   string  $prefix  Application prefix
	 *
	 * @return  NApplication object
	 *
	 * @see     NApplication
	 * @since   1.0
	 * @throws  Exception
	 */
	public static function getApplication($id = null, array $config = array(), $prefix = 'N')
	{
		if (!self::$application)
		{
			if (!$id && !$prefix)
			{
				throw new Exception('Application Instantiation Error', 500);
			}

			self::$application = new NApplication($id);
		}

		return self::$application;
	}


	/**
	 * Get a document object.
	 *
	 * Returns the global {@link NDocument} object, only creating it if it doesn't already exist.
	 *
	 * @return  NDocument object
	 *
	 * @see     NDocument
	 * @since   1.1
	 */
	public static function getDocument()
	{
		if (!self::$document)
		{
//			self::$document = new NDocument();
		}

		return self::$document;
	}


	/**
	 * Get a session object.
	 *
	 * Returns either the global {@link NSession} object if name is false or
	 * Return one of the predefined subsession scopes app|doc|tmp (located in namespace __nawala->SCOPE) or
	 * If using a non predefined name, the appropriate scope is created!
	 * 
	 * Usage: Using scopes as using the standard JObject. Simply ->get() and ->set() vars as you want.
	 * Take care of using objects in the session to prevent: __PHP_Incomplete_Class Object
	 * 
	 * Please note that NObject extends the JObject class
	 *
	 * @param   string  $name       Name of session item
	 * @param   string  $namespace  Optional namespace to store in the session item. Useful to avoid conflicts for multiple extensions
	 * 
	 * @return  NSession object
	 *
	 * @see     NSession
	 * @since   1.0
	 */
	public static function getSession($name = false, $namespace = false)
	{
		$sessionObject = '';
		$empty = new JObject();
		$session = JFactory::getSession();

		if ( !$name ) {
			return $session;
		}

		// Determine namespace
		if ( !$namespace ) {
			$config = self::getConfig();
			$namespace = $config->getNamespace();
		}

		// Create a sessionKey based on name/namepace string
		$sessionKey = $name . '_' . $namespace;

		// Check if we have to get an existing session scope.
		if ( $session->has($name, $namespace) ) {
			$sessionObject = $session->get($name, null, $namespace);
		} else {
			$session->set($name, $empty, $namespace);
			$sessionObject = $session->get($name, null, $namespace);
		}

		return $sessionObject;
	}


	/**
	 * Get a document object.
	 *
	 * Returns the global {@link NDocument} object, only creating it if it doesn't already exist.
	 *
	 * @return  NDocument object
	 *
	 * @see     NDocument
	 * @since   1.1
	 */
	public static function getCache()
	{
		if (!self::$cache)
		{
			self::$cache = new NCache();
		}

		return self::$cache;
	}


	/**
	 * Get a class object.
	 *
	 * Returns an appropriate class object.
	 *
	 * @param   string  $classString  A dot notated string of the class name which should be load
	 * @param   string  $prefix       The global class name prefix
	 *
	 * @return  Appropriate class object
	 *
	 * @see     Appropriate class
	 * @since   1.0
	 */
	public static function getClass( $classString, $prefix = 'N' )
	{
		$parts = explode('.', $classString);

		// Build the className and varName
		$className = $prefix;
		foreach ( $parts as $part ) {
			$className .= ucfirst($part);
		}
		
		$instance = new $className;

		return $instance;
	}


	/**
	 * Get a ajax object.
	 *
	 * Returns the global {@link NAjax} object, only creating it if it doesn't already exist.
	 *
	 * @return  NAjax object
	 *
	 * @see     NAjax
	 * @since   1.1
	 */
	public static function getAjax()
	{
		if (!self::$ajax)
		{
			self::$ajax = new NAjax();
		}

		return self::$ajax;
	}
}