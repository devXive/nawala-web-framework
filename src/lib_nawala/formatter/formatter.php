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
 * Nawala Framework Formatter Core Class
 *
 * This class allows the nawala framework use for gantry templates
 * and the unrelated use of functions for newer frameworks than its
 * integrated in gantry itself, such as Bootstrap3, etc...
 *
 * @package       Framework
 * @subpackage    Formatter
 * @since         1.0
 */
class NFormatter
{
	/**
	 * JSON Formatter
	 */
	public $json;


	/**
	 * Constructor method
	 * Initialise all subclasses or a specific
	 */
	function __construct()
	{
		// Initialize global nawala
		global $nawala;

		// Init JSON Formatter
		$this->json = new NFormatterJson();
	}
}