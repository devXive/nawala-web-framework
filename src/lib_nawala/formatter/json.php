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
 * Nawala Framework FormatterJson Class
 *
 * This class allows the nawala framework use for gantry templates
 * and the unrelated use of functions for newer frameworks than its
 * integrated in gantry itself, such as Bootstrap3, etc...
 *
 * @package       Framework
 * @subpackage    Formatter
 * @since         1.0
 */
class NFormatterJson
{
	/**
	 * Constructor
	 */
	function __construct()
	{
		// Initialize global nawala
		global $nawala;
	}


	/**
	 * Method to get a simple message output for ajax calls
	 * 
	 * @param     string     $message    Preformatted message for further use (may for growl like notifications)
	 * 
	 * @return    json
	 */
	public function simple($message = null) {
		return json_encode( $message );
	}


	/**
	 * Method to get a simple message output for ajax calls, optimized for calls in gantry admin area
	 * 
	 * @param     string     $message    Preformatted message for further use (may for growl like notifications)
	 * 
	 * @return    json
	 */
	public function gantryAdmin($message = null) {
		return $message;
	}


	/**
	 * Method to get the core basic/standard output for nawala ajax calls
	 * 
	 * @param     boolean    $status     Status of the response (true|false)
	 * @param     string     $message    Preformatted message for further use (may for growl like notifications)
	 * @param     int        $id         Database item id if available (Standard = 0)
	 * 
	 * @return    json
	 */
	public function core($status = false, $message = null, $id = 0) {
		$array = array(
			'status' => $status,
			'message' => $message,
			'id' => $id
		);

		return json_encode( $array );
	}
}