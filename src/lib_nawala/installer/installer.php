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
defined('_NRDKRA') or die();

/**
 * Nawala Framework Installer Core Class
 *
 * Support class to extend custom install routes and methods
 *
 * @package       Framework
 * @subpackage    Installer
 * @since         1.1
 */
class NInstaller
{
	/**
	 * Constructor
	 */
	 function __construct()
	{
	}


	/**
	 * Method to removes the admin menu item for a given component
	 *
	 * This method was pilfered from JInstallerComponent::_removeAdminMenus()
	 * /libraries/cms/installer/adapter/component.php : _removeAdminMenus
	 *
	 * @param   string   $component    The component's name
	 *
	 * @return  bool  True on success, false on failure
	 *
	 * @ref   http://stackoverflow.com/questions/10642936/component-without-admin-menu
	 * @ref   https://gist.github.com/piotr-cz/4603186, https://groups.google.com/forum/#!topic/joomla-dev-general/mjuEhvV9euw
	 * @note  matching element name + type gives uniqueness
	 * @note  Could use $parent->stepStack: Array([0] => Array('type' => 'menu', 'id' => 826));
	 */
	public function removeAdminMenus($component)
	{
		// Initialise Variables
		$db 		= JFactory::getDbo();
		$query 		= $db->getQuery(true);
		$table 		= JTable::getInstance('menu');
	
		// Get component id
		$id			= JTable::getInstance('extension')->find(array('name' => str_replace('com_', '', $component), 'element' => $component, 'client_id' => 1));
	
		// Get the ids of the menu items
		$query
		->select(	'id' )
		->from(		'#__menu' )
		->where(	$db->qn('client_id') . ' = ' . $db->q( 1 ) )
		->where(	$db->qn('component_id') . ' = ' . (int) $id )
		;
	
		$db->setQuery($query);
	
		$ids 		= $db->loadColumn();
	
		// Check for error
		if ($error = $db->getErrorMsg())
		{
			return false;
		}
		elseif ( !empty($ids))
		{
			// Iterate the items to delete each one.
			foreach ($ids as $menuid)
			{
				if (!$table->delete((int) $menuid))
				{
					return false;
				}
			}
	
			// Rebuild the whole tree
			$table->rebuild();
		}
	
		return true;
	}
}