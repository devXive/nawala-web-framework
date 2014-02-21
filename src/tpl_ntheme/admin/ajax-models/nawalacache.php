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
 * Nawala AjaxModels for Gantry's Built-In Ajax System
 *
 * This model allows Nawala!RDK kit the use of gantry's
 * built-in ajax system to execute template specific ajax calls
 *
 * @package       Template
 * @subpackage    GantryAjax System
 * @since         1.0
 */

/** @var $gantry Gantry */
global $gantry;

$action = JFactory::getApplication()->input->getString('action');

switch ($action) {
	case 'clear':
		echo nawalaAjaxClearNawalaCache();
		break;
	default:
		echo "error";
}

function nawalaAjaxClearNawalaCache()
{
//	$cache = JFactory::getCache();
//	$cache->clean('nawala');

	$config = NFactory::getConfig();

	// Load the formatter
	$formatter = NFactory::getClass('formatter.json');

	$folder = $config->getPlatform()->cachePath;
	if ( JFolder::exists($folder) )
	{
		JFolder::delete($folder);
		return $formatter->gantryAdmin(JText::_('Nawala cache cleared.'));
	}
	else
	{
		return $formatter->gantryAdmin(JText::_('Nawala cache already cleared.'));
	}
}