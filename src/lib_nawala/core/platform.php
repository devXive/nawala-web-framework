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
 * Nawala Framework NCorePlatform Class
 *
 * Class that build and push all informations we need in all nawala environments
 *
 * @package       Framework
 * @subpackage    Platform
 * @since         1.0
 */
class NCorePlatform
{
	/**
	 * @var string
	 */
	public $name;
	public $nameRDK;

	/**
	 * @var string
	 */
	public $version;

	/**
	 * @var    string    $baseUrl    Relative url to the webos (instance eg: "/mySiteInSubfolder")
	 */
	public $baseUrl;

	/**
	 * @var    string    $baseHttp    Full url to the webos root (http://127.0.0.1/mySiteInSubfolder)
	 */
	public $baseHttp;

	/**
	 * @var    string    $basePath    System path to the webos instance (var/www/mySiteInSubfolder)
	 */
	public $basePath;

	/**
	 * @var    string    $baseRoot    Server filesystem root path (eg: /var/www)
	 */
	public $baseRoot;

	/**
	 * @var    string    $baseAdminUrl    Relative url to the webos administrator (instance eg: "/mySiteInSubfolder/administrator")
	 */
	public $baseAdminUrl;

	/**
	 * @var    string    $baseAdminPath    System path to the webos instance administrator (var/www/mySiteInSubfolder/administrator)
	 */
	public $baseAdminPath;

	/**
	 * @var    string    $cachePath    Path of the nawala cache folder
	 * @var    string    $cacheUrl     Url of the nawala cache folder  // @deprecated in 2.0. Use core global -> convertToUrl method to get a relative url
	 */
	public $cachePath;
	public $cacheUrl;
	public $cacheAdminPath;
	public $cacheAdminUrl;

	/**
	 * @var    string    $nawalaPath    System path to nawala library
	 */
	public $nawalaPath;

	/**
	 * @var    string    $nawalaUrl     Url path to nawala library
	 * @deprecated in 2.0. Use core global -> convertToUrl method to get a relative url
	 */
	public $nawalaUrl;

	/**
	 * Name of the nawala ajax component
	 */
	public $nawalaAjaxComponentName;

	/**
	 * Path/Url to the nawala component
	 */
	public $nawalaComponentUrl;
	public $nawalaComponentPath;
	public $nawalaAdminComponentUrl;
	public $nawalaAdminComponentPath;

	/**
	 * @var    string     $nawalaAjaxUrl    Url to the nawala built-in ajax component/processor
	 */
	public $nawalaAjaxUrl;

	/**
	 * @var    string     $gantryAjaxUrl    Url to the gantry built-in ajax function
	 */
	public $gantryAjaxUrl;

	/**
	 * @var    string     $coreAjaxUrl    Url to the core ajax component
	 */
	public $coreAjaxUrl;

	/**
	 * @var string
	 */
	public $php_version;

	/**
	 * @var string
	 */
	public $webos;

	/**
	 * @var string
	 */
	public $generator;

	/**
	 * @var string
	 */
	public $webos_version;

	/**
	 * @var string
	 */
	public $shortVersion;

	/**
	 * @var string;
	 */
	public $longVersion;

	/**
	 * @var string
	 */
	public $jslib;

	/**
	 * @var string
	 */
	public $jslib_version;

	/**
	 * @var string
	 */
	public $jslib_shortname;

	/**
	 * @var string
	 */
	public $stylelib;

	/**
	 * @var string
	 */
	public $stylelib_version;

	/**
	 * @var string
	 */
	public $stylelib_shortname;

	/**
	 * Library and platform specific filenames, used to check on addScript, if we have an appropriate file in here
	 * @var array
	 */
	public $js_file_checks = array();

	/**
	 * Library and platform specific filenames, used to check on addStyle, if we have an appropriate file in here
	 * @var array
	 */
	public $css_file_checks = array();

	/**
	 * Library and platform specific filenames, used to check on addLess, if we have an appropriate file in here
	 * @var array
	 */
	public $less_file_checks = array();

	/**
	 * Platform specific subfolders, used to check on addScript/addStyle and therefore if we have an appropriate file in here
	 * @var array
	 */
	public $platform_dir_checks = array();

	/**
	 * Ajax model specific subfolders, used to check on new NAjax(); $ajax->useModel() and therefore if we have an appropriate file in here
	 * @var array
	 */
	public $ajax_model_dir_checks = array();

