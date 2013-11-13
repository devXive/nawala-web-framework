<?php
/**
 * @version   $Id: gantryupdates.class.php 4060 2012-10-02 18:03:24Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('_NRDKRA') or die();


/**
 *
 */
class NCore
{
	/**
	 * @var
	 */
	static $instance;

	/**
	 *
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
}