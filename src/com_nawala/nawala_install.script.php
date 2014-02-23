<?php
/**
 * @package          Nawala Rapid Development Kit
 * @subPackage       Nawala - Component
 * @author           devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright        Copyright (C) 1997 - 2013 devXive - research and development. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense    devXive Proprietary Use License (http://www.devxive.com/license)
 */

// Check to ensure this file is included in Nawala!RDK environment
defined('_NRDKRA') or die();

class Com_NawalaInstallerScript
{
	/**
	 * @param $type
	 * @param $parent
	 */
	public function postflight($type, $parent)
	{
		$cache = JFactory::getCache();
		$cache->clean('nrdk');
		$cache->clean('Nawala');
		$cache->clean('NawalaAdmin');

		// Remove the admin menu
		$installer = new NInstaller();
		$installer->removeAdminMenus('com_nawala');
	}
}