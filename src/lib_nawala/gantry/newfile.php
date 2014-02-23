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
 * Nawala Framework GantryHelper Class
 *
 * This class allows the nawala framework use for gantry templates
 * and the unrelated use of functions for newer frameworks than its
 * integrated in gantry itself, such as Bootstrap3, etc...
 *
 * @package Framework
 * @subpackage Gantry
 * @since 1.0
 */
class NGantryHelper
{
	/**
	 */
	const DEFAULT_STYLE_PRIORITY = 10;

	/**
	 * Path for the compressed / combined files, which will be used later to load files from
	 *
	 * @var string $cachePath System path to nawala cache dir
	 * @var string $cacheUrl Url path to nawala cache dir
	 */
	protected $cachePath;

	protected $cacheUrl;

	/**
	 *
	 * @var array $mediaPaths Array of absolute paths where to look for files used in addLess(), addStyle(), addScript().
	 */
	private $mediaPaths;

	/**
	 *
	 * @var object $compression Object that hold informations if a compression should be used or not.
	 *      strung ->less LESS compression.
	 *      string ->css CSS compression.
	 *      string ->js JS compression.
	 */
	private $compression;

	/**
	 * Hold conditional array of style variables which will be used in compiler
	 *
	 * @var object $lessOptions Object that hold informations for use in compiler
	 *      array ->variables Style variables to used for compiler.
	 *      string ->md5 MD5 version of current variables.
	 *      string ->string String version of current variables.
	 *      array ->cOptions Array of special compiler options.
	 */
	private $lessOptions;

	/**
	 *
	 * @var array $lessImportFiles Conditional array of @import files which will be used in compiler
	 */
	private $lessImportFiles;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Initialize global gantry
		global $gantry;
		
		// Initialize global nawala
		global $nawala;
		
		$this->cachePath = $nawala->document->cache->nawalaCachePath . '/media';
		$this->cacheUrl = $nawala->document->cache->nawalaCacheUrl . '/media';
		
		// Declare the main Entry Points to look for
		$this->mediaPaths = $nawala->template->mediaPaths;
		
		// Init Compressions
		$comp = new stdClass();
		$comp->less = (string) $gantry->get('less-compression', true);
		$comp->css = (string) $gantry->get('css-compression', true);
		$comp->js = (string) $gantry->get('js-compression', true);
		$this->compression = $comp;
		
		// Set empty md5 to use if we have no variables
		$lessVar = new stdClass();
		$lessVar->variables = array();
		$lessVar->md5 = md5('');
		$lessVar->string = '';
		$lessVar->coptions = array();
		$this->lessOptions = $lessVar;
		
		// Set the lessImportFiles var
		$this->lessImportFiles = array();
		
		// initCheck
		$this->_initCheck();
	}

	/**
	 * Method to check dependencies and all other stuff that is needed
	 */
	private function _initCheck()
	{
		// Initialize global nawala
		global $nawala;
		
		// Check if nawala cache folder and appropriate subdirectories exists
		$indexFiles = array(
			$this->cachePath . '/css/index.html', 
			$this->cachePath . '/css/swap/index.html', 
			$this->cachePath . '/js/index.html', 
			$this->cachePath . '/js/swap/index.html');
		
		foreach ($indexFiles as $indexFile)
		{
			if (!JFile::exists($indexFile))
			{
				$buffer = '<!DOCTYPE html><title></title>';
				JFile::write($indexFile, $buffer);
			}
		}
	}

	/**
	 * Method to set the media paths
	 * 
	 * @param string $path
	 *        	Full system path to add to the pathDirectories
	 * @param string $type
	 *        	Type of the files in this paths ( less|css|js )
	 *        	
	 * @return void
	 */
	public function setMediaPath($path, $type)
	{
		// Check supported type
		switch ($type)
		{
			case 'less':
			case 'css':
			case 'js':
				break;
			
			default:
				return;
				break;
		}
		
		// Get the media type and store in array
		$pathArray = $this->mediaPaths->get($type);
		
		if (is_array($path))
		{
			foreach ($path as $dir)
			{
				$pathArray[] = $dir;
			}
		}
		else
		{
			$pathArray[] = $path;
		}
		
		// Store back
		$this->mediaPaths->set($type, $pathArray);
	}
}