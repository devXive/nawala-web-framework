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
 * Nawala Framework NCoreUtilityOnline Class
 *
 * @package       Framework
 * @subpackage    Utility
 * @since         1.1
 */
class NCoreUtilityOnline
{
	/**
	 * @var string    $instance    Timestamp of current instance to know last updated state
	 */
	private static $instance;

	/**
	 * @var    object    $isOnline    Determine if we have an online connection
	 */
	public $isOnline = null;

	/**
	 * @var    object    $isRemote    Determine if we are on a remote server
	 */
	public $isRemote = null;

	/**
	 * @var    object    $isSSL    Determine if we are connected with ssl
	 */
	public $isSSL = null;


	/**
	 * Constructor
	 */
	function __construct( array $data = array() ) {
		// Init global
		global $nawala;

		// Set connection info
		$this->getConnectionInfo();
	}


	/**
	 * Method to check and set connection vars
	 * 
	 * @return array
	 */
	public function getConnectionInfo() {
		$this->getOnlineInfo();
		$this->getRemoteInfo();
		$this->getSSLInfo();

		$connArray = array(
			'isOnline' => $this->isOnline,
			'isRemote' => $this->isRemote,
			'isSSL'    => $this->isSSL
		);

		return $connArray;
	}


	/**
	 * Method to check the online connection
	 * TODO: Add more connection stuff will be API#s or whatever else to communicate with oter services or servers under /connection/... .php
	 *       Examples:
	 *           http://stackoverflow.com/questions/4860365/determine-in-php-script-if-connected-to-internet
	 *           http://menzerath.eu/artikel/php-fsockopen/
	 *           http://www.php.net/manual/de/function.fsockopen.php
	 *       May it is better to store some connection info in /cache/nawala/system with an access denie (look at gantry cache for example)
	 *
	 * @param boolean $force    Force check and ignore needUpdate
	 *
	 * @return boolean
	 */
	private function getOnlineInfo( $force = false ) {
		if ( self::needUpdate(300) || $this->isOnline == null || $force )
		{
			$connected = @fsockopen('google.com', 80, $errno, $errstr, 5);
	
			if ( $connected )
			{
				$this->isOnline = true;
			}
			else
			{
				$this->isOnline = false;
			}
	
			fclose($connected);
		}
	}
	
	
	/**
	 * Method to check the remote connection
	 *
	 * @param boolean $force    Force check and ignore needUpdate
	 *
	 * @return boolean
	 */
	private function getRemoteInfo( $force = false ) {
		if ( self::needUpdate(300) || $this->isRemote == null || $force )
		{
			// TODO: Add check here
			$this->isRemote = false;
		}
	}
	
	
	/**
	 * Method to check if the current access is within a SSL connection
	 *
	 * @return boolean
	 */
	private function getSSLInfo()
	{
		$this->isSSL = false;
	
		if ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off' ) {
			$this->isSSL = true;
		}
	}


	/**
	 * Method to determine if we need an update (used in methods and functions with timeout or any other tests with a longer test) to increase load speed
	 * 
	 * @param    int    $seconds    Seconds which should min between the saved instance and now. Standard timeout is 900 seconds (15min)
	 * 
	 * @return boolean    True if we need an update, false if we don't
	 * 
	 * TODO: Check if we really need this function or if we should go to work with a javascript check!
	 */
	private static function needUpdate( $seconds = 900 ) {
		$instance = self::$instance + $seconds;
		$timestamp = time();

		if ( $timestamp >= $instance ) {
			self::$instance = $timestamp;
			return true;
		} else {
			return false;
		}
	}
}