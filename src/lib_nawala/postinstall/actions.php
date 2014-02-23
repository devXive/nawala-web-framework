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
 * Enables the plugin and redirects the user to their
 * user profile page so that they can enable two factor authentication on their
 * account.
 * 
 * To get more informations about, please review the JMagazine Article
 * http://magazine.joomla.org/issues/issue-december-2013/item/1649-joomla-3-2-new-features-postinstall-messages
 * 
 * Language Tools
 * http://docs.joomla.org/Creating_a_language_definition_file
 * 
 * @return  void
 * 
 * @since   3.2
 */
function nawala_postinstall_action()
{
	// Load extra lib language file from assets
	$lang = JFactory::getLanguage();
	$lang->load('lib_nawala', JPATH_LIBRARIES . '/nawala/assets', $lang->getTag(), true);

	// Enable the plugin
	$db = JFactory::getDbo();

	// Get the ntheme template id
	$query = $db->getQuery(true)
		->select('*')
		->from($db->qn('#__template_styles'))
		->where($db->qn('template') . ' = ' . $db->q('ntheme'));
	$db->setQuery($query);
	$templates = $db->loadObjectList();

	// Delete all hidden messages
	$query = $db->getQuery(true);
	$query = 'DELETE FROM ' . $db->quoteName('#__postinstall_messages') .
		' WHERE ' . $db->quoteName('language_extension') . ' = ' . $db->quote('lib_nawala') . ' AND ' . $db->quoteName('enabled') . ' = ' . $db->quote('0');
	$db->setQuery($query);
	$db->execute();

	// Redirect the user to the plugin configuration page
	$url = 'index.php?option=com_gantry&task=template.edit&id=' . $templates[0]->id;
	JFactory::getApplication()->redirect($url);
}