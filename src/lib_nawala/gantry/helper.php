<?php
/**
 * @package          Nawala Rapid Development Kit
 * @subPackage	Nawala - Library
 * @author           devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright        Copyright (C) 1997 - 2013 devXive - research and development. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense    devXive Proprietary Use License (http://www.devxive.com/license)
 */

// Check to ensure this file is included in Nawala!RDK environment
defined('_NRDKRA') or die;

/**
 * Nawala Framework GantryHelper Class
 *
 * This class allows the nawala framework use for gantry templates
 * and the unrelated use of functions for newer frameworks than its
 * integrated in gantry itself, such as Bootstrap3, etc...
 *
 * @package       Framework
 * @subpackage    Gantry
 * @since         1.0
 */
class NGantryHelper
{
	/**
	 *
	 */
	const DEFAULT_STYLE_PRIORITY = 10;

	/**
	 * @var    string    $basePath    System base website path
	 * @var    string    $baseUrl     Url base website path
	 */
	static $basePath;
	static $baseUrl;

	/**
	 * @var    string    $templateName      Name of the active template
	 * @var    string    $templatePath      Path of the active template
	 * @var    string    $templateUrl       Url of the active template
	 * @var    string    $templatePrefix    Prefix of the active template
	 */
	static $templateName;
	static $templatePath;
	static $templateUrl;
	static $templatePrefix;

	/**
	 * @var    string    $basePath    System path to nawala library
	 * @var    string    $baseUrl     Url path to nawala library
	 */
	static $nawalaPath;
	static $nawalaUrl;

	/**
	 * Path for the compressed / combined files, which will be used later to load files from
	 *
	 * @var    string    $cachePath    System path to nawala cache dir
	 * @var    string    $cacheUrl     Url path to nawala cache dir
	 */
	static $cachePath;
	static $cacheUrl;

	/**
	 * @var    array    $stylePaths    Array of absolute paths where to look for files used in addLess(), addStyle(), addScript().
	 */
	static $stylePaths;

	/**
	 * @var    object    $compression    Object that hold informations if a compression should be used or not.
	 *         strung                ->less    LESS compression.
	 *         string                ->css     CSS compression.
	 *         string                ->js      JS compression.
	 */
	static $compression;

	/**
	 * Hold conditional array of style variables which will be used in compiler
	 *
	 * @var    object    $lessOptions         Object that hold informations for use in compiler
	 *         array                 ->variables    Style variables to used for compiler.
	 *         string                ->md5          MD5 version of current variables.
	 *         string                ->string       String version of current variables.
	 *         array                 ->cOptions     Array of special compiler options.
	 */
	private $lessOptions;

	/**
	 * @var    array    $lessImportFiles    Conditional array of @import files which will be used in compiler
	 */
	static $lessImportFiles;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Initialize global gantry
		global $gantry;

		$this->basePath = $gantry->basePath;
		$this->baseUrl  = $gantry->baseUrl;

		$this->templateName   = $gantry->templateName;
		$this->templateUrl    = $gantry->templateUrl;
		$this->templatePath   = $gantry->templatePath;
		$this->templatePrefix = $gantry->template_prefix;

		$this->nawalaPath = $this->basePath . '/libraries/nawala';
		$this->nawalaUrl  = $this->baseUrl . '/libraries/nawala';

		$this->cachePath = $this->basePath . '/cache/nawala';
		$this->cacheUrl  = $this->baseUrl . '/cache/nawala';

		// Declare the main Entry Points to look for
		$this->stylePaths = array(
			$this->templatePath . '/less',
			$this->templatePath . '/css',
			$this->templatePath . '/js',
			$this->nawalaPath . '/assets/less',
			$this->nawalaPath . '/assets/css',
			$this->nawalaPath . '/assets/js'
		);

		// Init Compressions
		$comp = new stdClass;
			$comp->less = (string) $gantry->get('less-compression', true);
			$comp->css = (string) $gantry->get('css-compression', true);
			$comp->js = (string) $gantry->get('js-compression', true);
		$this->compression = $comp;

		// Set empty md5 to use if we have no variables
		$lessVar = new stdClass;
			$lessVar->variables = array();
			$lessVar->md5       = md5('');
			$lessVar->string    = '';
			$lessVar->coptions  = array();
		$this->lessOptions = $lessVar;

		// Set the lessImportFiles var
		$this->lessImportFiles = array();
	}


}