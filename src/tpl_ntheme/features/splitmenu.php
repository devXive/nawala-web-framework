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
 * Nawala!Theme Feature GantryFeatureSplitMenu Class
 *
 * Class that build the nawala theme splitmenu menu
 *
 * @package       Theme
 * @subpackage    Feature
 * @since         1.0
 */
class GantryFeatureSplitMenu extends GantryFeature {
	var $_feature_name = 'splitmenu';
	var $_feature_prefix = 'menu';
	var $_menu_picker = 'menu-type';

	function isEnabled()
	{
		global $gantry;

		$menu_enabled = $gantry->get('menu-enabled');
		$selected_menu = $gantry->get($this->_menu_picker);
		$cookie = 0;
		if (1 == (int)$menu_enabled && $selected_menu == $this->_feature_name && $cookie==0) return true;

		return false;
	}

	function isInPosition($position)
	{
		if ($this->get('mainmenu-position') == $position || $this->get('submenu-position') == $position || $this->get('sidemenu-position') == $position) return true;

		return false;
	}

	function isOrderable()
	{
		return false;
	}

	function render($position)
	{
		global $gantry;

		$output='';
		$renderer	= $gantry->document->loadRenderer('module');
		$options	 = array( 'style' => "menu" );

		$params=array();

		$group_params = $gantry->getParams($this->_feature_prefix."-".$this->_feature_name, true);
		$group_params_reg = new JRegistry();
		foreach ($group_params as $param_name => $param_value){
			$group_params_reg->set($param_name,$param_value['value']);
		}

		if ($position == $this->get('mainmenu-position')) {
			$params = $gantry->getParams($this->_feature_prefix."-".$this->_feature_name."-mainmenu", true);
			$module	 = JModuleHelper::getModule('mod_roknavmenu','_z_empty');

			$reg = new JRegistry();
			foreach ($params as $param_name => $param_value){
				$reg->set($param_name, $param_value['value']);
			}
			$reg->set('style', 'mainmenu');
			$reg->merge($group_params_reg);
			$module->params = $reg->toString();

			$output .= $renderer->render( $module, $options );
		}

		if ($position == $this->get('submenu-position')) {
			$params = $gantry->getParams($this->_feature_prefix."-".$this->_feature_name."-submenu", true);
			$options = array( 'style' => "submenu");
			$module	 = JModuleHelper::getModule('mod_roknavmenu','_z_empty');

			$reg = new JRegistry();
			foreach ($params as $param_name => $param_value){
				$reg->set($param_name, $param_value['value']);
			}
			$reg->set('style', 'submenu');
			$reg->merge($group_params_reg);
			$module->params = $reg->toString();
			$render = $renderer->render( $module, $options );

			$output .= $render;
		}

		if ($position == $this->get('sidemenu-position')) {
			$params = $gantry->getParams($this->_feature_prefix."-".$this->_feature_name."-sidemenu", true);
			$options = array( 'style' => "sidemenu");
			$module	 = JModuleHelper::getModule('mod_roknavmenu','_z_empty');

			$reg = new JRegistry();
			foreach ($params as $param_name => $param_value){
				$reg->set($param_name, $param_value['value']);
			}
			$reg->set('style','sidemenu');
			$reg->merge($group_params_reg);
			$module->params = $reg->toString();

			$output .= $renderer->render( $module, $options );
		}

		return $output;
	}
}