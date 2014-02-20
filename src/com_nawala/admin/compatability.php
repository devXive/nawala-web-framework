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

if (!class_exists('GantryLegacyJView', false)) {
	$jversion = new JVersion();
	if (version_compare($jversion->getShortVersion(), '2.5.5', '>'))
	{
		class GantryLegacyJView extends JViewLegacy
		{
		}

		class GantryLegacyJController extends JControllerLegacy
		{
		}

		class GantryLegacyJModel extends JModelLegacy
		{
		}
	}
	else
	{
		jimport('joomla.application.component.view');
		jimport('joomla.application.component.controller');
		jimport('joomla.application.component.model');
		class GantryLegacyJView extends JView
		{
		}

		class GantryLegacyJController extends JController
		{
		}

		class GantryLegacyJModel extends JModel
		{
		}
	}
}

if (method_exists('JSession','checkToken')) {
	function gantry_checktoken($method = 'post')
	{
		if ($method == 'default')
		{
			$method = 'request';
		}
		return JSession::checkToken($method);
	}
} else {
	function gantry_checktoken($method = 'post')
	{
		return JRequest::checkToken($method);
	}
}

 
