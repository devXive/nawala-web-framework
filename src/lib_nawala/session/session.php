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
	 * Get data from session object
	 */
	public function get($name = false)
	{
		$session = JFactory::getSession()->get('nawala');

		foreach ( $session as $key => $val ) {
			$return->$key = (object) $val;
		}

		if ( $name ) {
			return $return->$name;
		} else {
			return $return;
		}
	}


	/**
	 * Update session object
	 */
	public function update($name = false)
	{
		$session = JFactory::getSession()->get('nawala');

		foreach ( $session as $key => $val ) {
			$return->$key = (object) $val;
		}

		if ( $name ) {
			return $return->$name;
		} else {
			return $return;
		}
	}
}