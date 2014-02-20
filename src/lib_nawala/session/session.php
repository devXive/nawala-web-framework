<?php
/**
 * @package          Nawala Rapid Development Kit
 * @subPackage       Nawala - Library
 * @author           devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright        Copyright (C) 1997 - 2013 devXive - research and development. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense    devXive Proprietary Use License (http://www.devxive.com/license)
 */

// Check to ensure this file is included in Nawala!RDK environment
defined('_NRDKRA') or die;

/**
 * Nawala Framework Session Class
 *
 * @package       Framework
 * @subpackage    Session
 * @since         1.0
 * 
 * @deprecated    1.1
 */
class NSession
{
	/**
	 * @var
	 */
	private $instance;

	/**
	 * @var    object    $object    Empty object with getters and setters and error
	 */
	private $appObject;

	/**
	 * @var    object    $object    Empty object with getters and setters and error
	 */
	private $docObject;

	/**
	 * @var    object    $object    Empty object with getters and setters and error
	 */
	private $tmpObject;

	/**
	 * @var
	 */
	protected $namespace;

	/**
	 * Global session object
	 *
	 * @var    NFactory::getSession
	 * @since  1.0
	 */
	private $session = null;


	/**
	 * Check and create session object if not exist
	 */
	public function __construct()
	{
			// Get the global namespace
		$this->namespace = NFactory::getConfig()->getPlatform()->name;

		if ( !$this->appObject ) {
			$this->appObject = new JObject();
		}

		if ( !$this->docObject ) {
			$this->docObject = new JObject();
		}

		if ( !$this->tmpObject ) {
			$this->tmpObject = new JObject();
		}

		// Initialize and prepare the structure
		$this->init();
	}


	/**
	 * Initialize session (used in construct)
	 */
	private function init() {
		if (!$this->session)
		{
			$this->session = JFactory::getSession();
		}

		/*
		 * Nawala Application Scope
		 */
		if ( !$this->session->has('app', $this->namespace) )
		{
			$app = $this->appObject;
		
			$this->session->set('app', $app, $this->namespace);
		}

		/*
		 * Nawala Document Scope
		 */
		if ( !$this->session->has('doc', $this->namespace) )
		{
			$doc = $this->docObject;
		
			$this->session->set('doc', $doc, $this->namespace);
		}

		/*
		 * Nawala Temp Scope
		 */
		if ( !$this->session->has('tmp', $this->namespace) )
		{
			$tmp = $this->tmpObject;
		
			$this->session->set('tmp', $tmp, $this->namespace);
		}
	}


	/**
	 * Get data from the session store
	 *
	 * @param   string  $name       Name of a variable
	 * @param   mixed   $default    Default value of a variable if not set
	 * @param   string  $namespace  Namespace to use, default to 'default'
	 *
	 * @return  mixed  Value of a variable
	 *
	 * @since   1.1
	 */
	public function get($name, $default = null, $namespace = false)
	{
		// Use sessionNamespace if namespace is not set
		if ( !$namespace ) {
			$namespace = $this->sessionNamespace;
		}

		return $this->session->get($name, $default, $namespace);
	}


	/**
	 * Set data into the session store.
	 *
	 * @param   string  $name       Name of a variable.
	 * @param   mixed   $value      Value of a variable.
	 * @param   string  $namespace  Namespace to use, default to 'default'.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function set($name, $value = null, $namespace = false)
	{
		// Use sessionNamespace if namespace is not set
		if( !$namespace ) {
			$namespace = $this->sessionNamespace;
		}

		$this->session->set($name, $value, $namespace);
	}


	/**
	 * Simple function to check if the given session item exist or not
	 * 
	 * @param   string  $name       Name of session item
	 * @param   string  $namespace  Optional namespace to store in the session item. Useful to avoid conflicts for multiple extensions
	 * 
	 * @return boolean
	 */
	public function exist($name, $namespace = false)
	{
		// Use sessionNamespace if namespace is not set
		if( !$namespace ) {
			$namespace = $this->sessionNamespace;
		}

		if ( $this->session->has($name, $namespace) ) {
			return true;
		} else {
			return false;
		}
	}
}