	/**
	 * @var boolean    True if webOS >= 3.2.x, else false
	 */
	public $full_nrdk;

	/**
	 * @var boolean
	 */
	public $gantryActive;

	/**
	 * RDK wide search paths
	 * @var array
	 */
	public $searchPaths = array();


	/**
	 * @var array
	 */
	private static $bcMatrix;
	protected $backwardCompatibilityMatrix;


	/**
	 * Constructor
	 * Note that static vars can not be override!
	 */
	public function __construct( array $data = array() )
	{
		// Init global
		global $nawala;

		$nawalaApp = NCoreUpdates::getInstance();
		$this->name     = 'nawala';
		$this->nameRDK  = 'nrdk';
		$this->version  = $nawalaApp->getCurrentVersion();

		$this->baseUrl       = JUri::base(true);
		$this->baseHttp      = $this->cleanPath( JURI::base() );
		$this->basePath      = $this->cleanPath( JPATH_ROOT );
		$this->baseRoot      = $this->cleanPath( $_SERVER['DOCUMENT_ROOT'] );
		$this->baseAdminUrl  = JUri::base(true) . '/administrator';
		$this->baseAdminPath = $this->cleanPath( JPATH_ROOT ) . '/administrator';
		
		// Deprecated in 2.0, use base instead
		$this->instanceUrlPath          = JURI::root(true);
		$this->instanceFilesystemPath   = $this->cleanPath(JPATH_ROOT);

		$this->cachePath      = $this->basePath . '/cache/' . $this->nameRDK;
		$this->cacheUrl       = $this->baseUrl . '/cache/' . $this->nameRDK; // @deprecated in future. Use core global $path -> convertToUrl method to get the relative url
		$this->cacheAdminPath = $this->baseAdminPath . '/cache/' . $this->nameRDK;
		$this->cacheAdminUrl  = $this->baseAdminUrl . '/cache/' . $this->nameRDK; // @deprecated in future. Use core global $path -> convertToUrl method to get the relative url

		$this->nawalaPath = $this->basePath . '/libraries/' . $this->name;
//		$this->nawalaPath = $this->cleanPath(realpath(dirname(__FILE__) . '/' . ".."));
		$this->nawalaUrl  = $this->baseUrl . '/libraries/' . $this->name; // @deprecated in future. Use core global $path -> convertToUrl method to get the relative url
//		$this->nawalaUrl  = $this->baseUrl . '/libraries/nawala';

		$this->nawalaAjaxComponentName = 'com_' . $this->name;

		$this->nawalaComponentUrl  = $this->baseUrl . '/components/' . $this->nawalaAjaxComponentName; // @deprecated in future. Use core global $path -> convertToUrl method to get the relative url
		$this->nawalaComponentPath = $this->basePath . '/components/' . $this->nawalaAjaxComponentName;
		$this->nawalaAdminComponentUrl  = $this->baseAdminUrl . '/components/' . $this->nawalaAjaxComponentName; // @deprecated in future. Use core global $path -> convertToUrl method to get the relative url
		$this->nawalaAdminComponentPath = $this->baseAdminPath . '/components/' . $this->nawalaAjaxComponentName;

		$this->php_version = phpversion();

		$this->getPlatformInfo();

		$this->checkNRDKCompatibility( $this->webos_version );

		// Set urls of built-in ajax processor
		$this->getAjaxUrls();

		// Check if gantry is active
		global $gantry;
		$this->gantryActive = empty($gantry) ? false : true;

		$matrix = self::setBackwardCompatibilityMatrix();
		$this->backwardCompatibilityMatrix = $matrix;

		$this->setSearchPaths(array(
			$this->nawalaPath . '/assets',
		));
	}


	/**
	 * Method to check webOS and determine if we have full_nrdk compatibility
	 * 
	 * @var    boolean    $full_nrdk    True if webOS >= 3.2.x, else false
	 */
	private function checkNRDKCompatibility( $webos_version )
	{
		$this->full_nrdk = version_compare($webos_version, '3.2', '>=') ? true : false;
	}


