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
 * Nawala Framework Session Class
 *
 * This class allows for ???? TODO: Description
 *
 * @package       Framework
 * @subpackage    Core
 * @since         1.0
 */
class NSession
{
	/**
	 * @var
	 */
	static $instance;


	/**
	 * Check and create session object if not exist
	 */
	public function __construct()
	{
		$session = JFactory::getSession();

		$nawalaSessionObject = new JObject;

		if ( !$session->get('nawala') ) {
			$session->set('nawala', $nawalaSessionObject);
		}
	}


	/**
	 * Get data from session object
	 */
	static function get( $data = false )
	{
		$session = JFactory::getSession();
		$nawalaSessionObject = $session->get('nawala');

		if ( $data ) {
			return $nawalaSessionObject->get((string)$data, null);
		} else {
			return $nawalaSessionObject;
		}
	}


	/**
	 * Update session object
	 */
	public function set( $string = false, $value = false )
	{
		$session = JFactory::getSession();
		$nawalaSessionObject = $session->get('nawala');

		if ( $string && $value ) {
			$nawalaSessionObject->set($string, $value);
		}
	}


	/**
	 * Simple function to check if the nawala session object exist or not
	 */
	public function exist()
	{
		$session = JFactory::getSession();

		if ( $session->get('nawala') ) {
			return true;
		} else {
			return false;
		}
	}
}