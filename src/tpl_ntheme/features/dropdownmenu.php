<?php
/**
 * @package          Nawala Rapid Development Kit
 * @subPackage       Nawala - Theme
 * @author           devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright        Copyright (C) 1997 - 2014 devXive - research and development. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense    devXive Proprietary Use License (http://www.devxive.com/license)
 * 
 * @origin based on  Gantry4, Copyright (C) 2007 - 2013 RocketTheme, LLC (http://www.rockettheme.com, http://gantry-framework.com)
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 */

// Check to ensure this file is included in Nawala!RDK environment
defined('_NRDKRA') or die();

gantry_import('core.gantryfeature');

/**
 * Nawala!Theme Feature GantryFeatureDropdownMenu Class
 *
 * Class that build the nawala theme dropdown menu
 *
 * @package       Theme
 * @subpackage    Feature
 * @since         1.0
 */
class GantryFeatureDropdownMenu extends GantryFeature {
    var $_feature_name = 'dropdownmenu';
    var $_legacy_name = 'fusionmenu';
    var $_feature_prefix = 'menu';
    var $_menu_picker = 'menu-type';

	function isEnabled()
    {
		global $gantry;

		$menu_enabled = $gantry->get('menu-enabled');
		$selected_menu = $gantry->get($this->_menu_picker);

		$cookie = 0;

		if (1 == (int)$menu_enabled && ($selected_menu == $this->_feature_name || $selected_menu == $this->_legacy_name) && $cookie == 0) return true;
		return false;
	}

	function isOrderable()
	{
		return false;
	}

	function render($position)
	{
		global $gantry;

		$renderer = $gantry->document->loadRenderer('module');
		$options = array('style' => "menu");
		$module = JModuleHelper::getModule('mod_roknavmenu','_z_empty');

		$params = $gantry->getParams($this->_feature_prefix . "-" . $this->_feature_name, true);
		$reg = new JRegistry();
        foreach ($params as $param_name => $param_value) {
			$reg->set($param_name, $param_value['value']);
		}
		$reg->set('style', 'menu');
		$module->params = $reg->toString();
		$rendered_menu = $renderer->render($module, $options);

		return $rendered_menu;
	}
}