	/**
	 * Method to set the backward compatibility matrix
	 * @return mixed
	 */
	private static function setBackwardCompatibilityMatrix()
	{
		$matrix = array();

		// Older versions
		$matrix['1.5']['1.5'] = true;
		
		$matrix['1.6']['1.5'] = true;
		$matrix['1.6']['1.6'] = true;

		$matrix['1.7']['1.6'] = true;
		$matrix['1.7']['1.7'] = true;

		$matrix['2.0']['1.6'] = true;
		$matrix['2.0']['1.7'] = true;
		$matrix['2.0']['2.0'] = true;

		$matrix['2.5']['1.6'] = true;
		$matrix['2.5']['1.7'] = true;
		$matrix['2.5']['2.0'] = true;
		$matrix['2.5']['2.5'] = true;

		$matrix['3.0']['3.0'] = true;
		$matrix['3.0']['3.1'] = true;
		$matrix['3.0']['3.2'] = true;

		$matrix['3.1']['3.0'] = true;
		$matrix['3.1']['3.1'] = true;
		$matrix['3.1']['3.2'] = true;

		$matrix['3.2']['3.0'] = true;
		$matrix['3.2']['3.1'] = true;
		$matrix['3.2']['3.2'] = true;

		self::$bcMatrix = $matrix;

		return $matrix;
	}


	/**
	 * Method to get advanced platform informations
	 * 
	 * TODO: Build 3.3 and 4.0 versions with jquery 1.11+ and bootstrap 3.2.x
	 */
	private function getPlatformInfo()
	{
		// See if its Nawala!RDK supported WebOS
		if (defined('_NRDKRA') && defined('_JEXEC') && defined('JVERSION')) {
			$this->webos         = 'joomla';
			$this->webos_version = JVERSION;
			if (version_compare(JVERSION, '3.0', '>=')) {
				$this->getOS30Info();
			} else if (version_compare(JVERSION, '4.0', '>=')) {
				$this->unsuportedInfo();
//				$this->getOS40Info();
			} else {
				$this->unsuportedInfo();
			}
		} else if (defined('_NRDKRA') && defined('_MEXEC') && defined('MVERSION')) {
			$this->unsuportedInfo();
//			$this->webos = 'mootombo';
//			$this->getOS40Info();
		} else {
			$this->unsuportedInfo();
		}

		// Set the generator tag
		$this->generator = 'MOOTOMBO!WebOS - Enterprise Edition';
	}


	/**
	 *
	 */
	private function unsuportedInfo()
	{
		foreach (get_object_vars($this) as $var_name => $var_value) {
			if (null == $var_value) $this->$var_name = "unsupported";
		}
	}

	/**
	 * Build an OS3.0 base set of relevant informations
	 */
	private function getOS30Info()
	{
		$jversion                 = new JVersion;
		$this->shortVersion       = $jversion->RELEASE;
		$this->longVersion        = $jversion->getShortVersion();
		$this->jslib              = 'jquery';
		$this->jslib_shortname    = 'jqy';
		$this->jslib_version      = '1.10.2';
		$this->stylelib           = 'bootstrap';
		$this->stylelib_shortname = 'bs';
		$this->stylelib_version   = '2.3.2';
		$this->js_file_checks     = array(
			'.' . $this->jslib,													// eg: .jquery
			'.' . $this->jslib . '-' . $this->jslib_version,					// eg: .jquery-1.10.2
			'.' . $this->jslib_shortname . '-' . $this->jslib_version			// eg: .jqy-1.10.2
		);
		$this->css_file_checks  = array(
			'.' . $this->stylelib,												// eg: .bootstrap
			'.' . $this->stylelib . '-' . $this->stylelib_version,				// eg: .bootstrap-2.3.2
			'.' . $this->stylelib_shortname . '-' . $this->stylelib_version		// eg: .bs-2.3.2
		);
		$this->less_file_checks  = array(
			'.' . $this->stylelib,												// eg: .bootstrap
			'.' . $this->stylelib . '-' . $this->stylelib_version,				// eg: .bootstrap-2.3.2
			'.' . $this->stylelib_shortname . '-' . $this->stylelib_version		// eg: .bs-2.3.2
		);
		$this->platform_dir_checks    = array(
			'/' . $this->webos . '/' . $this->longVersion,						// eg: /joomla/3.2.0
			'/' . $this->webos . '/' . $this->shortVersion,						// eg: /joomla/3.2
			''																	// eg: 
		);
		$this->ajax_model_dir_checks     = array(
			'/' . $this->webos . '/' . $this->longVersion,						// eg: /joomla/3.2.0
			'/' . $this->webos . '/' . $this->shortVersion,						// eg: /joomla/3.2
			''																	// eg: 
		);
	}


