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
 * Nawala Framework Access Class
 *
 * This class allows for ???? TODO: Description
 *
 * @package       Framework
 * @subpackage    Access
 * @since         1.0
 */
class NAccess
{
	/**
	 * Method to get note of 2FA methods
	 */
	public static function getTwoFactorMethods()
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php';
		return UsersHelper::getTwoFactorMethods();
	}
}