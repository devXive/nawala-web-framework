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
 * Nawala Framework NCacheJoomlaNoexpiredriver Class
 *
 * @package       Framework
 * @subpackage    Cache
 * @since         1.2
 */
class NCacheJoomlaNoexpiredriver extends NCacheJoomlaDriver
{
	public function __construct($groupName)
	{
		$this->cache = JFactory::getCache($groupName, 'output');
		$handler     = 'output';
		$options     = array(
			'storage'      => 'file',
			'defaultgroup' => $groupName,
			'locking'      => true,
			'locktime'     => 15,
			'checkTime'    => false,
			'caching'      => true
		);
		$this->cache = JCache::getInstance($handler, $options);
	}
}