	/**
	 * Build possible filenames or filepaths based on appropriate getOS__Info
	 * Library based checks
	 * 
	 * @param    string     $file         Filepath and/or filename with file extension
	 * @param    bool       $keep_path    If true, the path will be kept, on false, the filenames only will return
	 *
	 * @return   array
	 */
	public function getAdditionalLibraryChecks($file, $keep_path = false)
	{
		$checkfiles = array();
		$ext        = substr($file, strrpos($file, '.'));
		$path       = ($keep_path) ? dirname($file) . '/' : '';
		$filename   = basename($file, $ext);

		switch ($ext) {
			case '.js':
				foreach ($this->js_file_checks as $suffix) {
					$checkfiles[] = $path . $filename . $suffix . $ext;
				}
				break;
			case '.css':
				foreach ($this->css_file_checks as $suffix) {
					$checkfiles[] = $path . $filename . $suffix . $ext;
				}
				break;
			case '.less':
				foreach ($this->less_file_checks as $suffix) {
					$checkfiles[] = $path . $filename . $suffix . $ext;
				}
				break;
			default:
				return false;
		}

		return $checkfiles;
	}


	/**
	 * Build possible paths based on appropriate getOS__Info
	 *
	 * @param      $dir    Directory where to look for
	 * @param      $dir
	 *
	 * @return array
	 */
	public function getAdditionalPlatformChecks($dir, $subfolder = null)
	{
		$dir = rtrim($dir,'/\\');
		$checkfiles = array();
		foreach ($this->platform_dir_checks as $platformdir) {
			$checkfiles[] = $dir . $platformdir;
		}

		return $checkfiles;
	}


	/**
	 * Build possible paths based on appropriate getOS__Info
	 *
	 * @param      $dir
	 *
	 * @return array
	 */
	public function getAjaxModelChecks($dir)
	{
		$dir = rtrim($dir,'/\\');
		$checkfiles = array();
		foreach ($this->ajax_model_dir_checks as $ajaxmodeldir) {
			$checkfiles[] = $dir . $ajaxmodeldir;
		}

		return $checkfiles;
	}


