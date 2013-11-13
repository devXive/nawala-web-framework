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

// Require Lessc
require "lessc.inc.php";

/**
 *
 */
class NCompilerLess extends lessc
{
	/**
	 * Template name of the active template
	 * @var
	 */
	protected static $template;

	/**
	 * Template path of the active template
	 * @var
	 */
	protected static $templatePath;

	/**
	 * @var
	 */
	protected static $urlPath;

	/**
	 * @var
	 */
	protected static $pathArray;

	/**
	 * Array of compiled files to load in header
	 * @var
	 */
	protected static $fileArray;


	/**
	 *
	 */
	public function __construct()
	{
		$this->template = NCore::get('template')->name;
		$this->templatePath = NAWALA_BASEPATH_FILESYSTEM . '/templates/' . $this->template;
		$this->urlPath = NAWALA_BASEPATH_URL;

		$this->pathArray = array(
			'templates/' . $this->template . '/less',
			'libraries/nawala/assets/less'
		);
	}


	/**
	 * Compile Less Style and add to Header
	 *
	 * @param     string    $styleDeclaration    Adding custom styles to less compiler. Eg: '.block { padding: 3 + 4px }'
	 *
	 * @return    void
	 */
	public function addLessStyle( $styleDeclaration )
	{
		$doc = JFactory::getDocument();

		$style = parent::compile( $styleDeclaration );

		$doc->addStyleDeclaration( $style );
	}


	/**
	 * Compile Less File and add to Header
	 *
	 * @param     string    $file        Filename of the file to compile. Looks first in templates less folder, then in nawala library assets/less folder
	 * @param     string    $compiled    Filename of the compiled file in templates css-compiled folder
	 * @param     string    $priority    Priority of the file. Should determine on which position file will be rendered/loaded
	 * @param     string    $check       Determine wether or not if the method shuld check if there already exist a compiled file or not. If the compiled file is older than it original or the file does not exist it will create a new one.
	 *
	 * @return    void
	 */
	public function addLess( $file, $compiled = false, $priority = 0, $lessVariables = false, $check = true )
	{
		$doc = JFactory::getDocument();

		// Find the file in the appropriate pathArray
		if ( !$fileIn = JPath::find($this->pathArray, $file) ) {
			return false;
		}

		// Check for existing index.html file in templates css-compiled folder. If not exist, create the file, folder will be created automatically
		$indexFile = $this->templatePath . '/css-compiled/index.html';
		if ( !JFile::exists($indexFile) ) {
			$buffer = '<!DOCTYPE html><title></title>';
			JFile::write($indexFile, $buffer);
		}

		if ( !$compiled ) {
			$compiled = str_replace('.less', '.css', $file);
		}

		$fileOut = $this->templatePath . '/css-compiled/' . $compiled;

		if ( $lessVariables ) {
			parent::setVariables($lessVariables);
		}

		if ( $check ) {
			$style = parent::checkedCompile( $fileIn, $fileOut );
		} else {
			$style = parent::compileFile( $fileIn, $fileOut );
		}

		$fileUrl = $this->urlPath . '/templates/' . $this->template . '/css-compiled/' . $compiled;

		// Add the style
		NFactory::addStyle( $fileUrl, $priority );
	}


	/**
	 * Method to add include paths where the compiler should look in to find the appropriate file
	 *
	 * @return mixed
	 */
	public function addPath( $addPath )
	{
		$newPaths = array();

		// Get existing paths
		if ( isset($this->pathArray) ) {
			foreach ( $this->pathArray as $key => $val ) {
				array_push($newPaths, NAWALA_BASEPATH_FILESYSTEM . '/' . $val);
			}
		}

		// Check and get new path/s
		if ( is_array($addPath) ) {
			foreach ( $addPath as $paths ) {
				array_push($newPaths, NAWALA_BASEPATH_FILESYSTEM . '/' . $paths);
			}
		} else {
			array_push($newPaths, NAWALA_BASEPATH_FILESYSTEM . '/' . $addPath);
		}

		return self::$pathArray = array_unique($newPaths);
	}


	/**
	 * Render Method (Used as last instance) after that function is called, no other less file will be imported
	 *
	 * @return mixed
	 */
	public function renderLess( $file )
	{
	}
}