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
 * Nawala Framework Ajax Core Class
 *
 * This class allows the nawala framework use for gantry templates
 * and the unrelated use of functions for newer frameworks than its
 * integrated in gantry itself, such as Bootstrap3, etc...
 * 
 * AjaxURL's are set in NPlatform
 * For documentation please visit
 *     CoreAjax support:
 *           http://docs.joomla.org/Using_Joomla_Ajax_Interface
 *           https://github.com/Joomla-Ajax-Interface
 *           http://matt-thomas.me/talks/ajax-interface/#/
 * 
 *     GantryAjax
 *           http://gantry-framework.org/documentation/joomla/advanced/ajax_system.md
 * 
 *     NawalaAjax
 *           Uses a similar syntax as the gantry framework ajax system
 *
 * @package       Framework
 * @subpackage    Ajax
 * @since         1.0
 */
class NAjax
{
	/**
	 * @var Nawala Support
	 */
	public $nawalaSupport = null;

	/**
	 * @var Gantry Support
	 */
	public $gantrySupport = null;

	/**
	 * @var Core Support
	 */
	public $coreSupport = null;

	/**
	 * @var Session Token
	 */
	protected static $sessionToken = null;

	/**
	 * @var JDocument
	 */
	protected static $document = null;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Init global
		global $nawala;

		if ( !self::$sessionToken ) {
			self::$sessionToken = NFactory::getDocument()->form->get('session.token'); 
		}

		if ( !self::$document ) {
			self::$document = JFactory::getDocument();
		}
	}


	/**
	 * Method to add the form session token as var to the script declaration area in the head
	 * 
	 * @param    boolean    $checkRegistered    Get Javascript session token only for registered users?
	 */
	private static function addSessionToken( $checkRegistered = true ) {
		$doc = self::$document;

		$user = JFactory::getUser();
		
		if ( $checkRegistered ) {
			if ( $user->id != 0 ) {
				$doc->addScriptDeclaration('var SessionToken = "' . self::$sessionToken . '";');
			} else {
				$doc->addScriptDeclaration('
					var SessionToken = "";
					console.log("Security Warning: Please login first to use the Nawala Ajax API (No ajax session token generated!)");
				');
			}
		} else {
			$doc->addScriptDeclaration('var SessionToken = "' . self::$sessionToken . '";');
		}
	}


	/**
	 * Method to add nawala ajax support to the current application
	 * 
	 * @return    html    Returns the appropriate AjaxURL and the current SessionToken
	 */
	public function getNawalaAjaxSupport()
	{
		// Set the nawala support indicator
		$this->nawalaSupport = true;

		$config = NFactory::getConfig();
		$doc    = self::$document;

		// Add script declarations
		self::addSessionToken();
		$doc->addScriptDeclaration("var AjaxURL = '" . $config->getPlatform()->nawalaAjaxUrl . "';");

		// Load ajax-call.js to the head
		global $gantry;
		$gantry->addScript('ajax-call-nawala.js'); // /templates/gantry/js/ajax-call.js
	}


	/**
	 * Method to add nawala ajax support to the current application
	 * 
	 * @return    html    Returns the appropriate AjaxURL and the current SessionToken
	 */
	public function getGantryAjaxSupport()
	{
		// Set the gantry support indicator
		$this->gantrySupport = true;

		$config = NFactory::getConfig();
		$doc    = self::$document;

		// Add script declarations
		self::addSessionToken();
		$doc->addScriptDeclaration("var AjaxURL = '" . $config->getPlatform()->gantryAjaxUrl . "';");

		// Load ajax-call.js to the head
		global $gantry;
		$gantry->addScript('ajax-call-gantry.js'); // /templates/gantry/js/ajax-call.js
	}


	/**
	 * Method to add core ajax support to the current application
	 * 
	 * @return    html    Returns the appropriate AjaxURL and the current SessionToken
	 */
	public function getCoreAjaxSupport()
	{
		// Set the core support indicator
		$this->coreSupport = true;

		$config = NFactory::getConfig();
		$doc    = self::$document;

		// Add script declarations
		self::addSessionToken();
		$doc->addScriptDeclaration("var AjaxURL = '" . $config->getPlatform()->coreAjaxUrl . "';");

		// Load ajax-call.js to the head
		global $gantry;
		$gantry->addScript('ajax-call-core.js'); // /templates/gantry/js/ajax-call.js
	}
}