	/**
	 * Get available platform version related directories (directories found in the given folder, based on the current platform and all subfolders which are found as subfolder)
	 * NOTE: This will ONLY return subfolders that are found in the given directory and NOT the directory itself.
	 * If a component name is given, this will check if the components subfolder exists under the appropriate platform subfolder!
	 * 
	 * 
	 * @param string $dir    Note: Only absolute paths are supported at this time (eg: /var/www/myWebsiteRoot/templates/myTemplate/css)
	 *                             Example above list all possible versions folders
	 * @param string $component    The component name to build dirs for
	 * @param bool   $useMatrix    Use the compatibility matrix to get only available directories to avoid to load files from older webOS versions
	 * 
	 * @return string
	 */
	public function getAvailablePlatformVersions($dir, $component = null, $useMatrix = true)
	{
		$dir  = rtrim($dir,'/\\');
		$keys = array();

		// Find all entries in the dir
		$entries = array();
		$platform_dir = $dir . '/' . $this->webos;
		$handle = @opendir($platform_dir);
		if ( $handle ) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".." && !preg_match('/^\./',$entry) && is_dir($platform_dir . '/' . $entry)) {
					$key          = (preg_match('/^\d+\.\d+$/', $entry)) ? $entry . '.0' : $entry;
					$keys[$entry] = $key;
				}
			}
			closedir($handle);
		}

		// Check the version compatibility matrix and sort the array
		if ( $useMatrix ) {
			$versions = array_filter($keys, array('NCorePlatform','bcMatrixVersionFilter'));
		} else {
			$versions = array_filter($keys, array('NCorePlatform','bcVersionFilter'));
		}
		uksort($versions, 'version_compare');
		$returned_array = array_reverse(array_keys($versions));

		// Build the appropriate paths
		foreach ( $returned_array as $version ) {
			if ( $component ) {
				$component_dir = $platform_dir . '/' . $version . '/' . $component;
				if ( is_dir($component_dir) && file_exists($component_dir) ) {
					$entries[] = $component_dir;
				}
			}
			$entries[] = $platform_dir . '/' . $version;
		}

		return $entries;
	}

	public static function bcMatrixVersionFilter($version)
	{
		$jversion = new JVersion();

		// Get the backward version compatibilities (bvcs)
		$scversion = explode('.', $jversion->getShortVersion());
		$bvcs      = self::$bcMatrix[$scversion[0] . '.' . $scversion[1]];

		foreach ( $bvcs as $bv => $bool ) {
			if ( version_compare($version, $bv, '>=') && version_compare($version, $jversion->getShortVersion(), '<=') ) {
				return true;
			}
		}

		return false;
	}

	public static function bcVersionFilter($version)
	{
		$jversion = new JVersion();
		return version_compare($version, $jversion->getShortVersion(), '<=');
	}


	/**
	 * Build possible paths based on appropriate getOS__Info
	 *
	 * @param      $dir
	 *
	 * @return array
	 */
	public function getTemplateChecks($dir)
	{
		$dir = rtrim($dir,'/\\');
		$checkfiles = array();
		foreach ($this->template_checks as $templatedir) {
			$checkfiles[] = $dir . $templatedir.'/';
		}
	
		return $checkfiles;
	}


	/**
	 * Build possible paths based on appropriate getOS__Info
	 *
	 * @param      $dir
	 *
	 * @return array
	 */
	public function getLibChecks($dir)
	{
		$dir = rtrim($dir,'/\\');
		$checkfiles = array();
		foreach ($this->library_checks as $librarydir) {
			$checkfiles[] = $dir . $librarydir.'/';
		}
	
		return $checkfiles;
	}


	/**
	 * @return string
	 */
	public function getJSInit()
	{
		return $this->jslib_shortname . '_' . str_replace('.', '_', $this->jslib_version);
	}


	/**
	 * @return mixed
	 */
	public function getJslib()
	{
		return $this->jslib;
	}


	/**
	 * @return mixed
	 */
	public function getJslibShortname()
	{
		return $this->jslib_shortname;
	}


	/**
	 * @return mixed
	 */
	public function getJslibVersion()
	{
		return $this->jslib_version;
	}


	/**
	 * @return string
	 */
	public function getPhpVersion()
	{
		return $this->php_version;
	}


	/**
	 * @return mixed
	 */
	public function getPlatform()
	{
		return $this->webos;
	}


	/**
	 * @return mixed
	 */
	public function getPlatformVersion()
	{
		return $this->webos_version;
	}


	/**
	 * @return string
	 */
	public function getShortVersion()
	{
		return $this->shortVersion;
	}


	/**
	 * @return string
	 */
	public function getLongVersion()
	{
		return $this->longVersion;
	}


	/**
	 * Method to get the correct url of the nawala built-in ajax processor
	 * 
	 * @return void
	 * 
	 * TODO: Check if baseUrl sets correct to https links !!!
	 */
	 protected function getAjaxUrls() {
		$app = JFactory::getApplication();
	 	
	 	$ajaxUrls = new stdClass();

		$this->nawalaAjaxUrl = '/index.php?option=' . $this->nawalaAjaxComponentName . '&task=ajax&format=raw&template=' . $app->getTemplate() . '&lib=' . $this->name;
		$this->gantryAjaxUrl = '/index.php?option=com_gantry&task=ajax&format=raw&template=' . $app->getTemplate() . '&lib=gantry';
		$this->coreAjaxUrl   = '/index.php?option=com_ajax&format=json';

		$ajaxUrls->nawalaAjaxUrl = $this->nawalaAjaxUrl;
		$ajaxUrls->gantryAjaxUrl = $this->gantryAjaxUrl;
		$ajaxUrls->coreAjaxUrl   = $this->coreAjaxUrl;

		return $ajaxUrls; 
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
	 * Set platform wide search paths. Note only valid paths will be added!
	 * 
	 * @param multitype: $searchPaths
	 */
	public function setSearchPaths( $searchPaths )
	{
		$paths = array();

		if ( is_array($searchPaths) ) {
			foreach ( $searchPaths as $searchPath ) {
				if ( is_dir($searchPath) && file_exists($searchPath) ) {
					$paths[] = $searchPath;
				}
			}
		} else {
			if ( is_dir($searchPaths) && file_exists($searchPaths) ) {
				$paths[] = $searchPaths;
			}
		}

		$sPaths = $this->searchPaths;
		$updateArray = array_merge($sPaths, $paths);

		$this->searchPaths = $updateArray;
	}
}