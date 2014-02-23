<?php
/**
 * @package          Nawala Rapid Development Kit
 * @subPackage       Postinstall
 * @author           devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright        Copyright (C) 1997 - 2014 devXive - research and development. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense    devXive Proprietary Use License (http://www.devxive.com/license)
 */

/**
 * Checks if the plugin is enabled. If not it returns true, meaning that the
 * message concerning nawala should be displayed.
 * 
 * To get more informations about, please review the JMagazine Article
 * http://magazine.joomla.org/issues/issue-december-2013/item/1649-joomla-3-2-new-features-postinstall-messages
 * 
 * Language Tools
 * http://docs.joomla.org/Creating_a_language_definition_file
 * 
 * @return  integer
 * 
 * @since   3.2
 */
function nawala_postinstall_condition()
{
	// Load extra lib language file from assets
	$lang = JFactory::getLanguage();
	$lang->load('lib_nawala', JPATH_LIBRARIES . '/nawala/assets', $lang->getTag(), true);

	$db = JFactory::getDbo();

	// Count all lib_nawala post messages
	$query = $db->getQuery(true)
		->select('*')
		->from($db->qn('#__postinstall_messages'))
		->where($db->qn('language_extension') . ' = ' . $db->q('lib_nawala'));
	$db->setQuery($query);
	$messages = $db->loadObjectList();

	return count($messages) > 0;
}