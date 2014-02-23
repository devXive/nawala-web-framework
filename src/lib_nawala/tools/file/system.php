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
 * Nawala Framework NToolsFileSystem Class
 *
 * @package       Framework
 * @subpackage    Tools
 * @since         1.2
 */
class NToolsFileSystem
{
	/**
	 * @var NCorePlatform
	 */
	protected $platform;

	/**
	 * @var message that returns in debug mode
	 */
	protected $debugMessage = null;

	/**
	 * @var File to work with
	 */
	protected $file;

	/**
	 * @var $file is a file
	 */
	public $isFile = false;

	/**
	 * @var $file is a directory
	 */
	public $isDir = false;

	/**
	 * @var blacklist which files will not be processed by
	 */
	public $blacklist = array();

	/**
	 * @var same as $file, only used in static blackListCheck()
	 */
	protected static $fileCheck;

	/**
	 * index.html file
	 */
	protected $indexFile = '<!DOCTYPE html><title></title>';


	/**
	 * Constructor
	 * Note that static vars can not be override!
	 */
	function __construct( $file = null )
	{
		// Get the nawala platform object
		$this->platform = new NCorePlatform();
		$this->setBlacklist();

		// Set the file
		if ( $file ) {
			$this->setFile($file);
		}
	}


	/**
	 * @param $file
	 */
	public function setFile($file)
	{
		$this->file = $file;
		self::$fileCheck = $file;

		$this->checkFile( $file );
		$this->checkDir( $file );
	}


	/**
	 * Method to get all variables used in a file
	 * 
	 * @return    array    Returns an array of vars found in that file, an empty array if no vars are found
	 */
	public function getFileVariables()
	{
		// Please consider that this is a file tool!
		if ( !$this->isFile ) return $this->quit(false, 'Could not locate:' . $this->file);
		if ( $this->isDir ) return $this->quit(false, 'The Path is a directory. You could not use a path for file tools');

		// Get files content
		$content = $this->getFileContent($this->file);

		// Get the vars
		preg_match_all('/\$[A-Za-z0-9-_]+/', $content, $vars);

		return $vars[0];
	}


	/**
	 * Method to get the content of a file
	 * 
	 * @return    string    Returns the files content (RAW)
	 */
	public function getFileContent()
	{
		// Please consider that this is a file tool!
		if ( !$this->isFile ) return $this->quit(false, 'Could not locate:' . $this->file);
		if ( $this->isDir ) return $this->quit(false, 'The Path is a directory. You could not use a path for file tools');

		$content = file_get_contents($this->file);

		return $content;
	}


	/**
	 * Method to create and mod a folder structure and return the relative url(s) to the created/existing path(s).
	 * 
	 * @param    string    $path      Path to dir where to reate the folder ( TIP: use $nawala->platform->basePath for a qualified absolute path )
	 *                                Example: ->createFolder( 'MyFolder' ) to create /var/www/WebsiteInstance/MyFolder
	 *                                Example: ->createFolder( 'MyFolder', $nawala->platform->basePath ) to create /var/www/WebsiteInstance/MyFolder
	 * 
	 * @param    string    $folder    Name of the folder
	 * 
	 * @return   mixed     $url       If $folder is an array, an array will return. If $folder is a string, a string will return.
	 */
	public function createFolder( $folder, $path = null )
	{
		$url = array();

		if ( is_null($path) ) {
			$app = JFactory::getApplication();
			if ( $app->isAdmin() ) {
				$path = $this->platform->basePath . '/';
			} else {
				$path = $this->platform->baseAdminPath . '/';
			}
		} else {
			// Remove the trailing slash at the end of the path
			$path = rtrim($path, '/');
		}

		$createPath = $path . '/' . $folder;

		if ( !$this->checkDir($createPath) ) {
			@JFolder::create($createPath, 0777);
			if ( !$this->checkDir($createPath) ) {
				throw new Exception(sprintf('Unable to create default directory (%s) for compiled less files. Please check your filesystem permissions.', $createPath));
			}
		}

		$this->setIndexFile( $createPath );

		return $createPath;
	}


	/**
	 * Method to check and create an index.html file if no one exists.
	 * 
	 * @param    string    $path    Path to the dir where to create the index.html file
	 * @param    bool      $subfolder    Check all subfolders and create index.html files if no one exists.
	 * @param    string    $folderPermissions    Set the folder permissions. Use 0777 only in cache dirs
	 * 
	 * @return void
	 * 
	 * @TODO    Restrict the usage of $folderPermissions and use native JFile stuff
	 */
	public function setIndexFile( $path, $subfolders = false, $folderPermissions = '0777' )
	{
		$indexFile = $path . '/index.html';

		if ( !$this->checkFile($indexFile) ) {
			@JFile::write($indexFile, $this->indexFile);
			if ( !$this->checkDir($indexFile) ) {
				$this->debugMessage = 'Unable to create an empty index.html file in directory ' . $indexFile . '. Please check your filesystem permissions.';
			}
		}
	}


	/**
	 * Method to check if file exists
	 * @return void
	 */
	public function checkFile( $file )
	{
		if ( file_exists($file) && is_file($file) && is_readable($file) ) {
			$this->isFile = true;
			return true;
		}

		return false;
	}


	/**
	 * Methof to check if direcotry exists
	 * @return void
	 */
	public function checkDir( $path )
	{
		if ( file_exists($path) && is_dir($path) ) {
			$this->isDir = true;
			return true;
		}

		return false;
	}


	/**
	 * Clean path string from trailing slashes, etc...
	 *  
	 * @param    string    $path
	 * @return   string
	 */
	protected function cleanPath($path)
	{
		if (!preg_match('#^/$#', $path)) {
			$path = preg_replace('#[/\\\\]+#', '/', $path);
			$path = preg_replace('#/$#', '', $path);
		}

		return $path;
	}


	/**
	 * Method to return either a quick short message if debug is enabled, or simply returns false
	 * 
	 * @param    boolean    $state           true|false
	 * @param    string     $debugMessage    Message for debugging
	 */
	private function quit($state = false, $debugMessage = null)
	{
		$this->debugMessage = $debugMessage;

		return $state;
	}


	/**
	 * @return the $debugMessage
	 */
	public function getDebugMessage()
	{
		return $this->debugMessage;
	}


	/**
	 * Set the file blacklist
	 */
	private function setBlacklist()
	{
		$blacklist = array(
			'configuration.php'
		);

		$this->blacklist = $blacklist;
	}

	/**
	 * Check Blacklist
	 * @TODO    Check the return !!!!
	 */
	public function checkBlacklist()
	{
		$check = array_map(array('NToolsFileSystem', 'blackListCheck'), $this->blacklist);

		return $this->quit(false, 'This file is blacklistet! File: ' . $this->file);
	}
	public static function blackListCheck($blackListEntry)
	{
		if ( preg_match('@' . $blackListEntry . '@', self::$fileCheck) ) return false;
	}
}